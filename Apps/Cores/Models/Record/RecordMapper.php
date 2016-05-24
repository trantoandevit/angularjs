<?php

namespace Apps\Cores\Models\Record;

use Libs\SQL\Mapper;
use Apps\Cores\Models\RecordAttachment\RecordAttachmentMapper;

class RecordMapper extends Mapper
{

    protected $loadAttachment;

    function setLoadAttachment($bool = true)
    {
        $this->loadAttachment = $bool;
        return $this;
    }

    function loadAttachment($recordId)
    {
        return RecordAttachmentMapper::makeInstance()
                        ->filterMember($recordId)
                        ->getAll();
    }

    public function makeEntity($rawData)
    {
        $entity = new RecordEntity($rawData);
        if ($this->loadAttachment)
        {
            $entity->attachments = $this->loadAttachment($entity->pk);
        }
        $entity->count = 10;
        return $entity;
    }

    public function tableAlias()
    {
        return 'r';
    }

    public function tableName()
    {
        return 'cores_record_type';
    }

    function __construct()
    {
        parent::__construct();
    }

    function search($data)
    {
        $name = $data['name'];
        $member = $data['member'];
        $status = $data['status'];
        $page = $data['page'];
        $limit = $data['limit'];
        $this
                ->setPage($page,$limit)
                ->filterName($name)
                ->filterMember($member)
                ->filterStatus($status);
        return $this;
    }

    function filterName($name)
    {
        if ($name)
        {
            $this->where('(r.c_name LIKE ? OR r.c_code LIKE ?)', __FUNCTION__)
                    ->setParam("%$name%", __FUNCTION__ . 1)
                    ->setParam("%$name%", __FUNCTION__ . 2);
        }
        return $this;
    }

    function filterMember($member)
    {
        if ($member)
        {
            $this->where('r.fk_member  = ?', __FUNCTION__)
                    ->setParam("$member", __FUNCTION__ . 1);
        }
        return $this;
    }

    function filterStatus($status)
    {
        if ($status != "")
        {
            $this->where('r.c_status = ?', __FUNCTION__)
                    ->setParam("$status", __FUNCTION__ . 1);
        }
        return $this;
    }

    function updateRecord($Data, $pk)
    {
        $this->db->update($this->tableName(), $Data, "pk=$pk");
        return $this->db->ErrorNo() == 0 ? TRUE : $this->db->ErrorMsg();
    }

    function deleteRecord($v_list_id)
    {
        return $this->db->Execute('DELETE FROM ' . $this->tableName() . " WHERE pk in ($v_list_id)");
    }

    function addRecord($data)
    {
        #$this->db->debug = 10;
        $result = $this->where($this->tableAlias() . '.c_code =  ("' . $data['c_code'] . '")')->getAll();
        $array = $result->{'var'};
        if (count($array) == 0)
        {
            $this->db->insert($this->tableName(), $data);
            return $this->db->Insert_ID();
        }
        else
        {
            return;
        }
    }

    /**
     * Tìm thủ tục theo mã đơn vị
     * @param string $recordTypeCode Mã thủ tục
     * @param int $notId Điều kiện ID !$notId
     * @return type
     */
    function filterCode($recordTypeCode, $notId = 0)
    {
        $resp = $this->where($this->tableAlias() . ".c_code =  '$recordTypeCode' ")
                ->where("pk != $notId")
                ->getAll();
        $resp = $resp->{'var'};
        return $resp;
    }

    /**
     * Lấy danh sách thủ tục dùng cho service GetInfoTHHC
     * @param srting $member_code mã đơn vị
     * @param srting $spec_code_code Mã lĩnh vực
     * @return array
     */
    public function getAllRecord($spec_code_code)
    {
        return $this->db->GetAll("SELECT 
                                    rt.*,
                                    lv.`c_name` AS c_spec_name ,
                                          (SELECT 
                                          CONCAT('<root>',GROUP_CONCAT('<row>'
                                                                  ,CONCAT('
                                                                          <pk>',pk,'</pk>
                                                                          <c_file_name>',c_file_name,'</c_file_name>
                                                                          <c_description>',c_description,'</c_description>
                                                                          ')
                                                          ,'</row>'
                                                   SEPARATOR  '') ,'</root>')
                                    FROM `cores_record_type_attachment` WHERE `fk_record_type` = rt.pk) AS c_xml_attachment
                                  FROM
                                    `cores_record_type` rt 
                                    LEFT JOIN cores_linh_vuc lv 
                                      ON lv.pk = rt.fk_linh_vuc 
                                  WHERE  
                                    `fk_linh_vuc` = 
                                    (SELECT 
                                      pk 
                                    FROM
                                      `cores_linh_vuc` 
                                    WHERE c_code = '$spec_code_code')
                                    ");
    }

}
