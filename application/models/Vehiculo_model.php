<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "vehiculo"
 */
class Vehiculo_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TM_VEHICULO', 'IDVEHICULO');
	}
	
	public function like($data) {
		$this->db->limit(20);
		$this->db->like($data);
		return $this->db->get($this->model_name)->result_array();
	}

	public function get_placas($modelo, $institucion){


        $sql ='SELECT IDDISPOGPS,PLACA FROM SISGESPATMI.TM_DISPOGPS
         		WHERE IDINSTITUCION ='.$institucion.'
         		AND IDPATRULLAJE IN ('.$modelo.')';
       	// echo $sql;
        $query = $this->db->query($sql);
        return $query->result_array();
	}
	
}