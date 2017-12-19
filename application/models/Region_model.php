<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "vehiculo"
 */
class Region_Model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TB_POLIGONO_DISTRITO'); 

	}

	public function get_ByUbigeo($ubigeo = ''){
        $this->db->select('"IDUBIGEO" AS "UbigeoID", "LATITUD" AS "RegionLat", "LONGITUD" AS "RegionLong"');
        $this->db->from($this->model_name); 

        if($ubigeo!='' && $ubigeo!='0'){
            $this->db->where('IDUBIGEO', trim($ubigeo));
        }

        $this->db->order_by('"IDUBIGEO" ASC, "ORDEN" ASC');

        $query = $this->db->get(); 

        if($query){
            return $query->result_array();
        }else{
            return array();
        }
	}
	
}