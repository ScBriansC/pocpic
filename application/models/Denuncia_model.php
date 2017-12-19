<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "vehiculo"
 */
class Denuncia_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		// parent::__construct('SISGESPATMI.TH_DENUNCIA_20170529171800', 'IDDENUNCIA');
        parent::__construct('SISGESPATMI.TH_DENUNCIA', 'IDDENUNCIA'); 

	}

	public function get_ByUbigeo($ubigeo = '', $idcomisaria = ''){
        $this->db->select('substr("LONGITUD",1,9) as "DenunciaLong" , substr("LATITUD",1,9) as "DenunciaLat", count("IDDENUNCIA") as "DenunciaCant"');
        // $this->db->select('LONGITUD as "DenunciaLong" , LATITUD as "DenunciaLat"');
        $this->db->from($this->model_name); 

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $this->db->where('SUBSTR("IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\'');
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $this->db->where('SUBSTR("IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\'');
            }else{ //Distrito
                $this->db->where('IDUBIGEO', trim($ubigeo));
            }
        }

        $this->db->where('FLGACTIVO', 1);

        $this->db->group_by('substr("LONGITUD",1,9), substr("LATITUD",1,9)');

        $query = $this->db->get(); 

        if($query){
            return $query->result_array();
        }else{
            return array();
        }
	}

    public function get_NuevoID(){
        $q = $this->db->query('SELECT SISGESPATMI.USEQ_THDENUNCIA_IDDENUNCIA.NEXTVAL as IDDENUNCIA from dual');
        $id = $q->row_array();
        return (int)@$id['IDDENUNCIA'];
    }

    public function registrar($direccion,$ubigeo,$latitud,$longitud,$idusuario,$ip){
        $id = $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDDENUNCIA', $id);
            $this->db->set('IDUSUREG', $idusuario);
            $this->db->set('UBICACION', $direccion);
            $this->db->set('IDUBIGEO', $ubigeo);
            $this->db->set('LATITUD', $latitud);
            $this->db->set('LONGITUD', $longitud);
            $this->db->set('FLGACTIVO', 1);
            $this->db->set('FECHA',"(SYSDATE)",false);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('IPMAQREG', $ip);
            if(@$this->db->insert($this->model_name)){
                return $id;
            }
            print_r($this->db->last_query());
        }
        return FALSE;
    }



    public function _get_consulta($iddenuncia = 0, $idinstitucion, $iddependencia = 0, $fechaini = null, $fechafin = null, $hora_ini = null, $hora_fin = null, $tipos = '', $idusuario = 0, $modo = 0){

        if($fechaini!=null && $fechaini!=''){
            $fecha_arr = explode('/',$fechaini);
            $fechanumini = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        }else{
            $fechanumini = @date('Y-m-d');
        }

        if($fechafin!=null && $fechafin!=''){
            $fecha_arr = explode('/',$fechafin);
            $fechanumfin = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        }else{
            $fechanumfin = @date('Y-m-d');
        }

        $fechaini = $fechanumini.' 00:00:00';
        $fechafin = $fechanumfin.' 23:59:59';

        if($hora_ini!='' && $hora_fin!='' && $hora_ini!=null && $hora_fin!=null){
            $fechaini = $fechanumini.' '.$hora_ini.':00';
            $fechafin = $fechanumfin.' '.$hora_fin.':59';
        }


        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $idinstitucion, "size" => 10);
        $variables[1] = array("parameter" => "p2", "value" => $idinstitucion, "size" => 10);
        $variables[2] = array("parameter" => "p3", "value" => $iddependencia, "size" => 10);
        $variables[3] = array("parameter" => "p4", "value" => $fechaini, "size" => 19);
        $variables[4] = array("parameter" => "p5", "value" => $fechafin, "size" => 19);
        $variables[5] = array("parameter" => "p6", "value" => $tipos, "size" => 100);
        $variables[6] = array("parameter" => "p7", "value" => $idusuario, "size" => 10);
        $variables[7] = array("parameter" => "p8", "value" => $modo, "size" => 10);
        $data = $this->_fn_exec("SISGESPATMI.UFN_DENUNCIAS_GET(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8)", $variables);
        //print_r($variables);
        if(@$data[0]){
            return $data;
        }else{
            return array();
        }
    }
	
}