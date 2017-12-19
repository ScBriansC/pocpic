<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Ubigeo_Model , entidad de la tabla "ubigeo"
 */
class Ubigeo_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TB_UBIGEO', 'IDUBIGEO');
	}

	public function get_Departamentos($codigo = ''){

        $this->db->select('IDUBIGEO, DEPARTAMENTO');
        $this->db->from($this->model_name); 
        $this->db->where('"IDUBIGEO" like \'%0000\'');

        if($codigo!=''){
            $this->db->where('"IDUBIGEO" like \''.substr($codigo,0,2).'0000\'');
        }

        $this->db->order_by('DEPARTAMENTO', 'asc');

        $query = $this->db->get();

        if($query){
            return $query->result_array();
        }else{
            return array();
        }
	}

	public function get_Provincias($ubigeo = ''){

        	$this->db->select('DISTINCT (SUBSTR("IDUBIGEO",0,4)||\'00\') as "IDUBIGEO", "PROVINCIA"');
        	$this->db->from($this->model_name); 
        	$this->db->where('"IDUBIGEO" like \''.substr($ubigeo,0,2).'%\'');

            if($ubigeo!='' && $ubigeo!=substr($ubigeo,0,2).'0000'){
                $this->db->where('"IDUBIGEO" like \''.substr($ubigeo,0,4).'%\'');
            }

        	$this->db->where('"IDUBIGEO" not like \'%0000\'');
			$this->db->order_by('PROVINCIA', 'asc');

        	$query = $this->db->get();
        	if($query){
	            return $query->result_array();
	        }else{
	            return array();
	        }
	}

    public function get_Distritos($ubigeo = ''){

        if($ubigeo!='' && $ubigeo!='0'){
            $this->db->select('IDUBIGEO, DISTRITO');
            $this->db->from($this->model_name); 
            $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');

            if($ubigeo!='' && $ubigeo!=substr($ubigeo,0,4).'00'){
                $this->db->where('"IDUBIGEO" like \''.$ubigeo.'%\'');
            }

            $this->db->where('"IDUBIGEO" not like \'%0000\' AND "IDUBIGEO" not like \'%00\'');
            $this->db->order_by('DISTRITO', 'asc');

            $query = $this->db->get();

            if($query){
                return $query->result_array();
            }else{
                return array();
            }
        }else{
            return array();
        }
    }

    public function get_Combo($tipo, $ubigeo = ''){

        if($tipo == 0){
            $this->db->select('"IDUBIGEO" as "UbigeoCodigo", DEPARTAMENTO as "UbigeoNombre"');
            $this->db->from($this->model_name); 
            $this->db->where('"IDUBIGEO" like \'%0000\'');
            if($codigo!=''){
                $this->db->where('"IDUBIGEO" like \''.substr($codigo,0,2).'0000\'');
            }
            $this->db->order_by('DEPARTAMENTO', 'asc');
        }

        if($tipo == 1){
            if($ubigeo != '' && $ubigeo != '0'){
                $this->db->select('DISTINCT (SUBSTR("IDUBIGEO",0,4)||\'00\') as "UbigeoCodigo", "PROVINCIA" as "UbigeoNombre"');
                $this->db->from($this->model_name); 
                $this->db->where('"IDUBIGEO" like \''.substr($ubigeo,0,2).'%\'');
                if($ubigeo!='' && $ubigeo!=substr($ubigeo,0,2).'0000'){
                    $this->db->where('"IDUBIGEO" like \''.substr($ubigeo,0,4).'%\'');
                }
                $this->db->where('"IDUBIGEO" not like \'%0000\'');
                $this->db->order_by('PROVINCIA', 'asc');
            }else{
                return array();
            }
        }

        if($tipo == 2){
            if($ubigeo != '' && $ubigeo != '0'){
                $this->db->select('"IDUBIGEO" as "UbigeoCodigo", DISTRITO as "UbigeoNombre"');
                $this->db->from($this->model_name); 
                $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
                if($ubigeo!='' && $ubigeo!=substr($ubigeo,0,4).'00'){
                    $this->db->where('"IDUBIGEO" like \''.$ubigeo.'%\'');
                }
                $this->db->where('"IDUBIGEO" not like \'%0000\' AND "IDUBIGEO" not like \'%00\'');
                $this->db->order_by('DISTRITO', 'asc');
            }else{
                return array();
            }
        }

        $query = $this->db->get();
        //print_r($this->db->last_query());
        if($query){
            return $query->result_array();
        }else{
            return array();
        }
    }

  
	
}