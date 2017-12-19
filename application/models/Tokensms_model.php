<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Tokensms_Model , entidad de la tabla "TH_TOKENSMS"
 */
class Tokensms_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_TOKENSMS', 'IDTOKENSMS');
    }

    function get_TokenActivo($idusuario, $token = ''){
    	$this->db->select('"IDTOKENSMS", "IDUSUARIO", "TOKEN", TO_CHAR(FECHAREG, \'YYYY-MM-DD HH24:MI:SS\') AS FECHAREG, 
							TO_CHAR(FECHAVIG, \'YYYY-MM-DD HH24:MI:SS\') AS FECHAVIG, "IPMAQREG",
							((FECHAVIG - "SISGESPATMI".FN_FECHA_ACTUAL(NULL))*60*60*24)  AS TIEMPO');
        $this->db->from($this->model_name); 
        $this->db->where('IDUSUARIO',$idusuario);
        $this->db->where('FLGUSO','0');
        $this->db->where('"SISGESPATMI".FN_FECHA_ACTUAL(NULL) BETWEEN "FECHAREG" AND "FECHAVIG"');
        if($token!=''){
        	$this->db->where('TOKEN',trim($token));
        }
        $this->db->order_by('IDTOKENSMS', 'desc');

        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->row_array();
    }

    function get_NuevoID(){
    	$q = $this->db->query('SELECT "SISGESPATMI".USEQ_THTOKENSMS.NEXTVAL as IDTOKENSMS from dual');
    	$id = $q->row_array();
		return (int)@$id['IDTOKENSMS'];
    }

    function add_TokenSMS($idusuario, $celular, $token, $ip, $vigencia){
    	$id = $this->get_NuevoID();
    	if($id > 0){
    		$this->db->set('IDTOKENSMS', $id);
			$this->db->set('IDUSUARIO', $idusuario);
			$this->db->set('CELULAR', $celular);
			$this->db->set('TOKEN', $token);
			$this->db->set('IPMAQREG', $ip);
			$this->db->set('FLGUSO', 0);
			$this->db->set('FECHAREG',"(SYSDATE)",false);
			$this->db->set('FECHAVIG',"(SYSDATE + interval '".$vigencia."' second)",false);
			if($this->db->insert($this->model_name)){
				return $id;
			}
			//print_r($this->db->last_query());
    	}
    	return FALSE;
    }

    function edit_TokenSMS($idtokensms){
    	if($idtokensms > 0){
			$this->db->set('FLGUSO', 1);
			$this->db->where('IDTOKENSMS', $idtokensms);
			if($this->db->update($this->model_name)){
				return $idtokensms;
			}
    	}
    	return FALSE;
    }
}