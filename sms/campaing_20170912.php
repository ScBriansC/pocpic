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




$campaign_list = array(
	'NOMBRE;CELULAR',
	'ANDREW;997086066',
	'JENNIFER;923236608',
	'ANGELICA;933494786',
	'ANGELLO;963702810',
	'BRIAN;997526863',
	'GIANCARLO;950152078',
	'JENNY;949741485',
	'RALPH;975119277',
	'ORMEÑO;987139031',
	'PALACIOS;997000802' 
);
/*
$campaign_list = array(
	'NOMBRE;CELULAR',
'Antonio Marcelino Loreño Beltran;968016972',
'Roman Bayes Gaspar Baltazar;979416439',
'Jose Enrique Ramirez Flores;979419407',
'Williams Felix Alvarez Aguilar;980122763',
'Sofia Masias Mory;943204998',
'Luis Ricardo Chavez Gil;968017169',
'Rolando Izquierdo Bueno;950036364',
'Walter Rivas Cabezas;968017839',
'Fidel Pintado Pasapera;980122649',
'Carlos Reyes Tello;980122693',
'Luis Armando Tello Guevara;980122719',
'Roberto Segura Carrasco;980122743',
'Milagros Raquel Perez Ramon;980122767',
'Elias Rayme Prado;980122875',
'Julio Ricardo Guerra Cunya;980122924',
'YOLANDA CAROLINA FALCON  LIZARASO;959916593',
'JUAN CARLOS MARTIN JAUREGUI CALDERON;959917922',
'JESUS MILAGROS RUPAY MORENO;959917994',
'Rafael  Vargas  Málaga;959918060',
'Flor María Del Pilar Carranza  Chunga;959918183',
'JOHAN HELDER TORRES DIAZ;959918327',
'HERBERT ALEX CORDERO CUISANO;959918642',
'SATURNINO SIERRA RAMOS;959918798',
'DANIEL GERMAN LOZADA HERRERA;959918853',
'LIDIA GUMERCINDA PRADO CARITAS;959918042',
'EDWIN WLADIMIR EDQUEN  SANCHEZ ;959918949',
'MAGDA ANGELICA CORTEZ MEDINA DE DEZA;959919046',
'EDWIN ESPINOZA HUILLCA;959919159',
'GUALBERTO LIBORIO LOPEZ SALDAÑA;959919337',
'ROCIO DEL CARMEN LOPEZ TUCTO;959917854',
'RENZZO CESAR SOTELO LOPEZ;959919330',
'TEODOMIRO ROMAN RODRIGUEZ;959919411',
'CARLOS FERNANDO ARMAS ABRILL;959919429',
'RAFAEL ANTONIO AITA CAMPODONICO;959919460',
'MARCIO MARINO BENDEZU ECHEVARRIA;959919507',
'MARCIO MARINO BENDEZU  ECHEVARRIA;959919623',
'CARMEN RAQUEL NUÑEZ RENGIFO;959918910',
'SELVA DEL ROSARIO CUYA CAMPOS;959920053',
'PAULINA LOURDES CANO OVIEDO;959920159',
'JHON HITLER TORRES SANCHEZ;959920336',
'PEDRO OSCAR HERNANDEZ CALDERON;959919861',
'PERPETUA TACA YANA VDA DE SOTOMAYOR;959920545',
'CARMEN ROBERTO NAVARRO YAMUNAQUE;959920547',
'RAFAEL VARGAS MALAGA;959920532',
'ALEJANDRO AREVALO ORTIZ;959920649',
'MARCELO RENGIFO ALVAN;959920746',
'PERSONAL;997000802'
);*/


$col_celular = 1;
$log = $campaign_list[0].";EXEC\n";
for ($i=1; $i < count($campaign_list); $i++) { 
	$data = explode(';', $campaign_list[$i]);
	$log.= $campaign_list[$i].';';

	$mensaje = "Hoy reconocemos a los Defensores de la Democracia, gracias a su lucha vivimos en paz. Nuestro mas sincero agradecimiento. Defensoria de la Policia - MININTER.";
	
	$envio = enviarSMS($data[$col_celular],$mensaje);
	

	if($envio){
		$log.="OK - ".$mensaje;
	}else{
		$log.="ERROR";
	}
	$log.="\n";
}

file_put_contents('logs/campaign_log_'.date('Ymdhis').'.csv', $log);
echo $log;
echo "\n";
echo "\n";
echo "\n";