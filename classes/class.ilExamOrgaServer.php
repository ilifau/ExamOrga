<?php
// fau: videoPortal - new class ilVideoPortalServer
use Slim\Http\Request;
use Slim\Http\Response;

/**
 * Video portal functions
 */
class ilExamOrgaServerServer extends Slim\App
{
    /** @var ilLanguage  */
    protected $lng;

    /** @var ilAccessHandler  */
    protected $access;

    /** @var string  */
    protected $token;

    /** @var ilExamOrgaPlugin|null  */
    protected $plugin;


    /**
     * ilRestServer constructor.
     * @param array $container
     */
    public function __construct($container = [])
    {
        global $DIC;

        $this->lng = $DIC->language();
        $this->access = $DIC->access();
        $this->plugin = $this->getPlugin();

        parent::__construct($container);
    }

    /**
     * Init server / add handlers
     */
    public function init()
    {
        foreach (['', '/fix', '/dev', '/test', '/lab'] as $prefix) {

            // mode: test|prod
            $this->get($prefix . '/xamo/{mode}', array($this, 'getExams'));
            $this->put($prefix . '/xamo/links', array($this, 'putLinks'));
            $this->put($prefix . '/xamo/notes', array($this, 'putNotes'));
        }
    }

    /**
     * Check Access to a video portal course or clip
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    public function checkAccess(Request $request, Response $response)
    {
        if (!$this->isPluginActive()) {
            return $this->getResponse($response, false, 'inactive');
        }

        // Authentication
//        $this->token = $this->plugin->getConfig()->get('access_token');
//        if (!empty($this->token)) {
//            $authorization = $request->getHeaderLine('Authorization');
//            if ($authorization != 'Bearer ' . $this->token) {
//                return $this->getResponse($response, false, 'unauthorized')
//                    ->withStatus(\Slim\Http\StatusCode::HTTP_UNAUTHORIZED);
//            }
//        }
    }


    /**
     * Get a Json list of all exams
     * @param Request  $request
     * @param Response $response
     * @param array $args
     * @return Response
     */
    protected function getExams(Request $request, Response $response, array $args) {
        $this->checkAccess($request, $response);

        $mode = $args['mode'];
    }

    protected function putLinks(Request $request, Response $response, array $args) {
        $this->checkAccess($request, $response);

    }

    protected function putNotes(Request $request, Response $response, array $args) {
        $this->checkAccess($request, $response);

    }


    /**
     * Get the response for an access check
     * @param Response $response
     * @param bool     $success
     * @param string   $messageId
     * @return Response
     */
    protected function getResponse(Response  $response, $success, $json) {

        return $response
            ->withStatus($success ? \Slim\Http\StatusCode::HTTP_OK: \Slim\Http\StatusCode::HTTP_INTERNAL_SERVER_ERROR)
            ->withHeader('Content-Type', 'application/json')
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
