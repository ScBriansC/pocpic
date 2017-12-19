<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Alarma extends Sys_Controller {

	function __construct()
    {
    	parent::__construct(false);
    	$this->load->model('alarma_model', 'm_alarma');
		$this->load->model('alarma_reporte_model', 'm_alarmareporte');
    	session_write_close();
    }

	public function index()
	{
	}

	public function encenderAlarma(){
		$json = $this->_getRaw();
		
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);

			$idalarma = $json->alarma;
			$flag = $json->flag;
			$lat = $json->lat;
			$lon = $json->lon;
			$fecha = @date('Y-m-d H:i:s');

			$idalarma = $this->m_alarma->set_Encendido($idalarma,$flag);
			$idreporte = $this->m_alarmareporte->addEncendido($idalarma,$lat,$lon,$fecha,$flag);

			if($idalarma > 0 && $idreporte > 0){
				$result['status'] = 'success';
				$result['msj'] = 'Alarma encendida!';
				$result['idalarma'] = $idalarma;
				$result['idreporte'] = $idreporte;
			}else{
				$result['status'] = 'error';
				$result['msj'] = 'No se pudo actualizar alarma!';
			}

		}
		echo json_encode($result);
		
	}

	public function apagarAlarma(){

		$json = $this->_getRaw();
		
		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';
		if(isset($json)){
			$json = @json_decode($json);
			$idalarma = $json->id;
			$lat = $json->lat;
			$lon = $json->lon;
			$tipo = $json->tipo;
			$motivo = $json->motivo;
			$detalle = $json->detalle;
			$fecha = @date('Y-m-d H:i:s');
			$flag = $json->enc;

			$idalarma = $this->m_alarma->set_Encendido($idalarma,2);
			$idreporte = $this->m_alarmareporte->setApagado($idalarma,$lat,$lon,$tipo,$motivo,$detalle,$fecha,$flag);

			if($idalarma > 0 && $idreporte > 0){
				$result['status'] = 'success';
				$result['msj'] = 'Alarma apagada!';
				$result['idalarma'] = $idalarma;
				$result['idreporte'] = $idreporte;
			}else{
				$result['idalarma'] = $idalarma;
				$result['idreporte'] = $idreporte;
				$result['status'] = 'error';
				$result['msj'] = 'No se pudo actualizar alarma!';
			}

		}
		echo json_encode($result);
	}

}

