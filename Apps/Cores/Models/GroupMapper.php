<?php

namespace Apps\Cores\Models;

use Libs\SQL\Mapper;

class GroupMapper extends Mapper
{

    protected $loadPermission;

    public function makeEntity($rawData)
    {
        $entity = new GroupEntity($rawData);
        if ($this->loadPermission)
        {
            $entity->permissions = $this->getPermissions($entity->pk);
        }
        return $entity;
    }

    public function tableAlias()
    {
        return 'gp';
    }

    public function tableName()
    {
        return 'cores_group';
    }

    function __construct()
    {
        parent::__construct();
        $this->filterDeleted(false);
    }

    function filterDeleted($bool)
    {
        $this->where('gp.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function filterPk($id)
    {
        $this->where('gp.pk=?', __FUNCTION__)->setParam($id, __FUNCTION__);
        return $this;
    }

    function filterStatus($bool)
    {
        if ($bool != -1)
        {
            $this->where('gp.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

    /** @return UserEntity */
    function loadUsers($groupPk)
    {
        return UserMapper::makeInstance()
                        ->innerJoin('cores_group_user gu ON u.pk=gu.userFk AND gu.groupFk=' . intval($groupPk))
                        ->getAll();
    }

    function updateGroup($pk, $data)
    {
        $update['groupCode'] = arrData($data, 'groupCode');
        $update['groupName'] = arrData($data, 'groupName');
        $update['stt'] = arrData($data, 'stt') ? 1 : 0;

        if (!$update['groupCode'] || !$update['groupName'])
        {
            return false;
        }
        
        $this->db->StartTrans();
        $pk = $this->replace($pk, $update);

        if (!$pk)
        {
            return false;
        }

        //user in group
        $this->db->delete('cores_group_user', 'groupFk=?', array($pk));
        foreach (arrData($data, 'users', array()) as $user)
        {
            $this->db->insert('cores_group_user', array(
                'userFk'  => $user,
                'groupFk' => $pk
            ));
        }

        //group permissions
        $this->db->delete('cores_group_permission', 'groupFk=?', array($pk));
        foreach (arrData($data, 'permissions', array()) as $pem)
        {
            $this->db->insert('cores_group_permission', array(
                'groupFk'    => $pk,
                'permission' => $pem
            ));
        }
        $this->db->CompleteTrans();

        return $pk;
    }

    function filterCode($code)
    {
        $this->where('gp.groupCode=?', __FUNCTION__)->setParam($code, __FUNCTION__);
        return $this;
    }

    function deleteGroup($pk)
    {
        if (!is_array($pk))
        {
            $pk = array($pk);
        }
        foreach ($pk as $i)
        {
            $this->db->Execute("UPDATE cores_group SET deleted=1, groupCode=CONCAT(groupCode, ?) WHERE pk=?", array('|' . uniqid() . $i, $i));
        }
    }

    function checkCode($pk, $code)
    {
        $inserted = $this->makeInstance()->filterCode($code)->getEntity();

        if (!$inserted->pk)
        {
            return true;
        }
        else if ($inserted->pk == $pk)
        {
            return true;
        }
        return false;
    }

    function setLoadPermission($bool = true)
    {
        $this->loadPermission = $bool;
        return $this;
    }

    function getPermissions($groupPk)
    {
        return $this->db->GetCol("SELECT permission FROM cores_group_permission WHERE groupFk=?", array($groupPk));
    }

}
