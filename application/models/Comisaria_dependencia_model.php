<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Comisaria_dependencia_Model , entidad de la tabla "comisaria_dependencia"
 */
class Comisaria_dependencia_Model extends Base_model {
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISGESPATMI.TM_DEPENDENCIA', 'IDDEPENDENCIA');
    }
    
}