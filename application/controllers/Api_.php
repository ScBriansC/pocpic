<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_ extends Sys_Controller {

	function __construct()
    {
    	parent::__construct(false);
		$this->load->model('alarma_model', 'm_alarma');
		$this->load->model('alarma_reporte_model', 'm_alarmareporte');
		$this->load->model('incidencia_model', 'm_incidencia');
    }

	public function index()
	{
		
	}

	// public function addAlarmaReporte(){
	// 	$json = $this->_getRaw();
		
	// 	$result = array();
	// 	$result['status'] = 'error';
	// 	$result['msj'] = 'No se detectó datos';
	// 	if(isset($json)){
	// 		$json = @json_decode($json);
	// 		$idalarma = $json->idalarma;
	// 		$tipo = $json->tipo;
	// 		$descripcion = $json->descripcion;
	// 		$latitud = $json->latitud;
	// 		$longitud = $json->longitud;
	// 		$fecha = @date('Y-m-d H:i:s');
	// 		$idusuario = $json->idusuario;

	// 		$idreporte = $this->m_alarmareporte->addReporte($idalarma,$tipo,$descripcion,$latitud,$longitud,$fecha,$idusuario);
			
	// 		if($idreporte > 0){
	// 			$result['status'] = 'success';
	// 			$result['msj'] = 'Reporte de alarma registrado!';
	// 			$result['idreporte'] = $idreporte;
	// 		}else{
	// 			$result['status'] = 'error';
	// 			$result['msj'] = 'No se pudo registrar reporte de alarma!';
	// 		}

	// 	}
	// 	echo json_encode($result);
		
	// }

	// public function encenderAlarma(){
	// 	$json = $this->_getRaw();
		
	// 	$result = array();
	// 	$result['status'] = 'error';
	// 	$result['msj'] = 'No se detectó datos';
	// 	if(isset($json)){
	// 		$json = @json_decode($json);
	// 		$idalarma = $json->alarma;
	// 		$flag = $json->flag;

	// 		$idalarma = $this->m_alarma->set_Encendido($idalarma,$flag);
			
	// 		if($idalarma > 0){
	// 			$result['status'] = 'success';
	// 			$result['msj'] = 'Alarma actualizada!';
	// 			$result['idreporte'] = $idreporte;
	// 		}else{
	// 			$result['status'] = 'error';
	// 			$result['msj'] = 'No se pudo actualizar alarma!';
	// 		}

	// 	}
	// 	echo json_encode($result);
		
	// }

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
			$idcomisaria = $json->idcomisaria;
			$idmunicipalidad = $json->idmunicipalidad;
			$idubigeo = $json->idubigeo;
			$fecha = @date('Y-m-d H:i:s');
			$nombre = $json->nombre;
			$apellido = $json->apellido;
			$correo = $json->correo;
			$celular = $json->celular;

			$idincidencia = $this->m_incidencia->addIncidencia($idtipo,$idsubtipo,$tipo,$subtipo,$descripcion,$latitud,$longitud,$idcomisaria,$idmunicipalidad,$idubigeo,$fecha,$nombre,$apellido,$correo,$celular);
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

}


