<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Camara_Model , entidad de la tabla "TM_CAMARA"
 */
class Modulo_model extends Base_model {
	
	function __construct() {
         parent::__construct('SISGESPATMI.TH_ROL_MODULO', 'IDROL'); 

	}

    public function _get_lista($idusuario = 0, $idmodulo = 0, $ruta = ''){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idusuario, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $idmodulo, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $ruta, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_MODULO_GET(:p1,:p2,:p3)", $variables);
        return $data;  
    }


}