<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Camara_Model , entidad de la tabla "TM_CAMARA"
 */
class Camara_Model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TM_CAMARA', 'IDCAMARA'); 

	}

	public function _get_lista($iddependencia = 0, $idinstitucion = 0, $idusuario = 0){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $iddependencia, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $idinstitucion, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_CAMARA_GET(:p1, :p2, :p3)", $variables);
        return $data;  
	}
}