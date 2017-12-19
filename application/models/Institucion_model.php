<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Ubigeo_Model , entidad de la tabla "ubigeo"
 */
class Institucion_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TM_INSTITUCION', 'IDINSTITUCION');
	}

    public function get_Combo($tipo, $padre = 0, $idusuario = 0, $tipoinst = '0'){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $tipo, "size" => 10);
        $variables[1] = array("parameter" => "p2", "value" => $padre, "size" => 10); //'2017-09-13 13:00:00'
        $variables[2] = array("parameter" => "p3", "value" => $idusuario, "size" => 10);
        $variables[3] = array("parameter" => "p4", "value" => $tipoinst, "size" => 250);
        $data = $this->_fn_exec("SISGESPATMI.UFN_DEP_LISTA_GET(:p1, :p2, :p3, :p4)", $variables);
        if(@$data[0]){
            return $data;
        }else{
            return array();
        }
    }

    public function _get_consulta($idinstitucion, $iddependencia = 0, $fecha = null, $hora_ini = null, $hora_fin = null, $nombre = '', $ubigeo = '', $idtipodepen = 0, $idclase = 0, $idtipocomi = 0, $idcategoria = 0, $idusuario = 0){

        if($fecha!=null && $fecha!=''){
            $fecha_arr = explode('/',$fecha);
            $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
            $fechaini = $fechanum.' 00:00:00';
            $fechafin = $fechanum.' 23:59:59';

            if($hora_ini!='' && $hora_fin!='' && $hora_ini!=null && $hora_fin!=null){
                $fechaini = $fechanum.' '.$hora_ini.':00';
                $fechafin = $fechanum.' '.$hora_fin.':59';
            }
        }


        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idinstitucion, "size" => 10);
        $variables[1] = array("parameter" => "p2", "value" => $iddependencia, "size" => 10);
        $variables[2] = array("parameter" => "p3", "value" => $fechaini, "size" => 19);
        $variables[3] = array("parameter" => "p4", "value" => $fechafin, "size" => 19);
        $variables[4] = array("parameter" => "p5", "value" => $nombre, "size" => 100);
        $variables[5] = array("parameter" => "p6", "value" => $ubigeo, "size" => 6);
        $variables[6] = array("parameter" => "p7", "value" => $idtipodepen, "size" => 10);
        $variables[7] = array("parameter" => "p8", "value" => $idclase, "size" => 10);
        $variables[8] = array("parameter" => "p9", "value" => $idtipocomi, "size" => 10);
        $variables[9] = array("parameter" => "p10", "value" => $idcategoria, "size" => 10);
        $variables[10] = array("parameter" => "p11", "value" => $idusuario, "size" => 10);
        $data = $this->_fn_exec("SISGESPATMI.UFN_COMISARIA_GET(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11)", $variables);
        if(@$data[0]){
            return $data;
        }else{
            return array();
        }
    }

    public function _get_jurisdiccion($idinstitucion = 0, $iddependencia = 0, $idubigeo = '', $idusuario = 0){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idinstitucion, "size" => 10);
        $variables[1] = array("parameter" => "p2", "value" => $iddependencia, "size" => 10);
        $variables[2] = array("parameter" => "p3", "value" => $idubigeo, "size" => 6);
        $variables[3] = array("parameter" => "p4", "value" => $idusuario, "size" => 10);
        $data = $this->_fn_exec("SISGESPATMI.UFN_JURISDICCION_GET(:p1, :p2, :p3, :p4)", $variables);
        if(@$data){
            return $data;
        }else{
            return array();
        }
    }

    public function _get_jurisdiccion_usuario($idusuario = 0){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idusuario, "size" => 10);
        $data = $this->_fn_exec("SISGESPATMI.UFN_JURISDICCION_USUARIO_GET(:p1)", $variables);
        if(@$data){
            return $data;
        }else{
            return array();
        }
    }

  
	
}