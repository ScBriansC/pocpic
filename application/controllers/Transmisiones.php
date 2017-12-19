<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Transmisiones extends Sys_Controller {

	function __construct()
    {
    	parent::__construct(FALSE);

		$this->load->model('ubigeo_model', 'm_ubigeo');
		$this->load->model('tracker_model', 'm_tracker');
		$this->load->model('institucion_model', 'm_institucion');

		session_write_close();
    }

    public function index()
	{
		
	}


    public function json_jurisdiccion(){
    	$ubigeo = 0;
		$idcomisaria = 0;
		$ubigeo = '';

		$data = array();
		$data['jurisdicciones'] = $this->m_institucion->_get_jurisdiccion($institucion, $dependencia, $ubigeo, 0);
		$this->json_output($data);
    }


    public function transmisiones(){
    	
    	$data = array();
		
		$this->load->view('admin/sipcop_capturador', $data);
    }

    public function call_salejurisdiccion(){

    	$unidades = $_REQUEST['unidades'];
    	$cantidad = 0;

    	foreach ($unidades as $unidad) {
    		$ok = @$this->m_tracker->_sale_jurisdiccion($unidad['dispogps'],$unidad['fecha'],$unidad['hora'],$unidad['latitud'],$unidad['longitud']);
    		if($ok){
    			$cantidad++;
    		}
    	}

    	$data = array();
		$data['unidades'] = $cantidad;
		$this->json_output($data);
    }







    private function _validarFechaActual($fecha, $ini, $fin)
    {
    	$val = ($fecha==@date('d/m/Y') && ($ini == '00:00' && $fin == '23:59'));
    	return $val;
    }

	public function json_patrullaje(){
		$fecha = @date('d/m/Y');
		$horaini = '00:00';
		$horafin = '23:59';
		$tipo_filtro = 0;

		$dispogps = (int)@$_POST['dispogps'];

		$dependencia = (int)@$_POST['dependencia'];
		$institucion = (int)@$_POST['institucion'];
		$descripcion = strtoupper(@$_POST['descripcion']);
		$placa = strtoupper(@$_POST['placa']);
		$idradio = strtoupper(@$_POST['idradio']);
		$serie = strtoupper(@$_POST['serie']);

		$tipo_patrullaje = array();
		$tipo_patrullaje[] = 'patrullero';
		$tipo_patrullaje[] = 'motorizado';
		$tipo_patrullaje[] = 'patpie';

		$data = array();

		$data['patrullaje'] = $this->m_tracker->_get_gps($fecha, $horaini, $horafin, $dispogps, $dependencia, $institucion, $tipo_patrullaje);

		$this->json_output($data);
	}

}