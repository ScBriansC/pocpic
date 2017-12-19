<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Sys_Controller extends CI_Controller {

	var $usuario_login;
	var $usuario_sesion;
	var $validar_login = false;
	var $tokensms_duration;
	var $tokenapi_duration;
	var $modulo_base;

	function __construct($validar = true)
	{
		parent::__construct();

		$this->validar_login = $validar;
		$this->tokensms_duration = $this->config->item('tokensms_duration');
		$this->tokenapi_duration = $this->config->item('tokensms_duration');

		$this->load->model('usuario_model', 'm_usuario');
		$this->load->model('sesion_model', 'm_sesion');
		$this->load->model('parametro_model', 'm_parametro');
		$this->load->model('tokensms_model', 'm_tokensms');		
		$this->load->model('tokenapi_model', 'm_tokenapi');
		$this->load->model('modulo_model','m_modulo');

		$tokensms_duration = $this->m_parametro->_get(2, 'VALOR');
		$this->tokensms_duration = (int)@$tokensms_duration['VALOR'];

		$tokenapi_duration = $this->m_parametro->_get(7, 'VALOR');
		$this->tokenapi_duration = (int)@$tokenapi_duration['VALOR'];

		$this->usuario_login = $this->session->userdata('usuario_login');
		$this->usuario_sesion = $this->session->userdata('usuario_sesion');
		//$this->usuario_login = 1;

		if(!$this->usuario_login && $this->validar_login){
			redirect('/login', 'refresh');
		}elseif($this->usuario_login && $this->validar_login){
			$sesion = $this->m_sesion->get_SesionActiva($this->usuario_login);
			if(!$sesion || $sesion['IPMAQREG']!=$this->_getIP()){
				$this->session->set_userdata('usuario_login', FALSE);
				$this->session->set_userdata('usuario_sesion', FALSE);
				redirect('/login', 'refresh');
			}

		}

		$this->modulo_base = array();
		$modulo = $this->m_modulo->_get_lista(0,0,uri_string());
		if(count($modulo)>0){
			$modulo = $this->m_modulo->_get_lista($this->usuario_login,0,uri_string());
			if(count($modulo)==0){
				redirect('/noautorizado', 'refresh');
			}else{
				$this->modulo_base = $modulo[0];
			}
		}

	}

	protected function get_menu($idusuario){
		$menu = $this->m_modulo->_get_lista($idusuario);
		$menu_tmp = array();

		foreach ($menu as $item) {
			$padre = $item['IDPADRE'];
			if(!$padre){
				$padre = $item['IDMODULO'];
			}

			if(!$menu_tmp[$padre]){
				$menu_tmp[$padre] = array('item'=>$item, 'subitems'=>array());
			}else{
				$menu_tmp[$padre]['subitems'][] = $item;
			}
		}

		return $menu_tmp;
	}

	protected function sys_render($view, $data = array(), $datah = array(), $dataf = array()){
		$data_header = array();
		$data_header['obj_usuario'] = $this->getUsuarioLogin();
        $data_header['sidebar']        = @$_COOKIE['sidebar'];
		$data_header = array_merge($data_header, $datah);

		// $data_header['usu_modulo'] = $this->m_modulo->_get_lista($usuario['IDUSUARIO']);
		$data_header['menu'] = $this->get_menu($data_header['obj_usuario']['IDUSUARIO']);

		$data_footer = array();
		$data_footer = array_merge($data_footer, $dataf);
  	    
		$data_footer['ses_duracion'] = $this->m_sesion->_get_duracion($data_header['obj_usuario']['IDUSUARIO']);

		$data['obj_usuario'] = $this->getUsuarioLogin();
		$data['obj_modulo'] = $this->modulo_base;

		$this->load->view('admin/sipcop_header', $data_header);
		$this->load->view($view, $data);
		$this->load->view('admin/sipcop_footer', $data_footer);
	}

	protected function getUsuarioLogin(){
		return $this->m_usuario->_get_usuario_info($this->usuario_login);
	}

	public function index()
	{
		
	}

	protected function _getIP(){
		$ipaddress = '';
        if ($_SERVER['HTTP_CLIENT_IP'])
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if($_SERVER['HTTP_X_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if($_SERVER['HTTP_X_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if($_SERVER['HTTP_FORWARDED_FOR'])
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if($_SERVER['HTTP_FORWARDED'])
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if($_SERVER['REMOTE_ADDR'])
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
	}

	protected function json_output($arr = array())
	{
		header('Content-Type: application/json');
		echo json_encode( $arr );
	}

	protected function _generarSMSToken(){
		return rand(100000,999999);
	}

	protected function _registrarTokenSMS($idusuario, $celular){
		$token = $this->_generarSMSToken();
		if(trim($celular)!='' && strlen(trim($celular)) == 9){
			$idtokensms = $this->m_tokensms->add_TokenSMS($idusuario, $celular, $token, $this->_getIP(), $this->tokensms_duration);
			if($idtokensms > 0){
				if($this->_enviarSMS($celular, "SIPCOP Código: ".$token)){
					return $idtokensms;
				}
			}
		}
		return FALSE;
	}

	protected function _registrarSesion($idusuario, $idtokensms = null){
		$idsesion = $this->m_sesion->add_Sesion($idusuario, $idtokensms, $this->_getIP(), @ini_get("session.gc_maxlifetime"), $this->_esDispositivoMovil(), $this->_getDispositivo());
		if($idsesion > 0){
			return $idsesion;
		}
		return FALSE;
	}

	protected function _getByTag($xml, $tag){
	    $pattern = "#<\s*?$tag\b[^>]*>(.*?)</$tag\b[^>]*>#s";
	    preg_match($pattern, $xml, $matches);
	    return $matches[1];
	}

	protected function _enviarSMS($celular, $mensaje){
		$tipo = 1;

		if(extension_loaded('curl')){ 

		    $apikey = "12FDEF0BD5D3";
		    $apicard = "3337967453";
		    $fields_string = "";

		    //Preparamos las variables que queremos enviar
		    $url = 'http://api2.gamacom.com.pe/smssend';
		    $fields = array(
		                        'apicard'=>urlencode($apicard),
		                        'apikey'=>urlencode($apikey),
		                        'smsnumber'=>urlencode($celular),
		                        'smstext'=>urlencode($mensaje),
		                        'smstype'=>urlencode($tipo)
		                );

		    //Preparamos el string para hacer POST (formato querystring)
		    foreach($fields as $key=>$value) { 
		       $fields_string .= $key.'='.$value.'&'; 
		    }
		    $fields_string = rtrim($fields_string,'&');


		    //abrimos la conexion
		    $ch = curl_init();

		    //configuramos la URL, numero de variables POST y los datos POST
		    curl_setopt($ch,CURLOPT_URL,$url);
		    curl_setopt($ch,CURLOPT_POST,count($fields));
		    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		    curl_setopt($ch,CURLOPT_POSTFIELDS,$fields_string);

		    //ejecutamos POST
		    $result = curl_exec($ch);

		    //cerramos la conexion
		    curl_close($ch);

		    //Resultado
		    $array = json_decode($result,true);
		    return ((@$array['message'] == '0')?TRUE:@$array['message']);
		}else{
			return FALSE;      
		}
		
		/*if(extension_loaded('curl')){ 

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
		    $result  = @$this->_getByTag(@curl_exec($ch),'envioSMSResult');

		    //cerramos la conexion
		    @curl_close($ch);

		    if($result=='OK'){
		    	return TRUE;
		    }

		    return FALSE;
		}else{
			return FALSE;      
		}*/
	}

	protected function _esDispositivoMovil(){
		return (int)@preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
	}

	

	protected function _getDispositivo(){
		return @str_replace("'", "", substr($_SERVER["HTTP_USER_AGENT"],0,100));
	}

	protected function _getRaw(){
		return @file_get_contents('php://input');
	}



}
