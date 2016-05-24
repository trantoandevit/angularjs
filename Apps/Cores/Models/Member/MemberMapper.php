<?php

namespace Apps\Cores\Models\Member;
use Libs\SQL\Mapper;

class MemberMapper extends Mapper
{
    public function makeEntity($rawData) {
        $entity = new MemberEntity($rawData);
        return $entity;
    }
    public function tableAlias() {
        return 'm';
    }
    public function tableName() {
        return 'cores_member';
    }
    function __construct() {
        parent::__construct();
    }
    function filterName($name) {
        $this->where($this->tableAlias() . '.c_name LIKE  ("%'.$name['keyword'].'%")');
        return $this;
    }
    function filterType($type) {
        $memberType = $type['type'];
        $this->where($this->tableAlias() . '.c_type = ("'.$memberType.'")');
        return $this;
    }
    function updateMember($data,$id)
    {
        $this->db->update($this->tableName(), $data, 'pk=?', array($id));
    }
    function deleteMember($data)
    {
        if(!is_array($data['pk']))
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() . ' WHERE pk = ?',$data['pk']);
        }
        foreach ($data['pk'] as $key => $value)
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() . ' WHERE pk = ?',$value);
        }
        $this->db->ErrorNo();
    }
    
    /**
     * Tìm đơn vị theo mã đơn vị
     * @param type $memberCode
     * @param int $notId Điều kiện ID !$notId
     * @return type
     */
    function filterCode($memberCode,$notId = 0) 
    {
        $resp = $this->where($this->tableAlias().".c_code =  '$memberCode' ")
                        ->where("pk != $notId")
                        ->getAll();
        $resp = $resp->{'var'};
        return $resp;
    }
    
    /**
     * Thêm mới đơn vị
     * @param type $data
     * @return type
     */
    function addMember($data)
    {
        return $this->db->insert($this->tableName(), $data);
    }
}
