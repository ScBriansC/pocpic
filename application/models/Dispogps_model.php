<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "TM_DISPOGPS"
 */
class Dispogps_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TM_DISPOGPS', 'IDDISPOGPS');
	}

	function _get_consulta($iddispogps = 0, $placa = '', $iddependencia = 0, $idinstitucion = 0, $idusuario = 0)
    {
        
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $iddispogps, "size" => 100);
        $variables[1] = array("parameter" => "p2", "value" => $placa, "size" => 100);
        $variables[2] = array("parameter" => "p3", "value" => $iddependencia, "size" => 100);
        $variables[3] = array("parameter" => "p4", "value" => $idinstitucion, "size" => 100);
        $variables[4] = array("parameter" => "p5", "value" => $idusuario, "size" => 100);
        $data = $this->_fn_exec("SISGESPATMI.UFN_DISPOGPS_GET(:p1, :p2, :p3, :p4, :p5)", $variables);
        return $data;  

    }

	function _guardar($dispogps,$comisaria,$placa,$descripcion,$patrullaje,$proveedor,$modelovh,$idradio,$idotro,$tipo,$serie,$modelo,$origen,$tei,$observacion,$categoria,$marca,$estado,$motivo,$idusuario)
    {
        
        $variables = array();
        $variables[0] = array("parameter" => "p1", "value" => $dispogps, "size" => 100);
		$variables[1] = array("parameter" => "p2", "value" => $comisaria, "size" => 100);
		$variables[2] = array("parameter" => "p3", "value" => $placa, "size" => 100);
		$variables[3] = array("parameter" => "p4", "value" => $descripcion, "size" => 100);
		$variables[4] = array("parameter" => "p5", "value" => $patrullaje, "size" => 100);
		$variables[5] = array("parameter" => "p6", "value" => $proveedor, "size" => 100);
		$variables[6] = array("parameter" => "p7", "value" => $modelovh, "size" => 100);
		$variables[7] = array("parameter" => "p8", "value" => $idradio, "size" => 100);
		$variables[8] = array("parameter" => "p9", "value" => $idotro, "size" => 100);
		$variables[9] = array("parameter" => "p10", "value" => $tipo, "size" => 100);
		$variables[10] = array("parameter" => "p11", "value" => $serie, "size" => 100);
		$variables[11] = array("parameter" => "p12", "value" => $modelo, "size" => 100);
		$variables[12] = array("parameter" => "p13", "value" => $origen, "size" => 100);
		$variables[13] = array("parameter" => "p14", "value" => $tei, "size" => 100);
		$variables[14] = array("parameter" => "p15", "value" => $observacion, "size" => 100);
		$variables[15] = array("parameter" => "p16", "value" => $categoria, "size" => 100);
		$variables[16] = array("parameter" => "p17", "value" => $marca, "size" => 100);
		$variables[17] = array("parameter" => "p18", "value" => $estado, "size" => 100);
		$variables[18] = array("parameter" => "p19", "value" => $motivo, "size" => 100);
		$variables[19] = array("parameter" => "p20", "value" => $idusuario, "size" => 100);

        $data = $this->_fn_exec("SISGESPATMI.UFN_DISPOGPS_UPD(:p1, :p2, :p3, :p4, :p5, :p6, :p7, :p8, :p9, :p10, :p11, :p12, :p13, :p14, :p15, :p16, :p17, :p18, :p19, :p20)", $variables);
        return @$data[0];  

    }

    function _get_proveedores(){

      $sql ='SELECT IDPROVEEDOR, NOMBRE FROM SISGESPATMI.TM_PROVEEDOR WHERE FLGACTIVO = \'1\'';
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }

    function _get_patrullaje(){

      $sql ='SELECT IDPATRULLAJE, NOMBRE FROM SISGESPATMI.TM_PATRULLAJE WHERE FLGACTIVO = \'1\'';
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }

    function _get_tipovh(){

      $sql ='SELECT IDTIPOVH, NOMBRE from SISGESPATMI.TM_TIPOVH';
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }

    function _get_marcavh(){

      $sql ='SELECT IDMARCAVH, NOMBRE, IDTIPOVH from SISGESPATMI.TM_MARCAVH';
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }

    function _get_modelovh(){

      $sql ='SELECT IDMODELOVH, NOMBRE, IDMARCAVH from SISGESPATMI.TM_MODELOVH';
            $query = $this->db->query($sql);
            return $query->result_array(); 
    }
	
	
}