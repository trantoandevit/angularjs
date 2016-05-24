<?php

namespace Apps\Cores\Controllers\Field;

use Apps\Cores\Models\Field\FieldMapper;
use Apps\Cores\Controllers\CoresCtrl;
use Libs\Json;

class FieldCtrl extends CoresCtrl {

    protected $fieldMapper;

    function init() {
        parent::init();
        $this->requireAdmin();
        $this->fieldMapper = FieldMapper::makeInstance();
    }

    public function index() {
        $data = $this->fieldMapper->getAll();
        $this->twoColsLayout
                ->render('Field/index.phtml', array('data' => $data));
    }
    public function getAllField()
    {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->fieldMapper
                ->getAll()->toArray();
        $this->resp->setBody(Json::encode($data));
    }
    public function getData() {
        $filterName = $this->req->post('name', '');
        $filterPage = $this->req->post('page', 1);
        $filterRowPerPage = $this->req->post('row_per_page', 6);
        
        $this->resp->headers()->set('Content-type', 'Application/json');
        
        $fieldMapper = $this->fieldMapper;
        
        $data = $this->fieldMapper
                ->filterName($filterName)
                ->setPage($filterPage, $filterRowPerPage)
                ->getAll()->toArray();
        $data['data'] = $data;
        $data['total'] = $this->fieldMapper
                            ->select('count(*)')
                            ->filterName($filterName)
                            ->getOne();
        
        $this->resp->setBody(Json::encode($data));
    }

    public function addField() {
        $update['c_name'] = unicode_to_composite(get_post_var('c_name'));
        $update['c_code'] = unicode_to_composite(get_post_var('c_code'));
        $update['c_order'] = get_post_var('c_order');
        $update['c_status'] = (get_post_var('c_status') == 'true') ? 1 : 0;
        $this->fieldMapper->addField($update);
    }

    public function updateField() {
        $id = get_post_var('pk');
        $update['c_code'] = unicode_to_composite(get_post_var('c_code'));
        $update['c_name'] = unicode_to_composite(get_post_var('c_name'));
        $update['c_order'] = get_post_var('c_order');
        $update['c_status'] = (get_post_var('c_status') == 'true') ? 1 : 0;
        $this->fieldMapper->updateField($update, $id);
    }

    public function deleteField() {
        $this->fieldMapper->deleteField($_POST);
    }

    public function searchByName() {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->fieldMapper->filterName($_GET)->getAll();
        $this->resp->setBody(Json::encode($data));
    }

    public function searchByType() {
        $data = $this->fieldMapper->filterType($_GET)->getAll();
        $this->resp->headers()->set('Content-type', 'Application/json');
        $this->resp->setBody(Json::encode($data));
    }

}
