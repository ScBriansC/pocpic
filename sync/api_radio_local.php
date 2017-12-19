<?php

require_once('api_cnf.php');

function get_ws($parametro, $his = false){
	global $ApiSync;
	$idparametro = $parametro['IDPARAMETRO'];
	$_id = $parametro['VALOR'];

	$diftime = $ApiSync->get_diftime_param($idparametro);

	if($diftime>5){
		$_id = getIDVal();

		if(!($_id>=$parametro['VALOR'])){
			$ApiSync->close();
			return 'err_sync';
		}
	}

	$resp['success_proc'] = 0;
	$resp['fail_proc'] = 0;

	$ultimo_id = $_id;

	$ws = @file_get_contents('http://172.22.0.67/sync/api_radio.php?acc=gtdt&_id='.$_id);
	$data_ws = @json_decode($ws, true);

	$q = "";

	$cnt_q = 0;

	$md_num = 500;

	$tot_num = count($data_ws['sq_data']);

	if(isset($data_ws['sq_data']) && count($data_ws['sq_data'])>0){
		foreach ($data_ws['sq_data'] as $obj) {

		  $id_location = (int)@$obj['LocationID'];

		  $dada = array();
		  $dada['idlocation'] = @$obj['LocationID'];
		  $dada['idradio'] = @$obj['Radio'];
		  $dada['longitud'] = @$obj['Longitud'];
		  $dada['latitud'] = @$obj['Latitud'];
		  $dada['altura'] = @$obj['Altura'];
		  $dada['velocidad'] = @$obj['Velocidad'];
		  $dada['fechaloc'] = substr(@$obj['FechaLoc'], 0, 19);
		  $dada['fechareg'] = substr(@$obj['FechaReg'], 0, 19);
		  $dada['numhora'] = @$obj['NumHora'];
		  $dada['geom'] = @$obj['GEOM'];
		  $dada['vehiculo'] = @$obj['Vehiculo'];
		  $dada['id'] = @$obj['NumHora'].'_'.@$obj['Radio'].'_'.@$obj['LocationID'];

		  if($cnt_q%$md_num == 0){
		  	$q = "BEGIN \n";
		  }

		  if($his){

		  $q.=" INSERT INTO SISGESPATMI.TH_RUTA_SYNC (IDRUTASYNC,IDPNPCENTRAL,IDPNPRADIO,LATITUD,LONGITUD,ALTURA,VELOCIDAD,FECHALOC,FECHAREG,FECHASYNC,PROVEEDOR,FLGPROC,FLGUSO,FLGCORRECTO)
				VALUES(SISGESPATMI.USEQ_THRUTASYNC_IDRUTASYNC.NEXTVAL, '".$dada['id']."', '".$dada['idradio']."', ".$dada['latitud'].", ".$dada['longitud'].", ".(int)$dada['altura'].", ".(($dada['velocidad']!='')?$dada['velocidad']:'0').", TO_DATE('".$dada['fechaloc']."', 'YYYY-MM-DD HH24:MI:SS'), TO_DATE('".$dada['fechareg']."', 'YYYY-MM-DD HH24:MI:SS'), SYSDATE, 1, 0,0,".((substr(@$dada['fechaloc'], 0, 13) == substr(@$dada['fechareg'], 0, 13))?1:0)."); \n";

			}else{
				$q.=" UPDATE SISGESPATMI.TM_RADIO SET 
                  LATITUD_ANT = LATITUD
                , LONGITUD_ANT = LONGITUD 
                , FECHALOC_ANT = FECHALOC                      
                , LATITUD = ".$dada['latitud']."
                , LONGITUD = ".$dada['longitud']." 
                , ALTURA = ".(int)$dada['altura']." 
                , VELOCIDAD = ".(($dada['velocidad']!='')?$dada['velocidad']:'0')."
                , FECHALOCINI = (CASE WHEN FECHALOCINI IS NULL OR TRUNC(FECHALOCINI) <> TRUNC(TO_DATE('".$dada['fechaloc']."', 'YYYY-MM-DD HH24:MI:SS')) THEN TO_DATE('".$dada['fechaloc']."', 'YYYY-MM-DD HH24:MI:SS') ELSE FECHALOCINI END )
                , FECHALOC = TO_DATE('".$dada['fechaloc']."', 'YYYY-MM-DD HH24:MI:SS')
                , HORALOC = TO_CHAR(TO_DATE('".$dada['fechaloc']."','YYYY-MM-DD HH24:MI:SS'), 'HH24MISS')
         		WHERE ETIQUETA = '".$dada['idradio']."' AND FLGACTIVO = 1; \n";
			}
		  
		  if(($cnt_q%$md_num == 499) || ($cnt_q == $tot_num -1)){
		  	$q = $q."\n COMMIT; END;\n";

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



function get_ws_text($_id){

	$ws = @file_get_contents('http://172.22.0.67/sync/api_radio.php?acc=gtdt&_id='.$_id);
	

	return $ws;

}


function getID(){
	$ws = @file_get_contents('http://172.22.0.67/sync/api_radio.php?acc=gtid');
	return $ws;
}


function getIDVal(){
	$ws = @json_decode(@getID(), true);
	return @$ws['sq_data'];
}

$acc = @$_REQUEST['acc'];

if($acc == 'sync'){
	$activar = @$ApiSync->get_parametro(5);

	if((int)@$activar['VALOR'] > 0){

		if(((int)@$activar['VALOR'] == 1 && $ApiSync->get_ip()=='::1') || ((int)@$activar['VALOR'] == 2)){
			$parametro = $ApiSync->get_parametro(8);
			$response = get_ws($parametro);
			echo json_encode($response);
		}else{
			die('No autorizado');
		}

	}else{
		die('Desactivado');
	}

}elseif($acc == 'sync_his'){
	$activar = @$ApiSync->get_parametro(5);

	if((int)@$activar['VALOR'] > 0){

		if(((int)@$activar['VALOR'] == 1 && $ApiSync->get_ip()=='::1') || ((int)@$activar['VALOR'] == 2)){
			$parametro = $ApiSync->get_parametro(4);
			$response = get_ws($parametro, true);
			echo json_encode($response);
		}else{
			die('No autorizado');
		}

	}else{
		die('Desactivado');
	}

}elseif($acc == 'gtid'){
	echo getID();
}elseif($acc == 'gtdt'){
	echo get_ws_text(@$_REQUEST['_id']);
}

$ApiSync->close();