<?php

namespace Apps\Cores\Models;

use Libs\SQL\Entity;

class DepartmentEntity extends Entity
{

    public $pk;
    public $depCode;
    public $depName;
    public $depFk;
    public $path;
    public $stt;

    /** @var DepartmentEntity */
    public $parent;

    /** @var UserEntity */
    public $users = array();

    /** @var DepartmentEntity */
    public $deps = array();

    /** @var DepartmentEntity */
    public $ancestors = array();

    function __construct($rawData = null)
    {
        parent::__construct($rawData);
        $this->stt = (bool) $this->stt;
    }

}
