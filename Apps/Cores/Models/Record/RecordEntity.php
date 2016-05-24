<?php

namespace Apps\Cores\Models\Record;

use Libs\SQL\Entity;

class RecordEntity extends Entity
{
    public $attachments = array();
    function __construct($rawData = null) {
        parent::__construct($rawData);
        $this->c_status = $this->c_status ? true : false;
        $this->c_scope = $this->c_scope ? $this->c_scope : 0;
        
        
        if ($this->fk_member == null) {
            $this->fk_member = array();
        }
        else
        {
            $this->fk_member = explode(',', $this->fk_member);
            foreach($this->fk_member as &$value)
            {
                $value = (int)$value;
            }
        }
    }
}