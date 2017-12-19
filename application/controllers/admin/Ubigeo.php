<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Ubigeo extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();
		$this->load->model('ubigeo_model', 'm_ubigeo');		
		$this->load->model('comisaria_model', 'm_comisaria');
		$this->load->model('institucion_model', 'm_institucion');
		session_write_close();
    }

	public function index()
	{
		
	}

	public function json_ubigeo(){
		$ubigeo = @$_POST['ubigeo'];
		$tipo = (int)@$_POST['tipo'];
		$data = array();
		$data['data'] = $this->m_ubigeo->get_Combo($tipo, $ubigeo);
		$this->json_output($data);
	}

	public function json_comisaria(){
		$ubigeo = @$_POST['ubigeo'];
		$data = array();
		$data['data'] = $this->m_comisaria->get_Comisarias($ubigeo);
		$this->json_output($data);
	}

	public function json_dependencia(){
		$tipo = @$_POST['tipo'];
		$padre = @$_POST['padre'];
		$usuario = $this->getUsuarioLogin();
		$tipoinst = @$_POST['tipoinst'];
		$data = array();
		$data['data'] = @$this->m_institucion->get_Combo($tipo, $padre,$usuario['IDUSUARIO'],$tipoinst);
		$this->json_output($data);
	}

}
