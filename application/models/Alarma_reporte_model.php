<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Camara_Model , entidad de la tabla "TM_CAMARA"
 */
class Alarma_reporte_model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TM_ALARMA_REPORTE', 'IDREPORTE'); 

	}

    function get_NuevoID(){
        $q = $this->db->query('SELECT SISGESPATMI.USEQ_TMALARMAREPORTE_IDREPORTE.NEXTVAL as IDREPORTE from dual');
        $id = $q->row_array();
        return (int)@$id['IDREPORTE'];
    }


    function addEncendido($idalarma,$lat,$lon,$fecha,$flag){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDREPORTE',$id);
            $this->db->set('IDALARMA', $idalarma);
            $this->db->set('LATITUD', $lat);
            $this->db->set('LONGITUD', $lon);
            $this->db->set('FECHAINI', "TO_DATE('".$fecha."','YYYY-MM-DD HH24:MI:SS')",false);
            $this->db->set('FLGENC',1);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('FLGACTIVO',1);
            if($this->db->insert($this->model_name)){
                return $id;
            }
             // print_r($this->db->last_query());
        }
        return FALSE;
    }


    function setApagado($idalarma,$lat,$lon,$tipo,$motivo,$detalle,$fecha,$flag){
        if($idalarma > 0){
            $this->db->set('LATITUD', $lat);
            $this->db->set('LONGITUD',$lon);
            $this->db->set('TIPO', $tipo);
            $this->db->set('MOTIVO', $motivo);
            $this->db->set('DETALLE', $detalle);
            $this->db->set('FECHAFIN', "TO_DATE('".$fecha."','YYYY-MM-DD HH24:MI:SS')",false);
            $this->db->set('FECHAMOD', "(SYSDATE)",false);
            $this->db->set('FLGENC', 2);
            $this->db->where('IDALARMA', $idalarma);
            $this->db->where('FLGENC', $flag);
            if($this->db->update($this->model_name)){
                return $idalarma;
            }
        }
        return FALSE;

    }









    // function addReporte($idalarma,$tipo,$descripcion,$latitud,$longitud,$fecha,$idusuario){
    //     $id = $this->get_NuevoID();
    //     if($id > 0){
    //         $this->db->set('IDREPORTE',$id);
    //         $this->db->set('IDALARMA', $idalarma);
    //         $this->db->set('TIPO', $tipo);
    //         $this->db->set('DESCRIPCION', $descripcion);
    //         $this->db->set('LATITUD', $latitud);
    //         $this->db->set('LONGITUD', $longitud);
    //         $this->db->set('FECHA', "TO_DATE('".$fecha."','YYYY-MM-DD HH:MI:SS')",false);
    //         $this->db->set('FECHAREG',"(SYSDATE)",false);
    //         $this->db->set('IDUSUREG', $idusuario);
    //         $this->db->set('FLGACTIVO',1);
    //         if($this->db->insert($this->model_name)){
    //             return $id;
    //         }
    //         // print_r($this->db->last_query());
    //     }
    //     return FALSE;
    // }



}