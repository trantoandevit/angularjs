<?php

namespace Libs;

class MvcContext
{

    public $config;

    /** @var Bootstrap */
    public $app;
    public $path;
    public $method;
    public $controller;
    public $action;
    public $rewriteBase;
    public $id;

    /**
     * @param String|Array $path
     * @param type $method
     * @param type $controller
     * @param type $action
     */
    function __construct($path, $method, $controller, $action)
    {
        $this->path = $path;
        $this->method = $method;
        $this->controller = $controller;
        $this->action = $action;
    }

    function getId($withMethod = true)
    {
        return $withMethod ? "{$method}:$controller/$action" : "$controller/$action";
    }

}
