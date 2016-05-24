<?php

namespace Apps\Cores\Models\Member;

use Libs\SQL\Entity;

class MemberEntity extends Entity
{
    protected $c_url_login;
        function __construct($rawData = null) {
            parent::__construct($rawData);
            $this->c_type = $this->c_type ? true : false;
            $this->c_status = $this->c_status ? true : false;
            $this->c_name = composite_to_unicode($this->c_name);
            $this->c_code = composite_to_unicode($this->c_code);
            $this->c_link_service = composite_to_unicode($this->c_link_service);
            $this->c_account = composite_to_unicode($this->c_account);
    }
}