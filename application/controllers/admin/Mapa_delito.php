<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Mapa_delito extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();

		$this->load->model('institucion_model', 'm_institucion');

		$this->load->model('ubigeo_model', 'm_ubigeo');

		$this->load->model('denuncia_model', 'm_denuncia');
		$this->load->model('jurisdiccion_model', 'm_jurisdiccion');

		$this->load->model('usuario_model', 'm_usuario');
		$this->load->model('tokensms_model', 'm_tokensms');

		session_write_close();
    }

    private function _validarFechaActual($fecha, $ini, $fin)
    {
    	$val = ($fecha==@date('d/m/Y') && ($ini == '00:00' && $fin == '23:59'));
    	return $val;
    }


    public function index()
	{
		$data = array();

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$usuario = $this->getUsuarioLogin();
		$data['usu_jurisdiccion'] = $this->m_institucion->_get_jurisdiccion_usuario($usuario['IDUSUARIO']);

		$data_header = array('wrapper_class'=>'wrapper_mapa');
        $this->sys_render('admin/sipcop_mapadelito', $data, $data_header, $parametroFooter);

	}


	public function json_ubigeo(){
		$ubigeo = @$_POST['ubigeo'];
		$tipo = (int)@$_POST['tipo'];
		$data = array();

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
			$idcomisaria = $usuario['IDCOMISARIA'];	
			$comisaria = $this->m_institucion->_get_consulta($idcomisaria);
			$ubigeo = $comisaria[0]['ComisariaUbigeo'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		if($tipo == 0){
			$data['departamentos'] = $this->m_ubigeo->get_Departamentos($ubigeo);
		}elseif($tipo == 1){
			$data['provincias'] = $this->m_ubigeo->get_Provincias($ubigeo);
		}elseif($tipo == 2){
			$data['distritos'] = $this->m_ubigeo->get_Distritos($ubigeo);
		}

		$this->json_output($data);

	}

    public function json_consultar(){
    	$iddependencia = (int)@$_POST['dependencia'];
		$idinstitucion = (int)@$_POST['idinstitucion'];

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$hora_ini = @$_POST['horaini'];
		$hora_fin = @$_POST['horafin'];

		$modo = @$_POST['modo'];

		$usuario = $this->getUsuarioLogin();

		$tipos = '';

		$data = array();
		$data['denuncias'] = $this->m_denuncia->_get_consulta(0, $idinstitucion, $iddependencia, $fechaini, $fechafin, $hora_ini, $hora_fin, $tipos, $usuario['IDUSUARIO'], $modo);
		
		$this->json_output($data);
    }

    public function json_jurisdiccion(){
    	$ubigeo = @$_POST['ubigeo'];
		$institucion = (int)@$_POST['institucion'];
		$dependencia = (int)@$_POST['dependencia'];

		$usuario = $this->getUsuarioLogin();

		$data = array();
		$data['jurisdicciones'] = $this->m_institucion->_get_jurisdiccion($institucion, $dependencia, $ubigeo, $usuario['IDUSUARIO']);
		$this->json_output($data);
    }


}
