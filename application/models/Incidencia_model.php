<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Incidencia_Model , entidad de la tabla "TH_INCIDENCIA"
 */
class Incidencia_Model extends Base_model {
	
	function __construct() {
        parent::__construct('SISGESPATMI.TH_INCIDENCIA', 'IDINCIDENCIA'); 

	}
    
    function get_NuevoID(){
        $q = $this->db->query('SELECT SISGESPATMI.USEQ_THINCIDENCIA_IDINCIDENCIA.NEXTVAL as IDINCIDENCIA from dual');
        $id = $q->row_array();
        return (int)@$id['IDINCIDENCIA'];
    }
    
    function get_MaxID(){
        $q = $this->db->query('SELECT MAX(IDINCIDENCIA) as IDINCIDENCIA from '.$this->model_name .' WHERE FECHA IS NOT NULL');
        $id = $q->row_array();
        return (int)@$id['IDINCIDENCIA'];
    }

    
    // function _add_incidencia($idtipo,$idsubtipo,$tipo,$subtipo,$descripcion,$latitud,$longitud,$idinstitucion,$idubigeo,$fecha,$nombre,$apellido,$correo,$celular,$direccion)
    // {
    //     $id = $this->get_NuevoID();
    //     if($id > 0){

    //         $variables = array();
    //         $variables[0] = array("parameter" => "p1", "value" => $id, "size" => 100);
    //         $variables[1] = array("parameter" => "p2", "value" => $idtipo, "size" => 100);
    //         $variables[2] = array("parameter" => "p3", "value" => $idsubtipo, "size" => 100);
    //         $variables[3] = array("parameter" => "p4", "value" => $tipo, "size" => 100);
    //         $variables[4] = array("parameter" => "p5", "value" => $subtipo, "size" => 100);
    //         $variables[5] = array("parameter" => "p6", "value" => $descripcion, "size" => 100);
    //         $variables[6] = array("parameter" => "p7", "value" => $latitud, "size" => 100);
    //         $variables[7] = array("parameter" => "p8", "value" => $longitud, "size" => 100);
    //         $variables[8] = array("parameter" => "p9", "value" => $idinstitucion, "size" => 100);
    //         $variables[9] = array("parameter" => "p10", "value" => $idubigeo, "size" => 100);
    //         $variables[10] = array("parameter" => "p11", "value" => $fecha, "size" => 100);
    //         $variables[11] = array("parameter" => "p12", "value" => $nombre, "size" => 100);
    //         $variables[12] = array("parameter" => "p13", "value" => $apellido, "size" => 100);
    //         $variables[13] = array("parameter" => "p14", "value" => $correo, "size" => 100);
    //         $variables[14] = array("parameter" => "p15", "value" => $celular, "size" => 100);
    //         $variables[15] = array("parameter" => "p16", "value" => $direccion, "size" => 100);
            
    //         $data = $this->_fn_exec("SISGESPATMI.UFN_INCIDENCIA_GET(:p1, :p2, :p3,:p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11, :p12, :p13, :p14, :p15, :p16)", $variables);
    //         if($data){
    //             return $id;
    //         }

    //     }
    //     return FALSE;
    // }

    public function _get_lista($fecha, $horaini='' , $horafin='' , $iddependencia=0, $idinstitucion=0, $idusuario='', $ultimo)
    {

        $fechaini = null;
        $fechafin = null;

        if($fecha!=null && $fecha!=''){
            $fecha_arr = explode('/',$fecha);
            $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
            $fechaini = $fechanum.' 00:00:00';
            $fechafin = $fechanum.' 23:59:59';

            if($hora_ini!='' && $hora_fin!='' && $hora_ini!=null && $hora_fin!=null){
                $fechaini = $fechanum.' '.$hora_ini.':00';
                $fechafin = $fechanum.' '.$hora_fin.':59';
            }
        }

        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $iddependencia, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $idinstitucion, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $idusuario, "size" => 100);
        $variables[3] = array("parameter" => "p4", "value" => $fechaini, "size" => 100);
        $variables[4] = array("parameter" => "p5", "value" => $fechafin, "size" => 100);
        $variables[5] = array("parameter" => "p6", "value" => $ultimo, "size" => 100);
        
        $data = $this->_fn_exec("SISGESPATMI.UFN_INCIDENCIA_GET(:p1, :p2, :p3,:p4, :p5, :p6)", $variables);


        return $data;  
    }
                           
    function addIncidencia($idtipo,$idsubtipo,$tipo,$subtipo,$descripcion,$latitud,$longitud,$idinstitucion,$idubigeo,$fecha,$nombre,$apellido,$correo,$celular,$direccion){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDINCIDENCIA',$id);
            $this->db->set('IDTIPO', $idtipo);
            $this->db->set('IDSUBTIPO', $idsubtipo);
            $this->db->set('TIPO', $tipo);
            $this->db->set('SUBTIPO', $subtipo);
            $this->db->set('DESCRIPCION', $descripcion);
            $this->db->set('LATITUD', $latitud);
            $this->db->set('LONGITUD', $longitud);
            $this->db->set('IDINSTITUCION', $idinstitucion);
            //$this->db->set('IDCOMISARIA',$idcomisaria);
            //$this->db->set('IDMUNICIPALIDAD',$idmunicipalidad);
            $this->db->set('IDUBIGEO',$idubigeo);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('FLGACTIVO',1);
            $this->db->set('NOMBRE',$nombre);
            $this->db->set('APELLIDO',$apellido);
            $this->db->set('CORREO',$correo);
            $this->db->set('CELULAR',$celular);
            $this->db->set('DIRECCION',$direccion);
            $this->db->set('FECHA', "TO_DATE('".$fecha."','YYYY-MM-DD HH24:MI:SS')",false);
            if($this->db->insert($this->model_name)){
                return $id;
                // return print_r($this->db->last_query());
            }
           
        }
        return FALSE;
    }


    // public function get_ByFechaUbigeo($fecha, $ubigeo = '', $idcomisaria = '', $idmunicipalidad = '', $ultimo = 0){
    //     $this->db->select('inci.*');
    //     $this->db->from($this->model_name . ' "inci"'); 

    //     $this->db->join('SISGESPATMI.TM_COMISARIA c',' inci.IDCOMISARIA=c.IDCOMISARIA', 'left');

    //     if($ubigeo!='' && $ubigeo!='0'){
    //         if(substr($ubigeo,2,4) == '0000'){ //Departamento
    //             $this->db->where('SUBSTR("inci"."IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\'');
    //         }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
    //             $this->db->where('SUBSTR("inci"."IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
    //         }else{ //Distrito
    //             $this->db->where('"inci".IDUBIGEO', trim($ubigeo));
    //         }
    //     }

    //     $fecha_arr = explode('/',$fecha);
    //     $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];

    //     $this->db->where('TRUNC("inci"."FECHA") = TO_DATE(\''.$fechanum.'\', \'YYYY-MM-DD\') ');

    //     if($idcomisaria!='' && $idcomisaria!='0'){
    //         $this->db->where('inci."IDCOMISARIA"', trim($idcomisaria));
    //     }

    //     if($ultimo!='' && $ultimo!='0'){
    //         $this->db->where('"inci"."IDINCIDENCIA" > '.(int)trim($ultimo).'');
    //     }

    //     $this->db->order_by('"inci"."IDINCIDENCIA" ASC');

    //     $query = $this->db->get(); 

    //     //print_r($this->db->last_query());
    //     if($query){
    //         return $query->result_array();
    //     }else{
    //         return array();
    //     }
    // }
}