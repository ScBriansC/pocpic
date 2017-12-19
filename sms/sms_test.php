<?php
ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

header("Access-Control-Allow-Origin: *");
header("Content-Type: text/plain");


function _getByTag($xml, $tag){
    $pattern = "#<\s*?$tag\b[^>]*>(.*?)</$tag\b[^>]*>#s";
    preg_match($pattern, $xml, $matches);
    return $matches[1];
}

function enviarSMS($celular, $mensaje, $once = 1){
	$max_onces = 3;
	if(extension_loaded('curl')){ 

	    $usuario = "mininter";
	    $clave = "1Xv3dt6Us1R8";

	    //Preparamos las variables que queremos enviar
	    $url = 'http://ws.pide.gob.pe/SMS_ServiceSoap ';
	    $data = "<soapenv:Envelope xmlns:soapenv=\"http://schemas.xmlsoap.org/soap/envelope/\" xmlns:app=\"http://appbinomio.com/\">
				   <soapenv:Header/>
				   <soapenv:Body>
				      <app:envioSMS>
				         <app:usuario>".$usuario."</app:usuario>
				         <app:keyws>".$clave."</app:keyws>
				         <app:celular>".$celular."</app:celular>
				         <app:mensaje>".$mensaje."</app:mensaje>
				      </app:envioSMS>
				   </soapenv:Body>
				</soapenv:Envelope>";

	    //Preparamos el string para hacer POST (formato querystring)
	    $ch = @curl_init();
		@curl_setopt($ch, CURLOPT_URL, $url);
		@curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml','Content-Length: ' . strlen($data)));
		@curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		@curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		@curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

	    //ejecutamos POST
	    $result  = _getByTag(@curl_exec($ch),'envioSMSResult');

	    //cerramos la conexion
	    @curl_close($ch);

	    if($result=='OK'){
	    	return TRUE;
	    }else{
	    	sleep(1);
	    	if($once <= $max_onces){
	    		return enviarSMS($celular, $mensaje, $once + 1);
	    	}
	    }

	    return FALSE;
	}else{
		return FALSE;      
	}
}

$archivo_log = 'logs/campaign_log_'.date('Ymdhis').'.csv';



$campaign_list = array(
	'NOMBRE;CELULAR',
'SIPCOP;980122819'
);


$col_celular = 1;
$log = $campaign_list[0].";EXEC\n";
for ($i=1; $i < count($campaign_list); $i++) { 
	$data = explode(';', $campaign_list[$i]);
	$log.= $campaign_list[$i].';';

	$mensaje = "PRUEBA MSJ DE CLARO. DI OKIS, SI TE LLEGO XD";
	//echo $data[$col_celular]."\n";
	$envio = enviarSMS($data[$col_celular],$mensaje);
	

	if($envio){
		$log.="OK - ".$mensaje;
	}else{
		$log.="ERROR";
	}
	$log.="\n";
	file_put_contents($archivo_log, $log);
}

file_put_contents($archivo_log, $log);
echo $log;
echo "\n";
echo "\n";
echo "\n";