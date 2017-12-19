<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Usuario_Model , entidad de la tabla "usuario"
 */
class Usuarioapp_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISTESTIGOMI.USUAPP', 'IDUSUARIO');
    }

    function get_NuevoID(){
        $q = $this->db->query('SELECT SISTESTIGOMI.USEQ_USUARIOAPP_IDUSUARIOPP.NEXTVAL as IDUSUAPP from dual');
        $id = $q->row_array();
        return (int)@$id['IDUSUAPP'];
    }

    function addUsuarioApp($nrodoc,$correo,$nombres,$apellidos,$fechanac,$alias,$clave,$celular,$sexo,$fecha,$idusuario){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDUSUAPP',$id);
            $this->db->set('NRODOC', $nrodoc);
            $this->db->set('CORREO', $correo);
            $this->db->set('NOMBRES', $nombres);
            $this->db->set('APELLIDOS', $apellidos);
            $this->db->set('FECHANAC', "TO_DATE('".$fechanac."','YYYY-MM-DD HH:MI:SS')",false);
            $this->db->set('ALIAS', $alias);
            $this->db->set('CLAVE', $clave);
            $this->db->set('CELULAR', $celular);
            $this->db->set('SEXO', $sexo);
            $this->db->set('FLGACTIVO',1);
            $this->db->set('IDUSUREG', null);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            if($this->db->insert($this->model_name)){
                return $id;
            }
             // print_r($this->db->last_query());
         
        }
        return FALSE;
    }

    function get_byEmail($email){

      $sql = 'SELECT * from SISTESTIGOMI.USUAPP where correo = \''.$email.'\' and  FLGACTIVO = 1';
      $query = $this->db->query($sql);
      $rows = $query->result_array();
      return $rows[0]; 

    }




}

