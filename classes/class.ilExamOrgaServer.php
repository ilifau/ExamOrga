<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

require_once('Services/Idm/classes/class.ilDBIdm.php');
require_once(__DIR__ . '/param/class.ilExamOrgaData.php');
require_once(__DIR__ . '/record/class.ilExamOrgaRecord.php');
require_once(__DIR__ . '/notes/class.ilExamOrgaNote.php');
require_once(__DIR__ . '/links/class.ilExamOrgaLink.php');

/**
 * Video portal functions
 */
class ilExamOrgaServer extends Slim\App
{
    const MODE_TEST = 'test';
    const MODE_PROD = 'prod';
    const MODE_ANY = 'any';

    /** @var ilDBInterface */
    protected $db;

    /** @var ilDBInterface */
    protected $idm;


    /** @var ilLanguage  */
    protected $lng;

    /** @var ilAccessHandler  */
    protected $access;

    /** @var string  */
    protected $token;

    /** @var ilExamOrgaPlugin|null  */
    protected $plugin;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $args;

    /** @var array */
    protected $params;

    /**
     * GET Parameter mode=test|prod|any
     * test: only data of ExamOrga objects with setting "Test Data" will be treated
     * prod: only data of ExamOrga objects without setting "Test Data" will be treated
     * any (or empty): test and production objects will be treated
     *
     * @var string
     */
    protected $mode;

    /**
     * GET Parameter id=123
     * Selects a single record to be treated
     * The record must match the mode
     * @var int
     */
    protected $record_id;

    /**
     * GET Parameter min_date=2021-01-25
     * @var string
     */
    protected $min_date;

    /**
     * GET Parameter min_date=2021-02-25
     * @var string
     */
    protected $max_date;


    /**
     * @var ilExamOrgaData[] (indexed by obj_id)
     */
    protected $obj_data = [];

    /**
     * Condition used to query orga records
     * @var string
     */
    protected $record_condition;

    /**
     * ilExamOrgaServer constructor.
     * @param array $container
     */
    public function __construct($container = [])
    {
        global $DIC;

        $this->db = $DIC->database();
        $this->lng = $DIC->language();
        $this->access = $DIC->access();
        $this->plugin = $this->getPlugin();


        $this->idm = ilDBIdm::getInstance();


        parent::__construct($container);
    }

    /**
     * Init server / add handlers
     */
    public function init()
    {
        foreach (['', '/fix', '/dev', '/test', '/lab'] as $prefix) {

            $this->get($prefix . '/xamo/exams', array($this, 'getExams'));
            $this->get($prefix . '/xamo/links', array($this, 'getLinks'));
            $this->put($prefix . '/xamo/links', array($this, 'putLinks'));
            $this->get($prefix . '/xamo/notes', array($this, 'getNotes'));
            $this->put($prefix . '/xamo/notes', array($this, 'putNotes'));
        }
    }

    /**
     * Prepare the request processing (access check, init of properties)
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return bool
     */
    protected function prepare(Request $request, Response $response, array $args)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->params = $request->getParams();

        if (!$this->isPluginActive()) {
            $this->setResponse(StatusCode::HTTP_INTERNAL_SERVER_ERROR, 'plugin is inactive');
            return false;
        }

        // Authorization
        $this->token = $this->plugin->getConfig()->get('api_token');
        if (!empty($this->token)) {
            $authorization = $this->request->getHeaderLine('Authorization');
            if ($authorization != 'Bearer ' . $this->token) {
                $this->setResponse(StatusCode::HTTP_UNAUTHORIZED, 'unauthorized');
                return false;
            }
        }

        // Determine the mode
        switch((string) $this->params['mode']) {
            case 'test':
                $this->mode = self::MODE_TEST;
                break;
            case 'prod':
                $this->mode = self::MODE_PROD;
                break;
            case 'any':
            case '':
                $this->mode = self::MODE_ANY;
                break;

            default:
                $this->setResponse(StatusCode::HTTP_BAD_REQUEST, "mode parameter must be 'test', 'prod' or 'any'");
                return false;
        }

