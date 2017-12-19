<?php

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

$system_path = 'system';
define('BASEPATH', $system_path);
define('ENVIRONMENT', 'production');

require_once('../application/config/database.php');



class ApiSync{

	var $oci;

	var $srv_ip;
	var $srv_ins;
	var $srv_user;
	var $srv_pass;

	var $err = array();

	function __construct($ip, $ins, $user, $pass){
		$this->srv_ip = $ip;
		$this->srv_ins = $ins;
		$this->srv_user = $user;
		$this->srv_pass = $pass;
	}

	public function connect(){
		$this->oci = @oci_connect($this->srv_user, $this->srv_pass, $this->srv_ip.'/'.$this->srv_ins);
		if(!$this->oci){
			die('Error Conexión Oracle');
		}
	}

	public function close(){
		@oci_close($this->oci);
	}

	public function get_rows($q){
		$this->connect();
		$stid = oci_parse($this->oci, $q);
		oci_execute($stid);
		$lista = array();
		while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
		    $lista[] = $row;
		}
		return $lista;
	}

	public function get_parametro($_id){
		$data = FALSE;
		$q = "SELECT IDPARAMETRO, VALOR FROM  SISGESPATMI.TM_PARAMETRO WHERE IDPARAMETRO = ".$_id;
		$rows = $this->get_rows($q);
		if(count($rows)>0){
			$data = $rows[0];
		}
		return $data;
	}

	public function exec_query($q){
		$this->connect();
		$stmt = oci_parse($this->oci, $q);
		$sp = @oci_execute($stmt);
		if(!$sp){
			$error = oci_error($this->oci);
			$this->err[] = htmlentities($error['message']);
		}
		//echo $q."\n\n";
		return $sp;
	}

	public function edit_parametro($_id, $_valor){
		$q = "UPDATE SISGESPATMI.TM_PARAMETRO SET VALOR='".$_valor."', FECHAMOD = SYSDATE WHERE IDPARAMETRO = ".$_id;
		return $this->exec_query($q);
	}

	public function get_ip() {
	    $ipaddress = '';
	    if (isset($_SERVER['HTTP_CLIENT_IP']))
	        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_X_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
	    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
	    else if(isset($_SERVER['HTTP_FORWARDED']))
	        $ipaddress = $_SERVER['HTTP_FORWARDED'];
	    else if(isset($_SERVER['REMOTE_ADDR']))
	        $ipaddress = $_SERVER['REMOTE_ADDR'];
	    else
	        $ipaddress = 'UNKNOWN';
	    return $ipaddress;
	}

	public function get_diftime_param($_id){
		$data = FALSE;
		$q = "SELECT ROUND((SYSDATE-FECHAMOD)*24*60) AS DIFERENCIA, FECHAMOD, SYSDATE FROM SISGESPATMI.TM_PARAMETRO WHERE IDPARAMETRO =  ".$_id;
		$rows = $this->get_rows($q);
		if(count($rows)>0){
			$data = $rows[0]['DIFERENCIA'];
		}
		return $data;
	}

	public function get_error(){
		return $this->err;
	}
}

//$ApiSync = new ApiSync($ip_srv, $ins_srv, $db['default']['username'],$db['default']['password']);
$ApiSync = new ApiSync($ip_srv, $ins_srv, 'APP_SISGESPATMI_SYNC','APP_SISGESPATMI_SYNC$$');
