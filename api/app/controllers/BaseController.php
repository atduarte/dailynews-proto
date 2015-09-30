<?php

namespace Notnull\DailyNews\Controllers;

class BaseController extends \Phalcon\Mvc\Controller
{

    public function initialize()
    {
    }

    public function redirect404()
    {
        $this->dispatcher->forward(array(
            "controller" => "home",
            "action" => "notFound"
        ));
    }

    public function json($result, $httpCode = 200)
    {
        $this->view->disable();
        $this->response->setContentType('application/json', 'UTF-8');
        $this->response->setHeader('Access-Control-Allow-Origin', '*');
        $this->response->setHeader('Access-Control-Allow-Headers', 'X-Requested-With');
        $this->response->sendHeaders();
        $this->response->setStatusCode($httpCode, '');

        echo json_encode($result);
        return false;
    }

    public function notFoundAction()
    {
        return $this->json([], 404);
    }

    public function succeed($result = [])
    {
        return $this->json($result, 200);
    }

    public function fail($result = [])
    {
        return $this->json($result, 500);
    }
}
