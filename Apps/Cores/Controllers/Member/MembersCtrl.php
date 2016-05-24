<?php

namespace Apps\Cores\Controllers\Member;

use Apps\Cores\Models\Member\MemberMapper;
use Apps\Cores\Controllers\CoresCtrl;
use Libs\Json;

class MembersCtrl extends CoresCtrl
{
    protected $memberMapper;
    
    function init(){
        parent::init();
        $this->requireAdmin();
        $this->memberMapper = MemberMapper::makeInstance();
    }
    
    public function index() {
        $this->twoColsLayout
                ->render('Member/index.phtml');
        
    }
    
    public function getData() {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->memberMapper->orderBy('c_order ASC')->getAll();
        $this->resp->setBody(Json::encode($data));
    }
    
    public function addMember() 
    {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $update['c_name']        = get_post_var('c_name');
        $update['c_code']        = get_post_var('c_code');
        $update['c_link_service']= get_post_var('c_link_service');
        $update['c_url_login']   = get_post_var('c_url_login');
        $update['c_account']     = get_post_var('c_account');
        $update['c_password']    = get_post_var('c_password');
        $update['c_type']        = (get_post_var('c_type')  =='true') ? 1 : 0;
        $update['c_order']       = (int)get_post_var('c_order');
        $update['c_status']      = (get_post_var('c_status')=='true') ? 1 : 0;
        $update['c_scope']       = get_post_var('c_scope');
        
        $resp = $this->memberMapper->filterCode($update['c_code'] );
        if(is_array($resp) && count($resp) >0)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Mã đơn vị đã tồn tại!!!'));
            exit();
        }
        $member_id = $this->memberMapper->addMember($update);
        echo json_encode(array('resp'=>'done','msg'=>'Cập nhật thành công','id'=>$member_id));
        exit();
    }
    public function updateMember() 
    {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $id                      = get_post_var('pk');
        $update['c_name']        = get_post_var('c_name');
        $update['c_code']        = get_post_var('c_code');
        $update['c_link_service']= get_post_var('c_link_service');
        $update['c_url_login']   = get_post_var('c_url_login');
        $update['c_account']     = get_post_var('c_account');
        $update['c_password']    = get_post_var('c_password');
        $update['c_type']        = (get_post_var('c_type')  =='true') ? 1 : 0;
        $update['c_order']       = (int)get_post_var('c_order');
        $update['c_status']      = (get_post_var('c_status')=='true') ? 1 : 0;
        $update['c_scope']       = get_post_var('c_scope');
        
        $resp = $this->memberMapper->filterCode($update['c_code'],$id);
        if(is_array($resp) && count($resp) >0)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Mã đơn vị đã tồn tại!!!'));
            exit();
        }
        $this->memberMapper->updateMember($update,$id);
        echo json_encode(array('resp'=>'done','msg'=>'Cập nhật thành công','id'=>$id));
        exit();
    }
    public function deleteMember() 
    {
        $this->memberMapper->deleteMember($_POST);
    }
    public function searchByName()
    {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->memberMapper->filterName($_GET)->getAll();
        $this->resp->setBody(Json::encode($data));
    }
    public function searchByType()
    {
        $data = $this->memberMapper->filterType($_GET)->getAll();
        $this->resp->headers()->set('Content-type', 'Application/json');
        $this->resp->setBody(Json::encode($data));
    }
}

