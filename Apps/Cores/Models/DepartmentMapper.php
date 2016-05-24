<?php

namespace Apps\Cores\Models;

use Libs\SQL\Mapper;

class DepartmentMapper extends Mapper
{

    protected $loadUsers;
    protected $loadUsersCallback;
    protected $loadChildDeps;
    protected $loadChildDepsRecusiveLy; //load cả cây
    protected $loadAncestors;
    protected $not = array();

    protected function userMapper()
    {
        return UserMapper::makeInstance();
    }

    function __construct()
    {
        parent::__construct();
        $this->orderBy('dep.path');
        $this->filterDeleted(false);
    }

    public function makeEntity($rawData)
    {
        $entity = new DepartmentEntity($rawData);
        $entity->pk = (int) $entity->pk;

        if ($this->loadUsers)
        {
            $entity->users = $this->loadUsers($entity->pk, $this->loadUsersCallback);
        }
        if ($this->loadChildDeps)
        {
            $entity->deps = $this->loadChildDeps($entity->pk, $this->loadChildDepsRecusiveLy);
        }
        if ($this->loadAncestors)
        {
            $entity->ancestors = $this->loadAncestors($entity);
        }

        return $entity;
    }

    public function tableAlias()
    {
        return 'dep';
    }

    public function tableName()
    {
        return 'cores_department';
    }

    function filterPk($depPk)
    {
        $this->where('dep.pk=?', __FUNCTION__)->setParam($depPk, __FUNCTION__);
        return $this;
    }

    function filterParent($depPk)
    {
        $this->where('dep.depFk=?', __FUNCTION__)->setParam($depPk, __FUNCTION__);
        return $this;
    }

    /**
     * Tự động load user trực thuộc
     */
    function setLoadUsers($bool = true, $callback = null)
    {
        $this->loadUsers = $bool;
        $this->loadUsersCallback = $callback;
        return $this;
    }

    /** Tự động load đơn vị trực thuộc */
    function setLoadChildDeps($bool = true, $rescusively = false)
    {
        $this->loadChildDeps = $bool;
        $this->loadChildDepsRecusiveLy = $rescusively;
        return $this;
    }

    /** Tự động load đơn vị tổ tiên */
    function setLoadAncestors($bool = true)
    {
        $this->loadAncestors = $bool;
        return $this;
    }

    /** @return UserEntity */
    function loadUsers($depPk, $callback = null)
    {
        return $this->userMapper()
                        ->filterDeleted(false)
                        ->filterParent($depPk)
                        ->getAll($callback);
    }

    /** @return DepartmentEntity */
    function loadChildDeps($depPk, $rescusively = false)
    {
        $mapper = $this;
        return $this->makeInstance()
                        ->filterParent($depPk)
                        ->filterDeleted(false)
                        ->filterNot($this->not)
                        ->getAll(function($rawData, $entity) use($mapper, $rescusively)
                        {
                            $entity->deps = $mapper->loadChildDeps($entity->pk, $rescusively);
                        });
    }

    /**
     * 
     * @param DepartmentEntity $dep
     * @return DepartmentEntity 
     */
    function loadAncestors(DepartmentEntity $dep)
    {
        $pks = explode('/', trim($dep->path, '/'));
        //remove this dep
        array_pop($pks);

        if (count($pks))
        {
            $pks = implode(',', $pks);
            return $this->makeInstance()
                            ->where("dep.pk IN($pks)")
                            ->getAll();
        }
        else
        {
            return $this->makeEntitySet();
        }
    }

    function filterNot($arr)
    {
        if (!is_array($arr))
        {
            $arr = array($arr);
        }
        $this->not = $arr;

        $where = array();
        foreach ($arr as &$id)
        {
            $id = (int) $id;
            $where[] = "dep.pk <> $id";
        }
        $this->where("(" . implode(' AND ', $where) . ")", __FUNCTION__);

        return $this;
    }

    function updateDep($depPk, $depFk, $code, $name, $stt)
    {
        if (!$code || !$name)
        {
            return;
        }
        $data = array(
            'depFk'   => (int) $depFk,
            'depCode' => $code,
            'depName' => $name,
            'stt'     => $stt ? 1 : 0
        );


        if ($depPk)
        {
            $this->db->update('cores_department', $data, 'pk=?', array($depPk));
        }
        else
        {
            $depPk = $this->db->insert('cores_department', $data);
        }
        //re-index path
        $this->rebuildDepPath();

        return $depPk;
    }

    protected function rebuildDepPath()
    {
        $this->rebuildPath('depFk', 'path', 'pk');
    }

    function deleteDepartments($arrId)
    {
        if (!is_array($arrId))
            return;
        $this->db->StartTrans();
        foreach ($arrId as $id)
        {
            $this->db->Execute("UPDATE cores_department SET deleted=1, depCode=CONCAT(depCode, ?) WHERE pk=?", array('|' . uniqid($id), $id));
            //chuyển đơn vị, tk vè thư mục gốc
            $this->db->update('cores_department', array('depFk' => 0), 'depFk=?', array($id));
            $this->db->update('cores_user', array('depFk' => 0), 'depFk=?', array($id));
        }
        $this->rebuildDepPath();
        $this->db->CompleteTrans();
    }

    function filterDeleted($bool)
    {
        $this->where('dep.deleted=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        return $this;
    }

    function moveDepartments($arrId, $depFk)
    {
        if (!is_array($arrId))
            return;
        $this->db->StartTrans();

        foreach ($arrId as $id)
        {
            $this->db->update('cores_department', array('depFk' => $depFk), 'pk=?', array($id));
        }
        $this->rebuildDepPath();
        $this->db->CompleteTrans();
    }

    function filterSearch($search)
    {
        $this->where('(dep.depName LIKE ? OR dep.depCode LIKE ?)', __FUNCTION__)
                ->setParams(array(
                    __FUNCTION__ . 1 => "%$search%",
                    __FUNCTION__ . 2 => "%$search%"
        ));
        return $this;
    }

    function filterStatus($bool)
    {
        if ($bool != -1)
        {
            $this->where('dep.stt=?', __FUNCTION__)->setParam($bool ? 1 : 0, __FUNCTION__);
        }
        return $this;
    }

}
