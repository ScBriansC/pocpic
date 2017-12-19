<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Usuario_Model , entidad de la tabla "usuario"
 */
class Usuario_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISGESPATMI.TM_USUARIO', 'IDUSUARIO');
    }

    function get_byLogin($codigo, $clave, $columns = null, $order_by = null, $order_asc = null)
    {
        $this->setDefault(compact('columns', 'order_by', 'order_asc'));

        $this->db->select($this->columns);
        $this->db->where('USUARIOCOD', strtoupper(trim($codigo)));
        $this->db->where('CLAVE', md5(trim($clave)));

        return $this->db->get($this->model_name)->row_array();   
    }

    function get_byApiLogin($codigo, $clave, $columns = null, $order_by = null, $order_asc = null)
    {
        $this->setDefault(compact('columns', 'order_by', 'order_asc'));

        $this->db->select($this->columns);
        $this->db->where('USUARIOCOD', strtoupper(trim($codigo)));
        $this->db->where('CLAVE', (trim($clave)));

        return $this->db->get($this->model_name)->row_array();   
    }

    function check_byId($id,$claveAnt,$claveNew){
        $NclaveAnt =md5($claveAnt);
        $NclaveNew = md5($claveNew);

        if($id > 0){
            $this->db->select('CLAVE');
            $this->db->where('IDUSUARIO',$id);
            // $this->db->where('CLAVE', (trim($clave)));
            $claveantigua = $this->db->get($this->model_name)->row_array();
        }

        $data = array();

        if($claveantigua['CLAVE']==$NclaveAnt){
            return $data['data'] = '1';
        }else{
            return $data['data'] = '2';
        }

    }

    function updatePassword_Byid($idusuario,$claveAnt,$claveNew){
        $NclaveAnt = md5($claveAnt);
        $NclaveNew = md5($claveNew);

        if($idusuario > 0){
            $this->db->set('CLAVE', $NclaveNew);
            $this->db->set('FECHAMOD',"(SYSDATE)",false);
            $this->db->where('IDUSUARIO', $idusuario);
            $this->db->where('CLAVE', $NclaveAnt);
            if($this->db->update($this->model_name)){
                return $idusuario;
            }
        }
        return FALSE;


    }

    function _get_usuario_info($idusuario = 0)
    {
        
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_USUARIO_GET(:p1)", $variables);
        return $data[0];  

    }
}

