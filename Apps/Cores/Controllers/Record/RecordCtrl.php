<?php

namespace Apps\Cores\Controllers\Record;

use Apps\Cores\Models\RecordAttachment\RecordAttachmentMapper;
use Apps\Cores\Models\Record\RecordMapper;
use Apps\Cores\Controllers\CoresCtrl;
use Libs\Json;

class RecordCtrl extends CoresCtrl 
{

    protected $recordMapper;
    protected $recordAttachmentMapper;
    function init() {
        parent::init();
        $this->requireAdmin();
        $this->recordMapper = RecordMapper::makeInstance();
        $this->recordAttachmentMapper = RecordAttachmentMapper::makeInstance();
    }

    public function index() 
    {
        $this->twoColsLayout
                ->render('Record/index.phtml');
    }

    public function getData() {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->recordMapper
                ->setLoadAttachment()
                ->getAll();
        $this->resp->setBody(Json::encode($data));
    }

    /**
     * Thêm mới thủ tục
     */
    public function addRecord() 
    {
        $Data['c_code']     = $this->req->post('c_code');
        $Data['c_name']     = $this->req->post('c_name');
        $Data['fk_linh_vuc']= $this->req->post('sel_fk_linh_vuc');
        $Data['fk_member']  = $this->req->post('chk_member');
        $Data['fk_member']  = implode(',', $Data['fk_member']);
        $Data['c_scope']    = $this->req->post('rd_scope');
        $Data['c_content']  = $this->req->post('c_content');
        $Data['c_xml_data'] = $this->req->post('c_xml_data');
        $Data['c_order']    = $this->req->post('c_order');
        $Data['c_status']   = $this->req->post('c_status');
        $Data['c_status']   = ($Data['c_status']== 'on') ? 1 : 0;
        
        //Check mã thủ tục
        $resp = $this->recordMapper->filterCode($Data['c_code']);
        if(is_array($resp) && count($resp) >0)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Mã thủ tục đã tồn tại!!!','line'=>__LINE__));
            exit();
        }
        
        
        
