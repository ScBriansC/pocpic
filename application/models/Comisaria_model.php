<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Usuario_Model , entidad de la tabla "comisaria"
 */
class Comisaria_Model extends Base_model {
	
	function __construct() {
		// Crea referencia a la clase de modelo padre
		parent::__construct('SISGESPATMI.TM_COMISARIA', 'IDCOMISARIA');
	}
	
	public function delete_at($id,$data) {
		$this->db->set($data);
		$this->db->where(['idcomisaria' => $id]);
		$this->db->update($this->model_name);
		$afected = $this->db->affected_rows();
		return ($afected > 0 ? $id: false);
	}

    function get_byID($idcomisaria)
    {

        $this->db->select('c.IDCOMISARIA as "ComisariaID"
            , c.IDUBIGEO as "ComisariaUbigeo"
            , c.TELEFONO as "ComisariaTelf"
            , c.LATITUD as "ComisariaLat"
            , c.LONGITUD as "ComisariaLong"
            , c.NOMBRE as "ComisariaNombre"
            , NVL("c_dep"."NOMBRE",\'\') as "ComisariaDependencia"
            , NVL("c_zona"."NOMBRE",\'\') as "ComisariaZona"
            , NVL("c_div"."NOMBRE",\'\') as "ComisariaDivision"
            , NVL("c_cla"."NOMBRE",\'\') as "ComisariaClase"
            , NVL("c_tipo"."NOMBRE",\'\') as "ComisariaTipo"
            , NVL("c_cat"."NOMBRE",\'\') as "ComisariaCategoria"
        ');
        $this->db->from($this->model_name . ' c'); 
        $this->db->join('SISGESPATMI.TM_DEPENDENCIA c_dep', 'c.IDDEPENDENCIA=c_dep.IDDEPENDENCIA', 'left');
        $this->db->join('SISGESPATMI.TM_ZONA c_zona', 'c.IDZONA=c_zona.IDZONA', 'left');
        $this->db->join('SISGESPATMI.TM_DIVISION c_div', 'c.IDDIVISION=c_div.IDDIVISION', 'left');
        $this->db->join('SISGESPATMI.TM_CLASE c_cla', 'c.IDCLASE=c_cla.IDCLASE', 'left');
        $this->db->join('SISGESPATMI.TM_TIPO c_tipo', 'c.IDTIPO=c_tipo.IDTIPO', 'left');
        $this->db->join('SISGESPATMI.TM_CATEGORIA c_cat', 'c.IDCATEGORIA=c_cat.IDCATEGORIA', 'left');

        $this->db->where('c.IDCOMISARIA', trim($idcomisaria));

        $query = $this->db->get(); 
        if($query){
            return $query->row_array();
        }else{
            return array();
        }
    }

    function get_byIDFecha($idcomisaria, $fecha='',$hora_ini='',$hora_fin='')
    {

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.'00:00:00';
        $fechafin = $fechanum.'23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':59';
        }

        $this->db->select('c.IDCOMISARIA as "ComisariaID"
            , c.IDUBIGEO as "ComisariaUbigeo"
            , c.TELEFONO as "ComisariaTelf"
            , c.LATITUD as "ComisariaLat"
            , c.LONGITUD as "ComisariaLong"
            , c.NOMBRE as "ComisariaNombre"
            , NVL("c_dep"."NOMBRE",\'\') as "ComisariaDependencia"
            , NVL("c_zona"."NOMBRE",\'\') as "ComisariaZona"
            , NVL("c_div"."NOMBRE",\'\') as "ComisariaDivision"
            , NVL("c_cla"."NOMBRE",\'\') as "ComisariaClase"
            , NVL("c_tipo"."NOMBRE",\'\') as "ComisariaTipo"
            , NVL("c_cat"."NOMBRE",\'\') as "ComisariaCategoria"
            , SISGESPATMI.PKG_RUTA.fn_GetTotalVhxCom("c"."IDCOMISARIA") AS "VehiculoTotal"
            , SISGESPATMI.PKG_RUTA.fn_GetTotalVhxFchCom("c"."IDCOMISARIA", TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\'), TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')) as "VehiculoActual"            
            , SISGESPATMI.PKG_RUTA.fn_GetTotalRdxCom("c"."IDCOMISARIA") AS "MotoTotal"
            , SISGESPATMI.PKG_RUTA.fn_GetTotalRdxFchCom("c"."IDCOMISARIA", TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\'), TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')) as "MotoActual"
        ');
        $this->db->from($this->model_name . ' c'); 
        $this->db->join('SISGESPATMI.TM_DEPENDENCIA c_dep', 'c.IDDEPENDENCIA=c_dep.IDDEPENDENCIA', 'left');
        $this->db->join('SISGESPATMI.TM_ZONA c_zona', 'c.IDZONA=c_zona.IDZONA', 'left');
        $this->db->join('SISGESPATMI.TM_DIVISION c_div', 'c.IDDIVISION=c_div.IDDIVISION', 'left');
        $this->db->join('SISGESPATMI.TM_CLASE c_cla', 'c.IDCLASE=c_cla.IDCLASE', 'left');
        $this->db->join('SISGESPATMI.TM_TIPO c_tipo', 'c.IDTIPO=c_tipo.IDTIPO', 'left');
        $this->db->join('SISGESPATMI.TM_CATEGORIA c_cat', 'c.IDCATEGORIA=c_cat.IDCATEGORIA', 'left');

        $this->db->where('c.IDCOMISARIA', trim($idcomisaria));

        $query = $this->db->get(); 
        if($query){
            return $query->row_array();
        }else{
            return array();
        }
    }

    function get_byVehiculo($idvehiculo)
    {

        $this->db->select('c.IDCOMISARIA as "ComisariaID"
            , c.IDUBIGEO as "ComisariaUbigeo"
            , c.TELEFONO as "ComisariaTelf"
            , c.LATITUD as "ComisariaLat"
            , c.LONGITUD as "ComisariaLong"
            , c.NOMBRE as "ComisariaNombre"
            , NVL("c_dep"."NOMBRE",\'\') as "ComisariaDependencia"
            , NVL("c_zona"."NOMBRE",\'\') as "ComisariaZona"
            , NVL("c_div"."NOMBRE",\'\') as "ComisariaDivision"
            , NVL("c_cla"."NOMBRE",\'\') as "ComisariaClase"
            , NVL("c_tipo"."NOMBRE",\'\') as "ComisariaTipo"
            , NVL("c_cat"."NOMBRE",\'\') as "ComisariaCategoria"
        ');
        $this->db->from($this->model_name . ' c'); 
        $this->db->join('SISGESPATMI.TM_DEPENDENCIA c_dep', 'c.IDDEPENDENCIA=c_dep.IDDEPENDENCIA', 'left');
        $this->db->join('SISGESPATMI.TM_ZONA c_zona', 'c.IDZONA=c_zona.IDZONA', 'left');
        $this->db->join('SISGESPATMI.TM_DIVISION c_div', 'c.IDDIVISION=c_div.IDDIVISION', 'left');
        $this->db->join('SISGESPATMI.TM_CLASE c_cla', 'c.IDCLASE=c_cla.IDCLASE', 'left');
        $this->db->join('SISGESPATMI.TM_TIPO c_tipo', 'c.IDTIPO=c_tipo.IDTIPO', 'left');
        $this->db->join('SISGESPATMI.TM_CATEGORIA c_cat', 'c.IDCATEGORIA=c_cat.IDCATEGORIA', 'left');

        $this->db->where('c.IDCOMISARIA IN (SELECT "cv"."IDCOMISARIA" FROM SISGESPATMI."TM_COMISARIAVH" "cv" WHERE "cv"."IDVEHICULO" = '.$idvehiculo.')');

        $query = $this->db->get(); 
        if($query){
            return $query->row_array();
        }else{
            return array();
        }
    }

    function get_byConsulta($nombre = '', $ubigeo = '', $iddependencia = 0, $idzona = 0, $iddivision = 0, $idclase = 0, $idtipo = 0, $idcategoria = 0, $unid_epecializada = FALSE,$fecha='',$hora_ini='',$hora_fin='')
    {
        
        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.'00:00:00';
        $fechafin = $fechanum.'23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }

        $q_where = '';

        if(strlen(trim($nombre))>0)
        {
           $q_where .= ' AND UPPER("c".NOMBRE) like (\'%'.strtoupper(utf8_decode(trim($nombre))).'%\')';
        }   

    
        if(!$unid_epecializada){
            $q_where .= ' AND ("c".IDDEPENDENCIA <> 2)';
        }

        if($iddependencia > 0){
            $q_where .= ' AND ("c".IDDEPENDENCIA = \''.trim($iddependencia).'\')';
        }

        if($idzona > 0){
            $q_where .= ' AND ("c".IDZONA = \''.trim($idzona).'\')';
            // $this->db->where('c.IDZONA', trim($idzona));
        }

        if($iddivision > 0){
            $q_where .= ' AND ("c".IDDIVISION = \''.trim($iddivision).'\')';
            // $this->db->where('c.IDDIVISION', trim($iddivision));
        }

        if($idclase > 0){
            $q_where .= ' AND ("c".IDCLASE = \''.trim($idclase).'\')';
            // $this->db->where('c.IDCLASE', trim($idclase));
        }

        if($idtipo > 0){
            $q_where .= ' AND ("c".IDTIPO =\''.trim($idtipo).'\')';
            // $this->db->where('c.IDTIPO', trim($idtipo));
        }

        if($idcategoria > 0){
             $q_where .= ' AND ("c".IDCATEGORIA = \''.trim($idcategoria).'\')';
            // $this->db->where('c.IDCATEGORIA', trim($idcategoria));
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("c".IDUBIGEO,0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("c".IDUBIGEO,0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "c".IDUBIGEO = \''.trim($ubigeo).'\' ';
            }
        }

        // if($ubigeo!='' && $ubigeo!='0'){
        //     if(substr($ubigeo,2,4) == '0000'){ //Departamento
        //         $this->db->where('SUBSTR("IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\'');
        //     }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
        //         $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
        //     }else{ //Distrito
        //         $this->db->where('c.IDUBIGEO', trim($ubigeo));
        //     }
        // }
                 // "c"."IDUBIGEO" = '150132'
        // $this->db->where('c.FLGACTIVO', 1);
        
        // $this->db->order_by('c.NOMBRE', 'asc');


        $sql = 'with vehiculos as (
                     SELECT REP.IDCOMISARIA, SUM(REP.CANTIDAD_V) CANTIDAD_V, SUM(REP.CANTIDAD_M) CANTIDAD_M
                       FROM (
                           SELECT DISTINCT RA.IDCOMISARIA, RA.IDRADIO,
                                  CASE WHEN RA.IDMODELOVH IN (1,2,5) THEN 1 ELSE 0 END AS CANTIDAD_V,
                                  CASE WHEN RA.IDMODELOVH = 3 THEN 1 ELSE 0 END AS CANTIDAD_M               
                             FROM SISGESPATMI.TM_RADIO RA 
                             JOIN SISGESPATMI.TM_COMISARIA "c" ON RA.IDCOMISARIA = "c".IDCOMISARIA
                            WHERE RA.FLGACTIVO = 1 
                              AND RA.PLACA IS NOT NULL AND RA.IDCOMISARIA IS NOT NULL
                              '.$q_where.'
                    ) REP 
                    GROUP BY REP.IDCOMISARIA
                ), detalle as (
                   SELECT REP.IDCOMISARIA, SUM(REP.CANTIDAD_V) CANTIDAD_V, SUM(REP.CANTIDAD_M) CANTIDAD_M
                     FROM (
                    SELECT DISTINCT RA.IDCOMISARIA, RA.IDRADIO,
                           CASE WHEN RA.IDMODELOVH IN (1,2,5) THEN 1 ELSE 0 END AS CANTIDAD_V,
                           CASE WHEN RA.IDMODELOVH = 3 THEN 1 ELSE 0 END AS CANTIDAD_M
                      FROM SISGESPATMI.TM_RADIO RA
                      JOIN SISGESPATMI.'.$this->_tbl_ruta($fecha).' RSYNC1 ON RSYNC1.IDRADIO = RA.IDRADIO
                      JOIN SISGESPATMI.TM_COMISARIA "c" ON RA.IDCOMISARIA = "c".IDCOMISARIA
                     WHERE RA.FLGACTIVO = 1  AND RSYNC1.PROVEEDOR = 1 AND RSYNC1.FLGCORRECTO = 1
                       AND RSYNC1.FECHALOC BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\')
                                                   AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
                       AND RA.PLACA IS NOT NULL AND RA.IDCOMISARIA IS NOT NULL
                       '.$q_where.'
                    ) REP
                    GROUP BY REP.IDCOMISARIA
                )
                SELECT "c"."IDCOMISARIA" as "ComisariaID",
                       "c"."IDUBIGEO" as "ComisariaUbigeo",
                       "c"."TELEFONO" as "ComisariaTelf",
                       "c"."LATITUD" as "ComisariaLat",
                       "c"."LONGITUD" as "ComisariaLong",
                       "c"."NOMBRE" as "ComisariaNombre",
                       NVL("c_dep"."NOMBRE", \'\') as "ComisariaDependencia",
                       NVL("c_zona"."NOMBRE", \'\') as "ComisariaZona",
                       NVL("c_div"."NOMBRE", \'\') as "ComisariaDivision",
                       NVL("c_cla"."NOMBRE", \'\') as "ComisariaClase",
                       NVL("c_tipo"."NOMBRE", \'\') as "ComisariaTipo",
                       NVL("c_cat"."NOMBRE", \'\') as "ComisariaCategoria",
                       NVL(T1.CANTIDAD_V, 0) AS "VehiculoTotal",
                       NVL(T2.CANTIDAD_V, 0) AS "VehiculoActual",
                       NVL(T1.CANTIDAD_M, 0) AS "MotoTotal",
                       NVL(T2.CANTIDAD_M, 0) AS "MotoActual"
                  FROM "SISGESPATMI"."TM_COMISARIA" "c"
                  LEFT JOIN vehiculos T1 ON T1.IDCOMISARIA = "c".IDCOMISARIA
                  LEFT JOIN detalle T2 ON T2.IDCOMISARIA = "c".IDCOMISARIA
                  LEFT JOIN "SISGESPATMI"."TM_DEPENDENCIA" "c_dep"
                    ON "c"."IDDEPENDENCIA" = "c_dep"."IDDEPENDENCIA"
                  LEFT JOIN "SISGESPATMI"."TM_ZONA" "c_zona"
                    ON "c"."IDZONA" = "c_zona"."IDZONA"
                  LEFT JOIN "SISGESPATMI"."TM_DIVISION" "c_div"
                    ON "c"."IDDIVISION" = "c_div"."IDDIVISION"
                  LEFT JOIN "SISGESPATMI"."TM_CLASE" "c_cla"
                    ON "c"."IDCLASE" = "c_cla"."IDCLASE"
                  LEFT JOIN "SISGESPATMI"."TM_TIPO" "c_tipo"
                    ON "c"."IDTIPO" = "c_tipo"."IDTIPO"
                  LEFT JOIN "SISGESPATMI"."TM_CATEGORIA" "c_cat"
                    ON "c"."IDCATEGORIA" = "c_cat"."IDCATEGORIA"
                 WHERE 
                 "c"."FLGACTIVO" = 1
                  '.$q_where.'
                
                 ORDER BY "c"."NOMBRE" ASC
                ';

        // $this->db->select('c.IDCOMISARIA as "ComisariaID"
        //     , c.IDUBIGEO as "ComisariaUbigeo"
        //     , c.TELEFONO as "ComisariaTelf"
        //     , c.LATITUD as "ComisariaLat"
        //     , c.LONGITUD as "ComisariaLong"
        //     , c.NOMBRE as "ComisariaNombre"
        //     , NVL("c_dep"."NOMBRE",\'\') as "ComisariaDependencia"
        //     , NVL("c_zona"."NOMBRE",\'\') as "ComisariaZona"
        //     , NVL("c_div"."NOMBRE",\'\') as "ComisariaDivision"
        //     , NVL("c_cla"."NOMBRE",\'\') as "ComisariaClase"
        //     , NVL("c_tipo"."NOMBRE",\'\') as "ComisariaTipo"
        //     , NVL("c_cat"."NOMBRE",\'\') as "ComisariaCategoria"
        //     , SISGESPATMI.PKG_RUTA.fn_GetTotalVhxCom("c"."IDCOMISARIA") AS "VehiculoTotal"
        //     , SISGESPATMI.PKG_RUTA.fn_GetTotalVhxFchCom("c"."IDCOMISARIA", TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\'), TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')) as "VehiculoActual"
        //     , SISGESPATMI.PKG_RUTA.fn_GetTotalRdxCom("c"."IDCOMISARIA") AS "MotoTotal"
        //     , SISGESPATMI.PKG_RUTA.fn_GetTotalRdxFchCom("c"."IDCOMISARIA", TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\'), TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')) as "MotoActual"
        // ');
        // $this->db->from($this->model_name . ' c'); 
        // $this->db->join('SISGESPATMI.TM_DEPENDENCIA c_dep', 'c.IDDEPENDENCIA=c_dep.IDDEPENDENCIA', 'left');
        // $this->db->join('SISGESPATMI.TM_ZONA c_zona', 'c.IDZONA=c_zona.IDZONA', 'left');
        // $this->db->join('SISGESPATMI.TM_DIVISION c_div', 'c.IDDIVISION=c_div.IDDIVISION', 'left');
        // $this->db->join('SISGESPATMI.TM_CLASE c_cla', 'c.IDCLASE=c_cla.IDCLASE', 'left');
        // $this->db->join('SISGESPATMI.TM_TIPO c_tipo', 'c.IDTIPO=c_tipo.IDTIPO', 'left');
        // $this->db->join('SISGESPATMI.TM_CATEGORIA c_cat', 'c.IDCATEGORIA=c_cat.IDCATEGORIA', 'left');

        // if(strlen(trim($nombre))>0){
        //     $this->db->like('UPPER("c"."NOMBRE")', strtoupper(utf8_decode(trim($nombre))));
        //         // strtoupper(trim($nombre)));
        // }
        // //$this->db->where('c.NOMBRE not like \'\'');

        // if(!$unid_epecializada){
        //     $this->db->where('"c"."IDDEPENDENCIA" <> 2');
        // }

        // if($iddependencia > 0){
        //     $this->db->where('c.IDDEPENDENCIA', trim($iddependencia));
        // }

        // if($idzona > 0){
        //     $this->db->where('c.IDZONA', trim($idzona));
        // }

        // if($iddivision > 0){
        //     $this->db->where('c.IDDIVISION', trim($iddivision));
        // }

        // if($idclase > 0){
        //     $this->db->where('c.IDCLASE', trim($idclase));
        // }

        // if($idtipo > 0){
        //     $this->db->where('c.IDTIPO', trim($idtipo));
        // }

        // if($idcategoria > 0){
        //     $this->db->where('c.IDCATEGORIA', trim($idcategoria));
        // }

        // if($ubigeo!='' && $ubigeo!='0'){
        //     if(substr($ubigeo,2,4) == '0000'){ //Departamento
        //         $this->db->where('SUBSTR("IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\'');
        //     }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
        //         $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
        //     }else{ //Distrito
        //         $this->db->where('c.IDUBIGEO', trim($ubigeo));
        //     }
        // }

        // $this->db->where('c.FLGACTIVO', 1);
        
        // $this->db->order_by('c.NOMBRE', 'asc');

        // $query = $this->db->get(); 

        //  print_r($this->db->last_query());
        // $query = $this->db->query($sql);


        // if($query){
        //     return $query->result_array();
        // }else{
        //     return array();
        // }


        $query = $this->db->query($sql);

        $array  = $query->result_array();

        // echo $sql;
        return $query->result_array(); 
    }


    function get_byConsultaAct($nombre = '', $ubigeo = '', $iddependencia = 0, $idzona = 0, $iddivision = 0, $idclase = 0, $idtipo = 0, $idcategoria = 0, $unid_epecializada = FALSE,$fecha='',$hora_ini='',$hora_fin='')
    {
        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.'00:00:00';
        $fechafin = $fechanum.'23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }

        $q_where = '';

        if(strlen(trim($nombre))>0)
        {
           $q_where .= ' AND UPPER("c".NOMBRE) like (\'%'.strtoupper(utf8_decode(trim($nombre))).'%\')';
        }   

    
        if(!$unid_epecializada){
            $q_where .= ' AND ("c".IDDEPENDENCIA <> 2)';
        }

        if($iddependencia > 0){
            $q_where .= ' AND ("c".IDDEPENDENCIA = \''.trim($iddependencia).'\')';
        }

        if($idzona > 0){
            $q_where .= ' AND ("c".IDZONA = \''.trim($idzona).'\')';
            // $this->db->where('c.IDZONA', trim($idzona));
        }

        if($iddivision > 0){
            $q_where .= ' AND ("c".IDDIVISION = \''.trim($iddivision).'\')';
            // $this->db->where('c.IDDIVISION', trim($iddivision));
        }

        if($idclase > 0){
            $q_where .= ' AND ("c".IDCLASE = \''.trim($idclase).'\')';
            // $this->db->where('c.IDCLASE', trim($idclase));
        }

        if($idtipo > 0){
            $q_where .= ' AND ("c".IDTIPO =\''.trim($idtipo).'\')';
            // $this->db->where('c.IDTIPO', trim($idtipo));
        }

        if($idcategoria > 0){
             $q_where .= ' AND ("c".IDCATEGORIA = \''.trim($idcategoria).'\')';
            // $this->db->where('c.IDCATEGORIA', trim($idcategoria));
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("c".IDUBIGEO,0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("c".IDUBIGEO,0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "c".IDUBIGEO = \''.trim($ubigeo).'\' ';
            }
        }

        // if($ubigeo!='' && $ubigeo!='0'){
        //     if(substr($ubigeo,2,4) == '0000'){ //Departamento
        //         $this->db->where('SUBSTR("IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\'');
        //     }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
        //         $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
        //     }else{ //Distrito
        //         $this->db->where('c.IDUBIGEO', trim($ubigeo));
        //     }
        // }
                 // "c"."IDUBIGEO" = '150132'
        // $this->db->where('c.FLGACTIVO', 1);
        
        // $this->db->order_by('c.NOMBRE', 'asc');


        $sql = 'with vehiculos as (
                     SELECT REP.IDCOMISARIA, SUM(REP.CANTIDAD_V) CANTIDAD_V, SUM(REP.CANTIDAD_M) CANTIDAD_M
                       FROM (
                           SELECT DISTINCT RA.IDCOMISARIA, RA.IDRADIO,
                                  CASE WHEN RA.IDMODELOVH IN (1,2,5) THEN 1 ELSE 0 END AS CANTIDAD_V,
                                  CASE WHEN RA.IDMODELOVH = 3 THEN 1 ELSE 0 END AS CANTIDAD_M               
                             FROM SISGESPATMI.TM_RADIO RA 
                             JOIN SISGESPATMI.TM_COMISARIA "c" ON RA.IDCOMISARIA = "c".IDCOMISARIA
                            WHERE RA.FLGACTIVO = 1 
                              AND RA.PLACA IS NOT NULL AND RA.IDCOMISARIA IS NOT NULL
                              '.$q_where.'
                    ) REP 
                    GROUP BY REP.IDCOMISARIA
                ), detalle as (
                   SELECT REP.IDCOMISARIA, SUM(REP.CANTIDAD_V) CANTIDAD_V, SUM(REP.CANTIDAD_M) CANTIDAD_M
                     FROM (
                    SELECT DISTINCT RA.IDCOMISARIA, RA.IDRADIO,
                           CASE WHEN RA.IDMODELOVH IN (1,2,5) THEN 1 ELSE 0 END AS CANTIDAD_V,
                           CASE WHEN RA.IDMODELOVH = 3 THEN 1 ELSE 0 END AS CANTIDAD_M
                      FROM SISGESPATMI.TM_RADIO RA
                      JOIN SISGESPATMI.TM_COMISARIA "c" ON RA.IDCOMISARIA = "c".IDCOMISARIA
                     WHERE RA.FLGACTIVO = 1 
                       AND RA.FECHALOC BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\')
                                                   AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
                       AND RA.PLACA IS NOT NULL AND RA.IDCOMISARIA IS NOT NULL
                       '.$q_where.'
                    ) REP
                    GROUP BY REP.IDCOMISARIA
                )
                SELECT "c"."IDCOMISARIA" as "ComisariaID",
                       "c"."IDUBIGEO" as "ComisariaUbigeo",
                       "c"."TELEFONO" as "ComisariaTelf",
                       "c"."LATITUD" as "ComisariaLat",
                       "c"."LONGITUD" as "ComisariaLong",
                       "c"."NOMBRE" as "ComisariaNombre",
                       NVL("c_dep"."NOMBRE", \'\') as "ComisariaDependencia",
                       NVL("c_zona"."NOMBRE", \'\') as "ComisariaZona",
                       NVL("c_div"."NOMBRE", \'\') as "ComisariaDivision",
                       NVL("c_cla"."NOMBRE", \'\') as "ComisariaClase",
                       NVL("c_tipo"."NOMBRE", \'\') as "ComisariaTipo",
                       NVL("c_cat"."NOMBRE", \'\') as "ComisariaCategoria",
                       NVL(T1.CANTIDAD_V, 0) AS "VehiculoTotal",
                       NVL(T2.CANTIDAD_V, 0) AS "VehiculoActual",
                       NVL(T1.CANTIDAD_M, 0) AS "MotoTotal",
                       NVL(T2.CANTIDAD_M, 0) AS "MotoActual"
                  FROM "SISGESPATMI"."TM_COMISARIA" "c"
                  LEFT JOIN vehiculos T1 ON T1.IDCOMISARIA = "c".IDCOMISARIA
                  LEFT JOIN detalle T2 ON T2.IDCOMISARIA = "c".IDCOMISARIA
                  LEFT JOIN "SISGESPATMI"."TM_DEPENDENCIA" "c_dep"
                    ON "c"."IDDEPENDENCIA" = "c_dep"."IDDEPENDENCIA"
                  LEFT JOIN "SISGESPATMI"."TM_ZONA" "c_zona"
                    ON "c"."IDZONA" = "c_zona"."IDZONA"
                  LEFT JOIN "SISGESPATMI"."TM_DIVISION" "c_div"
                    ON "c"."IDDIVISION" = "c_div"."IDDIVISION"
                  LEFT JOIN "SISGESPATMI"."TM_CLASE" "c_cla"
                    ON "c"."IDCLASE" = "c_cla"."IDCLASE"
                  LEFT JOIN "SISGESPATMI"."TM_TIPO" "c_tipo"
                    ON "c"."IDTIPO" = "c_tipo"."IDTIPO"
                  LEFT JOIN "SISGESPATMI"."TM_CATEGORIA" "c_cat"
                    ON "c"."IDCATEGORIA" = "c_cat"."IDCATEGORIA"
                 WHERE 
                 "c"."FLGACTIVO" = 1
                  '.$q_where.'
                
                 ORDER BY "c"."NOMBRE" ASC
                ';


        $query = $this->db->query($sql);

        $array  = $query->result_array();

        // echo $sql;
        return $query->result_array(); 
    }
    function get_DetalleComisaria($comisaria){

        $q_where = '';

        if($comisaria > 0){
            $q_where .= 'WHERE "IDCOMISARIA" = '.$comisaria.' ';
        }

        $sql = 'SELECT NOMBRE,TELEFONO,
                   (CASE 
                        WHEN IDDEPENDENCIA = 1 THEN \'COMISARIAS\'
                        WHEN IDDEPENDENCIA = 2 THEN \'UNIDADES ESPECIALES\'
                    END) AS DEPENDENCIA,
                    (CASE 
                        WHEN IDCLASE = 1 THEN \'COMISARIA BASICA\'
                        WHEN IDCLASE = 2 THEN \'COMISARIA ESPECIALIZADA\'
                    END) AS CLASE,
                    (CASE 
                        WHEN IDTIPO = 1 THEN \'COMISARIA TIPO A\'
                        WHEN IDTIPO = 2 THEN \'COMISARIA TIPO B\'
                        WHEN IDTIPO = 3 THEN \'COMISARIA TIPO C\'
                        WHEN IDTIPO = 4 THEN \'COMISARIA TIPO D\'
                        WHEN IDTIPO = 5 THEN \'COMISARIA TIPO E\'
                    END) AS TIPO,
                    (CASE 
                        WHEN IDCATEGORIA = 1 THEN \'SECTORIAL\'
                        WHEN IDCATEGORIA = 2 THEN \'NO SECTORIAL\'
                   END ) AS CATEGORIA FROM "SISGESPATMI"."TM_COMISARIA"
                    
                   '.$q_where.'';

        $query = $this->db->query($sql);

        $rows = $query->result_array();
        return $rows[0];   

    }

    public function get_Comisarias($ubigeo = ''){

        if($ubigeo!='' && $ubigeo!='0'){
            $this->db->select('IDCOMISARIA as "ComisariaID", NOMBRE as "ComisariaNombre"');
            $this->db->from($this->model_name); 
            $this->db->where('SUBSTR("IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\'');
            if($ubigeo!='' && $ubigeo!=substr($ubigeo,0,4).'00'){
                $this->db->where('"IDUBIGEO" like \''.$ubigeo.'%\'AND FLGACTIVO = 1');
            }
            $this->db->where('"IDUBIGEO" not like \'%0000\' AND "IDUBIGEO" not like \'%00\'');
            $this->db->where('"NOMBRE" IS NOT NULL');
            $this->db->order_by('NOMBRE', 'asc');

            $query = $this->db->get();

            if($query){
                return $query->result_array();
            }else{
                return array();
            }
        }else{
            return array();
        }
    }
	
	
}