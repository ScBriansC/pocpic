<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Camara_Model , entidad de la tabla "TM_CAMARA"
 */
class Alarma_model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TM_ALARMA', 'IDALARMA'); 

	}

    public function _get_lista($iddependencia = 0, $idinstitucion = 0, $idusuario = 0, $encendido = 0){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $iddependencia, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $idinstitucion, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $idusuario, "size" => 100);
        $variables[3] = array("parameter" => "p4", "value" => $encendido, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_ALARMA_GET(:p1, :p2, :p3, :p4)", $variables);
        return $data;  
    }

    public function set_Encendido($idalarma, $flag){
        if($idalarma > 0){
            $this->db->set('FLGENC', $flag);
            $this->db->where('IDALARMA', $idalarma);
            if($this->db->update($this->model_name)){
                return $idalarma;
            }
        }
        return FALSE;
    }

}