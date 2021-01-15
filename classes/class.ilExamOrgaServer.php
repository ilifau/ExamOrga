<?php
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\StatusCode;

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
     * Request Parameter ?mode='test|prod|any'
     * test: only data of ExamOrga objects with setting "Test Data" will be treated
     * prod: only data of ExamOrga objects without setting "Test Data" will be teeated
     * any (or empty): test and production objects will be treated
     *
     * @var string
     */
    protected $mode;

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

        require_once('Services/Idm/classes/class.ilDBIdm.php');
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

        $exams = [];

        require_once(__DIR__ . '/param/class.ilExamOrgaData.php');
        require_once(__DIR__ . '/record/class.ilExamOrgaRecord.php');

        $obj_ids = ilExamOrgaData::getObjectIdsForMode($this->mode);

        /** @var ilExamOrgaRecord[] $records */
        $records = ilExamOrgaRecord::where($this->db->in('obj_id', $obj_ids, false, 'integer'))->get();

        // collect user data
        $owners = [];
        $monitors = [];
        $externals = [];

        // collect user data
        foreach ($records as $record) {
            $owners[$record->owner_id] = [];
            foreach (explode(',', $record->monitors) as $login) {
                if(!empty(trim($login))) {
                    $monitors[trim($login)] = [];
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

        foreach ($records as $record)
        {
            $exam_runs = [];
            foreach (explode(',', $record->exam_runs) as $run) {
               $exam_runs[] = trim($run);
            }

            $sessions = ceil(($record->num_participants / max(count($exam_runs), 1) / 20));

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
                'links' => $this->getLinksArray($record->id)
            ];

            $exams[] = $exam;
        }

        return $this->setResponse(StatusCode::HTTP_OK, $exams);
    }

    /**
     * GET a Json list of all exams
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

        require_once(__DIR__ . '/param/class.ilExamOrgaData.php');
        require_once(__DIR__ . '/record/class.ilExamOrgaRecord.php');

        $obj_ids = ilExamOrgaData::getObjectIdsForMode($this->mode);

        /** @var ilExamOrgaRecord[] $records */
        $record_ids = array_keys(ilExamOrgaRecord::where($this->db->in('obj_id', $obj_ids, false, 'integer'))->getArray('id', []));

        if (isset($this->params['id'])) {
            if (in_array((int) $this->params['id'], $record_ids)) {
                $record_ids = [(int) $this->params['id']];
            }
            else {
                $record_ids = [];
            }
        }

        $links = [];
        foreach ($record_ids as $record_id) {
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

        require_once(__DIR__ . '/links/class.ilExamOrgaLink.php');

        $parsed = [];
        foreach ($entries as $entry) {

            if (!empty($entry['id'])  && !empty($entry['links'])
                && is_int($entry['id']) && is_array($entry['links'])) {

                /** @var ilExamOrgaLink $link */
                $links = [];
                foreach (ilExamOrgaLink::where(['record_id' => $entry['id']])->get() as $link) {
                    $links[$link->exam_run][$link->link] = $link;
                }

                $parsed_entry = [
                    'id' => (int) $entry['id'],
                    'links' => []
                ];
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
                        $parsed_entry['links'][$run][] = $url;
                    }
                }
                // delete the not found links
                foreach ($links as $run => $old_links) {
                    foreach ($old_links as $url => $link) {
                        $link->delete();
                    }
                }

                $parsed[] = $parsed_entry;
            }
            else {
                return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'wrong entry format');
            }
        }

        return $this->setResponse(StatusCode::HTTP_OK, $parsed);

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

        require_once(__DIR__ . '/notes/class.ilExamOrgaNote.php');

        $parsed = [];
        foreach ($entries as $entry) {

            if (!empty($entry['id'])  && !empty($entry['code']) && !empty($entry['note'])
                && is_int($entry['id']) && is_int($entry['code']) && is_string($entry['note'])) {

                if (!ilExamOrgaNote::where(['record_id' => $entry['id'], 'note' => $entry['note']])->count()) {
                    $note = new ilExamOrgaNote();
                    $note->record_id = $entry['id'];
                    $note->code = $entry['code'];
                    $note->note = $entry['note'];
                    $note->save();
                }

                $parsed[] = [
                    'id' => (int) $entry['id'],
                    'code' => (int) $entry['code'],
                    'note' => (string) $entry['note']
                ];
            }
            else {
                return $this->setResponse(StatusCode::HTTP_BAD_REQUEST, 'wrong entry format');
            }
        }

        return $this->setResponse(StatusCode::HTTP_OK, $parsed);
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
     * get an array of links for a record
     * @param $record_id
     * @return array run => [url, url, ...]
     */
    protected function getLinksArray($record_id)
    {
        require_once(__DIR__ . '/links/class.ilExamOrgaLink.php');

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
