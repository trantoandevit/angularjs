<?php

namespace Apps\Cores\CLIs;

use Libs\CLI;

class TestCLI extends CLI
{

    function index($args)
    {
        var_dump($args);
    }

}
