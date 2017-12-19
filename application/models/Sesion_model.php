<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Sesion_Model , entidad de la tabla "TH_SESION"
 */
class Sesion_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISGESPATMI.TH_SESION', 'IDSESION');
    }

    function get_SesionActiva($idusuario){
    	$this->db->select('IDSESION, IDUSUARIO, IDTOKENSMS, FLGACTIVO, TO_CHAR(FECHAREG, \'YYYY-MM-DD HH24:MI:SS\') AS FECHAREG, TO_CHAR(FECHAFIN, \'YYYY-MM-DD HH24:MI:SS\') AS FECHAFIN, IPMAQREG');
        $this->db->from($this->model_name); 
        $this->db->where('IDUSUARIO',$idusuario);
        $this->db->where('FLGACTIVO','1');
        $this->db->where('SISGESPATMI.FN_FECHA_ACTUAL(NULL) BETWEEN "FECHAREG" AND "FECHAFIN"');
        $this->db->order_by('IDSESION', 'desc');

        $query = $this->db->get();
        return $query->row_array();
    }

    function get_NuevoID(){
        $q = $this->db->query('SELECT SISGESPATMI.USEQ_THSESION.NEXTVAL as IDSESION from dual');
        $id = $q->row_array();
        return (int)@$id['IDSESION'];
    }

    function add_Sesion($idusuario, $idtokensms, $ip, $vigencia, $flgmovil, $dispositivo){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDSESION', $id);
            $this->db->set('IDUSUARIO', $idusuario);
            if($idtokensms > 0){
                $this->db->set('IDTOKENSMS', $idtokensms);
            }
            $this->db->set('FLGMOVREG', $flgmovil);
            $this->db->set('DISPOREG', $dispositivo);
            $this->db->set('FLGACTIVO', 1);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('FECHAFIN',"(SYSDATE + interval '".$vigencia."' second)",false);
            $this->db->set('IPMAQREG', $ip);
            if($this->db->insert($this->model_name)){
                return $id;
            }
            //print_r($this->db->last_query());
        }
        return FALSE;
    }

    function finalizar($idsesion, $flgactivo = 2, $ip = '', $flgmovil = 0, $dispositivo = ''){
        if($idsesion > 0){
            $this->db->set('IPMAQFIN', $ip);
            $this->db->set('FLGMOVFIN', $flgmovil);
            $this->db->set('DISPOFIN', $dispositivo);
            $this->db->set('FLGACTIVO', $flgactivo);
            $this->db->set('FECHAFIN',"(SYSDATE)",false);
            $this->db->where('IDSESION', $idsesion);
            if($this->db->update($this->model_name)){
                return $idsesion;
            }
        }
        return FALSE;
    }

    
    function getActivos(){
        $q = $this->db->query('SELECT 
            U.IDUSUARIO as "UsuarioID",
            S.IDSESION as "SesionID",
            U.USUARIOCOD as "UsuarioCodigo",
            U.NOMBRE as "UsuarioNombre",
            U.APELLIDO as "UsuarioApellido",
            C.NOMBRE as "ComisariaNombre",
            TO_CHAR(S.FECHAREG, \'yyyy-mm-dd hh24:mi:ss\') as "SesionFecha",
            S.IPMAQREG as "SesionIP"
            FROM SISGESPATMI.TM_USUARIO U
            INNER JOIN SISGESPATMI.TH_SESION S ON S.IDUSUARIO = U.IDUSUARIO
            LEFT JOIN SISGESPATMI.TM_COMISARIA C ON C.IDCOMISARIA = U.IDCOMISARIA
            WHERE S.FLGACTIVO = 1');
        return $q->result_array();
    }


    function _get_duracion($idusuario)
    {
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_SESION_TIEMPO_GET(:p1)", $variables);
        return $data[0];   
    }
}