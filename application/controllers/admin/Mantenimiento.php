<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Mantenimiento extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();

		$this->load->model('institucion_model', 'm_institucion');
		$this->load->model('ubigeo_model', 'm_ubigeo');
		$this->load->model('dispogps_model', 'm_dispogps');


		session_write_close();
    }

    public function index()
	{

	}

    public function unidpol()
	{
		$usuario = $this->getUsuarioLogin();
		

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

		$data['obj_usuario'] = $this->getUsuarioLogin();

		$data['patrullaje'] = $this->m_dispogps->_get_patrullaje();
		$data['proveedores'] = $this->m_dispogps->_get_proveedores();
		$data['modelovh'] = $this->m_dispogps->_get_modelovh();
		$data['marcavh'] = $this->m_dispogps->_get_marcavh();
		$data['tipovh'] = $this->m_dispogps->_get_tipovh();

        $data_header = array('wrapper_class'=>'wrapper_mapa');
        $this->sys_render('admin/mantenimiento/sipcop_unidpol', $data, $data_header, $parametroFooter);


	}



	public function json_unidpol(){
		$dispogps = @$_POST['dispogps'];
		$institucion = @$_POST['institucion'];
		$dependencia = @$_POST['dependencia'];
		$placa = @$_POST['placa'];
		$usuario = $this->getUsuarioLogin();
		$id_usuario = $usuario['IDUSUARIO'];

		$data = array();
		$data['data'] = $this->m_dispogps->_get_consulta($dispogps,$placa,$dependencia,$institucion,$id_usuario);
		$this->json_output($data);
	}

	public function call_guardar_unidpol(){
		$dispogps = (int)@$_POST['dispogps'];
		$comisaria = @$_POST['comisaria'];
		$placa = @$_POST['placa'];
		$descripcion = @$_POST['descripcion'];
		$patrullaje = @$_POST['patrullaje'];
		$proveedor = @$_POST['proveedor'];
		$tipovh = @$_POST['tipovh'];
		$marcavh = @$_POST['marcavh'];
		$modelovh = @$_POST['modelovh'];
		$idradio = @$_POST['idradio'];
		$idotro = @$_POST['idotro'];
		$tipo = @$_POST['tipo'];
		$serie = @$_POST['serie'];
		$modelo = @$_POST['modelo'];
		$origen = @$_POST['origen'];
		$tei = @$_POST['tei'];
		$observacion = @$_POST['observacion'];
		$categoria = @$_POST['categoria'];
		$marca = @$_POST['marca'];
		$estado = @$_POST['estado'];
		$motivo = @$_POST['motivo'];

		$usuario = $this->getUsuarioLogin();
		$id_usuario = $usuario['IDUSUARIO'];

		$data = array();
		$data = $this->m_dispogps->_guardar($dispogps,$comisaria,$placa,$descripcion,$patrullaje,$proveedor,$modelovh,$idradio,$idotro,$tipo,$serie,$modelo,$origen,$tei,$observacion,$categoria,$marca,$estado,$motivo,$id_usuario);
		$this->json_output($data);
	}

}
