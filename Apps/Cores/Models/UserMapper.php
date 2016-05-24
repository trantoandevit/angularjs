<?php

namespace Apps\Cores\Models;

use Libs\SQL\Mapper;

class UserMapper extends Mapper
{

    protected $loadPermissions = 0;
    protected $loadGroups = false;

    public function makeEntity($rawData)
    {
        $entity = new UserEntity($rawData);
        if ($this->loadPermissions)
        {
            $entity->permissions = $this->loadPermissions($entity->pk, $this->loadPermissions == 2 ? true : false);
        }
        if ($this->loadGroups)
        {
            $entity->groups = $this->loadGroups($entity->pk);
        }
        return $entity;
    }

    public function tableAlias()
    {
        return 'u';
    }

    public function tableName()
    {
        return 'cores_user';
    }

    function __construct()
    {
        parent::__construct();
        $this->filterDeleted(false);
    }

    function filterParent($depPk)
    {
        $this->where('u.depFk=?', __FUNCTION__)->setParam($depPk, __FUNCTION__);
        return $this;
    }

    function filterAccount($acc)
    {
        $this->where('u.account=?', __FUNCTION__)->setParam($acc, __FUNCTION__);
        return $this;
    }

    /** @return GroupEntity */
    function loadGroups($userPk)
    {
        return GroupMapper::makeInstance()
                        ->innerJoin('cores_group_user gu ON gu.groupFk=gp.pk AND gu.userFk=' . intval($userPk))
                        ->getAll();
    }

    function updateUser($id, $data)
    {
        $update['account'] = arrData($data, 'account');
        $update['fullName'] = arrData($data, 'fullName');
        $update['depFk'] = (int) arrData($data, 'depFk');
        $update['jobTitle'] = arrData($data, 'jobTitle');
        $update['stt'] = arrData($data, 'stt') ? 1 : 0;

        if (!$update['account'] || !$update['fullName'])
        {
            return;
        }
        if (!$id && !$data['newPass'])
        {
            return;
        }

        if (arrData($data, 'changePass') || !$id)
        {
            $update['pass'] = md5(arrData($data, 'newPass'));
        }

        $groups = arrData($data, 'groups', array());
        $permissions = arrData($data, 'permissions', array());

        $this->db->StartTrans();
        if ($id)
        {
            $this->db->update('cores_user', $update, 'pk=?', array($id));
        }
        else
        {
            $id = $this->db->insert('cores_user', $update);
        }

        //group
        $this->db->delete('cores_group_user', 'userFk=?', array($id));
        foreach ($groups as $groupFk)
        {
            $this->db->insert('cores_group_user', array('userFk' => $id, 'groupFk' => $groupFk));
        }

        //permissions
        $this->db->delete('cores_user_permission', 'userFk=?', array($id));
        foreach ($permissions as $pem)
        {
            $this->db->insert('cores_user_permission', array('userFk' => $id, 'permission' => $pem));
        }

        $this->db->CompleteTrans();
        return $id;
    }

    function checkUniqueAccount($userPk, $account)
    {
        $inserted = $this->makeInstance()
                ->filterAccount($account)
                ->getEntity();

        if ($userPk && $inserted->pk == $userPk)
        {
            return true;
        }
        if (!$inserted->pk)
        {
            return true;
        }

        return false;
    }

    function deleteUsers($arrId)
    {
        if (!is_array($arrId))
            return;
        foreach ($arrId as $id)
        {
            if ($id == 1)
                continue;
            $this->db->Execute("UPDATE cores_user SET deleted=1, account=CONCAT(account, ?) WHERE pk=?", array('|' . uniqid($id), $id));
        }
    }

    function filterDeleted($bool)
    {
        $this->where('u.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function moveUsers($arrId, $depFk)
    {
        if (!is_array($arrId))
            return;
        foreach ($arrId as $id)
        {
            $this->db->update('cores_user', array('depFk' => $depFk), 'pk=?', array($id));
        }
    }

    function filterStatus($bool)
    {
        if ($bool != -1)
        {
            $this->where('u.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

    function filterSearch($search)
    {
        $this->where('(u.fullName LIKE ? OR u.jobTitle LIKE ? OR u.account LIKE ? OR u.email LIKE ? OR u.phone LIKE ?)', __FUNCTION__);
        $this->setParams(array(
            __FUNCTION__ . 1 => "%$search%",
            __FUNCTION__ . 2 => "%$search%",
            __FUNCTION__ . 3 => "%$search%",
            __FUNCTION__ . 4 => "%$search%",
            __FUNCTION__ . 5 => "%$search%"
        ));
        return $this;
    }

    function setLoadPermissions($includeGroupPem = false)
    {
        $this->loadPermissions = $includeGroupPem ? 2 : 1;
        return $this;
    }

    function loadPermissions($userPk, $includeGroupPem = false)
    {
        $userPk = (int) $userPk;
        if (!$userPk)
        {
            return array();
        }

        $sql = "SELECT permission FROM cores_user_permission WHERE userFk=$userPk";
        if ($includeGroupPem)
        {
            $groups = "SELECT groupFk FROM cores_group_user WHERE userFk=$userPk";
            $sql .= "\nUNION SELECT permission FROM cores_group_permission WHERE groupFk IN($groups)";
        }

        return $this->db->GetCol($sql);
    }

    function setLoadGroups($bool = true)
    {
        $this->loadGroups = $bool;
        return $this;
    }

    /**
     * Lỗi array('status' => false, 'error' => '[code]')<br>
     * badRequest: chưa điền account hoặc pass<br>
     * notFound: không tìm thấy<br>
     * inactive: không kích hoạt<br>
     * wrongPassword: sai mật khẩu<Br>
     * Thành công array('status'=> true, 'user' => [UserEntity])
     * @param type $account
     * @param type $password
     * @return arra 
     */
    function authenticate($account, $password)
    {
        if (!$account || !$password)
        {
            return array(
                'status' => false,
                'error'  => 'badRequest'
            );
        }

        $user = $this->makeInstance()
                ->setLoadPermissions(true)
                ->setLoadGroups()
                ->filterAccount($account)
                ->filterDeleted(false)
                ->getEntity();

        if (!$user->pk)
        {
            return array(
                'status' => false,
                'error'  => 'notFound'
            );
        }

        if ($user->stt == 0)
        {
            return array(
                'status' => false,
                'error'  => 'inactive'
            );
        }

        if ($user->pass != md5($password))
        {
            return array(
                'status' => false,
                'error'  => 'wrongPassword'
            );
        }

        return array(
            'status' => true,
            'user'   => $user
        );
    }

    function changePassword($userPk, $pass)
    {
        $this->update($userPk, array('pass' => md5($pass)));
    }

}