        // determine the record_id
        if (isset($this->params['id'])) {
            if (is_numeric($this->params['id'])) {
                $this->record_id = (int) $this->params['id'];
            }
            else {
                $this->setResponse(StatusCode::HTTP_BAD_REQUEST, "id parameter must be numeric");
                return false;
            }
        }

        // min and max date
        if (isset($this->params['min_date'])) {
            $this->min_date = (string) $this->params['min_date'];
        }
        if (isset($this->params['max_date'])) {
            $this->max_date = (string) $this->params['max_date'];
        }


        // get the settings of the objects for the mode
        foreach(ilExamOrgaData::getObjectIdsForMode($this->mode) as $obj_id) {
           $this->obj_data[$obj_id] = new ilExamOrgaData($this->plugin, $obj_id);
            $this->obj_data[$obj_id]->read();
        }

        // build the record query condition
        $conditions = [];
        $conditions[] = $this->db->in('obj_id', array_keys($this->obj_data), false, 'integer');
        if (!empty($this->record_id)) {
            $conditions[] = 'id = ' . $this->db->quote($this->record_id, 'integer');
        }
        if (!empty($this->min_date)) {
            $conditions[] = 'exam_date >= ' . $this->db->quote($this->min_date, 'text');
        }
        if (!empty($this->max_date)) {
            $conditions[] = 'exam_date <= ' . $this->db->quote($this->max_date, 'text');
        }

        $this->record_condition = implode(' AND ', $conditions);

