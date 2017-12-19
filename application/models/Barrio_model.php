<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Barrio_Model , entidad de la tabla "TB_POLIGONO_BARRIOSEGURO"
 */
class Barrio_Model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TB_POLIGONO_BARRIOSEGURO'); 

	}

	public function get_ByUbigeo($ubigeo = '', $idcomisaria = ''){
        $this->db->select('pj.IDCOMISARIA AS "ComisariaID", pj.LATITUD AS "BarrioLat", pj.LONGITUD AS "BarrioLong", c.IDUBIGEO AS "UbigeoID"');
        $this->db->from($this->model_name . ' pj'); 

        $this->db->join('SISGESPATMI.TM_COMISARIA c','pj.IDCOMISARIA=c.IDCOMISARIA', 'inner');
        $this->db->join('SISGESPATMI.TB_UBIGEO u','c.IDUBIGEO=u.IDUBIGEO', 'inner');

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $this->db->where('SUBSTR(u."IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\'');
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $this->db->where('SUBSTR("u"."IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
            }else{ //Distrito
                $this->db->where('u.IDUBIGEO', trim($ubigeo));
            }
        }

        if($idcomisaria!='' && $idcomisaria!='0'){
            $this->db->where('pj.IDCOMISARIA', trim($idcomisaria));
        }

        $this->db->order_by('pj.IDCOMISARIA ASC, "ORDEN" ASC');

        $query = $this->db->get(); 

         //print_r($this->db->last_query());
        if($query){
            return $query->result_array();
        }else{
            return array();
        }
	}


    public function _get_barrio($idinstitucion = 0, $iddependencia = 0, $idubigeo = '', $idusuario = 0){
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idinstitucion, "size" => 10);
        $variables[1] = array("parameter" => "p2", "value" => $iddependencia, "size" => 10);
        $variables[2] = array("parameter" => "p3", "value" => $idubigeo, "size" => 6);
        $variables[3] = array("parameter" => "p4", "value" => $idusuario, "size" => 10);
        $data = $this->_fn_exec("SISGESPATMI.UFN_BARRIO_GET(:p1, :p2, :p3, :p4)", $variables);
        if(@$data){
            return $data;
        }else{
            return array();
        }
    }
	
}