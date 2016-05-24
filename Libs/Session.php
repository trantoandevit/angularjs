<?php

namespace Libs;

use \Slim\Helper\Set;

class Session extends Set
{

    /** @var MvcContext */
    protected $context;

    function __construct(MvcContext $context)
    {
        parent::__construct($_SESSION);
        $this->context = $context;
    }
    
    function __destruct()
    {
        $_SESSION = $this->data;
    }

}