        return true;
    }

    /**
     * GET a Json list of all exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getExams(Request $request, Response $response, array $args)
    {
        // common checks and initializations
        if (!$this->prepare($request, $response, $args)) {
            return $this->response;
        }

        $records = $this->getRecords();

        // collect  data
        $exams = [];
        $owners = [];
        $monitors = [];
        $externals = [];

        // collect user data
        foreach ($records as $record) {
            $owners[$record->owner_id] = [];
            foreach (explode(',', $record->monitors) as $login) {
                $login = trim($login);
                if(!empty($login)) {
                    $monitors[$login] = [];
                }
            }
        }

        $query = "SELECT usr_id, login, firstname, lastname, email, ext_account FROM usr_data WHERE ";

        $owner_query = $query. $this->db->in('usr_id', array_keys($owners), false, 'integer');
        $owner_result = $this->db->query($owner_query);
        while ($row = $this->db->fetchAssoc($owner_result)) {
            $owners[$row['usr_id']] = $row;
            if (!empty($row['ext_account'])) {
                $externals[$row['ext_account']] = [];
            }
        }

        $monitors_query = $query. $this->db->in('login', array_keys($monitors), false, 'text');
        $monitors_result = $this->db->query($monitors_query);
        while ($row = $this->db->fetchAssoc($monitors_result)) {
            $monitors[$row['login']] = $row;
            if (!empty($row['ext_account'])) {
                $externals[$row['ext_account']] = [];
            }
        }

        $external_query = "SELECT pk_persistent_id, mail FROM identities WHERE "
            . $this->idm->in('pk_persistent_id', array_keys($externals), false, 'text');


        $external_result = $this->idm->query($external_query);
        while ($row = $this->idm->fetchAssoc($external_result)) {
            $externals[$row['pk_persistent_id']] = $row;
        }

        foreach ($records as $record) {

            $exam_runs = [];
            foreach (explode(',', $record->exam_runs) as $run) {
                $run = trim($run);
                if (!empty($run)) {
                    $exam_runs[] = $run;
                }
            }

            $users_per_session = (int) ($this->obj_data[$record->obj_id]->get(ilExamOrgaData::PARAM_USERS_PER_SESSION));
            if (empty($users_per_session)) {
                $users_per_session = 20;
            }
            $sessions = ceil(($record->num_participants / max(count($exam_runs), 1) / $users_per_session));

            $users = [];

            if (!empty($owners[$record->owner_id])) {
                $owner = $owners[$record->owner_id];
                $login = $owner['login'];
                $user = [
                    'firstname' => (string) $owner['firstname'],
                    'lastname' => (string) $owner['lastname'],
                    'ilias_login' => (string) $owner['login'],
                    'ilias_mail' => (string) $owner['email'],
                    'idm_login' => '',
                    'idm_mail' => ''
                ];
                if (!empty($externals[$owner['ext_account']])) {
                    $external = $externals[$owner['ext_account']];
                    $user['idm_login'] = (string) $external['pk_persistent_id'];
                    $user['idm_mail'] = (string) $external['mail'];
                }
                $users[$login] = $user;
            }

            foreach (explode(',', $record->monitors) as $login) {
                $login = trim($login);
                if (!empty($login)) {
                    $user = [
                        'firstname' => '',
                        'lastname' => '',
                        'ilias_login' => (string) $login,
                        'ilias_mail' => '',
                        'idm_login' => '',
                        'idm_mail' => ''
                    ];

                    if (!empty($monitors[$login])) {
                        $monitor = $monitors[$login];
                        $user['firstname'] =(string)  $monitor['firstname'];
                        $user['lastname'] = (string) $monitor['lastname'];
                        $user['ilias_mail'] = (string) $monitor['email'];

                        if (!empty($externals[$monitor['ext_account']])) {
                            $external = $externals[$monitor['ext_account']];
                            $user['idm_login'] = (string) $external['pk_persistent_id'];
                            $user['idm_mail'] = (string) $external['mail'];
                        }
                    }
                    $users[$login] = $user;
                }
            }

            $exam = [
                'id' => (int) $record->id,
                'fau_unit' => (string) $record->fau_unit,
                'fau_chair' => (string) $record->fau_chair,
                'fau_lecturer' => (string) $record->fau_lecturer,
                'mail_address' => (string) $record->mail_address,
                'mail_title' => (string) $record->mail_address,
                'exam_title' => (string) $record->exam_title,
                'exam_format' => (string) $record->exam_format,
                'exam_type' => (string) $record->exam_type,
                'exam_date' => (string) $record->exam_date,
                'exam_runs' => $exam_runs,
                'rum_minutes' => (int) $record->run_minutes,
                'num_participants' => (int) $record->num_participants,
                'parallel_sessions' => (int)  $sessions,
                'monitors' => array_values($users),
                'links' => $this->getLinksArray($record->id),
                'notes' => $this->getNotesArray($record->id)
            ];

            $exams[] = $exam;
        }

        return $this->setResponse(StatusCode::HTTP_OK, $exams);
    }

    /**
     * GET a Json list of all links
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getLinks(Request $request, Response $response, array $args)
    {
        // common checks and initializations
        if (!$this->prepare($request, $response, $args)) {
            return $this->response;
        }

        $links = [];
        foreach ($this->getRecordsIds() as $record_id) {
            $links[] = [
                    'id' => $record_id,
                    'links' => $this->getLinksArray($record_id)
                ];
        }

        return $this->setResponse(StatusCode::HTTP_OK, $links);
    }



    /**
     * PUT links to exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function putLinks(Request $request, Response $response, array $args)
    {
        // common checks and initializations
        if (!$this->prepare($request, $response, $args)) {
            return $this->response;
        }

        $entries = $this->request->getParsedBody();

        if (!is_array($entries)) {
            return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'list of json objects expected');
        }


        $return = [];
        foreach ($entries as $entry) {

            if (is_int($entry['id']) && is_array($entry['links'])) {

                /** @var ilExamOrgaLink $link */
                $links = [];
                foreach (ilExamOrgaLink::where(['record_id' => $entry['id']])->get() as $link) {
                    $links[$link->exam_run][$link->link] = $link;
                }

                foreach ($entry['links'] as $run => $urls) {
                    foreach ($urls as $url) {
                        if (isset($links[$run][$url])) {
                            // should not be deleted
                            unset($links[$run][$url]);
                        } else {
                            // add new links
                            $link = new ilExamOrgaLink();
                            $link->record_id = $entry['id'];
                            $link->exam_run = $run;
                            $link->link = $url;
                            $link->create();
                        }
                    }
                }
                // delete the not found links
                foreach ($links as $run => $old_links) {
                    foreach ($old_links as $url => $link) {
                        $link->delete();
                    }
                }

                $return[] = [
                    'id' => (int) $entry['id'],
                    'links' => $this->getLinksArray($entry['id'])
                ];
            }
            else {
                return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'wrong entry format');
            }
        }

        return $this->setResponse(StatusCode::HTTP_OK, $return);

    }


    /**
     * GET a Json list of all notes
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function getNotes(Request $request, Response $response, array $args)
    {
        // common checks and initializations
        if (!$this->prepare($request, $response, $args)) {
            return $this->response;
        }

        $notes = [];
        foreach ($this->getRecordsIds() as $record_id) {
            $notes[] = [
                'id' => $record_id,
                'notes' => $this->getNotesArray($record_id)
            ];
        }

        return $this->setResponse(StatusCode::HTTP_OK, $notes);
    }


    /**
     * PUT notes to exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function putNotes(Request $request, Response $response, array $args)
    {
        // common checks and initializations
        if (!$this->prepare($request, $response, $args)) {
            return $this->response;
        }

        $entries = $this->request->getParsedBody();

        if (!is_array($entries)) {
            return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'list of json objects expected');
        }

        $return = [];
        foreach ($entries as $entry) {

            if (is_int($entry['id']) && is_array($entry['notes'])) {

                ilExamOrgaNote::setRecordNotesByData($entry['id'], ilExamOrgaNote::TYPE_ZOOM, $entry['notes']);

                $return[] = [
                    'id' => (int) $entry['id'],
                    'notes' => $this->getNotesArray($entry['id'])
                ];
            }
            else {
                return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'wrong entry format');
            }
        }

        return $this->setResponse(StatusCode::HTTP_OK, $return);
    }


    /**
     * Modify the response
     * @param int      $status
     * @param array    $json
     * @return Response
     */
    protected function setResponse($status, $json = [])
    {
        return $this->response = $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status)
            ->withJson($json);
    }


    /**
     * Get the relevant records
     * @return ilExamOrgaRecord[]
     */
    public function getRecords()
    {
        return ilExamOrgaRecord::where($this->record_condition)->get();
    }

    /**
     * Get the relevant record ids
     * @return int[]
     */
    public function getRecordsIds()
    {
        return array_keys(ilExamOrgaRecord::where($this->record_condition)->getArray('id', []));
    }


    /**
     * get an array of links for a record
     * @param $record_id
     * @return array run => [url, url, ...]
     */
    protected function getLinksArray($record_id)
    {

        /** @var ilExamOrgaLink $link */
        $links = [];
        foreach (ilExamOrgaLink::where(['record_id' => $record_id])->get() as $link) {
            if (!isset($links[$link->exam_run])) {
                $links[$link->exam_run] = [];
            }
            $links[$link->exam_run][] = $link->link;
        }
        return $links;
    }


    /**
     * get an array of notes for a record
     * @param $record_id
     * @return array []
     */
    protected function getNotesArray($record_id)
    {
        $notes = [];
        /** @var ilExamOrgaNote $note */
        foreach (ilExamOrgaNote::getRecordNotesForType($record_id, ilExamOrgaNote::TYPE_ZOOM) as $note) {
            $notes[] = [
                'code' => $note->code,
                'note' => $note->note
            ];
        }
        return $notes;
    }

    /**
     * Get the external content plugin object
     */
    protected function getPlugin() {
        return ilPlugin::getPluginObject(IL_COMP_SERVICE, 'Repository','robj', 'ExamOrga');
    }


    /**
     * Check if external content plugin is active
     * @return bool
     */
    protected function isPluginActive() {
        if (isset($this->plugin)) {
            return $this->plugin->isActive();
        }
        return false;
    }
}