        $id = $this->recordMapper->addRecord($Data);
        if((int)$id <= 0)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Cập nhật thất bại!!!','line' => __LINE__));
            exit();
        }

        //Upload file
        $resp = $this->update_template_file_type($id);
        var_dump($resp);
        if($resp !== TRUE)
        {
            $this->deleteRecord($id);
            echo json_encode($resp);
            exit();
        }

    }

    public function updateRecord()
    {
        $pk                 = $this->req->post('pk');
        $v_list_file_delete = $this->req->post('hdn_list_file_delete');
        
        $Data['c_code']     = $this->req->post('c_code');
        $Data['c_name']     = $this->req->post('c_name');
        $Data['fk_linh_vuc']= $this->req->post('sel_fk_linh_vuc');
        $Data['fk_member']  = $this->req->post('chk_member');
        $Data['fk_member']  = implode(',', $Data['fk_member']);
        $Data['c_scope']    = $this->req->post('rd_scope');
        $Data['c_content']  = $this->req->post('c_content');
        $Data['c_xml_data'] = $this->req->post('c_xml_data');
        $Data['c_order']    = $this->req->post('c_order');
        $Data['c_status']   = $this->req->post('c_status');
        $Data['c_status']   = ($Data['c_status']== 'on') ? 1 : 0;
        
        //Check mã thủ tục
        $resp = $this->recordMapper->filterCode($Data['c_code'],$pk);
        if(is_array($resp) && count($resp) >0)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Mã thủ tục đã tồn tại!!!','line'=>__LINE__));
            exit();
        }
        
        //update
        $resp  = $this->recordMapper->updateRecord($Data,$pk);
        if($resp !== TRUE)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Cập nhật thất bại','line'=>__LINE__));
            exit();
        }
        
        
        //Upload file
        $resp = $this->update_template_file_type($pk);
        if($resp !== TRUE)
        {
            echo json_encode(array('resp'=>'fail','msg'=>'Cập nhật tập tin đính kèm thất bại','line'=>__LINE__));
            exit();
        }
        
        //xoa file dinh kem da chon xoa
        if(trim($v_list_file_delete) != '')
        {
            $resp = $this->delete_file_tempate_type($v_list_file_delete);
            if($resp !== TRUE)
            {
                echo json_encode($resp);
                exit();
            }
        }
        echo json_encode(array('resp'=>'done','msg'=>'Cập nhật thành công','line'=>__LINE__));
        exit();
    }
     
    function update_template_file_type($v_record_type_id)
    {
        //Mang chứa danh sách tập tin đã tải lên thành công
        $arr_file_upload = array();
        
        for($i =0; $i<count($_FILES['files']['name']) ;$i ++)
        {
            if ($_FILES['files']['name'][$i] == '')
            {
                continue;
            }
            if ($_FILES['files']['error'][$i] != 0)
            {
                return array('resp'=>'fail','msg'=>'Cập nhật thất bại','line'=>__LINE__);
            }
            
            //Kiem tra loại file có đúng định dạng hay không?
            $file_except = end(explode('.', $_FILES['files']['name'][$i]));
            $arr_except = explode(',', strtolower(CONST_TYPE_FILE_ACCEPT));
            
            if(!in_array(strtolower($file_except), $arr_except))
            {
                return array('resp'=>'fail','msg'=>'Định dạng tập tin ['.$_FILES['files']['name'].'] không hợp lệ!','line'=>__LINE__);
            }
            
            //kiểm tra dung lượng file
            $file_size = $_FILES["files"]['size'][$i];
            if($file_size >= CONST_UPLOAD_FILE_MAX_SIZE)
            {
                return array('msg' => "Tập tin '$arr_name[$i]' kích thước > ".CONST_UPLOAD_FILE_MAX_SIZE .'Kb','line'=>__LINE__,'resp'=>'fail');
            }

            $v_file_name     = $_FILES['files']['name'][$i];
            if($v_file_name == '')
            {
                continue;
            }
            $v_tmp_name      = $_FILES['files']['tmp_name'][$i];
            
            $v_file_ext      = array_pop(explode('.', $v_file_name));
            $v_cur_file_name = $v_record_type_id.'_'.uniqid().'.' . $v_file_ext;

            //check folder root
            if(file_exists(CONST_DIR_UPLOAD) == FALSE)
            {
                mkdir(CONST_DIR_UPLOAD, 0777, true);
            }

            if (!move_uploaded_file($v_tmp_name, CONST_DIR_UPLOAD . '\\' . $v_cur_file_name))
            {
                //Xóa danh sách tập tin đã tải lên nếu có
                for($j=0;$j <count($arr_file_upload);$j++)
                {
                    @unlink($arr_file_upload[$j]);
                }
                return array('line'=>__LINE__,'msg'=> 'Xảy ra lỗi cập nhật','resp'=>'fail','line'=>__LINE__);
            }
            else
            {
                $update['fk_record_type']=  $v_record_type_id;
                $update['c_file_name']   =  $v_cur_file_name;
                $update['c_description'] =  $v_file_name;
                $AttachmentID = $this->recordAttachmentMapper->addAttachment($update);
                if((int)$AttachmentID <= 0)
                {
                    //Xóa danh sách tập tin đã tải lên nếu có
                    for($j=0;$j <count($arr_file_upload);$j++)
                    {
                        @unlink($arr_file_upload[$j]['dir']);
                    }
                    return array('line'=>__LINE__,'msg'=> 'Xảy ra lỗi cập nhật','resp'=>'fail','lile'=>__LINE__);
                }
                else
                {
                    $arr_file_upload[$i]['id'] = $AttachmentID;
                }
            }
            
            //save file upload done
            $arr_file_upload[$i]['dir'] = CONST_DIR_UPLOAD . '\\' . $v_cur_file_name;
        }
        return TRUE;
    }
    
    
    /**
     * Xóa danh sách tập tin đính kèm
     * @param string $v_list_file_delete danh sách tên file đính kèm
     * @return type
     */
    public function delete_file_tempate_type($v_list_file_delete)
    {
        $arr_file_name  = explode(',',$v_list_file_delete);
        for($i = 0;$i <count($arr_file_name);$i ++)
        {
            $file_name  = $arr_file_name[$i];
            $resp = $this->recordAttachmentMapper->deleteAttachment($file_name);
            if($resp !== TRUE)
            {
                return array('resp'=>'fail','msg'=>'Cập nhật không thành công','line'=>__LINE__,'detail'=>$resp);
            }
            
            $v_path_file = CONST_DIR_UPLOAD .'\\' . $file_name;
            if(is_file($v_path_file))
            {
                //Xoa file
                @unlink($v_path_file);
            }
        }
    }

    public function deleteRecord($v_list_id = 0) 
    {
        if($v_list_id == 0)
        {
            $v_list_id = $this->req->post('pk');
            $v_list_id = trim($v_list_id,',');
        }
        $this->recordMapper->deleteRecord($v_list_id);
        //deleted attachment
        $this->recordAttachmentMapper->deleteAttachment('',$v_list_id);
    }

    public function searchByName()
    {
        $this->resp->headers()->set('Content-type', 'Application/json');
        $data = $this->recordMapper->filterName($_GET)->getAll();
        $this->resp->setBody(Json::encode($data));
    }

    public function searchByMember() {
        $data = $this->recordMapper->filterMember($_GET)->getAll();
        $this->resp->headers()->set('Content-type', 'Application/json');
        $this->resp->setBody(Json::encode($data));
    }
    public function searchByStatus() {
        $data = $this->recordMapper->filterStatus($_GET)->getAll();
        $this->resp->headers()->set('Content-type', 'Application/json');
        $this->resp->setBody(Json::encode($data));
    }

    public function search()
    {
        $data['name'] = $this->req->get('keyword'); 
        $data['member'] = $this->req->get('member');
        $data['status'] = $this->req->get('status');
        $data['page'] = $this->req->get('page',1);
        $data['limit'] = $this->req->get('limit');
        $resp = $this->recordMapper->search($data)->getAll();
        $this->resp->headers()->set('Content-type', 'Application/json');
        $this->resp->setBody(Json::encode($resp));
    }
}
