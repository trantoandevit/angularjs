<?php

namespace Libs;

class View extends \Slim\View
{

    /** @var MvcContext */
    protected $context;

    /**
     * 
     * @param \Libs\MvcContext $context
     */
    function __construct(MvcContext $context)
    {
        parent::__construct();
        $this->context = $context;
        $this->init();
    }

    protected function init()
    {
        
    }

    function render($template, $data = array())
    {
        $this->context->app->slim->response->setBody(parent::render($template, $data));
    }

    function getOutput($template, $data = array())
    {
        $this->setData($data);
        return parent::render($template);
    }

}
