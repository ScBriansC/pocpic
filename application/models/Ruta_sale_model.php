<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Ruta_sale_Model , entidad de la tabla "TH_RUTA_SALE"
 */
class Ruta_sale_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
	function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_RUTA_SALE');
    }

    

    function registrarSalidaJurisdiccion($radio, $fecha, $hora, $latitud, $longitud, $direccion='', $turno=0){

    $this->db->set('IDRADIO', $radio);
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
    if($this->db->insert($this->model_name)){
    	return $id;
    }
    //print_r($this->db->last_query());echo "\n\n";
    return FALSE;
    	
    }
}