<?php

namespace Apps\Cores\Models\Field;

use Libs\SQL\Mapper;

class FieldMapper extends Mapper
{

    public function makeEntity($rawData)
    {
        $entity = new FieldEntity($rawData);
        return $entity;
    }

    public function tableAlias()
    {
        return 'f';
    }

    public function tableName()
    {
        return 'cores_linh_vuc';
    }

    function __construct()
    {
        parent::__construct();
    }

    function filterName($name = '')
    {
        if ($name != '')
            $this->where($this->tableAlias() . '.c_name LIKE  ("%' . $name . '%")');
        return $this;
    }

    function filterType($type)
    {
        $memberType = $type['type'];
        $this->where($this->tableAlias() . '.c_type = ("' . $memberType . '")');
        return $this;
    }

    function updateField($update, $id)
    {
        $this->db->update($this->tableName(), $update, 'pk=?', array($id));
    }

    function deleteField($data)
    {
        if (!is_array($data['pk']))
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() . ' WHERE pk = ?', $data['pk']);
        }
        foreach ($data['pk'] as $key => $value)
        {
            $this->db->Execute('DELETE FROM ' . $this->tableName() . ' WHERE pk = ?', $value);
        }
    }

    function addField($data)
    {
        $result = $this->where($this->tableAlias() . '.c_code =  ("' . $data['c_code'] . '")')->getAll();
        $array = $result->{'var'};
        if (count($array) == 0)
        {
            $this->db->insert($this->tableName(), $data);
        }
        else
        {
            return;
        }
    }

    /**
     * Trả về danh sách lĩnh vực
     * @param string $orgCode Mã đơn vị
     * @return array Danh sách đơn vị
     */
    function getAllField()
    {
        return $this->makeInstance()->where(' c_status = 1 ')->getAll();
    }

}
