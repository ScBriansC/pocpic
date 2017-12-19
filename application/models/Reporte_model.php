<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Rutasync_Model , entidad de la tabla "TH_RUTA_SYNC"
 */
class Reporte_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
    function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_RUTA_SYNC', 'IDRUTASYNC');
        
    }
    function get_distancia($periodo, $fechaini, $fechafin, $ubigeo, $idcomisaria='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;


        if($idcomisaria > 0){
            $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
            $tipo_ubigeo = 3;
        }
        else{
            if($ubigeo!='' && $ubigeo!='0'){
                if(substr($ubigeo,2,4) == '0000'){ //Departamento
                    $q_where .= ' AND SUBSTR("A"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
                    $tipo_ubigeo = 1;
                }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                    $q_where .= ' AND SUBSTR("A"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
                    $tipo_ubigeo = 2;
                }else{ //Distrito
                    $q_where .= ' AND "A"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
                    $tipo_ubigeo = 3;
                }
            }
        }

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RT"."FECHAREG",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RT"."FECHAREG",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RT"."FECHAREG",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RT"."FECHAREG",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RT"."FECHAREG",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RT"."FECHAREG",\'YYYY\')';
        }



        if($tipo_ubigeo == 0){
            $columnas = 'NVL("A"."DEPARTAMENTO", \'SIN UBIGEO\') as "RptLocalidad", '.$periodo_col.', SUM( "RT"."DISTANCIA") as "RptDistancia"';
            $grupo = '"A"."DEPARTAMENTO", '.$periodo_gpo.'';
            $orden = '1,2,3';
        }elseif($tipo_ubigeo == 1){
            $columnas = '(NVL("A"."PROVINCIA", \'SIN UBIGEO\')) as "RptLocalidad", '.$periodo_col.', SUM( "RT"."DISTANCIA") as "RptDistancia"';
            $grupo = '"A"."DEPARTAMENTO", "A"."PROVINCIA", '.$periodo_gpo.'';
            $orden = '1,2,3,4';
        }elseif($tipo_ubigeo == 2){
            $columnas = '(NVL("A"."DISTRITO", \'SIN UBIGEO\')) as "RptLocalidad", '.$periodo_col.', SUM( "RT"."DISTANCIA") as "RptDistancia"';
            $grupo = '"A"."DEPARTAMENTO", "A"."PROVINCIA", "A"."DISTRITO", '.$periodo_gpo.'';
            $orden = '1,2,3,4,5';

        }elseif($tipo_ubigeo == 3){
            $columnas = '(NVL("C"."NOMBRE", \'SIN COMISARÍA\')) as "RptLocalidad", '.$periodo_col.', SUM( "RT"."DISTANCIA") as "RptDistancia"';
            $grupo = '"A"."DEPARTAMENTO", "A"."PROVINCIA", "A"."DISTRITO", "C"."NOMBRE", '.$periodo_gpo.'';
            $orden = '1,2,3,4,5,6';
        }

        $orden = '2,1,3';



        $sql = 'SELECT '.$columnas.'
                        FROM "SISGESPATMI"."TH_RESUMEN_TURNO" "RT"
                        INNER JOIN "SISGESPATMI"."TM_RADIO" "R"
                        ON "RT".IDRADIO = "R".IDRADIO
                        INNER JOIN "SISGESPATMI".TM_COMISARIA "C"
                        ON "R".IDCOMISARIA = "C".IDCOMISARIA
                        INNER JOIN "SISGESPATMI"."TB_UBIGEO" "A"
                        ON "C".IDUBIGEO = "A".IDUBIGEO 
                        WHERE 1=1
                        '.$q_where.'
                        AND TRUNC("RT"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
        $query = $this->db->query($sql);
        return $query->result_array();

        // echo $sql;
    }


    function get_notrans($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }

        if($tipo == 0){

            $cabecera = '"MACREG".IDINSTITUCION as "codigoMACRO",NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",';
            $grupo = '"MACREG".IDINSTITUCION,"MACREG"."NOMBRE",';
            $select = '"codigoMACRO" as "DependenciaID","macroREGION" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoMACRO","macroREGION","DependenciaNivel"';
            $orden = '1,2,3';
        }elseif($tipo == 1){    
            $cabecera = '"REGPOL".IDINSTITUCION as "codigoREGION",NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",';
            $select = '"codigoREGION" as "DependenciaID","regionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoREGION","regionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){           
            $cabecera = '"DIVTER".IDINSTITUCION as "codigoDIVISION",NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",';
            $grupo = '"DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",';
            $select = '"codigoDIVISION" as "DependenciaID","divisionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDIVISION","divisionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 3 || $tipo == 4){
            $cabecera ='"INS"."IDINSTITUCION" as "codigoDEPENDENCIA",NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE",';
            $select = '"codigoDEPENDENCIA" as "DependenciaID","dependencia" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDEPENDENCIA","dependencia","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }
        $orden = '2,1,3';
        $sql = 'WITH BASE AS (
                    SELECT 
                        '.$cabecera.'
                         '.$tipo.' as "DependenciaNivel",
                        "R".IDDISPOGPS as "IdRadio",
                        "R".PLACA as "Placa",
                        "RD".TURNO as "Turno",
                        '.$periodo_col.',
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"                      
                        FROM SISGESPATMI.TH_TRACKER_NOTRANS "RD"
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1                                           
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)

                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY 
                        '.$grupo.'
                        R.IDDISPOGPS,
                        R.PLACA,
                        RD.TURNO,
                        '.$periodo_gpo.'
                )
                        SELECT '.$select.', "RptPeriodo", 
                        COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                        TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                        SUM("RptTiempo") AS "RptTiempo",
                        (SUM("RptTiempo")/COUNT(DISTINCT "IdRadio")) as "RptRatioTiempo",
                        ROUND(SUM("RptVeces")/COUNT(DISTINCT "IdRadio"),2) as "RptRatioVeces",
                        SUM("RptVeces") AS "RptVeces"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';


        $query = $this->db->query($sql);
        return $query->result_array();
        // echo $sql;
    }

    function get_notrans_xls($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }

        if($tipo == 0){

            $cabecera = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION"';
            $grupo = '"MACREG"."NOMBRE",';
            $select = '"macroREGION" as "RptLocalidad"';
            $group = '"macroREGION"';
            $orden = '1,2,3';
            
        }elseif($tipo == 1){    
            $cabecera = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL"';
            $grupo = '"REGPOL"."NOMBRE",';
            $select = '"regionPOLICIAL" as "RptLocalidad"';
            $group = '"regionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){           
            $cabecera = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL"';
            $grupo = '"DIVTER"."NOMBRE",';
            $select = '"divisionPOLICIAL" as "RptLocalidad"';
            $group = '"divisionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $cabecera ='NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia"';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE",';
            $select = '"dependencia" as "RptLocalidad"';
            $group = '"dependencia"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2,1,3';



        $sql = 'WITH BASE AS (
                    SELECT 
                        '.$cabecera.',
                        "R".IDDISPOGPS as "IdRadio",
                        "R".PLACA as "Placa",
                        "RD".TURNO as "Turno",
                        '.$periodo_col.',
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"   
                        FROM SISGESPATMI.TH_TRACKER_NOTRANS "RD"
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1                                           
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
             
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY 
                        '.$grupo.'
                        R.IDDISPOGPS,
                        R.PLACA,
                        RD.TURNO,
                        '.$periodo_gpo.'
                )
                        SELECT '.$select.', "RptPeriodo", 
                       
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                        TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                        TO_CHAR(TRUNC(SUM("RptTiempo")/3600),\'FM9900\') || \':\' ||
                        TO_CHAR(TRUNC(MOD(SUM("RptTiempo"),3600)/60),\'FM00\') || \':\' ||
                        TO_CHAR(MOD(SUM("RptTiempo"),60),\'FM00\') AS "RptTiempo",
                       SUM("RptVeces") AS "RptVeces",
                        COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                        TO_CHAR(TRUNC((SUM("RptTiempo")/COUNT(DISTINCT "IdRadio"))/3600),\'FM9900\') || \':\' ||
                        TO_CHAR(TRUNC(MOD((SUM("RptTiempo")/COUNT(DISTINCT "IdRadio")),3600)/60),\'FM00\') || \':\' ||
                        TO_CHAR(MOD((SUM("RptTiempo")/COUNT(DISTINCT "IdRadio")),60),\'FM00\') as "RptRatioTiempo",
                        ROUND(SUM("RptVeces")/COUNT(DISTINCT "IdRadio"),2) as "RptRatioVeces"                      
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';


        $query = $this->db->query($sql);
        return $query->result_array();
                        // echo $sql;
    }

    function get_notrans_det($dependencia='', $nivel,$fecha ,$periodo)
    {
       $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;


        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }

        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno","Proveedor"';           
        $group = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "IdRadio","Placa", "Turno","Proveedor"';

        $orden = '2,1,3';


        $sql = 'WITH BASE AS (
                    SELECT NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                        "R".IDPNPRADIO as "IdRadio",
                        "R".IDDISPOGPS as "iddispogps",
                        "R".PLACA as "Placa",
                        (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                        '.$periodo_col.',
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces",
                        PA.NOMBRE as "Proveedor"
                        FROM SISGESPATMI.TH_TRACKER_NOTRANS "RD"
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        INNER JOIN SISGESPATMI.TM_PROVEEDOR PA ON PA.IDPROVEEDOR = R.IDPROVEEDOR
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1=1
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDPATRULLAJE IN(1,2,4)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                        AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                        GROUP BY 
                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                        R.IDPNPRADIO,
                        R.IDDISPOGPS,
                        R.PLACA,
                        RD.TURNO,
                        PA.NOMBRE,
                        '.$periodo_gpo.'
                )
                        SELECT '.$select.', "RptPeriodo", 
                        COUNT(DISTINCT "iddispogps") AS "RptVehiculos", TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo", 
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                         SUM("RptTiempo") AS "RptTiempo",
                        (SUM("RptTiempo")/COUNT(DISTINCT "iddispogps")) as "RptRatio",
                         SUM("RptVeces") AS "RptVeces","Proveedor"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';



        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
    }

    function get_notrans_det_xls($dependencia='', $nivel,$fecha ,$periodo)
    {
       $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;


        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }


        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno"';           
        $group = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "IdRadio","Placa", "Turno"';

        $orden = '2,1,3';



        $sql = 'WITH BASE AS (
                    SELECT 
                        NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                        "R".ETIQUETA as "IdRadio",
                        "R".PLACA as "Placa",
                        (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                        '.$periodo_col.',
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"
                      
                        FROM SISGESPATMI.TH_RUTA_NOTRANS "RD"
                        INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDCOMISARIA = INS.IDANTIGUO
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1
                                            
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                        AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                        GROUP BY 
                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                        R.ETIQUETA,
                        R.PLACA,
                        RD.TURNO,
                        '.$periodo_gpo.'
                )
                    SELECT '.$select.', 
                    TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                    TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo", 
                    TO_CHAR(TRUNC(SUM("RptTiempo")/3600),\'FM9900\') || \':\' ||
                    TO_CHAR(TRUNC(MOD(SUM("RptTiempo"),3600)/60),\'FM00\') || \':\' ||
                    TO_CHAR(MOD(SUM("RptTiempo"),60),\'FM00\') AS "RptTiempo",
                    SUM("RptVeces") AS "RptVeces"
                    FROM BASE 
                    WHERE "RptTiempo" > 0
                    GROUP BY '.$group.', "RptPeriodo"
                    order by 1,2,3';

        $query = $this->db->query($sql);
        return $query->result_array(); 

        // echo $sql;
    }

    // FIN REPORTE DEJO DE TRANSMITIR


    //REPORTE SALIO DE JURISDICCION
    
    function get_salio($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';


        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }


        if($tipo == 0){

            $cabecera = '"MACREG".IDINSTITUCION as "codigoMACRO",NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",';
            $grupo = '"MACREG".IDINSTITUCION,"MACREG"."NOMBRE",';
            $select = '"codigoMACRO" as "DependenciaID","macroREGION" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoMACRO","macroREGION","DependenciaNivel"';
            $orden = '1,2,3';
            
        }elseif($tipo == 1){    
            $cabecera = '"REGPOL".IDINSTITUCION as "codigoREGION",NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",';
            $select = '"codigoREGION" as "DependenciaID","regionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoREGION","regionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){           
            $cabecera = '"DIVTER".IDINSTITUCION as "codigoDIVISION",NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",';
            $grupo = '"DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",';
            $select = '"codigoDIVISION" as "DependenciaID","divisionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDIVISION","divisionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $cabecera ='"INS"."IDINSTITUCION" as "codigoDEPENDENCIA",NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE",';
            $select = '"codigoDEPENDENCIA" as "DependenciaID","dependencia" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDEPENDENCIA","dependencia","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2,1,3';

        $sql = 'WITH BASE AS (
                    SELECT 
                        '.$cabecera.'
                        '.$tipo.' as "DependenciaNivel",
                        "R".IDRADIO as "IdRadio",
                        "R".PLACA as "Placa",
                        "RD".TURNO as "Turno",
                        '.$periodo_col.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\') as "Hora",
                        MAX("RD".FECHALOC) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        ((MAX("RD".FECHALOC)-MIN("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"                      
                        FROM SISGESPATMI.TH_RUTA_SALE "RD"
                        INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDCOMISARIA=INS.IDANTIGUO
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1                                           
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
                        '.$q_where.'
                          AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                         GROUP BY
                                '.$grupo.'
                                "R".IDRADIO,
                                R.PLACA,
                                RD.TURNO,
                                TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\'),
                                '.$periodo_gpo.'
                        ORDER BY 2, 1                
                )
                        SELECT '.$select.', 
                            "RptPeriodo",
                           COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                           TO_CHAR(MAX("Maximo"), \'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                           TO_CHAR(MIN("Minimo"), \'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                           SUM("RptTiempo") AS "RptTiempo",
                           (SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")) as "RptRatioTiempo",
                           ROUND(SUM("RptVeces") / COUNT(DISTINCT "IdRadio"), 2) as "RptRatioVeces",
                           SUM("RptVeces") AS "RptVeces"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';

         $query = $this->db->query($sql);
         return $query->result_array();
         // echo $sql;
    }
    function get_salio_det($dependencia='', $nivel,$fecha ,$periodo)
    {

       $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }

        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno"';           
        $group = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "IdRadio","Placa", "Turno"';

        $orden = '2,1,3';



        $sql = 'WITH BASE AS (
                    SELECT 
                        NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",

                        "R".ETIQUETA as "IdRadio",
                        "R".PLACA as "Placa",
                        (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                        '.$periodo_col.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\') as "Hora",
                        MAX("RD".FECHALOC) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        ((MAX("RD".FECHALOC)-MIN("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"
                        FROM SISGESPATMI.TH_RUTA_SALE "RD"
                        INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDCOMISARIA = INS.IDANTIGUO
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                        AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                        GROUP BY 
                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                        R.ETIQUETA,
                        R.PLACA,
                        RD.TURNO,
                        '.$periodo_gpo.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\')
                )
                        SELECT '.$select.', "RptPeriodo", 
                        COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                        TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo", 
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo", 
                        SUM("RptTiempo") AS "RptTiempo",
                        (SUM("RptTiempo")/COUNT(DISTINCT "IdRadio")) as "RptRatio",
                         SUM("RptVeces") AS "RptVeces"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';

        $query = $this->db->query($sql);
        return $query->result_array();
        // echo  $sql;
        // echo "asdasd";
    }

    function get_salio_xls($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';


        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }


        if($tipo == 0){

            $cabecera = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION"';
            $grupo = '"MACREG"."NOMBRE",';
            $select = '"macroREGION" as "RptLocalidad"';
            $group = '"macroREGION"';
            $orden = '1,2,3';
            
        }elseif($tipo == 1){    
            $cabecera = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL"';
            $grupo = '"REGPOL"."NOMBRE",';
            $select = '"regionPOLICIAL" as "RptLocalidad"';
            $group = '"regionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){           
            $cabecera = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL"';
            $grupo = '"DIVTER"."NOMBRE",';
            $select = '"divisionPOLICIAL" as "RptLocalidad"';
            $group = '"divisionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $cabecera ='NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia"';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE",';
            $select = '"dependencia" as "RptLocalidad"';
            $group = '"dependencia"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2,1,3';

        $sql = 'WITH BASE AS (
                    SELECT 
                        '.$cabecera.',
                        "R".IDRADIO as "IdRadio",
                        "R".PLACA as "Placa",
                        "RD".TURNO as "Turno",
                        '.$periodo_col.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\') as "Hora",
                        MAX("RD".FECHALOC) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        ((MAX("RD".FECHALOC)-MIN("RD".FECHALOC))*24*60*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"                      
                        FROM SISGESPATMI.TH_RUTA_SALE "RD"
                        INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDCOMISARIA=INS.IDANTIGUO
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1                                           
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
                        '.$q_where.'
                          AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                         GROUP BY
                                '.$grupo.'
                                "R".IDRADIO,
                                R.PLACA,
                                RD.TURNO,
                                TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\'),
                                '.$periodo_gpo.'
                        ORDER BY 2, 1                
                )
                        SELECT '.$select.', 
                          "RptPeriodo",
                           TO_CHAR(MIN("Minimo"), \'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                           TO_CHAR(MAX("Maximo"), \'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                           TO_CHAR(TRUNC(SUM("RptTiempo")/3600),\'FM9900\') || \':\' ||
                           TO_CHAR(TRUNC(MOD(SUM("RptTiempo"),3600)/60),\'FM00\') || \':\' ||
                           TO_CHAR(MOD(SUM("RptTiempo"),60),\'FM00\') AS "RptTiempo",
                           SUM("RptVeces") AS "RptVeces",
                           COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                           TO_CHAR(TRUNC((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio"))/3600),\'FM9900\') || \':\' ||
                        TO_CHAR(TRUNC(MOD((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")),3600)/60),\'FM00\') || \':\' ||
                        TO_CHAR(MOD((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")),60),\'FM00\')  as "RptRatioTiempo",
                           ROUND(SUM("RptVeces") / COUNT(DISTINCT "IdRadio"), 2) as "RptRatioVeces"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';

         $query = $this->db->query($sql);
         return $query->result_array();
         // echo $sql;
    }

    function rpt_salio_det_xls($dependencia='', $nivel,$fecha ,$periodo)
    {

       $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }

        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno"';           
        $group = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "IdRadio","Placa", "Turno"';

        $orden = '2,1,3';



        $sql = 'WITH BASE AS (
                    SELECT 
                        NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",

                        "R".ETIQUETA as "IdRadio",
                        "R".PLACA as "Placa",
                        (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                        '.$periodo_col.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\') as "Hora",
                        MAX("RD".FECHALOC) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        ((MAX("RD".FECHALOC)-MIN("RD".FECHALOC))*24*60*60)  as "RptTiempo",                        
                        COUNT("RD".FECHALOC) AS "RptVeces"
                        FROM SISGESPATMI.TH_RUTA_SALE "RD"
                        INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDCOMISARIA = INS.IDANTIGUO
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                        AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                        GROUP BY 
                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                        R.ETIQUETA,
                        R.PLACA,
                        RD.TURNO,
                        '.$periodo_gpo.',
                        TO_CHAR("RD".FECHALOC, \'YYYYMMDDHH24\')
                )
                        SELECT '.$select.', 
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo", 
                        TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo",                        
                        TO_CHAR(TRUNC(SUM("RptTiempo")/3600),\'FM9900\') || \':\' ||
                        TO_CHAR(TRUNC(MOD(SUM("RptTiempo"),3600)/60),\'FM00\') || \':\' ||
                        TO_CHAR(MOD(SUM("RptTiempo"),60),\'FM00\') AS "RptTiempo",
                         SUM("RptVeces") AS "RptVeces"
                        FROM BASE 
                        WHERE "RptTiempo" > 0
                        GROUP BY '.$group.', "RptPeriodo"
                        order by 1,2,3';

        $query = $this->db->query($sql);
        return $query->result_array();
        // echo  $sql;
        // echo "asdasd";
    }

 


    // FIN DE REPORTE SALIO DE JURISDICCION

    //REPORTE DE GALONES POR KILOMETROS RECORRIDOS

    function get_kmxgalones($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'YYYY\')';
        }

        if($tipo == 0){
            $columnas = 'NVL("MACREG"."IDINSTITUCION", 0) AS "DependenciaID", 0 as "DependenciaNivel", NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "RptLocalidad", '.$periodo_col.',';
            $grupo = '"MACREG"."IDINSTITUCION","MACREG"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."IDINSTITUCION", 0) AS "DependenciaID", 1 as "DependenciaNivel", NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"REGPOL"."IDINSTITUCION","REGPOL"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."IDINSTITUCION", 0) AS "DependenciaID",2 as "DependenciaNivel", NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN TERRITORIAL\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"DIVTER"."IDINSTITUCION","DIVTER"."NOMBRE",'.$periodo_gpo.'';
            $orden = '';

        }elseif($tipo == 3 || $tipo == 4 ){
            $columnas = 'NVL("INS"."IDINSTITUCION", 0) AS "DependenciaID", 3 as "DependenciaNivel", NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"INS"."IDINSTITUCION","INS"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }


        $orden = '2';

        // if($tiene_placa){
        //     $q_where .= ' AND RA.PLACA IS NOT NULL';
        // }

        // TRUNC(((SUM(TP.DISTANCIA)/1000)*0.2648),2) as "Galones", 100 AS "ABC"

        $sql = 'SELECT '.$columnas.'
                        COUNT(DISTINCT R.IDDISPOGPS) as "CantidadVehiculo",
                        TRUNC((SUM(TP.DISTANCIA)/1000),2) as "KmRecorrido",
                        TRUNC(((SUM(TP.DISTANCIA)/1000)*0.0248),2) as "Galones",
                        (TRUNC(((SUM(TP.DISTANCIA) / 1000) * 0.0248), 2) / COUNT(DISTINCT R.IDDISPOGPS)) as "Ratio"
                        FROM SISGESPATMI.TH_TRACKER_RESUMEN TP
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "TP"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1=1
                        AND R.FLGACTIVO = 1 
                        AND R.IDPATRULLAJE IN (1,2,4)
                        AND R.PLACA IS NOT NULL
                        AND TP.FECHALOCFIN IS NOT NULL
                        AND TRUNC("TP"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        '.$q_where.'
                        GROUP BY '.$grupo.' 
                        ORDER BY '.$orden;
         
        $query = $this->db->query($sql);
        return $query->result_array();
        // echo $sql;
        //echo "\n".$sql."\n"; 
        //print_r($this->db->last_query());
    }

    function get_kmxgalones_xls($periodo, $fechaini, $fechafin,$tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("TP"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TP"."FECHALOC",\'YYYY\')';
        }

        if($tipo == 0){
            $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "RptLocalidad", '.$periodo_col.',';
            $grupo = '"MACREG"."IDINSTITUCION","MACREG"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"REGPOL"."IDINSTITUCION","REGPOL"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN TERRITORIAL\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"DIVTER"."IDINSTITUCION","DIVTER"."NOMBRE",'.$periodo_gpo.'';
            $orden = '';

        }elseif($tipo == 3 || $tipo == 4 ){
            $columnas = 'NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", '.$periodo_col.', ';
            $q_where = 'AND "INS"."IDINSTITUCION" IN
                            (SELECT IDINSTITUCION
                            FROM SISGESPATMI.TM_INSTITUCION
                            START WITH IDINSTITUCION = '.$dependencia.'
                            CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            $grupo = '"INS"."IDINSTITUCION","INS"."NOMBRE", '.$periodo_gpo.'';
            $orden = '';
        }

        $orden = '2';

        // if($tiene_placa){
        //     $q_where .= ' AND RA.PLACA IS NOT NULL';
        // }

        // TRUNC(((SUM(TP.DISTANCIA)/1000)*0.2648),2) as "Galones", 100 AS "ABC"

        $sql = 'SELECT '.$columnas.'
                        COUNT(DISTINCT R.IDDISPOGPS) as "CantidadVehiculo",
                        TRUNC((SUM(TP.DISTANCIA)/1000),2) as "KmRecorrido",
                        TRUNC(((SUM(TP.DISTANCIA)/1000)*0.0248),2) as "Galones",
                        ROUND((TRUNC(((SUM(TP.DISTANCIA) / 1000) * 0.0248), 2) / COUNT(DISTINCT R.IDDISPOGPS)),2)  as "Ratio"
                        FROM SISGESPATMI.TH_TRACKER_RESUMEN TP
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "TP"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1=1
                        AND R.FLGACTIVO = 1 
                        AND R.IDPATRULLAJE IN (1,2,4)
                        AND R.PLACA IS NOT NULL
                        AND TP.FECHALOCFIN IS NOT NULL
                        AND TRUNC("TP"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        '.$q_where.'
                        GROUP BY '.$grupo.' 
                        ORDER BY '.$orden;
         
        $query = $this->db->query($sql);

        //echo "\n".$sql."\n"; 
        //print_r($this->db->last_query());
        return $query->result_array(); 
// 
        // echo $sql;
    }

    function get_kmxgalones_det($dependencia='', $nivel,$fecha ,$periodo)
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $q_grupo= '';

        if($fecha > 0){
            if($periodo == 1){
                 $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\')';
                 $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'DD/MM/YYYY\'';
            }elseif($periodo == 2){
                $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'MM/YYYY\')';
                $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'MM/YYYY\'';
            }elseif($periodo == 3){
               $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'YYYY\')';
               $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'YYYY\'';
            }
        }


        $sql = 'SELECT NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                       NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                       NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                       NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                       '.$nivel.' as "DependenciaNivel",
                       "R".IDPNPRADIO as "radioID",
                       R.Placa as "placa",
                       ROUND((SUM(TP.DISTANCIA)/1000),2) as "KmRecorrido",
                       ROUND(((SUM(TP.DISTANCIA)/1000)*0.0248),2) AS "Galon",
                       PA.NOMBRE as "Proveedor"
                       FROM SISGESPATMI.TH_TRACKER_RESUMEN TP
                       INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "TP"."IDDISPOGPS" = "R"."IDDISPOGPS"
                       INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                       INNER JOIN SISGESPATMI.TM_PROVEEDOR PA ON PA.IDPROVEEDOR = R.IDPROVEEDOR
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                       WHERE 1=1
                       AND R.FLGACTIVO = 1
                       AND R.IDPATRULLAJE IN (1,2,4)
                       AND R.PLACA IS NOT NULL
                       AND TP.FECHALOCFIN IS NOT NULL
                       '.$q_where.'
                       AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                       GROUP BY
                                "MACREG"."NOMBRE",
                                "REGPOL"."NOMBRE",
                                "DIVTER"."NOMBRE",
                                "INS"."NOMBRE",
                                R.IDPNPRADIO,
                                R.Placa,
                                PA.NOMBRE,
                                '.$q_grupo.')
                        ORDER BY 2, 1
       
                ';

        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
    }

    function get_kmxgalones_det_xls($dependencia='', $nivel,$fecha ,$periodo)
    {
        $q_where = '';

        $tipo_ubigeo = 0;

        $q_grupo= '';

        if($fecha > 0){
            if($periodo == 1){
                 $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\')';
                 $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'DD/MM/YYYY\'';
            }elseif($periodo == 2){
                $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'MM/YYYY\')';
                $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'MM/YYYY\'';
            }elseif($periodo == 3){
               $q_where .= 'AND TRUNC(TP.FECHALOC) = TO_DATE(\''.$fecha.'\',\'YYYY\')';
               $q_grupo .=  'TO_CHAR("TP"."FECHALOC", \'YYYY\'';
            }
        }


        $sql = 'SELECT NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                       NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                       NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                       NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                       "R".IDPNPRADIO as "radioID",
                       R.Placa as "placa",
                       ROUND((SUM(TP.DISTANCIA)/1000),2) as "KmRecorrido",
                       ROUND(((SUM(TP.DISTANCIA)/1000)*0.0248),2) AS "Galon",
                       PA.NOMBRE as "Proveedor"
                       FROM SISGESPATMI.TH_TRACKER_RESUMEN TP
                       INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "TP"."IDDISPOGPS" = "R"."IDDISPOGPS"
                       INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                       INNER JOIN SISGESPATMI.TM_PROVEEDOR PA ON PA.IDPROVEEDOR = R.IDPROVEEDOR
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                       LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                       WHERE 1=1
                       AND R.FLGACTIVO = 1
                       AND R.IDPATRULLAJE IN (1,2,4)
                       AND R.PLACA IS NOT NULL
                       AND TP.FECHALOCFIN IS NOT NULL
                       '.$q_where.'
                       AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                       GROUP BY
                                "MACREG"."NOMBRE",
                                "REGPOL"."NOMBRE",
                                "DIVTER"."NOMBRE",
                                "INS"."NOMBRE",
                                R.IDPNPRADIO,
                                R.Placa,
                                PA.NOMBRE,
                                '.$q_grupo.')
                        ORDER BY 2, 1
       
                ';

        $query = $this->db->query($sql);
        return $query->result_array(); 
                        // echo $sql;
    }


    // FIN  DE REPORTE GALONES POR KILOMETROS RECORRIDOS

    //REPORTE DE VEHICULOS QUE SE DETUVIERON

    function get_detuvo($periodo, $fechaini, $fechafin, $tipo, $dependencia=''){

        $q_where = '';

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }



        if($tipo == 0){

            $cabecera = '"MACREG".IDINSTITUCION as "codigoMACRO",NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",';
            $grupo = '"MACREG".IDINSTITUCION,"MACREG"."NOMBRE",';
            $select = '"codigoMACRO" as "DependenciaID","macroREGION" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoMACRO","macroREGION","DependenciaNivel"';
            $orden = '1,2,3';
            // $select = 'SUBSTR("UbigeoID",0,2)||\'0000\' as "UbigeoID", 0 as "ComisariaID", "Departamento" AS "RptLocalidad"';           
            // $group = 'SUBSTR("UbigeoID",0,2)||\'0000\' , "Departamento"';
            
        }elseif($tipo == 1){    
            $cabecera = '"REGPOL".IDINSTITUCION as "codigoREGION",NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",';
            $select = '"codigoREGION" as "DependenciaID","regionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoREGION","regionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){           
            $cabecera = '"DIVTER".IDINSTITUCION as "codigoDIVISION",NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",';
            $grupo = '"DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",';
            $select = '"codigoDIVISION" as "DependenciaID","divisionPOLICIAL" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDIVISION","divisionPOLICIAL","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $cabecera ='"INS"."IDINSTITUCION" as "codigoDEPENDENCIA",NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE",';
            $select = '"codigoDEPENDENCIA" as "DependenciaID","dependencia" as "RptLocalidad", "DependenciaNivel" as "DependenciaNivel"';
            $group = '"codigoDEPENDENCIA","dependencia","DependenciaNivel"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2,1,3';


        $sql = 'WITH BASE AS (
                    SELECT
                        '.$cabecera.'
                        '.$tipo.' as "DependenciaNivel",
                        "R".IDDISPOGPS as "IdRadio",                        
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG) - ("RD".FECHALOC)) * 24 * 60) as "RptTiempo",
                        COUNT("RD".FECHALOC) AS "RptVeces",
                        '.$periodo_col.'                    
                        FROM SISGESPATMI.TH_TRACKER_DETIENE "RD"
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL
                        AND "RD".FECHASIG IS NOT NULL
                        AND "R".IDPATRULLAJE IN(1,2,4)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY
                                '.$grupo.'
                                "R".IDDISPOGPS,
                                '.$periodo_gpo.'
                        ORDER BY 2, 1
                    )
                    SELECT  '.$select.',
                           "RptPeriodo",
                           COUNT(DISTINCT "IdRadio") AS "RptVehiculos",
                           TO_CHAR(MAX("Maximo"), \'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                           TO_CHAR(MIN("Minimo"), \'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                           SUM("RptTiempo") AS "RptTiempo",
                           (SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")) as "RptRatioTiempo",
                           ROUND(SUM("RptVeces") / COUNT(DISTINCT "IdRadio"), 2) as "RptRatioVeces",
                           SUM("RptVeces") AS "RptVeces"
                      FROM BASE
                     WHERE "RptTiempo" > 0
                     GROUP BY '.$group.',
                               "RptPeriodo"
                     order by 1, 2, 3';

    
        $query = $this->db->query($sql);
        return $query->result_array();

        // echo $sql;
    }

  
    function get_detuvo_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia='')
    {


        $q_where = '';

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }



        if($tipo == 0){
            $select = '"macroREGION" as "RptLocalidad"';
            $group = '"macroREGION"';
            $orden = '1,2,3';
            // $select = 'SUBSTR("UbigeoID",0,2)||\'0000\' as "UbigeoID", 0 as "ComisariaID", "Departamento" AS "RptLocalidad"';           
            // $group = 'SUBSTR("UbigeoID",0,2)||\'0000\' , "Departamento"';
            
        }elseif($tipo == 1){    
            $select = '"regionPOLICIAL" as "RptLocalidad"';
            $group = '"regionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
            // $select = 'SUBSTR("UbigeoID",0,4)||\'00\' as "UbigeoID", 0 as "ComisariaID", "Provincia" AS "RptLocalidad"';           
            // $group = 'SUBSTR("UbigeoID",0,4)||\'00\' , "Departamento", "Provincia"';
            // $orden = '1,2,3,4';
        }elseif($tipo == 2){           
            // $select = '"UbigeoID", 0 as "ComisariaID", "Distrito" AS "RptLocalidad"';           
            // $group = '"UbigeoID" , "Departamento", "Provincia", "Distrito"';
            // $orden = '1,2,3,4,5';
            $select = '"divisionPOLICIAL" as "RptLocalidad"';
            $group = '"divisionPOLICIAL"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $select = '"dependencia" as "RptLocalidad"';
            $group = '"dependencia"';
            $orden = '1,2,3';
             $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

            // $select = '"UbigeoID" , "ComisariaID", "Comisaria" AS "RptLocalidad"';           
            // $group = '"UbigeoID" , "ComisariaID", "Departamento", "Provincia", "Distrito", "Comisaria"';
            // $orden = '1,2,3,4,5,6';
        }

        $orden = '2,1,3';


        $sql = 'WITH BASE AS (
                    SELECT
                        NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                        '.$tipo.' as "DependenciaNivel",
                        "R".IDDISPOGPS as "IdRadio",                        
                        MAX("RD".FECHASIG) AS "Maximo",
                        MIN("RD".FECHALOC) AS "Minimo",
                        SUM((("RD".FECHASIG) - ("RD".FECHALOC)) * 24 * 60) as "RptTiempo",
                        COUNT("RD".FECHALOC) AS "RptVeces",
                        '.$periodo_col.'                    
                        FROM SISGESPATMI.TH_TRACKER_DETIENE "RD"
                        INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1 = 1
                        AND "R".FLGACTIVO = 1
                        AND "R".PLACA IS NOT NULL
                        AND "RD".FECHASIG IS NOT NULL
                        AND "R".IDPATRULLAJE IN(1,2,4)
                        '.$q_where.'
                        AND TRUNC("RD"."FECHALOC") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY
                                "MACREG"."NOMBRE",
                                "REGPOL"."NOMBRE",
                                "DIVTER"."NOMBRE",
                                "INS"."NOMBRE",
                                "R".IDDISPOGPS,
                                '.$periodo_gpo.'
                        ORDER BY 2, 1
                    )
                    SELECT  '.$select.',
                           "RptPeriodo",
                           TO_CHAR(MIN("Minimo"), \'DD/MM/YYYY HH24:MI:SS\') as "Minimo",
                           TO_CHAR(MAX("Maximo"), \'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                           TO_CHAR(TRUNC(SUM("RptTiempo")/3600),\'FM9900\') || \':\' ||
                           TO_CHAR(TRUNC(MOD(SUM("RptTiempo"),3600)/60),\'FM00\') || \':\' ||
                           TO_CHAR(MOD(SUM("RptTiempo"),60),\'FM00\') AS "RptTiempo",
                           SUM("RptVeces") AS "RptVeces",
                           COUNT(DISTINCT "IdRadio") AS "RptVehiculos",                      
                           TO_CHAR(TRUNC((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio"))/3600),\'FM9900\') || \':\' ||
                           TO_CHAR(TRUNC(MOD((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")),3600)/60),\'FM00\') || \':\' ||
                           TO_CHAR(MOD((SUM("RptTiempo") / COUNT(DISTINCT "IdRadio")),60),\'FM00\')  as "RptRatioTiempo",
                           ROUND(SUM("RptVeces") / COUNT(DISTINCT "IdRadio"), 2) as "RptRatioVeces"
                           
                      FROM BASE
                     WHERE "RptTiempo" > 0
                     GROUP BY '.$group.',
                               "RptPeriodo"
                     order by 1, 2, 3';

    
        $query = $this->db->query($sql);
        return $query->result_array();
    }
  

    // function get_detuvo_det($ubigeo, $idcomisaria='', $fecha ,$periodo)
    function get_detuvo_det($dependencia='', $nivel, $fecha, $periodo)
    {
       
       $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }


        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "Placa", "IdRadio","Turno"';           
        $group = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia", "Placa","IdRadio", "Turno"';

        $orden = '2,1,3';


        $sql = 'WITH BASE AS (
                                SELECT 
                                   NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                                   NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                                   NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                                   NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                                   "R".IDDISPOGPS as "IdRadio",
                                   "R".PLACA as "Placa",
                                   (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                                   '.$periodo_col.',
                                   MAX("RD".FECHASIG) AS "Maximo",
                                   MIN("RD".FECHALOC) AS "Minimo",
                                   SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                                   COUNT("RD".FECHALOC) AS "RptVeces"     
                                   FROM SISGESPATMI.TH_TRACKER_DETIENE "RD"
                                   INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                                   INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                                   WHERE 1 = 1
                                   AND "R".FLGACTIVO = 1
                                   AND "R".PLACA IS NOT NULL 
                                   AND "R".IDPATRULLAJE IN(1,2,4)
                                   AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                                   AND "INS"."IDINSTITUCION" IN
                                   (SELECT IDINSTITUCION
                                   FROM SISGESPATMI.TM_INSTITUCION
                                   START WITH IDINSTITUCION = '.$dependencia.'
                                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                                   GROUP BY 
                                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                                        "R".IDDISPOGPS,
                                        "R".PLACA,
                                        "RD".TURNO,
                                        '.$periodo_gpo.'
                            )
                SELECT '.$select.', "RptPeriodo",
                       COUNT(DISTINCT "IdRadio") AS "RptVehiculos", 
                       TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo", 
                       TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo", 
                       SUM("RptTiempo") AS "RptTiempo",
                       (SUM("RptTiempo")/COUNT(DISTINCT "IdRadio")) as "RptRatio", 
                       SUM("RptVeces") AS "RptVeces"
                FROM BASE 
                WHERE "RptTiempo" > 0
                GROUP BY '.$group.', "RptPeriodo"
                order by 1,2,3';


        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;

    }

    function get_detuvo_det_xls($dependencia='', $nivel, $fecha, $periodo)
    {
       
        $q_where = '';

        $tipo_ubigeo = 0;

        $columnas = '';
        $grupo = '';
        $orden = '';
        $select = '';
        $group = '';
        $periodo_col = '';
        $periodo_gpo = '';
        $periodo = 1;

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("RD"."FECHALOC",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("RD"."FECHALOC",\'YYYY\')';
        }


        $select = '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno"';           
        $group =  '"macroREGION", "regionPOLICIAL","divisionPOLICIAL","dependencia","IdRadio","Placa","Turno"';

        $orden = '2,1,3';


        $sql = 'WITH BASE AS (
                                SELECT 
                                   NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                                   NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                                   NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN REGIÓN\') as "divisionPOLICIAL",
                                   NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                                   "R".IDDISPOGPS as "IdRadio",
                                   "R".PLACA as "Placa",
                                   (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
                                   '.$periodo_col.',
                                   MAX("RD".FECHASIG) AS "Maximo",
                                   MIN("RD".FECHALOC) AS "Minimo",
                                   SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60)  as "RptTiempo",                        
                                   COUNT("RD".FECHALOC) AS "RptVeces"     
                                   FROM SISGESPATMI.TH_TRACKER_DETIENE "RD"
                                   INNER JOIN SISGESPATMI.TM_DISPOGPS "R" ON "RD"."IDDISPOGPS" = "R"."IDDISPOGPS"
                                   INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON R.IDINSTITUCION = INS.IDINSTITUCION
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                                   LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                                   WHERE 1 = 1
                                   AND "R".FLGACTIVO = 1
                                   AND "R".PLACA IS NOT NULL AND "R".IDPATRULLAJE IN(1,2,4)
                                   AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                                   AND "INS"."IDINSTITUCION" IN
                                   (SELECT IDINSTITUCION
                                   FROM SISGESPATMI.TM_INSTITUCION
                                   START WITH IDINSTITUCION = '.$dependencia.'
                                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                                   GROUP BY 
                                        "MACREG".IDINSTITUCION,"MACREG"."NOMBRE",
                                        "REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE",
                                        "DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE",
                                        "INS"."IDINSTITUCION","INS"."NOMBRE",
                                        "R".IDDISPOGPS,
                                        "R".PLACA,
                                        "RD".TURNO,
                                        '.$periodo_gpo.'
                            )
                SELECT '.$select.', 
                        TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo", 
                        TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo",
                        TO_CHAR(TRUNC(SUM("RptTiempo") /3600),\'FM9900\') || \':\' ||
                        TO_CHAR(TRUNC(MOD(SUM("RptTiempo") ,3600)/60),\'FM00\') || \':\' ||
                        TO_CHAR(MOD(SUM("RptTiempo") ,60),\'FM00\') AS "RptTiempo",
                        SUM("RptVeces") AS "RptVeces"
                FROM BASE 
                WHERE "RptTiempo" > 0
                GROUP BY '.$group.', "RptPeriodo"
                order by 1,2,3';


        $query = $this->db->query($sql);
        return $query->result_array(); 


        // $sql = 'WITH BASE AS (
        //             SELECT 
        //                 "A"."IDUBIGEO" as "UbigeoID", "C".IDCOMISARIA as "ComisariaID",
        //                 "A".DEPARTAMENTO as "Departamento",
        //                 "A".PROVINCIA as "Provincia",
        //                 "A".DISTRITO as "Distrito",
        //                 "C".NOMBRE as "Comisaria",
        //                 "R".ETIQUETA as "IdRadio",
        //                 "R".PLACA as "Placa",
        //                 (CASE "RD".TURNO WHEN \'1\' THEN \'MADRUGADA\' WHEN \'2\' THEN \'MAÑANA\' WHEN \'3\' THEN \'TARDE\' WHEN \'4\' THEN \'NOCHE\' ELSE \'\' END) as "Turno",
        //                 '.$periodo_col.',
        //                 MAX("RD".FECHASIG) AS "Maximo",
        //                 MIN("RD".FECHALOC) AS "Minimo",
        //                 SUM((("RD".FECHASIG)-("RD".FECHALOC))*24*60*60)  as "RptTiempo",                        
        //                 COUNT("RD".FECHALOC) AS "RptVeces"
                      
        //                 FROM SISGESPATMI.TH_RUTA_DETIENE "RD"
        //                 INNER JOIN SISGESPATMI.TM_RADIO "R" ON "RD"."IDRADIO" = "R"."IDRADIO"
        //                 INNER JOIN SISGESPATMI.TM_COMISARIA "C" ON "C"."IDCOMISARIA" = "R"."IDCOMISARIA"
        //                 INNER JOIN SISGESPATMI.TB_UBIGEO "A" ON "A"."IDUBIGEO" = "C"."IDUBIGEO"
        //                 WHERE 1=1
                                            
        //                 AND "R".FLGACTIVO = 1
        //                 AND "R".PLACA IS NOT NULL AND "R".IDMODELOVH IN(1,2,3,5)
        //                 '.$q_where.'
        //                 AND TRUNC("RD"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
        //                 GROUP BY 
        //                 "A"."IDUBIGEO", "A"."DEPARTAMENTO",
        //                 A.DEPARTAMENTO,
        //                 A.PROVINCIA,
        //                 A.DISTRITO,
        //                 "C".IDCOMISARIA,
        //                 C.NOMBRE,
        //                 R.ETIQUETA,
        //                 R.PLACA,
        //                 RD.TURNO,
        //                 '.$periodo_gpo.'
        //         )
        //         SELECT '.$select.', 
        //         TO_CHAR(MIN("Minimo"),\'DD/MM/YYYY HH24:MI:SS\') as "Minimo",                        
        //         TO_CHAR(MAX("Maximo"),\'DD/MM/YYYY HH24:MI:SS\') as "Maximo", 
        //         TO_CHAR(TRUNC(SUM("RptTiempo") /3600),\'FM9900\') || \':\' ||
        //         TO_CHAR(TRUNC(MOD(SUM("RptTiempo") ,3600)/60),\'FM00\') || \':\' ||
        //         TO_CHAR(MOD(SUM("RptTiempo") ,60),\'FM00\') AS "RptTiempo",
        //         SUM("RptVeces") AS "RptVeces"
        //         FROM BASE 
        //         WHERE "RptTiempo" > 0
        //         GROUP BY '.$group.', "RptPeriodo"
        //         order by 1,2,3';


        // $query = $this->db->query($sql);
        // return $query->result_array(); 
                        // echo $sql;


    }

    function get_consultar_detuvo_det($ubigeo, $idcomisaria='', $tiene_placa=true)
    {
        $q_where = '';

        $tipo_ubigeo = 0;


        if($idcomisaria > 0){
            $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
            $tipo_ubigeo = 3;
        }
        else{
            if($ubigeo!='' && $ubigeo!='0'){
                if(substr($ubigeo,2,4) == '0000'){ //Departamento
                    $q_where .= ' AND SUBSTR("A"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
                    $tipo_ubigeo = 1;
                }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                    $q_where .= ' AND SUBSTR("A"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
                    $tipo_ubigeo = 2;
                }else{ //Distrito
                    $q_where .= ' AND "A"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
                    $tipo_ubigeo = 3;
                }
            }
        }

        if($tiene_placa){
            $q_where .= ' AND RA.PLACA IS NOT NULL';
        }

        $sql = 'SELECT 
                    C.IDUBIGEO as "UbigeoID",
                    A.DEPARTAMENTO as "UbigeoDepa",
                    A.PROVINCIA as "UbigeoProv",
                    A.DISTRITO as "UbigeoDist",
                    C.IDCOMISARIA as "ComisariaID",
                    C.NOMBRE as "ComisariaNombre",
                    RA.IDRADIO as "RadioID",
                    RA.ETIQUETA as "RadioEtiqueta",
                    RA.PLACA as "RadioPlaca", 
                    RA.MODELO as "RadioModelo",
                    RA.ORIGEN as "RadioOrigen",
                    RA.TEI as "RadioTEI",
                    (CASE WHEN RA.IDMODELOVH IN(1,2,5) THEN \'PATRULLERO\' WHEN RA.IDMODELOVH IN(3) THEN \'MOTORIZADO\' WHEN RA.IDMODELOVH IN(4) THEN \'OTRO\' END) AS "RadioTipo"
                FROM SISGESPATMI.TM_RADIO RA
                INNER JOIN SISGESPATMI.TM_COMISARIA C ON C.IDCOMISARIA = RA.IDCOMISARIA
                INNER JOIN SISGESPATMI.TB_UBIGEO A ON A.IDUBIGEO = C.IDUBIGEO
                WHERE RA.FLGACTIVO = 1 '.$q_where;

        $query = $this->db->query($sql);
        return $query->result_array(); 
    }
    //FIN REPORTE DE VEHICULOS QUE SE DETUVIERON

    // REPORTE DE SESIONES
    function get_sesiones($periodo, $fechaini, $fechafin, $tipo, $dependencia='')
    {
        $q_where = '';



        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'YYYY\')';
        }



        if($tipo == 0){
            $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "RptLocalidad", '.$periodo_col.',
                        COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            $grupo = '"MACREG"."NOMBRE", '.$periodo_gpo.'';

            $orden = '1,2,3';
        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", '.$periodo_col.', 
                        COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            $grupo = '"REGPOL"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
            $orden = '1,2,3';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "RptLocalidad", '.$periodo_col.',
                         COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            $grupo = '"DIVTER"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
            $orden = '1,2,3';

        }elseif($tipo == 3 || $tipo ==4){
            $columnas = 'NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", '.$periodo_col.', 
                        COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            $grupo = '"INS"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
            $orden = '1,2,3';
        }

        $orden = '2,1,3';
        $sql = 'SELECT '.$columnas.'
                        FROM "SISGESPATMI".TH_SESION "S"
                        INNER JOIN "SISGESPATMI"."TM_USUARIO" "U" ON "U"."IDUSUARIO" = S."IDUSUARIO"
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "INS" ON "INS".IDANTIGUO = "U".IDCOMISARIA
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1=1
                        '.$q_where.'
                        AND "U"."FLGACTIVO" = 1
                        AND TRUNC("S"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
        $query = $this->db->query($sql);
        // echo $sql;
        return $query->result_array();
    }

    function get_sesiones_xls($periodo, $fechaini, $fechafin, $tipo, $dependencia='')
    {
        $q_where = '';

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("S"."FECHAREG",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("S"."FECHAREG",\'YYYY\')';
        }

         $orden = '1,2,3,4,5,6,7,8,9,10,11';

      if($tipo == 0){
            // $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION", '.$periodo_col.',
            //             COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            // $grupo = '"MACREG"."NOMBRE", '.$periodo_gpo.'';


        }elseif($tipo == 1){
            // $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", '.$periodo_col.', 
            //             COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            // $grupo = '"REGPOL"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
           
        }elseif($tipo == 2){
            // $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "RptLocalidad", '.$periodo_col.',
            //              COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            // $grupo = '"DIVTER"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
      

        }elseif($tipo == 3 || $tipo ==4){
            // $columnas = 'NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", '.$periodo_col.', 
            //             COUNT(DISTINCT "S"."IDUSUARIO") as "RptCantidad"';
            // $grupo = '"INS"."NOMBRE", '.$periodo_gpo.'';
            $q_where = ' AND "INS"."IDINSTITUCION" IN
                        (SELECT IDINSTITUCION
                        FROM SISGESPATMI.TM_INSTITUCION
                        START WITH IDINSTITUCION = '.$dependencia.'
                        CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';

        }

        $orden = '2,1,3';
        $sql = 'SELECT '.$columnas.'
                        NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                        NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                        NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                        NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia"
                        , '.$periodo_col.', 
                        ("U"."NOMBRE"||\' \'||"U"."APELLIDO") as "UsuarioNombre",
                        "U"."USUARIOCOD" AS "UsuarioCodigo", 
                        (CASE WHEN "S"."FLGMOVREG" = \'1\' THEN \'SI\' ELSE \'NO\' END) AS "SesionFlagMovil",
                        "S"."IPMAQREG" AS "SesionIP", 
                        TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY HH24:MI:SS\') AS "SesionFechaIni", 
                        TO_CHAR("S"."FECHAFIN",\'DD/MM/YYYY HH24:MI:SS\') AS "SesionFechaFin"
                        FROM "SISGESPATMI".TH_SESION "S"
                        INNER JOIN "SISGESPATMI"."TM_USUARIO" "U" ON "U"."IDUSUARIO" = S."IDUSUARIO"
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "INS" ON "INS".IDANTIGUO = "U".IDCOMISARIA
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE 1=1
                        '.$q_where.'
                        AND "U"."FLGACTIVO" = 1
                        AND TRUNC("S"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
  
                        ORDER BY '.$orden;
        $query = $this->db->query($sql);
        // echo $sql;
        return $query->result_array();
    }
    // FIN REPORTE DE SESIONES

    //REPORTE DE INVENTARIOS

    function get_inventario($tipo, $dependencia='')
    {
        $q_where = '';

        $columnas = '';
        $grupo = '';
        $orden = '';


        if($tipo == 0){
           $columnas = '"MACREG".IDINSTITUCION as "DependenciaID", NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\')  as "RptLocalidad",';
            $grupo = '"MACREG".IDINSTITUCION,"MACREG"."NOMBRE"';
            $orden = '';

        }elseif($tipo == 1){
            $columnas = '"REGPOL".IDINSTITUCION as "DependenciaID",NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", ';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){
            $columnas = '"DIVTER".IDINSTITUCION as "DependenciaID",NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\')  as "RptLocalidad",';
            $grupo = '"DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $columnas = '"INS"."IDINSTITUCION" as"DependenciaID", NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", ';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2';

        $sql = 'SELECT '.$columnas.'
                        '.$tipo.' as "DependenciaNivel",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(1) THEN "RA".IDDISPOGPS END) AS "RptPatrullero",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(2) THEN "RA".IDDISPOGPS END) AS "RptMotorizado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(3) THEN "RA".IDDISPOGPS END) AS "RptPatpie",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(4) THEN "RA".IDDISPOGPS END) AS "RptPatintegrado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(5) THEN "RA".IDDISPOGPS END) AS "RptBase",
                        COUNT("RA".IDDISPOGPS) AS "RptTotal"
                        FROM SISGESPATMI.TM_DISPOGPS "RA"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE "RA".FLGACTIVO = 1 '.$q_where.'
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
         
        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
    }


    function get_inventario_det($tipo, $dependencia='')
    {
        $q_where = '';

        $sql = 'SELECT 
                    NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                    NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                    NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                    NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                    '.$tipo.' as "DependenciaNivel",
                    RA.IDPNPRADIO as "RadioEtiqueta",
                    RA.PLACA as "RadioPlaca", 
                    RA.MODELO as "RadioModelo",
                    RA.ORIGEN as "RadioOrigen",
                    RA.SERIE as "RadioSerie",
                    PRO.NOMBRE as "RadioProveedor",
                    (CASE WHEN RA.IDPATRULLAJE IN(1) THEN \'PATRULLERO\' 
                          WHEN RA.IDPATRULLAJE IN(2) THEN \'MOTORIZADO\' 
                          WHEN RA.IDPATRULLAJE IN(3) THEN \'PATRULLAJE A PIE\' 
                          WHEN RA.IDPATRULLAJE IN(4) THEN \'PATRULLAJE INTEGRADO\' 
                          WHEN RA.IDPATRULLAJE IN(5) THEN \'BASE\' 
                     END) 
                     AS "RadioTipo",
                    (CASE 
                          WHEN RA.FLGACTIVO IN(1) THEN \'OPERATIVO\'
                          WHEN RA.FLGACTIVO IN(0) THEN \'INOPERATIVO\'
                     END) 
                     AS "RadioEstado"
                FROM SISGESPATMI.TM_DISPOGPS RA
                INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                INNER JOIN SISGESPATMI.TM_PROVEEDOR PRO   ON RA.IDPROVEEDOR = PRO.IDPROVEEDOR
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                WHERE 1=1
                AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)

                ';

        $query = $this->db->query($sql);
        return $query->result_array(); 

        // echo $sql;

    }

    function get_inventario_xls($tipo, $dependencia='')
    {
        $q_where = '';

        $columnas = '';
        $grupo = '';
        $orden = '';

      if($tipo == 0){
           $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\')  as "RptLocalidad",';
            $grupo = '"MACREG"."NOMBRE"';
            $orden = '';

        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", ';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\')  as "RptLocalidad",';
            $grupo = '"DIVTER"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';

        }elseif($tipo == 3 || $tipo == 4){
            $columnas = 'NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", ';
            $grupo = '"INS"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2';

        if($tiene_placa){
            $q_where .= ' AND RA.PLACA IS NOT NULL';
        }

       $sql = 'SELECT '.$columnas.'
                         COUNT(CASE WHEN "RA".IDPATRULLAJE IN(1) THEN "RA".IDDISPOGPS END) AS "RptPatrullero",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(2) THEN "RA".IDDISPOGPS END) AS "RptMotorizado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(3) THEN "RA".IDDISPOGPS END) AS "RptPatpie",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(4) THEN "RA".IDDISPOGPS END) AS "RptPatintegrado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(5) THEN "RA".IDDISPOGPS END) AS "RptBase",
                        COUNT("RA".IDDISPOGPS) AS "RptTotal"
                        FROM SISGESPATMI.TM_DISPOGPS "RA"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE "RA".FLGACTIVO = 1 '.$q_where.'
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
         
        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
    }

    function get_inventario_det_xls($tipo, $dependencia='')
    {
        $q_where = '';

      $sql = 'SELECT 
                    NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                    NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                    NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                    NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                    RA.IDPNPRADIO as "RadioEtiqueta",
                    RA.PLACA as "RadioPlaca", 
                    RA.SERIE as "RadioSerie",
                    PRO.NOMBRE as "RadioProveedor",
                    (CASE WHEN RA.IDPATRULLAJE IN(1) THEN \'PATRULLERO\' 
                          WHEN RA.IDPATRULLAJE IN(2) THEN \'MOTORIZADO\' 
                          WHEN RA.IDPATRULLAJE IN(3) THEN \'PATRULLAJE A PIE\' 
                          WHEN RA.IDPATRULLAJE IN(4) THEN \'PATRULLAJE INTEGRADO\' 
                          WHEN RA.IDPATRULLAJE IN(5) THEN \'BASE\' 
                     END) 
                     AS "RadioTipo",
                    (CASE 
                          WHEN RA.FLGACTIVO IN(1) THEN \'OPERATIVO\'
                          WHEN RA.FLGACTIVO IN(0) THEN \'INOPERATIVO\'
                     END) 
                     AS "RadioEstado"
                FROM SISGESPATMI.TM_DISPOGPS RA
                INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                INNER JOIN SISGESPATMI.TM_PROVEEDOR PRO   ON RA.IDPROVEEDOR = PRO.IDPROVEEDOR
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                WHERE 1=1
                AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                ';

        $query = $this->db->query($sql);
        return $query->result_array(); 

        // echo $sql;
    }


    //FIN DE REPORTE DE INVENTARIOS



    function get_transmisiones($fechaini, $tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;


        $columnas = '';
        $grupo = '';
        $orden = '';


        if($tipo == 0){
            $columnas = '"MACREG".IDINSTITUCION as "DependenciaID", NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\')  as "RptLocalidad",';
            $grupo = '"MACREG".IDINSTITUCION,"MACREG"."NOMBRE"';
            $orden = '';

        }elseif($tipo == 1){
            $columnas = '"REGPOL".IDINSTITUCION as "DependenciaID",NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", ';
            $grupo = '"REGPOL".IDINSTITUCION,"REGPOL"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){
            $columnas = '"DIVTER".IDINSTITUCION as "DependenciaID",NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\')  as "RptLocalidad",';
            $grupo = '"DIVTER".IDINSTITUCION,"DIVTER"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 3 || $tipo==4){
            $columnas = '"INS"."IDINSTITUCION" as"DependenciaID", NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", ';
            $grupo = '"INS".IDINSTITUCION,"INS"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2';



        $sql = 'SELECT '.$columnas.'
                        '.$tipo.' as "DependenciaNivel",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(1) THEN "RA".IDDISPOGPS END) AS "RptPatrullero",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(2) THEN "RA".IDDISPOGPS END) AS "RptMotorizado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(3) THEN "RA".IDDISPOGPS END) AS "RptPatpie",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(4) THEN "RA".IDDISPOGPS END) AS "RptPatintegrado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(5) THEN "RA".IDDISPOGPS END) AS "RptBase",
                        COUNT("RA".IDDISPOGPS) AS "RptTotal"  
                        FROM SISGESPATMI.TM_DISPOGPS "RA"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE "RA".FLGACTIVO = 1 '.$q_where.'
                        AND EXISTS(SELECT 1 FROM SISGESPATMI.TH_TRACKER_RESUMEN "H" WHERE TRUNC("H"."FECHALOC") = TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') AND "H"."IDDISPOGPS"="RA"."IDDISPOGPS")
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
         
        $query = $this->db->query($sql);
        return $query->result_array(); 
         echo $sql;
        
        
    }

    function get_transmisiones_xls($fecha, $tipo, $dependencia='')
    {
        $q_where = '';

        $tipo_ubigeo = 0;


        $columnas = '';
        $grupo = '';
        $orden = '';


        if($tipo == 0){
            $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\')  as "RptLocalidad",';
            $grupo = '"MACREG"."NOMBRE"';
            $orden = '';

        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", ';
            $grupo = '"REGPOL"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\')  as "RptLocalidad",';
            $grupo = '"DIVTER"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }elseif($tipo == 3 || $tipo==4){
            $columnas = 'NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "RptLocalidad", ';
            $grupo = '"INS"."NOMBRE"';
            $orden = '';
            $q_where .= 'AND "INS"."IDINSTITUCION" IN
                   (SELECT IDINSTITUCION
                   FROM SISGESPATMI.TM_INSTITUCION
                   START WITH IDINSTITUCION = '.$dependencia.'
                   CONNECT BY PRIOR IDINSTITUCION = IDPADRE)';
        }

        $orden = '2';



        $sql = 'SELECT '.$columnas.'
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(1) THEN "RA".IDDISPOGPS END) AS "RptPatrullero",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(2) THEN "RA".IDDISPOGPS END) AS "RptMotorizado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(3) THEN "RA".IDDISPOGPS END) AS "RptPatpie",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(4) THEN "RA".IDDISPOGPS END) AS "RptPatintegrado",
                        COUNT(CASE WHEN "RA".IDPATRULLAJE IN(5) THEN "RA".IDDISPOGPS END) AS "RptBase",
                        COUNT("RA".IDDISPOGPS) AS "RptTotal"  
                        FROM SISGESPATMI.TM_DISPOGPS "RA"
                        INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                        WHERE "RA".FLGACTIVO = 1 '.$q_where.'
                        AND EXISTS(SELECT 1 FROM SISGESPATMI.TH_TRACKER_RESUMEN "H" WHERE TRUNC("H"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') AND "H"."IDDISPOGPS"="RA"."IDDISPOGPS")
                        GROUP BY '.$grupo.' ORDER BY '.$orden;
         
        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
    
    }

    function get_transmisiones_det($tipo, $dependencia='', $fecha)
    {
        $q_where = '';

        $sql = 'WITH BASE AS
                    (SELECT 
                       NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                       NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                       NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                       NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                       '.$tipo.' as "DependenciaNivel",
                       "INS".IDINSTITUCION as "InstitucionID",
                       RA.IDPNPRADIO as "RadioEtiqueta",
                       RA.PLACA as "VehiculoPlaca",
                      (CASE
                       WHEN "RA"."IDPATRULLAJE" IN ( \'1\') THEN \'PATRULLERO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'2\') THEN \'MOTORIZADO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'3\') THEN \'PATRULLAJE A PIE\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'4\') THEN \'PATRULLAJE INTEGRADO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'5\') THEN \'RADIO BASE\'
                     END) AS "Vehiculo",
                       (CASE
                         WHEN EXISTS (SELECT 1
                                 FROM SISGESPATMI.TH_TRACKER_RESUMEN "H"
                                WHERE TRUNC("H"."FECHALOC") =
                                      TO_DATE(\''.$fecha.'\', \'DD/MM/YYYY\')
                                  AND "H"."IDDISPOGPS" = "RA"."IDDISPOGPS") THEN
                          \'X\'
                       END) AS "Transmite"
                FROM SISGESPATMI.TM_DISPOGPS "RA"
                INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                WHERE RA.FLGACTIVO = 1 
                AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                ),
                BASE2 AS
                (SELECT * FROM BASE B)
                SELECT *
                FROM BASE2 B2 WHERE B2."Transmite" = \'X\''.'';

        $query = $this->db->query($sql);
        return $query->result_array(); 
         // echo $sql;
        // echo $fecha;
    }

    function get_transmisiones_det_xls($fecha, $tipo, $dependencia='')
    {
        $q_where = '';

           $sql = 'WITH BASE AS
                    (SELECT 
                       NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "macroREGION",
                       NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "regionPOLICIAL",
                       NVL("DIVTER"."NOMBRE", \'SIN DIVISÓN REGIÓN\') as "divisionPOLICIAL",
                       NVL("INS"."NOMBRE", \'SIN DEPENDENCIA\') as "dependencia",
                       RA.IDPNPRADIO as "RadioEtiqueta",
                       RA.PLACA as "VehiculoPlaca",
                      (CASE
                       WHEN "RA"."IDPATRULLAJE" IN ( \'1\') THEN \'PATRULLERO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'2\') THEN \'MOTORIZADO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'3\') THEN \'PATRULLAJE A PIE\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'4\') THEN \'PATRULLAJE INTEGRADO\'
                       WHEN "RA"."IDPATRULLAJE" IN ( \'5\') THEN \'RADIO BASE\'
                     END) AS "Vehiculo",
                       (CASE
                         WHEN EXISTS (SELECT 1
                                 FROM SISGESPATMI.TH_TRACKER_RESUMEN "H"
                                WHERE TRUNC("H"."FECHALOC") =
                                      TO_DATE(\''.$fecha.'\', \'DD/MM/YYYY\')
                                  AND "H"."IDDISPOGPS" = "RA"."IDDISPOGPS") THEN
                          \'Transmitió\'
                       END) AS "Transmite"
                FROM SISGESPATMI.TM_DISPOGPS "RA"
                INNER JOIN SISGESPATMI.TM_INSTITUCION INS ON RA.IDINSTITUCION = INS.IDINSTITUCION
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("INS"."IDPADRE", "INS"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE", "DIVTER"."IDINSTITUCION")
                LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE", "REGPOL"."IDINSTITUCION")
                WHERE RA.FLGACTIVO = 1 
                AND "INS"."IDINSTITUCION" IN
                       (SELECT IDINSTITUCION
                       FROM SISGESPATMI.TM_INSTITUCION
                       START WITH IDINSTITUCION = '.$dependencia.'
                       CONNECT BY PRIOR IDINSTITUCION = IDPADRE)
                ),
                BASE2 AS
                (SELECT * FROM BASE B)
                SELECT *
                FROM BASE2 B2 WHERE B2."Transmite" = \'Transmitió\''.'';

        $query = $this->db->query($sql);
        return $query->result_array(); 
        // echo $sql;
        // echo $fecha;
    }


    // REPORTE CONEXIONES

    function get_conexiones($periodo, $fechaini, $fechafin, $tipo, $institucion)
    {
        $q_where = '';

        if($institucion > 0){
            $q_where .= ' AND "C"."IDINSTITUCION" IN(SELECT IDINSTITUCION FROM SISGESPATMI.TM_INSTITUCION  start with IDINSTITUCION = '.$institucion.' CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
        }

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'YYYY\')';
        }


        if($tipo == 0){
            $columnas = 'NVL("MACREG"."IDINSTITUCION",0) AS "DependenciaID", 0 as "DependenciaNivel", NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "RptLocalidad", '.$periodo_col.' ';
            $grupo = '"MACREG"."IDINSTITUCION", "MACREG"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo == 1){
            $columnas = 'NVL("REGPOL"."IDINSTITUCION",0) AS "DependenciaID", 1 as "DependenciaNivel", NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "RptLocalidad", '.$periodo_col.' ';
            $grupo = '"REGPOL"."IDINSTITUCION", "REGPOL"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo == 2){
            $columnas = 'NVL("DIVTER"."IDINSTITUCION",0) AS "DependenciaID", 2 as "DependenciaNivel", NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN TERRITORIAL\') as "RptLocalidad", '.$periodo_col.' ';
            $grupo = '"DIVTER"."IDINSTITUCION", "DIVTER"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo == 3 || $tipo == 4){
            $columnas = 'NVL("C"."IDINSTITUCION",0) AS "DependenciaID", 3 as "DependenciaNivel", NVL("C"."NOMBRE", \'SIN NOMBRE\') as "RptLocalidad", '.$periodo_col.' ';
            $grupo = '"C"."IDINSTITUCION", "C"."NOMBRE", '.$periodo_gpo.'';
        }

        $orden = '2,1';

        $sql = '
        with "USUARIOS" as (
             SELECT "U"."IDINSTITUCION", COUNT("U"."IDUSUARIO") AS "TOTAL"
             FROM "SISGESPATMI"."TM_USUARIO" "U"
             WHERE "U"."FLGPNP" > \'0\' AND "U"."IDINSTITUCION" > 0
             GROUP BY "U"."IDINSTITUCION"
        ),
         "CONEX" as (
             SELECT "U"."IDINSTITUCION", TRUNC("S"."FECHAREG") AS "FECHA", COUNT(DISTINCT "U"."IDUSUARIO") AS "CANTIDAD"
             FROM "SISGESPATMI"."TM_USUARIO" "U"
             INNER JOIN "SISGESPATMI".TH_SESION "S" ON  "U"."IDUSUARIO" = "S"."IDUSUARIO"
             WHERE "U"."FLGPNP" = 1 AND "U"."IDINSTITUCION" > 0
             AND TRUNC("S"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
             GROUP BY "U"."IDINSTITUCION", TRUNC("S"."FECHAREG")
        )
        SELECT '.$columnas.', NVL(MAX("TU"."TOTAL"),0) AS "RptTotal", SUM("TC"."CANTIDAD") AS "RptCantidad"

        FROM "SISGESPATMI"."TM_INSTITUCION" "C"
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("C"."IDPADRE","C"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE","DIVTER"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE","REGPOL"."IDINSTITUCION")
        LEFT JOIN "USUARIOS" "TU" ON "TU"."IDINSTITUCION" = "C"."IDINSTITUCION"
        LEFT JOIN "CONEX" "TC" ON "TC"."IDINSTITUCION" = "C"."IDINSTITUCION"
        WHERE 1=1 '.$q_where.'
        GROUP BY '.$grupo.' ORDER BY '.$orden;

        $query = $this->db->query($sql);        
        // echo($sql);
        return $query->result_array();
    }

    function get_conexiones_xls($periodo, $fechaini, $fechafin, $tipo_ubigeo, $institucion = 0)
    {
        $q_where = '';

        if($institucion > 0){
            $q_where .= ' AND "C"."IDINSTITUCION" IN(SELECT IDINSTITUCION FROM SISGESPATMI.TM_INSTITUCION  start with IDINSTITUCION = '.$institucion.' CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
        }

        $columnas = '';
        $grupo = '';
        $orden = '';
        $periodo_col = '';
        $periodo_gpo = '';

        if($periodo == 1){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'DD/MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'DD/MM/YYYY\')';
        }elseif($periodo == 2){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'MM/YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'MM/YYYY\')';
        }elseif($periodo == 3){
            $periodo_col = 'TO_CHAR("TC"."FECHA",\'YYYY\') as "RptPeriodo"';
            $periodo_gpo = 'TO_CHAR("TC"."FECHA",\'YYYY\')';
        }



        if($tipo_ubigeo == 0){
            $columnas = 'NVL("MACREG"."NOMBRE", \'SIN MACRO REGIÓN\') as "MACREG", '.$periodo_col.' ';
            $grupo = '"MACREG"."IDINSTITUCION", "MACREG"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo_ubigeo == 1){
            $columnas = 'NVL("REGPOL"."NOMBRE", \'SIN REGIÓN POLICIAL\') as "MACREG", '.$periodo_col.' ';
            $grupo = '"REGPOL"."IDINSTITUCION", "REGPOL"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo_ubigeo == 2){
            $columnas = 'NVL("DIVTER"."NOMBRE", \'SIN DIVISIÓN TERRITORIAL\') as "MACREG", '.$periodo_col.' ';
            $grupo = '"DIVTER"."IDINSTITUCION", "DIVTER"."NOMBRE", '.$periodo_gpo.'';
        }elseif($tipo_ubigeo == 3 || $tipo_ubigeo == 4){
            $columnas = 'NVL("C"."NOMBRE", \'SIN NOMBRE\') as "MACREG", '.$periodo_col.' ';
            $grupo = '"C"."IDINSTITUCION", "C"."NOMBRE", '.$periodo_gpo.'';
        }

        $orden = '2,1';

        $sql = '
        with "USUARIOS" as (
             SELECT "U"."IDINSTITUCION", COUNT("U"."IDUSUARIO") AS "TOTAL"
             FROM "SISGESPATMI"."TM_USUARIO" "U"
             WHERE "U"."FLGPNP" > \'0\' AND "U"."IDINSTITUCION" > 0
             GROUP BY "U"."IDINSTITUCION"
        ),
         "CONEX" as (
             SELECT "U"."IDINSTITUCION", TRUNC("S"."FECHAREG") AS "FECHA", COUNT(DISTINCT "U"."IDUSUARIO") AS "CANTIDAD"
             FROM "SISGESPATMI"."TM_USUARIO" "U"
             INNER JOIN "SISGESPATMI".TH_SESION "S" ON  "U"."IDUSUARIO" = "S"."IDUSUARIO"
             WHERE "U"."FLGPNP" = 1 AND "U"."IDINSTITUCION" > 0
             AND TRUNC("S"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\')
             GROUP BY "U"."IDINSTITUCION", TRUNC("S"."FECHAREG")
        )
        SELECT '.$columnas.', NVL(MAX("TU"."TOTAL"),0) AS "TOTAL", SUM("TC"."CANTIDAD") AS "CANTIDAD"

        FROM "SISGESPATMI"."TM_INSTITUCION" "C"
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("C"."IDPADRE","C"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE","DIVTER"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE","REGPOL"."IDINSTITUCION")
        LEFT JOIN "USUARIOS" "TU" ON "TU"."IDINSTITUCION" = "C"."IDINSTITUCION"
        LEFT JOIN "CONEX" "TC" ON "TC"."IDINSTITUCION" = "C"."IDINSTITUCION"
        WHERE 1=1 '.$q_where.'
        GROUP BY '.$grupo.' ORDER BY '.$orden;



        $query = $this->db->query($sql);        
        // echo($sql);
        return $query->result_array();
    }

    function get_conexiones_det($tipo, $institucion, $fechaini, $fechafin){
        $q_where = '';


        if($institucion > 0){
            $q_where .= ' AND "C"."IDINSTITUCION" IN(SELECT IDINSTITUCION FROM SISGESPATMI.TM_INSTITUCION start with IDINSTITUCION = '.$institucion.' CONNECT BY PRIOR IDINSTITUCION = IDPADRE) ';
            $tipo_ubigeo = 3;
        }

        $sql = '        
        SELECT 
        NVL((CASE WHEN "MACREG"."IDTIPOINST" = 7 THEN "MACREG"."NOMBRE" WHEN "REGPOL"."IDTIPOINST" = 7 THEN "REGPOL"."NOMBRE" WHEN "DIVTER"."IDTIPOINST" = 7 THEN "DIVTER"."NOMBRE" WHEN "C"."IDTIPOINST" = 7 THEN "C"."NOMBRE" END), \'SIN MACRO REGIÓN\') as "MacRegNombre",
        NVL((CASE WHEN "REGPOL"."IDTIPOINST" = 8 THEN "REGPOL"."NOMBRE" WHEN "DIVTER"."IDTIPOINST" = 8 THEN "DIVTER"."NOMBRE" WHEN "C"."IDTIPOINST" = 8 THEN "C"."NOMBRE" END), \'SIN REGIÓN POLICIAL\') as "RegPolNombre",
        NVL((CASE WHEN "DIVTER"."IDTIPOINST" = 3 THEN "DIVTER"."NOMBRE"  WHEN "C"."IDTIPOINST" = 3 THEN "C"."NOMBRE" END), \'SIN DIVISIÓN TERRITORIAL\') as "DivTerNombre",
        NVL((CASE WHEN "C"."IDTIPOINST" IN(4,5) THEN "C"."NOMBRE" END), \' - \') as "ComisariaNombre",
        "U"."USUARIOCOD" AS "UsuarioCodigo", "U"."NOMBRE" AS "UsuarioNombre", "U"."APELLIDO" AS "UsuarioApellido", 
        TO_CHAR("S"."FECHAREG",\'DD/MM/YYYY HH24:MI:SS\') AS "SesionFechaInicio", TO_CHAR("S"."FECHAFIN",\'DD/MM/YYYY HH24:MI:SS\') AS "SesionFechaFin", 
        "S"."FLGACTIVO" AS "SesionEstado", (CASE WHEN "S"."FLGACTIVO" = 1 THEN \'Conectado\' ELSE \'Desconectado\' END) AS "SesionEstadoTexto"
        FROM "SISGESPATMI"."TM_USUARIO" "U"
        INNER JOIN "SISGESPATMI".TH_SESION "S" ON  "U"."IDUSUARIO" = "S"."IDUSUARIO"
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "C" ON "C"."IDINSTITUCION" = "U"."IDINSTITUCION"
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "DIVTER" ON "DIVTER"."IDINSTITUCION" = NVL("C"."IDPADRE","C"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "REGPOL" ON "REGPOL"."IDINSTITUCION" = NVL("DIVTER"."IDPADRE","DIVTER"."IDINSTITUCION")
        LEFT JOIN "SISGESPATMI"."TM_INSTITUCION" "MACREG" ON "MACREG"."IDINSTITUCION" = NVL("REGPOL"."IDPADRE","REGPOL"."IDINSTITUCION")
        WHERE 1=1 '.$q_where.' AND "U"."FLGPNP" > \'0\' AND "U"."IDINSTITUCION" > 0 AND TRUNC("S"."FECHAREG") BETWEEN TO_DATE(\''.$fechaini.'\',\'DD/MM/YYYY\') and  TO_DATE(\''.$fechafin.'\',\'DD/MM/YYYY\') ';

        $query = $this->db->query($sql);
        // echo($sql);
        return $query->result_array();
    }


}


   