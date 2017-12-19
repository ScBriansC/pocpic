<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Barrio_Model , entidad de la tabla "TB_POLIGONO_BARRIOSEGURO"
 */
class Hojaruta_model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TM_HOJARUTA'); 

	}
    function get_NuevoID(){
        $q = $this->db->query('SELECT SISGESPATMI.USEQ_TM_HOJARUTA.NEXTVAL as IDHOJARUTA from dual');
        $id = $q->row_array();
        return (int)@$id['IDHOJARUTA'];
    }


    function saveHojaruta($placa,$operador,$chofer,$fecha,$idinstitucion){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDHOJARUTA',$id);
            $this->db->set('PLACA', $placa);
            $this->db->set('OPERADOR', $operador);
            $this->db->set('CHOFER', $chofer);
            $this->db->set('FECHA', "TO_DATE('".$fecha."','YYYY-MM-DD')",false);
            $this->db->set('FLGACTIVO',1);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('FLGACTIVO',1);
            if($this->db->insert($this->model_name)){
                return $id;
            }
             // print_r($this->db->last_query());
        }
        return FALSE;
    }




	
}