<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Reporte extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();
		$this->load->model('tracker_model', 'm_tracker');
		$this->load->model('tracker_radio_model', 'm_tracker_radio');
		$this->load->model('comisaria_model', 'm_comisaria');

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

		$this->load->model('rutasync_model', 'm_rutasync');

		$this->load->model('incidencia_model', 'm_incidencia');
		$this->load->model('camara_model', 'm_camara');
		$this->load->model('alarma_model', 'm_alarma');

		$this->load->model('reporte_model', 'm_reporte');

		session_write_close();
    }

    private function _validarFechaActual($fecha, $ini, $fin)
    {
    	$val = ($fecha==@date('d/m/Y') && ($ini == '00:00' && $fin == '23:59'));
    	return $val;
    }

	public function index()
	{
	
	}

	public function rpt_notrans(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];
		

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_notransmite', $data, array(), $parametroFooter);
	}

	public function rpt_consultar_notrans()
	{

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = array();
		$data['data'] = $this->m_reporte->get_notrans($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['RptVehiculos'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function rpt_consultar_notrans_xls()
	{

        $fechaini = @$_POST['fechaini'];
        $fechafin = @$_POST['fechafin'];
        $periodo = @$_POST['periodo'];
        $tipo = @$_POST['tipo'];
        $dependencia = @$_POST['dependencia'];

		$data = $this->m_reporte->get_notrans_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Vehículos que dejaron de transmitir")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PERIODO',  'FECHA INICIO', 'FECHA FIN' , 'TIEMPO', '#VECES', '#VEHÍCULOS', 'RATIO TIEMPO', 'RADIO VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_notransmite_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function rpt_notrans_det()
	{
		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];
		$data = array();
		$data['data'] = $this->m_reporte->get_notrans_det($dependencia, $nivel,$fecha ,$periodo);
		$this->json_output($data);
	}

	public function rpt_notrans_det_xls()
	{
    	$dependencia = @$_POST['dependencia'];
        $nivel = @$_POST['nivel'];
        $fecha = @$_POST['fecha'];
        $periodo = @$_POST['periodo'];


		$data = $this->m_reporte->get_notrans_det_xls($dependencia, $nivel,$fecha ,$periodo);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Patrullaje que dejaron de transmitir")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('DEPARTAMENTO', 'PROVINCIA',  'DISTRITO', 'COMISARIA' , 'RADIO', 'PLACA','TURNO', 'FECHA INICIO', 'FECHA FIN', 'TIEMPO', '#VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detalle');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_DetalleNoTrans_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	// CONTROLADOR REPORTE SALIO DE JURISDICCION


	public function rpt_salio(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_salio', $data, array(), $parametroFooter);
	}

	public function rpt_consultar_salio(){


		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = array();
		$data['data'] = $this->m_reporte->get_salio($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['RptVehiculos'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function rpt_consultar_salio_xls()
	{


		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = array();
		$data = $this->m_reporte->get_salio_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Vehículos que salierón de Jurisdicción")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PERIODO',  'FECHA INICIO', 'FECHA FIN' , 'TIEMPO','#VECES', '#VEHÍCULOS',  'RATIO TIEMPO', 'RATIO VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_SalioJurisdiccion_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function rpt_salio_det(){
		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];
		$data = array();
		$data['data'] = $this->m_reporte->get_salio_det($dependencia, $nivel,$fecha ,$periodo);
		$this->json_output($data);
	}

	public function rpt_salio_det_xls()
	{

		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];


		$data = $this->m_reporte->rpt_salio_det_xls($dependencia, $nivel,$fecha ,$periodo);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Vehículos que salierón de Jurisdicción")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('DEPARTAMENTO', 'PROVINCIA', 'DISTRITO', 'COMISARIA','RADIO', 'PLACA','TURNO' ,'FECHA INICIO', 'FECHA FIN' , 'TIEMPO','#VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detalle');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_DetalleSalieronJurisdiccion_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function rpt_KMxgalones(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_KmxGalones', $data, array(), $parametroFooter);
	}


	public function json_calcular_KmxGalones(){
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		// $ubigeo_ant = @$_POST['ubigeo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];


		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		if($ubigeo_ant!='' && $ubigeo_ant!='0' && $ubigeo_ant!=$ubigeo){
			$ubigeo = $ubigeo_ant;
		}

		$data = array();
		$data['data'] = $this->m_reporte->get_kmxgalones($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['Galones'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;
        // echo "asdasd";
		$this->json_output($data);
	}

    public function json_calcular_KmxGalones_xls(){

    	$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

	
		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		$data = $this->m_reporte->get_kmxgalones_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Galones x Kilómetros recorridos")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PERIODO', '#VEHÍCULO' ,  'KILÓMETROS', '#GALONES', 'RATIO');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte GalonesxKM');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_GalonesxKm_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
    } 


	public function json_kmxgalones_det(){
		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];

		$data = array();
		$data['data'] = $this->m_reporte->get_kmxgalones_det($dependencia, $nivel,$fecha ,$periodo);
		$this->json_output($data);
	}

	public function json_kmxgalones_det_xls(){
		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}


		$data = $this->m_reporte->get_kmxgalones_det_xls($dependencia, $nivel,$fecha ,$periodo);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte Detalle Galones x KM Recorridos")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('MACROREGIÓN', 'REGIÓN POLICIAL', 'DIVISIÓN TERRITORIAL' ,  'COMISARIA', 'RADIO', 'PLACA', 'KILÓMETROS', 'GALONES', 'PROVEEDOR');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detalle');
		$this->phpexcel->setActiveSheetIndex(0);
	
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_DetalleGalonesxKM' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
    } 


	public function rpt_detuvo(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_detuvo', $data, array(), $parametroFooter);
	}

	public function json_consultar_detuvo()
	{

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];



		$data = array();
		$data['data'] = $this->m_reporte->get_detuvo($periodo, $fechaini, $fechafin, $tipo, $dependencia);


		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['RptVehiculos'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function json_consultar_detuvo_xls()
	{
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = $this->m_reporte->get_detuvo_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte de Patrullaje Detenido")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PERIODO',  'FECHA INICIO', 'FECHA FIN' , 'TIEMPO','#VECES', 'VEHÍCULO',  'RATIO TIEMPO', 'RATIO VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detención de Patrullaje');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_detuvo_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}


	public function json_consultar_detuvo_det(){

		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];

		$data = array();
		$data['data'] = $this->m_reporte->get_detuvo_det($dependencia, $nivel, $fecha, $periodo);

		// $data['data'] = $this->m_reporte->get_detuvo_det($ubigeo, $comisaria,$fecha,$periodo);

		$this->json_output($data);
	}


	public function json_consultar_detuvo_det_xls(){

		$dependencia = @$_POST['dependencia'];
		$nivel = @$_POST['nivel'];
		$fecha = @$_POST['fecha'];
		$periodo = @$_POST['periodo'];

		$data = $this->m_reporte->get_detuvo_det_xls($dependencia, $nivel ,$fecha,$periodo);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Reporte Detalle de Patrullaje Detenido")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('MACROREGIÓN', 'REGIÓN POLICIAL',  'DIVISIÓN POLICIAL', 'DEPENDENCIA' , 'RADIO', 'PLACA', 'TURNO', 'FECHA INICIO', 'FECHA FIN' , 'TIEMPO','#VECES');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detalle');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_DetuvoDetalle_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function rpt_sesiones(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_sesiones', $data, array(), $parametroFooter);
	}

	public function json_consultar_sesiones(){

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = array();
		$data['data'] = $this->m_reporte->get_sesiones($periodo, $fechaini, $fechafin, $tipo, $dependencia);

		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['RptCantidad'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function xls_reporte_sesiones(){
		
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];


		$data = $this->m_reporte->get_sesiones_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Sesiones")
		                       ->setCategory("Usuarios");

       	$lst = array();
        
		$lst[] = array('MACROREGIÓN', 'REGIÓN POLICIAL', 'DIVISIÓN POLICIAL', 
								'COMISARIA', 'PERIODO', 'NOMBRE', 'USUARIO', 
								'ACCESO MÓVIL', 'IP',
								'INICIO', 'FIN');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Sesiones');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_sesiones_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}




	public function rpt_inventario(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_inventario', $data, array(), $parametroFooter);
	}

	public function json_consultar_inventario(){

		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = array();
		$data['data'] = $this->m_reporte->get_inventario($tipo, $dependencia);

        $categorias      = array();
        $categorias_temp = array();
        $series          = array();
        $series_temp     = array();

        $localidades  = array();
        $cantidades = array();

        foreach ($data['data'] as $k => $v) {
        	$categorias_temp[$v['RptLocalidad']] = $v['RptLocalidad'];
            $cantidades[$v['RptLocalidad']]['Patrulleros'] = $v['RptPatrullero'];
            $cantidades[$v['RptLocalidad']]['Motorizados'] = $v['RptMotorizado'];
            $cantidades[$v['RptLocalidad']]['Patrullaje a Pie'] = $v['RptPatpie'];
            $cantidades[$v['RptLocalidad']]['Patrullaje Integrado'] = $v['RptPatintegrado'];
            $cantidades[$v['RptLocalidad']]['Radio Base'] = $v['RptBase'];
        }

        foreach ($categorias_temp as $vCategoria) {
        	$categorias[] = $vCategoria;
        }

        foreach ($cantidades as $kCantidad => $vCantidad) {
            foreach ($vCantidad as $kItem => $vItem) {
            	$series_temp[$kItem][] = (int)$vItem;
            }
        }

        foreach ($series_temp as $kSerie => $vSerie) {
        	$series[] = array('name'=>$kSerie, 'data'=>$vSerie);
        }


        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function xls_consultar_inventario(){
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = $this->m_reporte->get_inventario_xls($tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Inventario")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PATRULLERO', 'MOTORIZADO', 'PAT.PIE', 'PAT.INTEGRADO', 'BASE', 'TOTAL');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Inventario');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_inventario_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');

	}
	public function json_consultar_inventario_det(){
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];
		$data = array();
		$data['data'] = $this->m_reporte->get_inventario_det($tipo, $dependencia);
		$this->json_output($data);
	}

	public function xls_inventario_det(){
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = $this->m_reporte->get_inventario_det_xls($tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Inventario")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('MACROREGIÓN', 'REGIÓN POLICIAL', 'DIVISIÓN POLICIAL', 
								'COMISARIA', 'RADIO', 'PLACA', 
								'SERIE', 'PROVEEDOR', 'TIPO','ESTADO');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Inventario');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_inventario_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}


	public function rpt_transmisiones(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_transmisiones', $data, array(), $parametroFooter);
	}

	public function json_consultar_transmisiones_det(){
		$fecha = @$_POST['fecha'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];
		$data = array();

		$data['data'] = $this->m_reporte->get_transmisiones_det($tipo, $dependencia, $fecha);
		$this->json_output($data);
	}

	public function xls_transmisiones_det(){
		$fecha = @$_POST['fecha'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];

		$data = $this->m_reporte->get_transmisiones_det_xls($fecha, $tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Transmisiones")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('MACROREGIÓN', 'REGIÓN POLICIAL', 'DIVISIÓN POLICIAL', 'COMISARIA',
								'RADIO', 'PLACA', 'TIPO','ESTADO');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Transmisiones');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_transmisiones_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');

	}
	public function json_consultar_transmisiones(){

		$fechaini = @$_POST['fechaini'];
        $tipo = @$_POST['tipo'];
        $dependencia = @$_POST['dependencia'];

		
		$data = array();
		$data['data'] = $this->m_reporte->get_transmisiones($fechaini, $tipo, $dependencia);

        $categorias      = array();
        $categorias_temp = array();
        $series          = array();
        $series_temp     = array();

        $localidades  = array();
        $cantidades = array();

        foreach ($data['data'] as $k => $v) {
        	$categorias_temp[$v['RptLocalidad']] = $v['RptLocalidad'];
            $cantidades[$v['RptLocalidad']]['Patrulleros'] = $v['RptPatrullero'];
            $cantidades[$v['RptLocalidad']]['Motorizados'] = $v['RptMotorizado'];            
            $cantidades[$v['RptLocalidad']]['Patrullaje a Pie'] = $v['RptPatpie'];
            $cantidades[$v['RptLocalidad']]['Patrullaje Integrado'] = $v['RptPatintegrado'];
            $cantidades[$v['RptLocalidad']]['Radio Base'] = $v['RptBase'];

        }

        foreach ($categorias_temp as $vCategoria) {
        	$categorias[] = $vCategoria;
        }

        foreach ($cantidades as $kCantidad => $vCantidad) {
            foreach ($vCantidad as $kItem => $vItem) {
            	$series_temp[$kItem][] = (int)$vItem;
            }
        }

        foreach ($series_temp as $kSerie => $vSerie) {
        	$series[] = array('name'=>$kSerie, 'data'=>$vSerie);
        }


        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);
	}

	public function xls_transmisiones(){

		$fecha = @$_POST['fecha'];
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];
		$data = $this->m_reporte->get_transmisiones_xls($fecha, $tipo, $dependencia);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Transmisiones")
		                       ->setCategory("Radios");

       	$lst = array();
        
		$lst[] = array('UBICACIÓN', 'PATRULLERO', 'MOTORIZADO', 
								'PATRULLAJE A PIE', 'PATRULLAJE INTEGRADO', 'RADIO BASE', 'TOTAL');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Transmisiones');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_inventario_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}


// ---------------------------------------------


	public function rpt_distancia(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_distancia', $data, array(), $parametroFooter);
	}

	public function json_consultar_distancias(){

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$ubigeo_ant = @$_POST['ubigeo'];
		$comisaria = @$_POST['comisaria'];

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		if($ubigeo_ant!='' && $ubigeo_ant!='0' && $ubigeo_ant!=$ubigeo){
			$ubigeo = $ubigeo_ant;
		}

		$data = array();
		$data['data'] = $this->m_reporte->get_distancia($periodo, $fechaini, $fechafin, $ubigeo, $comisaria);

		$categorias      = array();
        $series          = array();
        $series_temp     = array();

        $fechas  = array();
        $estados = array();

        foreach ($data['data'] as $k => $v) {
            $fechas[$v['RptPeriodo']]   = $v['RptPeriodo'];
            $estados[$v['RptLocalidad']] = $v['RptLocalidad'];
        }
        foreach ($fechas as $vFecha) {
            $categorias[] = $vFecha;
            foreach ($estados as $vEstado) {
                $series_temp[$vEstado][$vFecha] = 0;
            }
        }
        foreach ($data['data'] as $k => $v) {
            $series_temp[$v['RptLocalidad']][$v['RptPeriodo']] = $v['RptDistancia'];
        }
        $pos = 0;
        foreach ($series_temp as $kEstado => $vEstado) {
            $series[$pos]['name'] = $kEstado;
            foreach ($vEstado as $kFecha => $vFecha) {
                $series[$pos]['data'][] = (int) $vFecha;
            }
            $pos++;
        }

        $data['categorias'] = $categorias;
        $data['series']     = $series;

		$this->json_output($data);

		// $data = array();
		// $data['data'] = array();
		// echo json_encode($data);
	}

	public function rpt_conexiones(){
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		// print_r($data);

		// Array ( [rolcomisario] => 1 [usu_comisaria] => [usu_ubigeo] => )

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/highcharts/highcharts.js',
                'assets/js/highcharts/highcharts.exporting.js'
            ),
        );

		$this->sys_render('admin/sipcop_rpt_conexiones', $data, array(), $parametroFooter);
	}

	private function _get_rango_fechas($first, $last, $step = '+1 day', $output_format = 'd/m/Y' ) {

	    $dates = array();
	    $current = strtotime($first);
	    $last = strtotime($last);

	    while( $current <= $last ) {

	        $dates[] = date($output_format, $current);
	        $current = strtotime($step, $current);
	    }

	    return $dates;
	}

	public function json_consultar_conexiones(){

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$dependencia = @$_POST['dependencia'];
		$tipo = @$_POST['tipo'];

		$fechaini_arr = explode('/', $fechaini);
		$fechafin_arr = explode('/', $fechafin);
		$fechaini2 = $fechaini_arr[2].'-'.$fechaini_arr[1].'-'.$fechaini_arr[0];
		$fechafin2 = $fechafin_arr[2].'-'.$fechafin_arr[1].'-'.$fechafin_arr[0];

		$fechas = $this->_get_rango_fechas($fechaini2, $fechafin2);

		$data = array();
		$conjunto = array();
		$conexiones = $this->m_reporte->get_conexiones(1, $fechaini, $fechafin, $tipo, $dependencia);

		foreach ($conexiones as $oConexion) {
			$localidad = @$oConexion['UbigeoID'].'_'.@$oConexion['ComisariaID'].'_'.@$oConexion['RptLocalidad'];
			if(!isset($conjunto[$localidad])){
				foreach ($fechas as $sFecha) {
					$sFecha_arr = explode('/', $sFecha);
					$sFecha2 = 'Rpt_'.$sFecha_arr[2].''.$sFecha_arr[1].''.$sFecha_arr[0];
					$conjunto[$localidad]['DependenciaNivel'] = $oConexion['DependenciaNivel'];
					$conjunto[$localidad]['DependenciaID'] = $oConexion['DependenciaID'];
					$conjunto[$localidad]['RptLocalidad'] = $oConexion['RptLocalidad'];
					$conjunto[$localidad][$sFecha2] = 0;
				}
			}
			if($oConexion['RptPeriodo']!=''){
				$sFecha_arr = explode('/', $oConexion['RptPeriodo']);
				$sFecha2 = 'Rpt_'.$sFecha_arr[2].''.$sFecha_arr[1].''.$sFecha_arr[0];
				$conjunto[$localidad][$sFecha2] += (int)$oConexion['RptCantidad'];
			}
		}

		$data_final = array();
		foreach ($conjunto as $key => $value) {
			$data_final[] = $value;
		}

		$data['data'] = $data_final;
		$this->json_output($data);
	}

	public function xls_reporte_conexiones(){
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$dependencia = @$_POST['dependencia'];
		$tipo_ubigeo = @$_POST['tipo'];



		$fechaini_arr = explode('/', $fechaini);
		$fechafin_arr = explode('/', $fechafin);
		$fechaini2 = $fechaini_arr[2].'-'.$fechaini_arr[1].'-'.$fechaini_arr[0];
		$fechafin2 = $fechafin_arr[2].'-'.$fechafin_arr[1].'-'.$fechafin_arr[0];

		$fechas = $this->_get_rango_fechas($fechaini2, $fechafin2);
	
		$data = array();
		$conjunto = array();
		$conexiones = $this->m_reporte->get_conexiones_xls(1, $fechaini, $fechafin, $tipo_ubigeo, $dependencia);

		$sFechaActual = @date('Ymd');

		foreach ($conexiones as $oConexion) {
			$localidad = @$oConexion['MACREG'].'_'.@$oConexion['REGPOL'].'_'.@$oConexion['DIVTER'].'_'.@$oConexion['COMISARIA'];
			if(!isset($conjunto[$localidad])){
				foreach ($fechas as $sFecha) {

					$sFecha2_arr = explode('/', $sFecha);
					$sFecha2 = (int)($sFecha2_arr[2].''.$sFecha2_arr[1].''.$sFecha2_arr[0]);

					if($tipo_ubigeo>=0){
						$conjunto[$localidad]['MACREG'] = $oConexion['MACREG'];
					}
					if($tipo_ubigeo>=1){
						$conjunto[$localidad]['REGPOL'] = $oConexion['REGPOL'];
					}
					if($tipo_ubigeo>=2){
						$conjunto[$localidad]['DIVTER'] = $oConexion['DIVTER'];
					}
					if($tipo_ubigeo>=3){
						$conjunto[$localidad]['COMISARIA'] = $oConexion['COMISARIA'];
					}
					
					if($sFecha2<=$sFechaActual){
						$conjunto[$localidad][$sFecha] ='0';
					}else{
						$conjunto[$localidad][$sFecha] ='-';
					}
				}
			}
			if($oConexion['RptPeriodo']!=''){
				$sFecha2 = $oConexion['RptPeriodo'];
				if((int)$oConexion['CANTIDAD']>0){
					$conjunto[$localidad][$sFecha2] = (int)$conjunto[$localidad][$sFecha2]+(int)$oConexion['CANTIDAD'];
				}
			}
		}

		$data = array();
		foreach ($conjunto as $key => $value) {
			$data[] = $value;
		}

		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Conexiones")
		                       ->setCategory("Usuarios");

       	$lst = array();
        
		$lst[0] = array();
		if($tipo_ubigeo>=0){
			$lst[0][] = 'MACREG';
		}
		if($tipo_ubigeo>=1){
			$lst[0][] = 'REGPOL';
		}
		if($tipo_ubigeo>=2){
			$lst[0][] = 'DIVTER';
		}
		if($tipo_ubigeo>=3){
			$lst[0][] = 'COMISARÍA';
		}
		
		foreach ($fechas as $sFecha) {
			$lst[0][] = $sFecha;
		}

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Conexiones');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_conexiones_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	public function json_consultar_conexiones_det(){
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$periodo = @$_POST['periodo'];
		$dependencia = @$_POST['dependencia'];
		$tipo = @$_POST['tipo'];

		$data = array();
		$data['data'] = $this->m_reporte->get_conexiones_det($tipo, $dependencia,$fechaini,$fechafin);
		$this->json_output($data);
	}

	public function xls_conexiones_det(){
		$tipo = @$_POST['tipo'];
		$dependencia = @$_POST['dependencia'];
		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$data = array();
		$data= $this->m_reporte->get_conexiones_det($tipo,$dependencia,$fechaini,$fechafin);

		//$this->json_output($data);
		
		$this->load->library('PHPExcel/PHPExcel.php');
		$this->phpexcel->getProperties()->setCreator('Ministerio del Interior')
		                       ->setLastModifiedBy('Ministerio del Interior')
		                       ->setTitle("Detalle de Conexiones")
		                       ->setCategory("Transmisiones");

       	$lst = array();
        
		$lst[] = array('MACREG', 'REGPOL', 'DIVTER', 
								'COMISARIA', 'USUARIO', 'NOMBRE', 
								'APELLIDO', 'F. INICIO', 'F. FIN', 'ACTIVO', 'ACTIVIDAD');

		$lst = array_merge($lst,$data);
		foreach (range('A', 'Z') as $columnID) {
		    $this->phpexcel->getActiveSheet()->getColumnDimension($columnID)
		                   ->setAutoSize(true);
		}
		$this->phpexcel->setActiveSheetIndex(0)->fromArray($lst, null, 'A1');
		$this->phpexcel->getActiveSheet()->setTitle('Reporte Detalle de Conexiones');


		$this->phpexcel->setActiveSheetIndex(0);
	

		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="rpt_inventario_' . @date('YmdHis') . '.xlsx"');
		header('Cache-Control: max-age=0');
		$objWriter = PHPExcel_IOFactory::createWriter($this->phpexcel, 'Excel2007');
		$objWriter->save('php://output');
	}

	
	// -------------------------------REPORTE-------------------------------------------------------

	public function rpt_notrans_bi(){
		$this->sys_render('admin/sipcop_notrans_bi');
	}
	public function rpt_KMxgalones_bi(){
		$this->sys_render('admin/sipcop_rpt_KmxGalones_bi');
	}

	public function rpt_transmisiones_bi(){
		$this->sys_render('admin/sipcop_rpt_transmisiones_bi');
	}

	public function rpt_conexiones_bi(){
		$this->sys_render('admin/sipcop_rpt_conexiones_bi');
	}

}

