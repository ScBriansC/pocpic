<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "vehiculo"
 */
class Radio_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TM_RADIO', 'IDRADIO');
	}
	
	public function like($data) {
		$this->db->limit(20);
		$this->db->like($data);
		return $this->db->get($this->model_name)->result_array();
	}
	
	
}