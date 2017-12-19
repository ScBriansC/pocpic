<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Denuncia extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();
		$this->load->model('denuncia_model', 'm_denuncia');
		session_write_close();
    }

	public function index()
	{
		$parametroFooter = array(
            'jslib' => array(
                'assets/sipcop/js/ineigeoref.js'
            ),
        );

        $parametroHeader = array(
            'csslib' => array(
                'assets/sipcop/css/ineigeoref.css'
            ),
        );

		$this->sys_render('admin/sipcop_denunciar', $data, $parametroHeader, $parametroFooter);
	}

	public function registrar()
	{
		$usuario = $this->getUsuarioLogin();
		$direccion = @$_REQUEST['direccion'];
		$latitud = @$_REQUEST['latitud'];
		$longitud = @$_REQUEST['longitud'];

		$data = array();

		$ubigeo = $this->_getUbigeoINEI($latitud,$longitud);

		$iddenuncia = $this->m_denuncia->registrar($direccion,$ubigeo['IDUBIGEO'],$latitud,$longitud,$usuario['IDUSUARIO'], $this->_getIP());

		if($iddenuncia>0){
			$data['status'] = 'success';
			$data['msj'] = 'Denuncia registrada!';
		}else{
			$data['status'] = 'error';
			$data['msj'] = 'No se pudo registrar la denuncia';
		}

		$data['ubigeo'] = $ubigeo['IDUBIGEO'];

		$this->json_output($data);
	}


	private function _getUbigeoINEI(){
		$latitud = -12.061795305673147;
		$longitud = -77.0426693558693;
		$data = @file_get_contents('http://sige.inei.gob.pe/test/atlas/index.php/area_influencia/buscar_area_influencia_dist?txtPuntox='.$longitud.'&txtPuntoy='.$latitud);
		$data = explode('|', $data);
		$ubigeo = array(
			'IDUBIGEO'=>$data[0],
			'DISTRITO'=>$data[1]
		);
		return $ubigeo;
	}

	
}
