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


    function addHojaruta($placa,$operador,$chofer,$fecha,$idinstitucion){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDHOJARUTA',$id);
            $this->db->set('IDINSTITUCION', $idinstitucion);
            $this->db->set('PLACA', $placa);
            $this->db->set('OPERADOR', $operador);
            $this->db->set('CHOFER', $chofer);
            $this->db->set('FECHA', "TO_DATE('".$fecha."','DD-MM-YYYY')",false);
            $this->db->set('FLGACTIVO',1);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            if($this->db->insert($this->model_name)){
                return $id;
            }
            // print_r($this->db->last_query());
        }
        return FALSE;
    }

    function addRuta($idhojaruta,$latitud,$longitud,$direccion,$motivo,$hora,$orden,$fecha){
        $this->db->set('IDHOJARUTA',$idhojaruta);
        $this->db->set('LATITUD', $latitud);
        $this->db->set('LONGITUD', $longitud);
        $this->db->set('DIRECCION', $direccion);
        $this->db->set('MOTIVO', $motivo);
        $this->db->set('HORA', "TO_DATE('".$fecha." ".$hora.":00','DD-MM-YYYY HH24:MI:SS')",false);
        $this->db->set('ORDEN', $orden);
        $this->db->set('FLGACTIVO',1);
        if($this->db->insert('SISGESPATMI.TH_RUTAS')){
            return $idhojaruta;
        }

        return FALSE;
        // print_r($this->db->last_query());
    }

    function get_hojaruta($fecha){
        $sql = 'SELECT IDHOJARUTA as "idhojaruta" ,PLACA as "placa",FECHA as "fecha",CHOFER as "chofer",OPERADOR as "operador" FROM SISGESPATMI.TM_HOJARUTA WHERE FLGACTIVO=1 AND FECHA = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\')';
        $query = $this->db->query($sql);
        return $query->result_array();   

    }

}