<?php

namespace Apps\Cores\Models;

use Libs\SQL\Entity;

class GroupEntity extends Entity
{

    public $pk;
    public $groupCode;
    public $groupName;
    public $stt;
    public $permissions = array();

    function __construct($rawData = null)
    {
        parent::__construct($rawData);
        $this->stt = (bool) $this->stt;
    }

}
