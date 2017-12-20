<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$active_group = 'default';
$query_builder = TRUE;

/*DB Desarrollo*/
//$ip_srv = '172.22.12.35';
//$ins_srv = 'bdmin';

/*DB Producción I*/
//$ip_srv = '172.22.0.12';
//$ins_srv = 'BD_TRAMITE';

/*DB Producción II*/
  // $ip_srv = '172.22.0.76';
 $ins_srv = 'bdstdmi';

/*DB DESARROLLO*/
$ip_srv = '172.22.0.68';
// $ins_srv = 'bdstdmi';

$tnsname = '(DESCRIPTION = (ADDRESS_LIST = (ADDRESS = (PROTOCOL = TCP)(HOST =  '.$ip_srv.')(PORT = 1521)) ) (CONNECT_DATA = (SERVICE_NAME = '.$ins_srv.') ) )';

$db['default'] = array(
	'dsn'	=> '',
	'hostname' => $tnsname,
	'username' => 'app_sisgespatmi',
	'password' => 'app_sisgespatmi$$',
	// 'username' => 'sisgespatmi', 
	// 'password' => 'sisgespatmi$$',
	'database' => $ins_srv,
	'dbdriver' => 'oci8',
	'dbprefix' => '',
	'pconnect' => TRUE,
	'db_debug' => (ENVIRONMENT !== 'production'),
	'cache_on' => FALSE,
	'cachedir' => '',
	'char_set' => 'utf8',
	'dbcollat' => 'utf8_general_ci',
	'swap_pre' => '',
	'encrypt' => FALSE,
	'compress' => FALSE,
	'autoinit' => TRUE,
	'stricton' => FALSE,
	'failover' => array(),
	'save_queries' => TRUE


);


