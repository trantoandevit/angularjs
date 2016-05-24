<?php

namespace Apps\Cores\Models\Field;

use Libs\SQL\Entity;

class FieldEntity extends Entity
{
    function __construct($rawData = null) {
        parent::__construct($rawData);
        $this->c_status = $this->c_status ? true : false;
        $this->c_name = composite_to_unicode($this->c_name);
        $this->c_code = composite_to_unicode($this->c_code);
    }
}