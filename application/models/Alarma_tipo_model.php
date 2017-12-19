<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Camara_Model , entidad de la tabla "TM_CAMARA"
 */
class Alarma_tipo_model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TM_ALARMA_TIPO', 'IDALARMATIPO'); 

	}

    function getAlarmatipo(){

        $this->db->select('IDALARMATIPO, NOMBRE');
        $this->db->from($this->model_name); 
        $this->db->where('"FLGACTIVO" = 1');
        $this->db->order_by('IDALARMATIPO', 'asc');

        $query = $this->db->get();

        // print_r($this->db->last_query());
        if($query){
            return $query->result_array();
        }else{
            return array();
        }

    }
}