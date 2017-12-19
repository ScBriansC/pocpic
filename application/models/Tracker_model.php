<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Rutasync_Model , entidad de la tabla "TH_TRACKER"
 */
class Tracker_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
    function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_TRACKER', 'IDTRACKER');
    }


    function _get_resumen_fecha($fecha, $hora_ini='', $hora_fin='' ,$iddependencia = 0, $idinstitucion = 0, $idusuario = 0)
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
        $variables[0] = array("parameter" => "p1", "value" => $fechaini, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $fechafin, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $iddependencia, "size" => 100);
        $variables[3] = array("parameter" => "p4", "value" => $idinstitucion, "size" => 100);
        $variables[4] = array("parameter" => "p5", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_DEP_DISPOCANT_FCH_GET(:p1, :p2, :p3, :p4, :p5)", $variables);
        return $data;  
    }

    function _get_resumen_total($iddependencia = 0, $idinstitucion = 0, $idusuario = 0)
    {
        
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $iddependencia, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $idinstitucion, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_DEP_DISPOCANT_GET(:p1, :p2, :p3)", $variables);
        return $data[0];  

    }


    function _get_gps($fecha, $hora_ini='', $hora_fin='', $iddispogps = 0, $dependencia = 0, $institucion = 0, $patrullaje = array(), $descripcion = null, $placa = null, $idradio = 0, $serie = null, $idusuario = 0)
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

        $idpatrullaje = '';

        foreach ($patrullaje as $tipopat) {
            if($tipopat =='patrullero')
            {
                $idpatrullaje .= ($idpatrullaje!=''?',':'').'\'1\',\'4\'';
            }
            if($tipopat =='motorizado')
            {
                $idpatrullaje .= ($idpatrullaje!=''?',':'').'\'2\'';
            }
            if($tipopat =='patpie')
            {
                $idpatrullaje .= ($idpatrullaje!=''?',':'').'\'3\'';
              
            }
            if($tipopat =='barrioseg')
            {
                $idpatrullaje .= ($idpatrullaje!=''?',':'').'\'6\'';
              
            }
            if($tipopat =='puestofijo')
            {
                $idpatrullaje .= ($idpatrullaje!=''?',':'').'\'7\'';
              
            }
        }

        if($idpatrullaje==''){
            $idpatrullaje = null;
        }

        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $fechaini, "size" => 19); //'2017-09-13 13:00:00'
        $variables[1] = array("parameter" => "p2", "value" => $fechafin, "size" => 19);
        $variables[2] = array("parameter" => "p3", "value" => (($iddispogps!='')?$iddispogps:null), "size" => 100);
        $variables[3] = array("parameter" => "p4", "value" => (($dependencia!='')?$dependencia:null), "size" => 100);
        $variables[4] = array("parameter" => "p5", "value" => (($institucion!='')?$institucion:null), "size" => 100);
        $variables[5] = array("parameter" => "p6", "value" => (($idpatrullaje!='')?$idpatrullaje:null), "size" => 100);
        $variables[6] = array("parameter" => "p7", "value" => (($descripcion!='')?$descripcion:null), "size" => 100);
        $variables[7] = array("parameter" => "p8", "value" => (($placa!='')?$placa:null), "size" => 100);
        $variables[8] = array("parameter" => "p9", "value" => (($idradio!='')?$idradio:null), "size" => 100);
        $variables[9] = array("parameter" => "p10", "value" => (($serie!='')?$serie:null), "size" => 100);
        $variables[10] = array("parameter" => "p11", "value" => (($idusuario!='')?$idusuario:null), "size" => 100);
        //print_r( $variables);
        $data = $this->_fn_exec("SISGESPATMI.UFN_TRACKER_GPS_GET(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11)", $variables);
        return $data;  
    }


    function _get_dispogps_ruta($iddispogps, $fecha, $hora_ini='', $hora_fin='')
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
        $variables[0] = array("parameter" => "p1", "value" => $iddispogps, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $fechaini, "size" => 19); //'2017-09-13 13:00:00'
        $variables[2] = array("parameter" => "p3", "value" => $fechafin, "size" => 19);
        $data = $this->_fn_exec("SISGESPATMI.UFN_TRACKER_RUTA_GET(:p1, :p2, :p3)", $variables);
        return $data;  
    }

     function _get_dispogps_info($iddispogps, $fecha = null, $hora_ini = null, $hora_fin = null)
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
        $variables[0] = array("parameter" => "p1", "value" => $iddispogps, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $fechaini, "size" => 19); //'2017-09-13 13:00:00'
        $variables[2] = array("parameter" => "p3", "value" => $fechafin, "size" => 19);
        $data = $this->_fn_exec("SISGESPATMI.UFN_TRACKER_DISPO_GET(:p1, :p2, :p3)", $variables);
        return $data[0];   
    }

    public function sp_get_dispogps_info(){
        print_r($this->_get_resumen_fecha(@date('d/m/Y'),'00:00','23:59'));
    }

    

    function _sale_jurisdiccion($dispogps, $fecha, $hora, $latitud, $longitud, $direccion='', $turno=0){

    $this->db->set('IDDISPOGPS', $dispogps);
    $this->db->set('FECHALOC',"TO_DATE('".$fecha." ".$hora."', 'DD/MM/YYYY HH24:MI:SS')",false);
    $this->db->set('LATITUD', $latitud);
    $this->db->set('LONGITUD', $longitud);
    if($direccion!=''){
        $this->db->set('DIRECCION', $direccion);
    }   
    if($turno==0){
        $horanum = str_replace(':', '0', $hora);
        if($horanum >= '000000' && $horanum <= '055959'){
            $turno = 1;
        }elseif($horanum >= '060000' && $horanum <= '115959'){
            $turno = 2;
        }elseif($horanum >= '120000' && $horanum <= '175959'){
            $turno = 3;
        }elseif($horanum >= '180000' && $horanum <= '235959'){
            $turno = 4;
        }
    }

    $this->db->set('TURNO', $turno);

    $this->db->set('FECHAREG',"(SYSDATE)",false);
    if($this->db->insert('"SISGESPATMI".TH_TRACKER_SALE')){
        return $id;
    }
    //print_r($this->db->last_query());echo "\n\n";
    return FALSE;
        
    }
}