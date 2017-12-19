<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Tablet extends Sys_Controller  {

	var $usuario_login;

	function __construct()
  {
  	parent::__construct(FALSE);
		
  }

  public function index(){
  	$this->load->view('sipcop_tablet');
  }

  public function buscar(){
  	$result = array();
  	$action = (int)@$_POST['action'];
  	if($action == 2){
  		$q = $this->db->query('SELECT V.IDALTERNO, VH.ADDRESS,V.LABEL,V.NOMBRE AS COMISARIA,V.DEPARTAMENTO, V.PROVINCIA,V.DISTRITO,TO_CHAR(V.FECHAMIN,\'YYYY-MM-DD HH24:MI:SS\') FECHAMIN,TO_CHAR(V.FECHAMAX,\'YYYY-MM-DD HH24:MI:SS\')FECHAMAX,NVL(P.NOMBRE,\'NO MATRICULADO\') AS PROVEEDOR from SISGESPATMI.TMP_TABLET V
  			INNER JOIN  SISGESPATMI.TM_VEHICULO_PNP VH ON VH.CARID = V.IDALTERNO 
  LEFT JOIN SISGESPATMI.TM_DISPOGPS DI ON DI.IDPNPVH = V.IDALTERNO AND DI.FLGACTIVO = 1
  LEFT JOIN SISGESPATMI.TM_PROVEEDOR P ON P.IDPROVEEDOR = DI.IDPROVEEDOR WHERE V.LABEL LIKE \'%'.@$_POST['placa'].'%\'');
  		$result['data'] = $q->result_array();
  	
  	}elseif($action == 1){
  		$q = $this->db->query('SELECT V.CARID, V.ADDRESS,V.LABEL, I.NOMBRE AS COMISARIA, U.DEPARTAMENTO, U.PROVINCIA, U.DISTRITO,NVL(P.NOMBRE,\'NO MATRICULADO\') AS PROVEEDOR FROM SISGESPATMI.TM_VEHICULO_PNP V
  LEFT JOIN SISGESPATMI.TM_DISPOGPS DI ON DI.IDPNPVH = V.CARID AND DI.FLGACTIVO = 1
  LEFT JOIN SISGESPATMI.TM_PROVEEDOR P ON P.IDPROVEEDOR = DI.IDPROVEEDOR
  LEFT JOIN SISGESPATMI.TM_INSTITUCION I ON I.IDINSTITUCION = DI.IDINSTITUCION AND I.IDTIPOINST IN(4,5)
  LEFT JOIN SISGESPATMI.TB_UBIGEO U ON U.IDUBIGEO = I.IDUBIGEO WHERE V.LABEL LIKE \'%'.@$_POST['placa'].'%\'');
  		$result['data'] = $q->result_array();
  	
  	}else{
  		$result['data'] = array();
  	}

  	echo json_encode($result);
  }


}
