<?php

require_once('api_cnf.php');

function get_ws($parametro){
	global $ApiSync;
	$idparametro = $parametro['IDPARAMETRO'];
	$_id = $parametro['VALOR'];

	$resp['success_proc'] = 0;
	$resp['fail_proc'] = 0;

	$ultimo_id = $_id;

	$ws = @file_get_contents('http://192.168.11.23/ws_patrullero/Api?sKey=MINTER_7045xD94&sType=dta_tracker&sID='.$_id);
	$data_ws = @json_decode($ws, true);

	$q = "";

	$cnt_q = 0;

	$md_num = 500;

	$tot_num = count($data_ws['tracker']);

	if(isset($data_ws['tracker']) && count($data_ws['tracker'])>0){
		foreach ($data_ws['tracker'] as $obj) {

		  $id_location = (int)@$obj['TrackerID'];

		  $dada = array();
		  $dada['idlocation'] = @$obj['TrackerID'];
		  $dada['idvehiculo'] = @$obj['CarID'];
		  $dada['longitud'] = @$obj['Longitude'];
		  $dada['latitud'] = @$obj['Latitude'];
		  $dada['altura'] = @$obj['Altitude'];
		  $dada['velocidad'] = @$obj['Speed'];
		  $dada['fechaloc'] = substr(@$obj['Time2'], 0, 19);
		  $dada['fechareg'] = substr(@$obj['TimeRegister'], 0, 19);
		  $dada['numhora'] = str_replace(' ', '', str_replace('-', '', str_replace(':', '', $dada['fechaloc'])));
		  $dada['geom'] = @$obj['Geometry'];
		  $dada['vehiculo'] = @$obj['CarID'];
		  $dada['id'] = @$dada['numhora'].'_'.@$obj['CarID'].'_'.@$obj['TrackerID'];

		  if($cnt_q%$md_num == 0){
		  	$q = "BEGIN \n";
		  }


		  $q.=" INSERT INTO SISGESPATMI.TH_RUTA_SYNC (IDRUTASYNC,IDPNPCENTRAL,IDPNPVEHICULO,LATITUD,LONGITUD,ALTURA,VELOCIDAD,FECHALOC,FECHAREG,FECHASYNC,PROVEEDOR,FLGPROC,FLGUSO,FLGCORRECTO)
				VALUES(SISGESPATMI.USEQ_THRUTASYNC_IDRUTASYNC.NEXTVAL, '".$dada['id']."', '".$dada['idvehiculo']."', ".$dada['latitud'].", ".$dada['longitud'].", ".(int)$dada['altura'].", ".(($dada['velocidad']!='')?$dada['velocidad']:'0').", TO_DATE('".$dada['fechaloc']."', 'YYYY-MM-DD HH24:MI:SS'), TO_DATE('".$dada['fechareg']."', 'YYYY-MM-DD HH24:MI:SS'), SYSDATE, 2, 0,0,".((substr(@$dada['fechaloc'], 0, 13) == substr(@$dada['fechareg'], 0, 13))?1:0)."); \n";

		  
		  if(($cnt_q%$md_num == 499) || ($cnt_q == $tot_num -1)){
		  	$q = $q."\n END;\n";

		  	if(@$ApiSync->exec_query($q)){
		  		$resp['success_proc']++;
		  	}else{
		  		$resp['fail_proc']++;
		  	}

		  	$q= "";
		  }

		  if($id_location > $ultimo_id){
  			$ultimo_id = $id_location;
  			
  		  }

		  $cnt_q++;
		}

		if($_id!=$ultimo_id){
			@$ApiSync->edit_parametro($idparametro, $ultimo_id);
		}
	}

	return $resp;

}



function getID(){
	$ws = @file_get_contents('http://192.168.11.23/ws_patrullero/Api?sKey=MINTER_7045xD94&sType=dta_last_tracker_id');
	echo $ws;
}



function getData($_id){
	$ws = @file_get_contents('http://192.168.11.23/ws_patrullero/Api?sKey=MINTER_7045xD94&sType=dta_tracker&sID='.$_id);
	echo $ws;
}



function getCar(){
	$ws = @file_get_contents('http://192.168.11.23/ws_patrullero/Api?sKey=MINTER_7045xD94&sType=dta_car');
	echo $ws;
}


$acc = @$_REQUEST['acc'];

if($acc == 'sync'){
	$activar = @$ApiSync->get_parametro(6);

	if((int)@$activar['VALOR'] > 0){

		if(((int)@$activar['VALOR'] == 1 && $ApiSync->get_ip()=='::1') || ((int)@$activar['VALOR'] == 2)){
			$parametro = $ApiSync->get_parametro(1);
			$response = get_ws($parametro);
			echo json_encode($response);
		}else{
			die('No autorizado');
		}

	}else{
		die('Desactivado');
	}

}elseif($acc == 'gtid'){
	getID();
}elseif($acc == 'gtdt' && (int)@$_REQUEST['_id']>0){
	gtData((int)@$_REQUEST['_id']);
}elseif($acc == 'gtcar'){
	getCar();
}

