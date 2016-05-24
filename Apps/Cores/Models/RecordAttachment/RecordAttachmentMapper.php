<?php

namespace Apps\Cores\Models\RecordAttachment;
use Libs\SQL\Mapper;

class RecordAttachmentMapper extends Mapper
{
    
    public function makeEntity($rawData) {
        $entity = new RecordAttachmentEntity($rawData);
        return $entity;
    }
    public function tableAlias() {
        return 'rm';
    }
    public function tableName() {
        return 'cores_record_type_attachment';
    }
    function __construct() {
        parent::__construct();
    }
    function filterMember($fk)
    {
        $this->where($this->tableAlias().'.fk_record_type = ?',__FUNCTION__)
                ->setParam($fk,__FUNCTION__);
        return $this;
    }
    function getDescription($fk)
    {
        $this->db->Execute('SELECT c_description from '.$this->tableName().' WHERE pk =?',$fk);
        return $this;
    }
    function updateAttachment($data)
    {
        $id = arrData($data, 'pk');
        $update['fk_record_type']=  arrData($data, 'fk_record_type');
        $update['c_file_name']=  arrData($data, 'c_file_name');
        $update['c_description']=  arrData($data, 'c_description');
        $this->db->update($this->tableName(), $update, 'pk=?', array($id));
    }
    
    function deleteAttachment($v_file_name ='',$fk_record_type = 0)
    {
        if($v_file_name != '')
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() .' WHERE c_file_name = ?',$v_file_name);
        }
        elseif($fk_record_type > 0)
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() . " WHERE fk_record_type in ($fk_record_type) ");
        }
        return ($this->db->ErrorNo() == 0) ? TRUE : $this->db->ErrorMsg();
    }
    function addAttachment($data)
    {
        $update['fk_record_type']=  arrData($data, 'fk_record_type');
        $update['c_file_name']   =  arrData($data, 'c_file_name');
        $update['c_description'] =  arrData($data, 'c_description');
        return $this->db->insert($this->tableName(),$update);
    }
}
