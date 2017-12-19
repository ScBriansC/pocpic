<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Ruta extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();
		$this->load->model('tracker_model', 'm_tracker');
		$this->load->model('comisaria_model', 'm_comisaria');
		$this->load->model('institucion_model', 'm_institucion');

		$this->load->model('comisaria_dependencia_model', 'm_comisaria_dependencia');
		$this->load->model('comisaria_zona_model', 'm_comisaria_zona');
		$this->load->model('comisaria_division_model', 'm_comisaria_division');
		$this->load->model('comisaria_clase_model', 'm_comisaria_clase');
		$this->load->model('comisaria_tipo_model', 'm_comisaria_tipo');
		$this->load->model('comisaria_categoria_model', 'm_comisaria_categoria');

		$this->load->model('ubigeo_model', 'm_ubigeo');

		$this->load->model('denuncia_model', 'm_denuncia');
		$this->load->model('jurisdiccion_model', 'm_jurisdiccion');
		$this->load->model('region_model', 'm_region');

		$this->load->model('incidencia_model', 'm_incidencia');
		$this->load->model('camara_model', 'm_camara');
		$this->load->model('alarma_model', 'm_alarma');
		$this->load->model('alarma_tipo_model','m_alarma_tipo');

		$this->load->model('usuario_model', 'm_usuario');
		$this->load->model('tokensms_model', 'm_tokensms');
		$this->load->model('barrio_model', 'm_barrio');
		$this->load->model('hojaruta_model','m_hojaruta');


		session_write_close();
    }

	public function index(){
		
		$usuario = $this->getUsuarioLogin();
		$data = array();
		$data['usu_jurisdiccion'] = $this->m_institucion->_get_jurisdiccion_usuario($usuario['IDUSUARIO']);


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

		if($usuario['IDROL'] == 5){
            redirect('/admin/home/sesusu', 'refresh');
        }else{
            $data_header = array('wrapper_class'=>'wrapper_mapa');
            $this->sys_render('admin/sipcop_hojaruta', $data, $data_header, $parametroFooter);
        }

	}

	public function json_save(){
		
		// $placa = @$_POST(objDatos['placa']);
		$placa =  @$_POST([objDatos]['placa']);
		$operador = @$_POST['operador'];
		$chofer = @$_POST['chofer'];
		$fecha = @$_POST['fecha'];
		$idinstitucion = @$_POST['idinstitucion'];

		// $result= array();

		// $result['status'] = 'error';
		// $result['msj'] = 'No se detectó datos';

		// $addhojaruta = $this->m_hojaruta->saveHojaruta($placa,$operador,$chofer,$fecha,$idinstitucion);
		// // $data['data'] = $this->m_hojaruta->saveHojaruta($placa,$operador,$chofer,$fecha,$idinstitucion);

		// if($addhojaruta > 0){
		// 	$result['status'] = 'success';
		// 	$result['msj'] = 'Incidencia Registrada!';
		// }else{
		// 	$result['status'] = 'error';
		// 	$result['msj'] = 'No se pudo registrar la incidencia!';
		// }

		// $addIncidencia = $this->m_incidencia->add_incidencias($titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq);
		// // print_r($addIncidencia);
		// if($addIncidencia > 0){
		// 	if(is_array($archivos) && count($archivos)>0){
		// 		foreach ($archivos as $archivo) {
		// 			// print_r($archivo);
		// 			$addArchivo = $this->m_incidencia->add_Archivos($addIncidencia,$idusuario,$archivo['ArchivoNombre'],$archivo['ArchivoTipo']);

		// 		}				
		// 	}
		// 	$result['status'] = 'success';
		// 	$result['msj'] = 'Incidencia Registrada!';
		// 	// $result['idreporte'] = $idreporte;
		// }

		// else{
		// 	$result['status'] = 'error';
		// 	$result['msj'] = 'No se pudo registrar la incidencia!';
		// }

		echo json_encode($placa);





	}

	// public function json_comisaria(){
	// 	$ubigeo = @$_POST['ubigeo'];
	// 	$data = array();
	// 	$data['data'] = $this->m_comisaria->get_Comisarias($ubigeo);
	// 	$this->json_output($data);
	// }

	// public function json_dependencia(){
	// 	$tipo = @$_POST['tipo'];
	// 	$padre = @$_POST['padre'];
	// 	$usuario = $this->getUsuarioLogin();
	// 	$tipoinst = @$_POST['tipoinst'];
	// 	$data = array();
	// 	$data['data'] = @$this->m_institucion->get_Combo($tipo, $padre,$usuario['IDUSUARIO'],$tipoinst);
	// 	$this->json_output($data);
	// }

}
