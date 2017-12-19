<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Soy_testigo extends Sys_Controller {

	function __construct()
    {
    	parent::__construct(false);


		$this->load->model('incidencia_model', 'm_mincidencia');
		$this->load->model('newincidencia_model', 'm_incidencia');
		$this->load->model('usuarioapp_model','m_usuarioapp');
		$this->load->model('tipo_incidencia_model','m_tipo_incidencia');
		session_write_close();

    }

	public function index()
	{
		
	}

	public function addIncidencia(){
		$json = $this->_getRaw();
		
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);
			$idtipo = $json->idtipo;
			$idsubtipo = $json->idsubtipo;
			$tipo = $json->tipo;
			$subtipo = $json->subtipo;
			$descripcion = $json->descripcion;
			$latitud = $json->latitud;
			$longitud = $json->longitud;
			$idinstitucion = $json->idinstitucion;
			$idubigeo = $json->idubigeo;
			$fecha = @date('Y-m-d H:i:s');
			$nombre = $json->nombre;
			$apellido = $json->apellido;
			$correo = $json->correo;
			$celular = $json->celular;

			$direccion = '';
			try{
				$ws = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?latlng='.$latitud.','.$longitud);
				$json = @json_decode($ws);
				$json = @$json->results[0];
				$direccion = $json->formatted_address;
			}catch(Exception $ex){ 
			}

			$idincidencia = $this->m_mincidencia->addIncidencia($idtipo,$idsubtipo,$tipo,$subtipo,$descripcion,$latitud,$longitud,$idinstitucion,$idubigeo,$fecha,$nombre,$apellido,$correo,$celular,$direccion);
			if($idincidencia > 0){
				$result['status'] = 'success';
				$result['msj'] = 'Incidencia registrada!';
				$result['idincidencia'] = $idincidencia;
			}else{
				$result['status'] = 'error';
				$result['msj'] = 'No se pudo registrar la incidencia!';
			}
		}


		echo json_encode($result);
	}




	public function login(){
		$json = $this->_getRaw();
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);
			$codigo = $json->codigo;
			$clave = $json->clave;
			if(trim($codigo)!='' && trim($clave)!=''){
				$login = $this->m_usuario->get_byApiLogin($codigo, $clave, 'IDUSUARIO, NOMBRE, APELLIDO, CKAPI');
				if($login){
					if($login['CKAPI'] == 1){

						$token = $this->m_tokenapi->get_Hash();
						$idtoken = $this->m_tokenapi->add_Token($login['IDUSUARIO'], $token, $this->_getIP(), $this->tokenapi_duration, $this->_esDispositivoMovil(), $this->_getDispositivo());
						if($idtoken){
							$result['usuario'] = $login;
							$result['token'] = $token;
							$result['status'] = 'success';
							$result['msj'] = 'Datos correctos';
						}else{
							$result['status'] = 'error';
							$result['msj'] = 'Error al registrar token';
						}
						
					}else{
						$result['status'] = 'error';
						$result['msj'] = 'No autorizado';
					}
				}else{
					$result['status'] = 'error';
					$result['msj'] = 'Datos de acceso incorrectos';
				}
				//$result['post'] = $json;
			}else{
				$result['status'] = 'error';
				$result['msj'] = 'No se detectó datos';
			}
			
		}		

		echo json_encode($result);
	}



	// API TESTIGO


	public function addUserApp(){
		$json = $this->_getRaw();
		
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);
			
			$email = $json->correo;
			$correovalido =  $this->m_usuarioapp->get_byEmail($email);

			if(!$correovalido){
				$nrodoc = $json->nrodoc;
				$nombres = $json->nombres;
				$apellidos = $json->apellidos;
				$fechanac = $json->fechanac;
				$alias = $json->alias;
				$clave = $json->clave;
				$celular = $json->celular;
				$correo = $json->correo;
				$sexo = $json->sexo;
				$fecha = @date('Y-m-d H:i:s');
				$idusuario = $json->idusuario;

				$idusuapp = $this->m_usuarioapp->addUsuarioApp($nrodoc,$correo,$nombres,$apellidos,$fechanac,$alias,$clave,$celular,$sexo,$fecha,$idusuario);

				if($idusuapp > 0){
					$result['status'] = 'success';
					$result['msj'] = 'Usuario registrado!';
					$result['idusuapp'] = $idusuapp;
				}else{
					$result['status'] = 'error';
					$result['msj'] = 'No se pudo registrar al usuario!';
				}
			}
			else{
					$result['status'] = 'error';
					$result['msj'] = 'Ya existe usuario con este correo!';
			}
	
		}
		echo json_encode($result);
		
	}

	public function addIncidenciaTestigo(){
		$json = $this->_getRaw();
		
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';

		if(isset($json)){
			$json = @json_decode($json);
			$titulo = $json->titulo;
			$detalle = $json->detalle;
			$idtipo = $json->idtipo;
			$estado = $json->estado;
			$direccion = $json->direccion;
			$latitud = $json->latitud;
			$longitud = $json->longitud;			
			$idusuario = $json->idusuario;
			$ipmaq  = $json->ipmaq;

			$idincidenciatestigo = $this->m_incidencia->add_incidencias($titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq);

			if($idincidenciatestigo > 0){
				$result['status'] = 'success';
				$result['msj'] = 'Incidencia registrada!';
				$result['idincidenciatestigo'] = $idincidenciatestigo;
			}else{
				$result['status'] = 'error';
				$result['msj'] = 'No se pudo registrar la incidencia!';
			}

		}
		echo json_encode($result);
		
	}


	public function login_mail(){
		$json = $this->_getRaw();
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);			
				$email = $json->email;

			if(trim($email)!=''){
				$login = $this->m_usuarioapp->get_byEmail($email);

				if($login){
					$result['status'] = 'success';
					$result['msj'] = 'Datos de acceso correctos';
					$result['usuario'] = $login;
				}else{
					$result['status'] = 'error';
					$result['msj'] = 'Datos de acceso incorrectos';
				}
			}else{
				$result['status'] = 'error';
				$result['msj'] = 'No se detectó datos';
			}
			
		}		

		echo json_encode($result);
	}

	
	public function get_tipos(){
		// $json = $this->_getRaw();
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';

		// echo 'asd';

		$tipo = $this->m_tipo_incidencia->get_tipo();
		if(isset($tipo)){
			$result['status'] = 'success';
			$result['msj'] = 'Datos correctos';
			$result['tipos'] = $tipo;
		}else{
			$result['status'] = 'error';
			$result['msj'] = 'Datos de acceso incorrectos';
		}


		echo json_encode($result);
	}

	
	public function get_maestros(){		
		$result = array();
		$result['status'] = 'success';
		$result['msj'] = 'Maestros';

		$result['parametro'] = array();
		$result['parametro'][] = (object)array('ParametroID'=>9001, 'ParametroParametro'=>'base', 'ParametroValor'=>base_url());
		$result['parametro'][] = (object)array('ParametroID'=>9002, 'ParametroParametro'=>'assets_ico', 'ParametroValor'=>'assets/soytestigo/ico/');
		$result['parametro'][] = (object)array('ParametroID'=>9003, 'ParametroParametro'=>'assets_media', 'ParametroValor'=>'assets/soytestigo/media/');

		$result['tipo_incidencia'] = $this->m_tipo_incidencia->get_tipo();

		echo json_encode($result);
	}
	


}


