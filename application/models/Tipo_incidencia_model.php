<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Usuario_Model , entidad de la tabla "usuario"
 */
class Tipo_incidencia_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISTESTIGOMI.TIPO', 'IDTIPO');
    }


    function get_tipo(){
      $sql = 'SELECT IDTIPO as "TipoID",NOMBRE as "TipoNombre",IMAGEN as "TipoImagen",ORDEN as "TipoOrden", IDPADRE as "TipoPadre", FLGACTIVO as "TipoEstado" FROM SISTESTIGOMI.TIPO ORDER BY ORDEN, NOMBRE';
      $query = $this->db->query($sql);
      return $query->result_array();    
    }




}

