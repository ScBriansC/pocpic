<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Toeknapi_Model , entidad de la tabla "TH_TOKENAPI"
 */
class Tokenapi_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_TOKENAPI', 'IDTOKENAPI');
    }

    function get_TokenActivo($idusuario, $token = ''){
    	$this->db->select('"IDTOKENAPI", "IDUSUARIO", "TOKEN"');
        $this->db->from($this->model_name); 
        $this->db->where('IDUSUARIO',$idusuario);
        $this->db->where('"SISGESPATMI".FN_FECHA_ACTUAL(NULL) BETWEEN "FECHAREG" AND "FECHAFIN"');
        $this->db->where('TOKEN',trim($token));
        $this->db->order_by('IDTOKENAPI', 'desc');

        $query = $this->db->get();
        //print_r($this->db->last_query());
        return $query->row_array();
    }

    function get_NuevoID(){
    	$q = $this->db->query('SELECT "SISGESPATMI".USEQ_THTOKENAPI.NEXTVAL as IDTOKENAPI from dual');
    	$id = $q->row_array();
		return (int)@$id['IDTOKENAPI'];
    }

    function add_Token($idusuario, $token, $ip, $vigencia,$movil='', $dispositivo=''){
    	$id = $this->get_NuevoID();
    	if($id > 0){
    		$this->db->set('IDTOKENAPI', $id);
			$this->db->set('IDUSUARIO', $idusuario);
			$this->db->set('TOKEN', $token);
			$this->db->set('IPMAQREG', $ip);
			$this->db->set('FECHAREG',"(SYSDATE)",false);
			$this->db->set('FECHAFIN',"(SYSDATE + interval '".$vigencia."' second)",false);
            $this->db->set('FLGMOVIL', $movil);
            $this->db->set('DISPOSITIVO', $dispositivo);
			if($this->db->insert($this->model_name)){
				return $id;
			}
			//print_r($this->db->last_query());
    	}
    	return FALSE;
    }

    function end_Token($token){
    	if($idtokensms > 0){
            $this->db->set('FECHAFIN',"(SYSDATE)",false);
			$this->db->where('IDTOKENAPI', $idtokensms);
			if($this->db->update($this->model_name)){
				return $idtokensms;
			}
    	}
    	return FALSE;
    }

    function get_Hash(){
        $hex_string = "0123456789ABCDEF";
        $hash = "";
        for($i=0; $i<10; $i++) {
            $hash .= $hex_string{rand(0,strlen($hex_string)-1)};
        }

        return $hash.'_'.uniqid();
    }
}