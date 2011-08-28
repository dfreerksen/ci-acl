<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$config['acl_table_users'] = 'users';
$config['acl_table_permissions'] = 'permissions';
$config['acl_table_role_permissions'] = 'role_permissions';

$config['acl_user_session_key'] = 'user_id';

$config['acl_cache'] = TRUE;
$config['acl_cache_time'] = 86400;
$config['acl_cache_prefix'] = 'acl_';
$config['acl_cache_adapter'] = 'file';
$config['acl_cache_backup_adapter'] = 'dummy';

$config['acl_restricted'] = array(

	'controller/method' => array(
			'allow_roles' => array(1),
			'allow_users' => array(1),
			'error_msg' => 'You do not have permission to visit this page!'
		)

);