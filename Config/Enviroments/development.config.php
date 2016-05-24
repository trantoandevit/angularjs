<?php

/**
 * 0 = tắt
 * 1 = PHP, Database trừ trên service
 * 10 = tất cả
 */
$exports['debugMode'] = 0;

//kết nối database
$exports['db'] = array(
    'type' => 'mysqli',
    'host' => '172.16.10.90',
    'name' => 'hcc-bacgiang-dsdv',
    'user' => 'web_user',
    'pass' => '123456'
);

define("SITE_ROOT", '/hcc-bacgiang-dsdv/');
if (empty($_SERVER['HTTPS']))
{
    define('FULL_SITE_ROOT', 'http://' . $_SERVER['HTTP_HOST'] . SITE_ROOT);
}
else
{
    define('FULL_SITE_ROOT',  SITE_ROOT);
}
$exports['cryptSecrect'] = 'abM)(*2312';

$exports['ws_login'] = 'onegatebg';
$exports['ws_password'] = 'onegatebg@2016';

date_default_timezone_set("Asia/Bangkok");


//Giới hạn dung lượng file tải lên server
define('CONST_UPLOAD_FILE_MAX_SIZE', 20971520); //20mb
define('CONST_TYPE_FILE_ACCEPT', 'jpg,png,pdf,gif,doc,docx,excel');
define('CONST_DIR_UPLOAD',BASE_DIR.'\Docroot\upload\record_type');