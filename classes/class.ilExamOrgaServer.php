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
    public function prepare(Request $request, Response $response, array $args)
    {
        $this->request = $request;
        $this->response = $response;
        $this->args = $args;
        $this->params = $request->getParams();

        if (!$this->isPluginActive()) {
            $this->setResponse(StatusCode::HTTP_INTERNAL_SERVER_ERROR, 'plugin is inactive');
            return false;
        }

        // Authentication
//        $this->token = $this->plugin->getConfig()->get('access_token');
//        if (!empty($this->token)) {
//            $authorization = $this->request->getHeaderLine('Authorization');
//            if ($authorization != 'Bearer ' . $this->token) {
//                $this->setResponse(StatusCode::HTTP_UNAUTHORIZED, 'unauthorized');
//                return false;
//            }
//        }

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
                'exam_type' => (string) $record->exam_type,
                'exam_date' => (string) $record->exam_date,
                'exam_runs' => $exam_runs,
                'rum_minutes' => (int) $record->run_minutes,
                'num_participants' => (int) $record->num_participants,
                'parallel_sessions' =>(int)  $sessions,
                'monitors' => array_values($users)
            ];

            $exams[] = $exam;
        }

        return $this->setResponse(StatusCode::HTTP_OK, $exams);
    }




    /**
     * PUT links to exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    protected function putLinks(Request $request, Response $response, array $args)
    {

    }

    /**
     * PUT notes to exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    protected function putNotes(Request $request, Response $response, array $args)
    {
    }


    /**
     * Modify the response
     * @param int      $status
     * @param array    $json
     * @return Response
     */
    protected function setResponse($status, $json = []) {

        return $this->response = $this->response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status)
            ->withJson($json);
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
