<?php

namespace Apps\Cores\Controllers\Rest;

use Libs\Json;
use Apps\Cores\Models\DepartmentMapper;
use Apps\Cores\Models\UserMapper;
use Apps\Cores\Models\GroupMapper;
use Apps\Cores\Models\Permission;

class UserCtrl extends RestCtrl
{

    protected $userMapper;
    protected $depMapper;
    protected $groupMapper;

    protected function init()
    {
        parent::init();
        $this->userMapper = UserMapper::makeInstance();
        $this->depMapper = DepartmentMapper::makeInstance();
        $this->groupMapper = GroupMapper::makeInstance();
    }

    function getDepartment($depPk)
    {
        $this->requireLogin();

        $depPk = (int) $depPk;
        $loadUsers = $this->req->get('users');
        $loadDeps = $this->req->get('departments');
        $rescusively = $this->req->get('rescusively');
        $loadAncestors = $this->req->get('ancestors');
        $not = $this->req->get('not');

        $userMapper = $this->userMapper;

        $dep = $this->depMapper
                ->makeInstance()
                //autoload related entities
                ->setLoadAncestors($loadAncestors)
                ->setLoadChildDeps($loadDeps, $rescusively)
                ->setLoadUsers($loadUsers, function($rawData, $entity) use ($userMapper)
                {
                    $entity->groups = $userMapper->db->GetCol('SELECT groupFk FROM cores_group_user WHERE userFk=?', array($entity->pk));
                    $entity->permissions = $userMapper->loadPermissions($entity->pk);
                })
                ->filterNot($not)
                //query
                ->filterPk($depPk)
                ->getEntity();

        $this->resp->setBody(Json::encode($dep));
    }

    function search()
    {
        $this->requireLogin();

        $search = $this->req->get('search');
        $stt = $this->req->get('status', -1);

        $deps = $this->depMapper
                ->makeInstance()
                ->filterStatus($stt)
                ->filterDeleted(false)
                ->filterSearch($search)
                ->getAll();

        $users = $this->userMapper
                ->makeInstance()
                ->filterStatus($stt)
                ->filterDeleted(false)
                ->filterSearch($search)
                ->getAll();



        $this->resp->setBody(Json::encode(array(
                    'departments' => $deps,
                    'users'       => $users
        )));
    }

    function updateDepartment($depPk)
    {
        $this->requireAdmin();

        $code = $this->restInput('depCode');
        $name = $this->restInput('depName');
        $stt = $this->restInput('stt');
        $depFk = $this->restInput('depFk');

        $depPk = $this->depMapper->updateDep($depPk, $depFk, $code, $name, $stt);
        $this->resp->setBody(Json::encode(array(
                    'status'   => true,
                    'resource' => url('/rest/department/' . $depPk)
        )));
    }

    function getGroups()
    {
        $this->requireLogin();

        $stt = $this->req->get('status', -1);
        $groups = $this->groupMapper->makeInstance()
                ->filterStatus($stt)
                ->setLoadPermission()
                ->getAll();

        $this->resp->setBody(Json::encode($groups));
    }

    function getGroupUsers($groupPk)
    {
        $this->requireLogin();

        $users = $this->userMapper
                ->makeInstance()
                ->select('dep.depName', false)
                ->filterDeleted(false)
                ->innerJoin('cores_group_user gu ON u.pk=gu.userFk AND gu.groupFk=' . intval($groupPk))
                ->leftJoin('cores_department dep ON u.depFk=dep.pk')
                ->getAll(function($rawData, $entity)
        {
            if (!$entity->depName)
            {
                $entity->depName = '[Thư mục gốc]';
            }
        });

        $this->resp->setBody(Json::encode($users));
    }

    /**
     * Trả về danh sách tất cả quyền của ứng dụng
     */
    function getBasePermissions()
    {
        $this->requireAdmin();

        $ret = array();
        foreach (\Libs\Setting::getAllApp() as $appId)
        {
            $setting = new \Libs\Setting($appId);
            if ($setting->xml->attributes()->active != 'true')
            {
                continue;
            }
            $app = array(
                'name'   => (string) $setting->xml->attributes()->name,
                'groups' => array()
            );
            foreach ($setting->xml->permissions->group as $groupXml)
            {
                $group = array(
                    'name'        => (string) $groupXml->attributes()->name,
                    'permissions' => array()
                );
                foreach ($groupXml->pem as $pem)
                {
                    $group['permissions'][] = array(
                        'id'   => (string) $pem->attributes()->id,
                        'name' => (string) $pem->attributes()->name
                    );
                }
                $app['groups'][] = $group;
            }
            $ret[] = $app;
        }

        $this->resp->setBody(Json::encode($ret));
    }

    function checkUniqueAccount()
    {
        $this->requireAdmin();

        $userPk = $this->restInput('pk');
        $acc = $this->restInput('account');
        $result = $this->userMapper->checkUniqueAccount($userPk, $acc);

        $this->resp->setBody(Json::encode($result));
    }

    function updateUser($id)
    {
        $this->requireAdmin();

        $data = $this->restInput();
        $id = $this->userMapper->updateUser($id, $data);

        $this->resp->setBody(Json::encode(array(
                    'status'   => true,
                    'resource' => url('/rest/user/' . $id)
        )));
    }

    function deleteUsers()
    {
        $this->requireAdmin();

        $users = $this->restInput();
        $this->userMapper->deleteUsers($users);

        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function deleteDepartments()
    {
        $this->requireAdmin();

        $deps = $this->restInput();
        $this->depMapper->deleteDepartments($deps);

        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function moveUsers()
    {
        $this->requireAdmin();

        $users = $this->restInput('pks');
        $dest = $this->restInput('dest');

        $this->userMapper->moveUsers($users, $dest);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function moveDepartments()
    {
        $this->requireAdmin();

        $deps = $this->restInput('pks');
        $dest = $this->restInput('dest');

        $this->depMapper->moveDepartments($deps, $dest);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

    function updateGroup($pk)
    {
        $this->requireAdmin();

        $group = $this->restInput();
        if (!$this->groupMapper->checkCode($pk, $group['groupCode']))
        {
            $this->resp->setBody(Json::encode(array(
                        'status' => false,
                        'error'  => 'duplicateCode'
            )));
            return;
        }

        $pk = $this->groupMapper->updateGroup($pk, $group);

        $this->resp->setBody(Json::encode(array(
                    'status' => true,
                    'pk'     => $pk
        )));
    }

    function deleteGroups()
    {
        $this->requireAdmin();

        $arrPk = $this->restInput('pk', array());
        $this->groupMapper->deleteGroup($arrPk);
        $this->resp->setBody(Json::encode(array(
                    'status' => true
        )));
    }

}
