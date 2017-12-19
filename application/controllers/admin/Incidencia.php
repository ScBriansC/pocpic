<?php
defined('BASEPATH') OR exit('No direct script access allowed');

ini_set('display_errors', 1);
ini_set('memory_limit', '-1');
set_time_limit(0);

class Incidencia extends Sys_Controller {

	function __construct()
    {
    	parent::__construct();
    	$this->load->model('newincidencia_model', 'm_incidencia');	

		$this->load->model('incidencia_tipo_model', 'm_incidencia_tipo');
		$this->load->model('incidencia_estado_model', 'm_incidencia_estado');
		$this->load->model('Incidencia_archivo_model', 'm_incidencia_archivo');
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

		$data['incidencia_tipo'] = $this->m_incidencia_tipo->_all('*');
		$data['incidencia_estado'] = $this->m_incidencia_estado->_all('*');

		$usuario = $this->getUsuarioLogin();

		$data['rolcomisario'] = $usuario['IDROL'];
		$data['usu_comisaria'] = $usuario['IDCOMISARIA'];
		$data['usu_ubigeo'] = $usuario['GRUPO'];

		$parametroFooter = array(
            'jslib' => array(
                'assets/js/advanced-datatable/js/jquery.dataTables.js',
                'assets/js/data-tables/DT_bootstrap.js',
                'assets/js/advanced-datatable/js/dataTables.fixedColumns.min.js',
                'assets/js/simpleUpload.js'
            ),
        );

		$this->sys_render('admin/sipcop_incidencia', $data, array(), $parametroFooter);
	}

	public function json_getById(){
		$id = @$_POST['id'];
		$data = array();
		$data['data'] = $this->m_incidencia->get_incidenciaById($id);
		$data['archivos'] = $this->m_incidencia_archivo->get_archivoById($id);

		$this->json_output($data);
	}

	public function json_deleteArchivoById(){
		$id = @$_POST['id'];
		$idincidencia = @$_POST['idincidencia'];
		$data = array();
		$data['data'] = $this->m_incidencia_archivo->delete_ArchivoById($id);
		$data['archivos'] = $this->m_incidencia_archivo->get_archivoById($idincidencia);

		$this->json_output($data);
	}

	public function json_incidencias(){

		$usuario = $this->getUsuarioLogin();
		if($usuario['IDROL'] == 3){
			$ubigeo = $usuario['GRUPO'];
		}elseif($usuario['IDROL'] == 4){
			$ubigeo = $usuario['GRUPO'];
		}

		$id_usuario = $usuario['IDUSUARIO'];


		$data = array();
		$data['data'] = $this->m_incidencia->get_incidencias();


		$this->json_output($data);
	}

	public function json_addIncidencia(){

		$titulo = @$_POST['titulo'];
		$detalle = @$_POST['detalle'];
		$idtipo = @$_POST['tipo'];
		$estado = @$_POST['estado'];
		$direccion = @$_POST['direccion'];
		$latitud = @$_POST['latitud'];
		$longitud = @$_POST['longitud'];
		$usuario = $this->getUsuarioLogin();
		$idusuario = $usuario['IDUSUARIO'];
		$archivos = @$_POST['archivos'];
		$ip = $this->_getIP();


		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';

	
		$addIncidencia = $this->m_incidencia->add_incidencias($titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq);
		// print_r($addIncidencia);
		if($addIncidencia > 0){
			if(is_array($archivos) && count($archivos)>0){
				foreach ($archivos as $archivo) {
					// print_r($archivo);
					$addArchivo = $this->m_incidencia->add_Archivos($addIncidencia,$idusuario,$archivo['ArchivoNombre'],$archivo['ArchivoTipo']);

				}				
			}7
			$result['status'] = 'success';
			$result['msj'] = 'Incidencia Registrada!';
			// $result['idreporte'] = $idreporte;
		}

		else{
			$result['status'] = 'error';
			$result['msj'] = 'No se pudo registrar la incidencia!';
		}

		echo json_encode($result);
		
	}

	public function json_updateIncidencia(){

		$id = @$_POST['id'];
		$titulo = @$_POST['titulo'];
		$detalle = @$_POST['detalle'];
		$idtipo = @$_POST['tipo'];
		$estado = @$_POST['estado'];
		$direccion = @$_POST['direccion'];
		$latitud = @$_POST['latitud'];
		$longitud = @$_POST['longitud'];
		$archivos = @$_POST['archivos'];

		$usuario = $this->getUsuarioLogin();
		$idusuario = $usuario['IDUSUARIO'];
		$ip = $this->_getIP();

		$result = array();
		$result['status'] = 'error';
		$result['msj'] = 'No se detectó datos';

	
		$updateIndicencia = $this->m_incidencia->update_indicendia($id,$titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq);
			
		if($archivos > 0)
		{
			if(is_array($archivos) && count($archivos)>0){
				foreach ($archivos as $archivo) {
					// print_r($archivo);
					$addArchivo = $this->m_incidencia->add_Archivos($updateIndicencia,$idusuario,$archivo['ArchivoNombre'],$archivo['ArchivoTipo']);

				}				
			}
			$result['status'] = 'success';
			$result['msj'] = 'Incidencia Actualizada!';
		}


		if($updateIndicencia > 0){
			$result['status'] = 'success';
			$result['msj'] = 'Incidencia Actualizada!';
			// $result['idreporte'] = $idreporte;
		}else{
			$result['status'] = 'error';
			$result['msj'] = 'No se pudo Actualizar la incidencia!';
		}

		echo json_encode($result);
	}

    public function subir_archivo()
    {
        @ini_set("file_uploads", "On");
        $response = array();

        if (isset($_FILES["fArchivo"])) {

            $directorio = "archivos/soytestigo/";
            $archivo    = @time() . '_' . abs(rand(9999, 999999999999)) . '.' . pathinfo(basename($_FILES["fArchivo"]["name"]), PATHINFO_EXTENSION);


            if (move_uploaded_file($_FILES["fArchivo"]["tmp_name"], $directorio . $archivo)) {
                $response['status']       = 'success';
                $response['msj']          = 'Archivo subido';
                $response['archivo']      = $archivo;
                $response['ruta_archivo'] = $directorio . $archivo;
            } else {
                $response['status'] = 'error';
                $response['msj']    = 'Error al subir archivo';
            }
        } else {
            $response['status'] = 'error';
            $response['msj']    = 'No ha seleccionado un archivo';
        }
        echo json_encode($response);
    }




}

