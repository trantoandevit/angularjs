<?php

use Apps\Cores\Models\Member\MemberMapper;
use Apps\Cores\Models\Record\RecordMapper;
use \Apps\Cores\Models\Field\FieldMapper;
use Libs\Json;

error_reporting(E_ALL);
ini_set('display_errors',1);
date_default_timezone_set("Asia/Bangkok");

define('BASE_DIR', dirname(__DIR__));

require_once BASE_DIR . '/Libs/Fn.php';

//autoload
require_once BASE_DIR . '/Libs/autoload.php';


$env = getConfig('Enviroments/enviroment.config.php');
$config = getConfig("Enviroments/$env.config.php");
$config['enviroment'] = $env;

//debug mode
$debug = isset($_GET['debug']) ? 10 : $config['debugMode'];
if ($debug)
{
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}
else
{
    ini_set('display_errors', 0);
    error_reporting(0);
}
$ws_login    = $config['ws_login'];
$ws_password = md5($config['ws_password']);




        
require '../Libs/nusoap/nusoap.php';
ini_set("soap.wsdl_cache_enabled", "0");

$server = new \soap_server();
$server->soap_defencoding = 'UTF-8';
$server->decode_utf8 = false;
$server->encode_utf8 = true;

$endpoint  = SITE_ROOT . 'webservice';
$server->configureWSDL('Soap Services phan mem mot cua',$endpoint);
$_SERVER['PHP_SELF'] = $endpoint;
 
#1. Load danh sách đơn vị
$server->register("GetListOrganization",array(),array('return'=>'xsd:string'));

#2. Tinh hình giải quyết
$server->register("GetDataProcess",array('FromDate'=>'xsd:string','ToDate'=>'xsd:string','DataOrg'=>'xsd:string'),array('return'=>'xsd:string'));

#3. Tra cứu thông tin hồ sơ sử dụng số biên nhận hồ sơ trên phiếu hẹn công dân
$server->register("GetInforHoSo",array('recordCode'=>'xsd:string'),array('return'=>'xsd:string'));


#3. Service trả về thông tin hướng dẫn thủ tục hành chính
$server->register("GetGuidForTTHC",array('OrgCode'=>'xsd:string','MaLinhVuc'=>'xsd:string'),array('return'=>'xsd:string'));

#3. Service trả về thông tin danh sách lĩnh vực
$server->register("GetListLinhVuByOrgCode",array(),array('return'=>'xsd:string'));


#3. Service trả về thông tin danh sách lĩnh vực
$server->register("GetListHoSo",array(
                    'tinh_trang_ho_so'=>'xsd:string'
                    ,'ma_don_vi'=>'xsd:string'
                    ,'ma_ho_so'=>'xsd:string'
                    ,'tiep_nhan_tu'=>'xsd:string'
                    ,'tiep_nhan_den'=>'xsd:string'
                    ,'ho_ten_nguoi_nop'=>'xsd:string'
                    ,'so_trang'=>'xsd:string'
                    ,'so_ban_ghi_tren_trang'=>'xsd:string'
                    ),array('return'=>'xsd:string')
                );



