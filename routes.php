<?php

use Libs\MvcContext;

$routes[] = new MvcContext(array('/', '/admin(/)'), 'GET', "Apps\\Cores\\Controllers\\HomeCtrl", 'index');

$routes[] = new MvcContext('/admin/config.js', 'GET', "Apps\\Cores\\Controllers\\HomeCtrl", 'configJS');

$routes[] = new MvcContext('/admin/user', 'GET', "Apps\\Cores\\Controllers\\UserCtrl", 'index');
$routes[] = new MvcContext('/admin/user/import', 'GET,POST', "Apps\\Cores\\Controllers\\UserCtrl", 'importUser');
$routes[] = new MvcContext('/admin/group', 'GET', "Apps\\Cores\\Controllers\\UserCtrl", 'group');

$routes[] = new MvcContext('/admin/application', 'GET', "Apps\\Cores\\Controllers\\SettingCtrl", 'application');
$routes[] = new MvcContext('/admin/setting', 'GET', "Apps\\Cores\\Controllers\\SettingCtrl", 'setting');
$routes[] = new MvcContext('/admin/setting/update', 'POST', "Apps\\Cores\\Controllers\\SettingCtrl", 'update');


$routes[] = new MvcContext('/rest/department/move', 'PUT', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'moveDepartments');
$routes[] = new MvcContext('/rest/department/:id', 'GET', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'getDepartment');
$routes[] = new MvcContext('/rest/department/:id', 'PUT', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'updateDepartment');
$routes[] = new MvcContext('/rest/department', 'DELETE', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'deleteDepartments');

$routes[] = new MvcContext('/rest/group', 'GET', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'getGroups');
$routes[] = new MvcContext('/rest/group/:id/user', 'GET', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'getGroupUsers');
$routes[] = new MvcContext('/rest/group/:id', 'PUT', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'updateGroup');
$routes[] = new MvcContext('/rest/group', 'DELETE', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'deleteGroups');

$routes[] = new MvcContext('/rest/basePermission', 'GET', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'getBasePermissions');

$routes[] = new MvcContext('/rest/user/search', 'GET', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'search');
$routes[] = new MvcContext('/rest/user/move', 'PUT', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'moveUsers');
$routes[] = new MvcContext('/rest/user/checkUniqueAccount', 'POST', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'checkUniqueAccount');
$routes[] = new MvcContext('/rest/user/:id', 'PUT', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'updateUser');
$routes[] = new MvcContext('/rest/user', 'DELETE', "Apps\\Cores\\Controllers\\Rest\\UserCtrl", 'deleteUsers');

$routes[] = new MvcContext('/admin/login/changePassword', 'GET,POST', "Apps\\Cores\\Controllers\\LoginCtrl", 'changePassword');
$routes[] = new MvcContext('/admin/login', 'GET,POST', "Apps\\Cores\\Controllers\\LoginCtrl", 'index');

$routes[] = new MvcContext('/admin/member', 'GET', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'index');
$routes[] = new MvcContext('/admin/member/getData', 'GET', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'getData');
$routes[] = new MvcContext('/admin/member/add', 'POST', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'addMember');
$routes[] = new MvcContext('/admin/member/update', 'POST', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'updateMember');
$routes[] = new MvcContext('/admin/member/delete', 'POST', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'deleteMember');
$routes[] = new MvcContext('/admin/member/searchName', 'GET', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'searchByName');
$routes[] = new MvcContext('/admin/member/searchType', 'GET', "Apps\\Cores\\Controllers\\Member\\MembersCtrl", 'searchByType');


$routes[] = new MvcContext('/admin/field', '*', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'index');
$routes[] = new MvcContext('/admin/field/getAllField', '*', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'getAllField');
$routes[] = new MvcContext('/admin/field/getData', '*', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'getData');
$routes[] = new MvcContext('/admin/field/add', 'POST', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'addField');
$routes[] = new MvcContext('/admin/field/update', 'POST', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'updateField');
$routes[] = new MvcContext('/admin/field/delete', 'POST', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'deleteField');
$routes[] = new MvcContext('/admin/field/searchName', 'GET', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'searchByName');
$routes[] = new MvcContext('/admin/field/searchType', 'GET', "Apps\\Cores\\Controllers\\Field\\FieldCtrl", 'searchByType');

$routes[] = new MvcContext('/admin/record', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'index');
$routes[] = new MvcContext('/admin/record/getData', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'getData');
$routes[] = new MvcContext('/admin/record/add', 'POST', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'addRecord');
$routes[] = new MvcContext('/admin/record/delete', 'POST', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'deleteRecord');
$routes[] = new MvcContext('/admin/record/searchName', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'searchByName');
$routes[] = new MvcContext('/admin/record/search', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'search');
$routes[] = new MvcContext('/admin/record/searchMember', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'searchByMember');
$routes[] = new MvcContext('/admin/record/searchStatus', 'GET', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'searchByStatus');
$routes[] = new MvcContext('/admin/record/update', 'POST', "Apps\\Cores\\Controllers\\Record\\RecordCtrl", 'updateRecord');

