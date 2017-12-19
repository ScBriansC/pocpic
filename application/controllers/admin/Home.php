<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Home extends Sys_Controller {

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

		$this->load->model('vehiculo_model','m_vehiculo');


		session_write_close();
    }

    private function _validarFechaActual($fecha, $ini, $fin)
    {
    	$val = ($fecha==@date('d/m/Y') && ($ini == '00:00' && $fin == '23:59'));
    	return $val;
    }


    public function index()
	{

		$usuario = $this->getUsuarioLogin();
		

		$data = array();
		$data['comisaria_dependencia'] = $this->m_comisaria_dependencia->_all('*');
		$data['comisaria_zona'] = $this->m_comisaria_zona->_all('*');
		$data['comisaria_division'] = $this->m_comisaria_division->_all('*');
		$data['comisaria_clase'] = $this->m_comisaria_clase->_all('*');
		$data['comisaria_tipo'] = $this->m_comisaria_tipo->_all('*');
		$data['comisaria_categoria'] = $this->m_comisaria_categoria->_all('*');
		$data['alarma_tipos'] = $this->m_alarma_tipo->getAlarmatipo();

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
            $this->sys_render('admin/sipcop_home', $data, $data_header, $parametroFooter);
        }

	}

	// public function index()
	// {
	// 	$data = array();
	// 	$data['comisaria_dependencia'] = $this->m_comisaria_dependencia->_all('*');
	// 	$data['comisaria_zona'] = $this->m_comisaria_zona->_all('*');
	// 	$data['comisaria_division'] = $this->m_comisaria_division->_all('*');
	// 	$data['comisaria_clase'] = $this->m_comisaria_clase->_all('*');
	// 	$data['comisaria_tipo'] = $this->m_comisaria_tipo->_all('*');
	// 	$data['comisaria_categoria'] = $this->m_comisaria_categoria->_all('*');

	// 	$usuario = $this->getUsuarioLogin();
	// 	$data['usu_jurisdiccion'] = $this->m_institucion->_get_jurisdiccion_usuario($usuario['IDUSUARIO']);

	// 	if($usuario['IDROL'] == 5){
 //            redirect('/admin/home/sesusu', 'refresh');
 //        }else{
 //            $data_header = array('wrapper_class'=>'wrapper_mapa');
 //            $this->sys_render('admin/sipcop_home', $data, $data_header);
 //        }

	// }

	public function json_checkpassword(){
		$claveAnt = @$_POST['claveAnt'];
		$claveNew = @$_POST['claveNew'];
		$result = array();
		$data = array();
		$usuario = $this->getUsuarioLogin();
		$data['data'] = $this->m_usuario->check_byId($usuario['IDUSUARIO'],$claveAnt,$claveNew);

		if($data['data'] == 1){
			if($this->_registrarTokenSMS($usuario['IDUSUARIO'],$usuario['CELULAR'])){
				$result['status'] = 'confirm';
				$result['msg'] = 'confirmar Token';
			}else{
				$result['status'] = 'error';
				$result['msg'] = 'Ocurrió un error al generar código';

			}
		}else{
			$result['msg'] = 'Clave Incorrecta';
		}
		$result['CELULAR'] = $usuario['CELULAR'];
		$result['data'] = $data['data'];
		$result['ant'] = md5($claveAnt);
		$this->json_output($result);
	}

	public function json_updatePassword(){
		$token = @$_POST['token'];
		$claveAnt = @$_POST['claveAnt'];
		$claveNew = @$_POST['claveNew'];
		$data = array();

		$usuario = $this->getUsuarioLogin();

		$tokensms = $this->m_tokensms->get_TokenActivo($usuario['IDUSUARIO'], $token);
		if($tokensms)
		{
			$data['data'] = $this->m_usuario->updatePassword_Byid($usuario['IDUSUARIO'],$claveAnt,$claveNew);
			
		}
		else{
			$data['data'] = 0;
		}

		$this->json_output($data);

	}

	public function json_patrullaje(){
		$fecha = @$_POST['fecha'];
		$horaini = @$_POST['horaini'];
		$horafin = @$_POST['horafin'];

		$tipo_filtro = (int)@$_POST['tipo_filtro'];

		$dispogps = (int)@$_POST['dispogps'];

		$dependencia = (int)@$_POST['dependencia'];
		$institucion = (int)@$_POST['institucion'];
		$descripcion = strtoupper(@$_POST['descripcion']);
		$placa = strtoupper(@$_POST['placa']);
		$idradio = strtoupper(@$_POST['idradio']);
		$serie = strtoupper(@$_POST['serie']);

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$institucion_ = @$usuario['IDINSTITUCION'];		
			$institucion = isset($institucion)?$institucion:$institucion_;
		}

		$tipo_patrullaje = array();
		if(@$_POST['flgpatrullero'] == 'true'){
			$tipo_patrullaje[] = 'patrullero';
		}
		if(@$_POST['flgmotorizado'] == 'true'){
			$tipo_patrullaje[] = 'motorizado';
		}
		if(@$_POST['flgpatpie'] == 'true'){
			$tipo_patrullaje[] = 'patpie';
		}
		if(@$_POST['flgbarrioseg'] == 'true'){
			$tipo_patrullaje[] = 'barrioseg';
		}
		if(@$_POST['flgpuestofijo'] == 'true'){
			$tipo_patrullaje[] = 'puestofijo';
		}

		//$comisaria = $this->m_institucion->get_byIDFecha($institucion,$fecha, $horaini, $horafin);

		$data = array();
		$data['comisaria'] = @$comisaria;

		$data['patrullaje'] = $this->m_tracker->_get_gps($fecha, $horaini, $horafin, $dispogps, $dependencia, $institucion, $tipo_patrullaje, $descripcion, $placa, $idradio, $serie, $usuario['IDUSUARIO']);


		if(($dispogps > 0)  && ($tipo_filtro == 2 || $tipo_filtro == 3)){
			$data['ruta'] = $this->m_tracker->_get_dispogps_ruta($dispogps, $fecha, $horaini, $horafin);
		}

		$this->json_output($data);
	}

	public function json_resumen(){
		$fecha = @$_POST['fecha'];
		$dependencia = (int)@$_POST['dependencia'];
		$institucion = (int)@$_POST['institucion'];
		$hora_ini = @$_POST['horaini'];
		$hora_fin = @$_POST['horafin'];

		$usuario = $this->getUsuarioLogin();

		$data = array();
		$resumen_total = $this->m_tracker->_get_resumen_total($dependencia, $institucion, $usuario['IDUSUARIO']);
		$data['resumen_total'] = $resumen_total;

		$resumen_turno = $this->m_tracker->_get_resumen_fecha($fecha,$hora_ini,$hora_fin, $dependencia, $institucion, $usuario['IDUSUARIO']);
		$data['resumen_turno'] = $resumen_turno;

		$this->json_output($data);
	}

	public function json_comisaria(){
		$nombre = strtoupper(@$_POST['nombre']);
		$iddependencia = (int)@$_POST['dependencia'];
		$idtipodepen = (int)@$_POST['idtipodepen'];
		$idzona = (int)@$_POST['zona'];
		$iddivision = (int)@$_POST['division'];
		$idclase = (int)@$_POST['clase'];
		$idtipo = (int)@$_POST['tipo'];
		$idcategoria = (int)@$_POST['categoria'];
		$ubigeo = @$_POST['ubigeo'];

		$fecha = @$_POST['fecha'];
		$hora_ini = @$_POST['horaini'];
		$hora_fin = @$_POST['horafin'];



		$data = array();

		$usuario = $this->getUsuarioLogin();

		$data['comisarias'] = $this->m_institucion->_get_consulta(0, $iddependencia, $fecha, $hora_ini, $hora_fin, $nombre, $ubigeo, $idtipodepen, $idclase, $idtipo, $idcategoria, $usuario['IDUSUARIO']);
		
		$this->json_output($data);
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

	public function json_info_dispogps(){
		$dispogps = (int)@$_POST['dispogps'];
		$fecha = @$_POST['fecha'];
		$hora_ini = @$_POST['horaini'];
		$hora_fin = @$_POST['horafin'];
		$info = $this->m_tracker->_get_dispogps_info($dispogps, $fecha, $hora_ini, $hora_fin);
		$this->json_output($info);
	}

	public function json_info_comisaria(){
		$institucion = (int)@$_POST['institucion'];
		$infocomi_resul = $this->m_institucion->_get_consulta($institucion);
		$this->json_output($infocomi_resul[0]);

	}

    public function json_denuncia(){
    	$iddependencia = (int)@$_POST['dependencia'];
		$idinstitucion = (int)@$_POST['idinstitucion'];

		$fechaini = @$_POST['fechaini'];
		$fechafin = @$_POST['fechafin'];
		$hora_ini = @$_POST['horaini'];
		$hora_fin = @$_POST['horafin'];



		$usuario = $this->getUsuarioLogin();

		$tipos = '';

		$data = array();
		$ini = date("d/m/Y", strtotime( date( "d/m/Y", strtotime( date("d/m/Y") ) ) . "-1 month" ) );
		$fin = @date('t/m/Y');
		$data['denuncias'] = $this->m_denuncia->_get_consulta(0, $idinstitucion, $iddependencia, $ini, $fin, '00:00', '23:59', $tipos, $usuario['IDUSUARIO'], 1);
		//$data['denuncias'] = $this->m_denuncia->_get_consulta(0, $idinstitucion, $iddependencia, $fechaini, $fechafin, $hora_ini, $hora_fin, $tipos, $usuario['IDUSUARIO'], 1);
		$data['ini'] = $ini;
		$data['fin'] = $fin;
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

    public function json_barrio(){
    	$ubigeo = @$_POST['ubigeo'];
		$institucion = (int)@$_POST['institucion'];
		$dependencia = (int)@$_POST['dependencia'];

		$usuario = $this->getUsuarioLogin();

		$data = array();
		$data['barrio'] = $this->m_barrio->_get_barrio($institucion, $dependencia, $ubigeo, $usuario['IDUSUARIO']);
		$this->json_output($data);
    }

    public function json_region(){
    	$ubigeo_ant = @$_POST['ubigeo'];
		$ubigeo = '';

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
			//$idcomisaria = $usuario['IDCOMISARIA'];		
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		if($ubigeo_ant!='' && $ubigeo_ant!='0' && $ubigeo_ant!=$ubigeo){
			$ubigeo = $ubigeo_ant;
		}

		$data = array();
		$data['regiones'] = $this->m_region->get_ByUbigeo($ubigeo);
		$this->json_output($data);
    }


    public function test(){
    	print_r($this->_esDispositivoMovil());
    	echo "\n";
    	print_r($this->_getDispositivo());
    }

    public function v_StreetView(){
    	$coordenadas = explode(',',$_GET['latlng']);
    	$data = array();
    	$data['coords']['lat'] = $coordenadas[0];
    	$data['coords']['lng'] = $coordenadas[1];
    	$this->load->view('admin/streetview', $data);
    }

    public function json_incidencia(){
    	$iddependencia = @$_POST['dependencia'];
		$idinstitucion = @$_POST['institucion'];
		$ultimo = @$_POST['ultimo'];
		$fecha = @$_POST['fecha'];
		$notificar = @$_POST['notificar'];
		$horaini = @$_POST['horaini'];
		$horafin = @$_POST['horafin'];


		$data = array();
		$idusuario = $this->getUsuarioLogin();		
		if($notificar=='false' || ($notificar=='true' && ($ultimo == '0' || !$ultimo))){
			$data['ultimo'] = $this->m_incidencia->get_MaxID();
			if($notificar=='true' && ($ultimo == '0' || !$ultimo)){
				$ultimo = $data['ultimo'];
			}
		}

		$data['incidencias'] = $this->m_incidencia->_get_lista($fecha, $horaini , $horafin , $iddependencia, $idinstitucion, $idusuario['IDUSUARIO'], $ultimo);

		$this->json_output($data);
    }


    public function json_camara(){
    	$dependencia = @$_POST['dependencia'];
		$institucion = @$_POST['institucion'];
		$usuario = $this->getUsuarioLogin();
		$data = array();
		$data['camaras'] = $this->m_camara->_get_lista($dependencia, $institucion, $usuario['IDUSUARIO']);
		$this->json_output($data);
    }


    public function json_alarma(){
    	$dependencia = @$_POST['dependencia'];
		$institucion = @$_POST['institucion'];
		$notificar = @$_POST['notificar'];

		$usuario = $this->getUsuarioLogin();

		$data = array();
		$data['alarmas'] = $this->m_alarma->_get_lista($dependencia, $institucion, $usuario['IDUSUARIO'], ($notificar=='true')?1:0);
		$this->json_output($data);
    }

    public function geoserver(){
    	//$ruta = 'http://172.22.0.80:8080/geoserver/GISDesa/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=GISDesa:ZARATE&maxFeatures=50&outputFormat=application%2Fjson';
    	$ruta='http://172.22.0.80:8080/geoserver/GISDesa/ows?service=WFS&version=1.0.0&request=GetFeature&typeName=GISDesa:Probando&maxFeatures=50&outputFormat=application%2Fjson';
    	echo file_get_contents($ruta);
    }

    public function sp_test(){
    	print_r($this->m_institucion->get_Combo(3,1795,20)); //1758
    }

    
    public function sesusu(){
        $data = array();

        $usuario = $this->getUsuarioLogin();
        $data['rolcomisario'] = $usuario['IDROL'];
        $data['usu_rol'] = $usuario['IDROL'];

        if(($data['usu_rol'] == 1 || $data['usu_rol'] == 5)){
            $data_footer = array(
                'jslib' => array(
                    'assets/js/advanced-datatable/js/jquery.dataTables.js',
                    'assets/js/data-tables/DT_bootstrap.js',
                    'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                    'assets/js/highcharts/highcharts.js',
                    'assets/js/highcharts/highcharts.exporting.js'
                ),
            );
            $data_header = array('wrapper_class'=>'wrapper_sesusu');
            $this->sys_render('admin/sipcop_sesusu', $data, $data_header, $data_footer);

        }
        
    }

    public function json_sesusu(){

        $data = array();

        $usuario = $this->getUsuarioLogin();
        $usu_rol = $usuario['IDROL'];

        if(($usu_rol == 1 || $usu_rol == 5)){

            $data['data'] = $this->m_sesion->getActivos();

        }
        $this->json_output($data);
    }

    public function forzar_cierre(){

        $data = array();

        $usuario = $this->getUsuarioLogin();
        $usu_rol = $usuario['IDROL'];

        $sesion = @$_REQUEST['sesion'];

        $data['status'] = 'error';
        $data['msj'] = 'No permitido';

        if(($usu_rol == 1 || $usu_rol == 5) && $sesion > 0){

            if($this->m_sesion->finalizar($sesion)){
                $data['status'] = 'success';
                $data['msj'] = 'Se forzó el cierre de la sesión';
            }else{
                $data['status'] = 'error';
                $data['msj'] = 'Error al forzar cierre de sesión';
            }

        }
        $this->json_output($data);
    }


    public function saliocomision(){

		// $dispogps = @$_POST['dispogps'];
		$data = array();
		$data['idgps'] = @$_POST['dispogps'];
		// $data['alarmas'] = $this->m_alarma->_get_lista($dependencia, $institucion, $usuario['IDUSUARIO'], ($notificar=='true')?1:0);
		// $this->json_output($data);
		echo @$_POST['dispogps'];

    }


	public function volvioComision(){


	}

	public function json_modelvehplaca(){
		$modelo = @$_POST['modelo'];
		$institucion = @$_POST['institucion'];

		$usuario = $this->getUsuarioLogin();

		$data = array();
		$data['placas'] = $this->m_vehiculo->get_placas($modelo, $institucion);
		$this->json_output($data);

	}




}