function GetListHoSo($tinh_trang_ho_so,$ma_don_vi,$ma_ho_so,$tiep_nhan_tu,$tiep_nhan_den,$ho_ten_nguoi_nop,$so_trang,$so_ban_ghi_tren_trang)
{
    $arr_single_member = qry_single_member($ma_don_vi);
    if(!is_array($arr_single_member) && count($arr_single_member) <= 0)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã hồ sơ không hợp lệ!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    $v_member_link = isset($arr_single_member[0]['c_link_service']) ? $arr_single_member[0]['c_link_service'] : '';
    $v_member_name = isset($arr_single_member[0]['c_name']) ? $arr_single_member[0]['c_name'] : '';
    
    if(trim($v_member_link) == '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã Đơn vị không hợp lệ!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    try
    {
        $client = new SoapClient($v_member_link);
        $result      = $client->__soapCall('GetListHoSo',array('tinh_trang_ho_so'=>$tinh_trang_ho_so
                                                                ,'ma_don_vi'=>$ma_don_vi
                                                                ,'ma_ho_so'=>$ma_ho_so
                                                                ,'tiep_nhan_tu'=>$tiep_nhan_tu
                                                                ,'tiep_nhan_den'=>$tiep_nhan_den
                                                                ,'ho_ten_nguoi_nop'=>$ho_ten_nguoi_nop
                                                                ,'so_trang'=>$so_trang
                                                                ,'so_ban_ghi_tren_trang'=>$so_ban_ghi_tren_trang)
                                        ); 
    }
    catch (Exception $ex) 
    {
        //check don vi
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Xảy ra lỗi </message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    return $result;
}


/**
 * Hien thi danh sach don vi
 * @return type
 */
function GetListOrganization()
{
    ob_start();
    require './index.php';
    $instanceMember = new MemberMapper();
    $arr_all_member =  $instanceMember->getAll();
    $arr_all_member = Json::encode($arr_all_member);
    $arr_all_member = Json::decode($arr_all_member);
    ob_end_clean();
    if(!is_array($arr_all_member))
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đồng bộ thất bại!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    $xml = '<?xml version="1.0"?><root>';
    for($i =0; $i <count($arr_all_member);$i ++)
    {
         $v_name = $arr_all_member[$i]['c_name'];
         $v_code = $arr_all_member[$i]['c_code'];
         $v_id = $arr_all_member[$i]['pk'];
         $c_link_service = $arr_all_member[$i]['c_link_service'];
         $c_scope = isset($arr_all_member[$i]['c_scope']) ? $arr_all_member[$i]['c_scope'] : 0;
         $c_url_login = isset($arr_all_member[$i]['c_url_login']) ? $arr_all_member[$i]['c_url_login'] : '';
         $xml .= "
                    <row>
                        <pk><![CDATA[$v_id]]></pk>
                        <ten_don_vi><![CDATA[$v_name]]></ten_don_vi>
                        <ma_don_vi><![CDATA[$v_code]]></ma_don_vi>
                        <c_link_service><![CDATA[$c_link_service]]></c_link_service>
                        <pham_vi_don_vi><![CDATA[$c_scope]]></pham_vi_don_vi>
                        <url><![CDATA[$c_url_login]]></url>
                    </row>";
    }
   return $xml .= ' </root>';
}






function CheckAuth()
{
    global $server;
    $user_id = isset($server->requestHeader['UserId'])?$server->requestHeader['UserId']:'';
    $user_password = isset($server->requestHeader['PassWord'])?$server->requestHeader['PassWord']:'';
   
    if($user_id === $ws_login && $ws_password === $user_password )
    {
        return true;
    }
    
    return false;
}

/*
 * Dịch vụ trả về số liệu xử lý thủ tục hành chính của các đơn vị
 */
function GetDataProcess($FromDate = '',$ToDate = '',$DataOrg ='')
{
   
    //    CheckAuth();
    if(trim($FromDate) != '' && !validateDate($FromDate,'Y-m-d'))
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Ngày bắt đầu không hợp lệ!</message><format>YYYY-mm-dd</format><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    if(trim($ToDate) != '' && !validateDate($ToDate,'Y-m-d'))
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Ngày kết thúc không hợp lệ!</message><format>YYYY-mm-dd</format><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    $arr_single_member = qry_single_member($DataOrg);
    $v_member_link = isset($arr_single_member[0]['c_link_service']) ? $arr_single_member[0]['c_link_service'] : '';
    $v_member_name = isset($arr_single_member[0]['c_name']) ? $arr_single_member[0]['c_name'] : '';
    
    if(trim($v_member_link) == '')
    {
        //check don vi
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đơn vị có mã "'.$DataOrg.'" Chưa cấu hình thông tin webservices '.$v_member_link.'</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    
    try
    {
        $client = new SoapClient($v_member_link);
        $result      = $client->__soapCall('GetDataProcess',array('FromDate'=>$FromDate,'ToDate'=>$ToDate)); 
    }
    catch (Exception $ex) 
    {
        //check don vi
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đơn vị có mã "'.$DataOrg.'" xảy ra lỗi service không thể cập nhật thông tin tổng hợp tình hình giải quyết hồ sơ</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    @$dom = simplexml_load_string($result);
    if(!$dom)
    {
        //check don vi
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đơn vị có mã "'.$DataOrg.'" xảy ra lỗi service không thể cập nhật thông tin tổng hợp tình hình giải quyết hồ sơ</message><status>fail</status></data>';
        return $xml;
    }
    
    $C_COUNT_KY_TRUOC =0;
    $C_COUNT_TIEP_NHAN =0;
    $C_COUNT_THU_LY_CHUA_DEN_HAN =0;
    $C_COUNT_THU_LY_QUA_HAN =0;
    $C_COUNT_TRA_SOM_HAN =0;
    $C_COUNT_TRA_DUNG_HAN =0;
    $C_COUNT_TRA_QUA_HAN =0;
    $C_COUNT_BO_SUNG =0;
    $C_COUNT_NVTC =0;
    $C_COUNT_TU_CHOI =0;
    $C_COUNT_CONG_DAN_RUT =0;
    $C_COUNT_CHO_TRA_KY_TRUOC =0;
    $C_COUNT_CHO_TRA_TRONG_KY =0;

    $arr_all_processing_field = $dom->xpath('//row');
    for($i =0;$i<count($arr_all_processing_field);$i ++)
    {
        $C_COUNT_KY_TRUOC += $arr_all_processing_field[$i]->C_COUNT_KY_TRUOC;
        $C_COUNT_TIEP_NHAN += $arr_all_processing_field[$i]->C_COUNT_TIEP_NHAN;
        $C_COUNT_THU_LY_CHUA_DEN_HAN += (int)$arr_all_processing_field[$i]->C_COUNT_THU_LY_CHUA_DEN_HAN;
        $C_COUNT_THU_LY_QUA_HAN += (int)$arr_all_processing_field[$i]->C_COUNT_THU_LY_QUA_HAN;
        $C_COUNT_TRA_SOM_HAN += (int)$arr_all_processing_field[$i]->C_COUNT_TRA_SOM_HAN;
        $C_COUNT_TRA_DUNG_HAN += (int)$arr_all_processing_field[$i]->C_COUNT_TRA_DUNG_HAN;
        $C_COUNT_TRA_QUA_HAN += (int)$arr_all_processing_field[$i]->C_COUNT_TRA_QUA_HAN;
        $C_COUNT_BO_SUNG += (int)$arr_all_processing_field[$i]->C_COUNT_BO_SUNG;
        $C_COUNT_NVTC += (int)$arr_all_processing_field[$i]->C_COUNT_NVTC;
        $C_COUNT_TU_CHOI += (int)$arr_all_processing_field[$i]->C_COUNT_TU_CHOI;
        $C_COUNT_CONG_DAN_RUT += (int)$arr_all_processing_field[$i]->C_COUNT_CONG_DAN_RUT;
        $C_COUNT_CHO_TRA_KY_TRUOC += (int)$arr_all_processing_field[$i]->C_COUNT_CHO_TRA_KY_TRUOC;
        $C_COUNT_CHO_TRA_TRONG_KY += (int)$arr_all_processing_field[$i]->C_COUNT_CHO_TRA_TRONG_KY;
    }
    $xml = '<?xml version="1.0"?>
                <root>
                    <row>
                        <unit_name><![CDATA['.$v_member_name.']]></unit_name>
                        <unit_code><![CDATA['.$DataOrg.']]></unit_code>
                        <tiep_nhan>
                            <ky_truoc>'. $C_COUNT_KY_TRUOC .'</ky_truoc>
                            <trong_ky>'. $C_COUNT_TIEP_NHAN .'</trong_ky>
                        </tiep_nhan>
                        <dang_giai_quyet>
                            <chua_den_han>'. $C_COUNT_THU_LY_CHUA_DEN_HAN .'</chua_den_han>
                            <qua_han>' . $C_COUNT_THU_LY_QUA_HAN . '</qua_han>
                        </dang_giai_quyet>
                        <da_giai_quyet>
                            <som_han>'. $C_COUNT_TRA_SOM_HAN .'</som_han>
                            <dung_han>'. $C_COUNT_TRA_DUNG_HAN .'</dung_han>
                            <qua_han>'. $C_COUNT_TRA_QUA_HAN .'</qua_han>
                        </da_giai_quyet>
                        <tam_dung>
                            <bo_sung_ho_so>'. $C_COUNT_BO_SUNG .'</bo_sung_ho_so>
                            <nghia_vu_tai_chinh>'. $C_COUNT_NVTC .'</nghia_vu_tai_chinh>
                        </tam_dung>
                        <ho_so_huy>
                            <tu_choi>'. $C_COUNT_TU_CHOI .'</tu_choi>
                            <cong_dan_rut>'. $C_COUNT_CONG_DAN_RUT .'</cong_dan_rut>
                        </ho_so_huy>
                        <cho_tra_ket_qua>
                            <trong_ky>'. $C_COUNT_CHO_TRA_KY_TRUOC .'</trong_ky>
                            <ky_truoc>'. $C_COUNT_CHO_TRA_TRONG_KY .'</ky_truoc>
                        </cho_tra_ket_qua>
                    </row>
                </root>
                ';
    
    return $xml;
}

function qry_single_member($DataOrg)
{
    //Lay webservice wsdl của đơn vị
    ob_start();
    require './index.php';
    $instanceMember = new MemberMapper();
    $arr_single_member =  $instanceMember->makeInstance()->where("c_code='$DataOrg'")->getAll();
    $arr_single_member = Json::encode($arr_single_member);
    $arr_single_member = Json::decode($arr_single_member);
    ob_end_clean();
    return $arr_single_member;
}

/**
 * Thông tin chi tiết tiến độ xử lý hồ sơ thông qua mã biên nhận
 * @param string $recordId Mã hồ sơ
 * @return string xml
 */
function GetInforHoSo($recordCode = '')
{
    //Check mã đơn vị theo mã hồ sơ
    //check theo mã cũ dạng: KH001-BG-ULATZJ
    $arr_tmp = explode('-', $recordCode);
    if(is_array($arr_tmp) && count($arr_tmp) == 3)
    {
        $unit_code = isset($arr_tmp[1]) ? $arr_tmp[1] : '';
    }
    else
    {
        //Mã mới dạng: BG.KH001-UKLLD
        $arr_tmp = explode('.', $recordCode);
        $unit_code = isset($arr_tmp[0]) ? $arr_tmp[0] : '';
    }
    
    if($unit_code == '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã hồ sơ không hợp lệ!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    $arr_single_member = qry_single_member($unit_code);
    if(!is_array($arr_single_member) && count($arr_single_member) <= 0)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã hồ sơ không hợp lệ!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    $v_member_link = isset($arr_single_member[0]['c_link_service']) ? $arr_single_member[0]['c_link_service'] : '';
    $v_member_name = isset($arr_single_member[0]['c_name']) ? $arr_single_member[0]['c_name'] : '';
    
    if(trim($recordCode) == '')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã hồ sơ không hợp lệ!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    try
    {
        $client = new SoapClient($v_member_link);
        $result      = $client->__soapCall('GetInforHoSo',array('recordCode'=>$recordCode)); 
    }
    catch (Exception $ex) 
    {
        //check don vi
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Xảy ra lỗi không thể tra cứu thông tin hồ sơ "'.$recordCode.'" </message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    return $result;
    
    
}


function GetGuidForTTHC($OrgCode,$MaLinhVuc)
{
    if($MaLinhVuc =='')
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Mã đơn vị và mã lĩnh vực không được để trống</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    ob_start();
    require './index.php';
    $instanceRecord = new RecordMapper();
    $instanceRecord->db->debug = 1;
    $arr_all_record =  $instanceRecord->getAllRecord($MaLinhVuc);
    $arr_all_record = Json::encode($arr_all_record);
    $arr_all_record = Json::decode($arr_all_record);
    ob_end_clean();

    if(!is_array($arr_all_record))
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đồng bộ thất bại!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    $xml = '<?xml version="1.0"?><root>';
    for($i =0; $i <count($arr_all_record);$i ++)
    {
        $v_name = $arr_all_record[$i]['c_name'];
        $v_code = $arr_all_record[$i]['c_code'];
        $v_content = $arr_all_record[$i]['c_content'];
        $v_scope = $arr_all_record[$i]['c_scope'];
        $c_xml_attachment = $arr_all_record[$i]['c_xml_attachment'];
        $c_xml_data = $arr_all_record[$i]['c_xml_data'];
        
        //build xmk attachment
        $xml_attachment = '';
        $dom = @simplexml_load_string($c_xml_attachment);
        if($dom)
        {
            $obj_attachment = $dom->xpath('//row');
            for($j =0;$j <count($obj_attachment);$j ++)
            {
                $att_pk = $obj_attachment[$j]->pk;
                $att_file_name = $obj_attachment[$j]->c_file_name;
                $att_description = $obj_attachment[$j]->c_description;
                $xml_attachment .= "<item>
                                    <pk>$att_pk</pk>
                                    <c_description><![CDATA[$att_description]]></c_description>
                                    <file_name><![CDATA[$att_description]]></file_name>
                                    <url><![CDATA[".FULL_SITE_ROOT."upload/record_type/$att_file_name]]></url>
                                </item>";
            }
        }
        
        $xml .= "
                    <row>
                            <ma_don_vi>$OrgCode</ma_don_vi>
                            <ma_tthc>$v_code</ma_tthc>
                            <ten_tthc><![CDATA[$v_name]]></ten_tthc>
                            <linh_vuc_tthc></linh_vuc_tthc>
                            <chi_tiet_tthc><![CDATA[$v_content]]></chi_tiet_tthc>
                            <muc_do_dvc>$v_scope</muc_do_dvc>
                            <file_dinh_kem>$xml_attachment</file_dinh_kem>
                            <c_xml_data><![CDATA[$c_xml_data]]></c_xml_data>
                    </row>";
    }
   return $xml .= ' </root>';
}



function GetListLinhVuByOrgCode()
{
    ob_start();
    require './index.php';
    $instanceField = new FieldMapper();
    $arr_all_field =  $instanceField->getAllField();
    $arr_all_field = Json::encode($arr_all_field);
    $arr_all_field = Json::decode($arr_all_field);
    ob_end_clean();
    if(!is_array($arr_all_field))
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<data><message>Đồng bộ thất bại!!!</message><status>fail</status><line>'.__LINE__.'</line></data>';
        return $xml;
    }
    
    $xml_data = "<root>";
    for($i =0; $i <count($arr_all_field);$i ++)
    {
        $v_filed_code = (string) $arr_all_field[$i]['c_code'];
        $v_filed_name = (string) $arr_all_field[$i]['c_name'];
        $xml_data .= " <row>
                            <ma_linh_vuc><![CDATA[$v_filed_code]]></ma_linh_vuc>
                            <ten_linh_vuc><![CDATA[$v_filed_name]]></ten_linh_vuc>
                        </row>";
    }
    $xml_data .= "</root>";
    
    return $xml_data;
}


$HTTP_RAW_POST_DATA = isset($HTTP_RAW_POST_DATA) ? $HTTP_RAW_POST_DATA :  file_get_contents('php://input');
$server->service($HTTP_RAW_POST_DATA);