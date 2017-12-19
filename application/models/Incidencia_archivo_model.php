<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Comisaria_clase_Model , entidad de la tabla "comisaria_clase"
 */
class Incidencia_archivo_model extends Base_model {
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISTESTIGOMI.ARCHIVO', 'IDARCHIVO');
    }

    function get_archivoById($id){

      $sql ='SELECT  
            A.IDARCHIVO as "ArchivoId",
            A.RUTA as "ArchivoNombre",
            A.IDINCIDENCIA as "IdIncidencia",
            A.RUTA as "ArchivoRuta",
            TA.EXTENSION as "ArchivoTipo",
            A.IDUSUREG as "IdUsuario"
            FROM SISTESTIGOMI.ARCHIVO A
            INNER JOIN SISTESTIGOMI.TIPO_ARCHIVO TA ON A.TIPO = TA.IDTIPOARCHIVO        
      		  WHERE A.FLGACTIVO= 1 AND TA.FLGACTIVO=1
       		  AND A.IDINCIDENCIA='.$id.'';
            // echo $sql;
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }

    function delete_ArchivoById($id){
      if($id > 0){
            $this->db->set('FLGACTIVO', 0);
            $this->db->where('IDARCHIVO', $id);
            if($this->db->update($this->model_name)){
                return $id;
            }
 
      }
      return FALSE;
    }


    
}