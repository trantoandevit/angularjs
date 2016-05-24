<?php

namespace Apps\Cores\Controllers;

use Apps\Cores\Models\UserMapper;
use Apps\Cores\Models\DepartmentMapper;

class UserCtrl extends CoresCtrl
{

    protected $userMapper;
    protected $depMapper;

    function init()
    {
        parent::init();
        $this->userMapper = UserMapper::makeInstance();
        $this->depMapper = DepartmentMapper::makeInstance();
    }

    function index()
    {
        $this->requireAdmin();
        $this->twoColsLayout->render('User/user.phtml');
    }

    function group()
    {
        $this->requireAdmin();
        $this->twoColsLayout->render('User/group.phtml');
    }
    function userManager()
    {
        $this->twoColsLayout->render('User/manager.phtml');
    }
}
