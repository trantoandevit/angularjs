<?php

namespace Libs;

class RestCtrl extends Controller
{

    protected function init()
    {
        parent::init();
        $this->resp->headers->set('Content-type', 'application/json');
        //allow request from every where
        $this->resp->headers->set('access-control-allow-origin', '*');
    }

    protected function restInput($key = null, $default = null)
    {
        $body = $this->req->getBody();
        if (!$body)
        {
            $body = file_get_contents('php://input');
        }
        $json = json_decode($body, true);
        return $key === null ? $json : arrData($json, $key, $default);
    }

}
