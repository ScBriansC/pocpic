<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Rutasync_Model , entidad de la tabla "TH_RUTA_SYNC"
 */
class Rutasync_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
    function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('"SISGESPATMI".TH_RUTA_SYNC', 'IDRUTASYNC');
    }


    function get_Resumen($fecha, $hora_ini='', $hora_fin='' ,$ubigeo = '', $idcomisaria = 0, $motorizado = false)
    {
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 00:00:00';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }

        if($idcomisaria > 0){
            $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "C"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        if($motorizado){
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN (\'3\') ';
        }else{
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN(\'1\',\'2\') ';
        }


        $sql = ' SELECT "IDTURNO", COUNT(DISTINCT "RUVHID") AS "CANTIDAD", MIN( CASE 
                   WHEN "IDTURNO"=1 THEN \'Madrugada\'
                   WHEN "IDTURNO"=2 THEN \'Mañana\'
                   WHEN "IDTURNO"=3 THEN \'Tarde\'
                   WHEN "IDTURNO"=4 THEN \'Noche\'
            END) AS "TURNO"
            FROM (
            select DISTINCT ( CASE 
                   WHEN "RUSYNC"."HORALOC">=\'000000\' AND "RUSYNC"."HORALOC" <=\'055959\' THEN 1
                   WHEN "RUSYNC"."HORALOC">=\'060000\' AND "RUSYNC"."HORALOC" <=\'115959\' THEN 2
                   WHEN "RUSYNC"."HORALOC">=\'120000\' AND "RUSYNC"."HORALOC" <=\'175959\' THEN 3
                   WHEN "RUSYNC"."HORALOC">=\'180000\' AND "RUSYNC"."HORALOC" <=\'235959\' THEN 4
            END) AS "IDTURNO", NVL("VH"."PLACA","RA"."PLACA")  AS "RUVHID"
            FROM "SISGESPATMI"."TH_RUTA_SYNC" "RUSYNC"
            LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RUSYNC"."IDRADIO"
            LEFT JOIN "SISGESPATMI"."TM_VEHICULO" "VH" ON "VH"."IDVEHICULO" = "RUSYNC"."IDVEHICULO"
            LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = NVL("RA"."IDCOMISARIA", "VH"."IDCOMISARIA")
            where 1=1 '.$q_where.' AND NVL("VH"."PLACA","RA"."PLACA") IS NOT NULL AND "RUSYNC"."FLGCORRECTO" = 1
            AND "RUSYNC"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
            AND "FLGCORRECTO" = 1
            AND ( nvl("RA".FLGACTIVO, nvl("VH".FLGACTIVO,0)) =1
            OR nvl("VH".FLGACTIVO, nvl("RA".FLGACTIVO,0))  = 1)
            ) "REP"
            GROUP BY "IDTURNO" ORDER BY 1';
      $query = $this->db->query($sql);
        // echo $sql;
        // 
        // return print_r($sql);
        return $query->result_array();   
        
    }



    function get_ResumenTotal($fecha, $ubigeo = '', $idcomisaria = 0, $motorizado = false)
    {
        
        $q_where1 = '';
        $q_where2 = '';

        if($idcomisaria > 0){
            $q_where1 .= ' AND  RA."IDCOMISARIA" = '.$idcomisaria.' ';
            $q_where2 .= ' AND  VH."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where1 .= ' AND SUBSTR(C1."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
                $q_where2 .= ' AND SUBSTR(C2."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where1 .= ' AND SUBSTR(C1"IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
                $q_where2 .= ' AND SUBSTR(C2"IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where1 .= ' AND C1."IDUBIGEO" = \''.trim($ubigeo).'\' ';
                $q_where2 .= ' AND C2."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        $sql = 'SELECT  count(distinct REP.PLACA) AS TOTAL FROM(
    SELECT RA.PLACA FROM SISGESPATMI.TM_RADIO RA LEFT JOIN SISGESPATMI.TM_COMISARIA C1 ON C1.IDCOMISARIA = RA.IDCOMISARIA
    WHERE RA.FLGACTIVO = 1  '.$q_where1.' AND RA.IDMODELOVH IN ('.($motorizado?'3':'1,2').')
    UNION ALL
    SELECT VH.PLACA FROM SISGESPATMI.TM_VEHICULO VH  LEFT JOIN SISGESPATMI.TM_COMISARIA C2 ON C2.IDCOMISARIA = VH.IDCOMISARIA
    WHERE VH.FLGACTIVO = 1  '.$q_where2.' AND VH.IDMODELOVH IN ('.($motorizado?'3':'1,2').')
    ) REP WHERE REP.PLACA IS NOT NULL';

        $query = $this->db->query($sql);

        // return print_r($query) ;
        $data = $query->row_array();
        // echo $sql;
        return @(int)$data['TOTAL'];   
    }



    function get_GPSbyFecha($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $idvehiculo = 0, $motorizado = false, $placa = '', $ubigeo = '', $idcomisaria = 0)
    {
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 00:00:00';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }

        if($idcomisaria > 0){
            $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "C"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        if($idvehiculo > 0){
          $q_where .= ' AND NVL("RSYNC2"."IDVEHICULO",0) = \''.trim($idvehiculo).'\' ';
        }

        if($idradio > 0){
          $q_where .= ' AND NVL("RSYNC2"."IDRADIO",0) = \''.trim($idradio).'\' ';
        }

        if($placa != ''){
          $q_where .= ' AND ("RA"."PLACA"  LIKE \'%'.trim($placa).'%\' OR "VH"."PLACA"  LIKE \'%'.trim($placa).'%\') ';
        }

        if($motorizado){
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN (\'3\') ';
        }else{
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN(\'1\',\'2\') ';
        }


        $sql = 'SELECT "RSYNC2"."IDRUTASYNC" as "TrackerID", (NVL("RSYNC2"."IDRADIO",\'0\')||\'_\'||NVL("RSYNC2"."IDVEHICULO",\'0\')) AS "RAVHID", 
        NVL("RA"."IDRADIO",0) as "RadioID", NVL("RA"."ETIQUETA",\'\') as "RadioEtiqueta", NVL("VH"."IDVEHICULO",0) as "VehiculoID", NVL("VH"."PLACA","RA"."PLACA") as "VehiculoPlaca", 
        "RSYNC2"."LATITUD" as "TrackerLat", "RSYNC2"."LONGITUD" as "TrackerLong", "RSYNC2"."VELOCIDAD" as "TrackerVelocidad", 
        TO_CHAR("RSYNC2"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", TO_CHAR("RSYNC2"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora",        
        (CASE WHEN "RSYNC2"."FECHALOC"  >= (SYSDATE - (5/ (24*60))) THEN \'1\' ELSE \'2\' END) AS "Indicador",        
        NVL("C"."IDCOMISARIA",0) AS "ComisariaID", NVL("C"."NOMBRE",\'\') AS "ComisariaNombre"
        FROM "SISGESPATMI"."TH_RUTA_SYNC" "RSYNC2"
        LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
        LEFT JOIN "SISGESPATMI"."TM_VEHICULO" "VH" ON "VH"."IDVEHICULO" = "RSYNC2"."IDVEHICULO"
        LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = NVL("RA"."IDCOMISARIA", "VH"."IDCOMISARIA")
        INNER JOIN (
            SELECT (NVL("RSYNC1"."IDRADIO",\'0\')||\'_\'||NVL("RSYNC1"."IDVEHICULO",\'0\')) AS "IDRAVH", MAX("RSYNC1"."IDRUTASYNC") as "IDRUTASYNC" FROM "SISGESPATMI"."TH_RUTA_SYNC" "RSYNC1"
            WHERE "RSYNC1"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')  AND "RSYNC1"."FLGCORRECTO" = 1
            GROUP BY (NVL("RSYNC1"."IDRADIO",\'0\')||\'_\'||NVL("RSYNC1"."IDVEHICULO",\'0\'))
        ) "G" ON "G"."IDRAVH" = (NVL("RSYNC2"."IDRADIO",\'0\')||\'_\'||NVL("RSYNC2"."IDVEHICULO",\'0\')) AND "G"."IDRUTASYNC" = "RSYNC2"."IDRUTASYNC"
         WHERE 1=1 AND ( nvl("RA".FLGACTIVO, nvl("VH".FLGACTIVO,0)) =1 OR nvl("VH".FLGACTIVO, nvl("RA".FLGACTIVO,0))  = 1)AND NVL("VH"."PLACA","RA"."PLACA") IS NOT NULL '.$q_where.' ORDER BY  NVL("C"."NOMBRE",\'\'), NVL("C"."IDCOMISARIA",0), "RSYNC2"."FECHALOC" DESC
        ';

      // NVL(NVL("RA"."FLGACTIVO","VH"."FLGACTIVO"),0) = 1
     // $query = $this->db->query($sql);
       echo $sql;
    //  return $query->result_array();   
    }



    function get_RutabyFechaVehiculo($fecha, $hora_ini='', $hora_fin='', $idradio, $idvehiculo)
    {
        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }

        $sql = '
            SELECT "RUSYNC"."IDRUTASYNC" as "TrackerID", NVL("RUSYNC"."IDRADIO",0) AS "RadioID", NVL("RUSYNC"."IDVEHICULO",0) as "VehiculoID", NVL("VH"."PLACA","RA"."PLACA") as "VehiculoPlaca", "RUSYNC"."LATITUD" as "TrackerLat", "RUSYNC"."LONGITUD" as "TrackerLong", "RUSYNC"."VELOCIDAD" as "TrackerVelocidad", TO_CHAR("RUSYNC"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", TO_CHAR("RUSYNC"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora"
            FROM "SISGESPATMI"."TH_RUTA_SYNC" "RUSYNC"
            LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RUSYNC"."IDRADIO"
            LEFT JOIN "SISGESPATMI"."TM_VEHICULO" "VH" ON "VH"."IDVEHICULO" = "RUSYNC"."IDVEHICULO"
            WHERE "RUSYNC"."FLGCORRECTO" = 1 
             AND ( nvl("RA".FLGACTIVO, nvl("VH".FLGACTIVO,0)) =1 OR nvl("VH".FLGACTIVO, nvl("RA".FLGACTIVO,0))  = 1)
             AND "RUSYNC"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')   AND "RUSYNC"."FLGCORRECTO" = 1
            AND NVL("RUSYNC"."IDVEHICULO",0) = \''.$idvehiculo.'\' AND NVL("RUSYNC"."IDRADIO",0) = \''.$idradio.'\'
            ORDER BY "RUSYNC"."FECHALOC" DESC';
       // echo $sql;
        $query = $this->db->query($sql);
        // AND NVL(NVL("RA"."FLGACTIVO","VH"."FLGACTIVO"),0) = 1
        return $query->result_array();   
    }

    function get_ReporteXLS($fecha,$hora_ini, $hora_fin, $ubigeo='', $comisaria='',$idcomisaria = 0, $placa = 0, $placa_radio= 0, $clase =0, $tipo, $categoria =0 ,$motorizado)
    {
        
        $q_where = '';

       $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 00:00:00';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':00';
        }
        // if($idcomisaria > 0){
        //     $q_where .= ' AND  c2."IDCOMISARIA" = '.$idcomisaria.' ';
        // }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR(U."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR(U."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND U."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        //  if($idcomisaria > 0){
        //     $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
        // }

        if($motorizado){
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN (\'3\') ';
              if($placa_radio!='' && $placa_radio!='0')
              {
                   $q_where .= 'AND ("RA"."PLACA" like \'%'.strtoupper($placa_radio).'%\' OR "VH"."PLACA" like \'%'.strtoupper($placa_radio).'%\') '; 
              }
        }else{
          $q_where .= ' AND NVL(NVL("RA"."IDMODELOVH","VH"."IDMODELOVH"),0) IN(\'1\',\'2\') ';
           if($placa!='' && $placa!='0')
            {
                 $q_where .= 'AND ("RA"."PLACA" like \'%'.strtoupper($placa).'%\' OR "VH"."PLACA" like \'%'.strtoupper($placa).'%\') '; 
            }

        }

       
      
        // if($comisaria!='' && $comisaria!='0')
        // {
        //      // $q_where .= 'AND "C"."NOMBRE" LIKE  \'%TUPAC%\' '; 
        //      $q_where .= 'AND c2."NOMBRE" LIKE  \'%'.$nombreComisaria.'%\' '; 
        // }
        // if($clase > 0)
        // {
        //      $q_where .= 'AND c2."IDCLASE"= '.$clase.' '; 
        // }
        // if($tipo > 0)
        // {
        //      $q_where .= 'AND c2."IDTIPO"= '.$tipo.' '; 
        // }
        // if($categoria > 0)
        // {
        //      $q_where .= 'AND c2."IDCATEGORIA"= '.$categoria.' '; 
        // }

        $sql = 'SELECT NVL("U"."DEPARTAMENTO",\'\') as "DEPARTAMENTO",
                   NVL("U"."PROVINCIA",\'\') as "PROVINCIA", 
                   NVL("U"."DISTRITO",\'\') as "DISTRITO",
                   NVL("C"."NOMBRE", \'\') AS "ComisariaNombre",
                   NVL("VH"."PLACA", "RA"."PLACA") as "VehiculoPlaca",
                   TO_CHAR("RSYNC2"."FECHALOC",\'DD/MM/YYYY\') as "Fecha",
                   TO_CHAR("RSYNC2"."FECHALOC",\'HH24:MI:SS\') as "Hora",
                   TO_CHAR(MIN("RSYNC2"."FECHAREG"), \'HH24:MI:SS\') as "TrackerHoraIni",
                   TO_CHAR(MAX("RSYNC2"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraIni",
                   (CASE 
                      WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=0 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=559 THEN \'1-MADRUGADA\'
                      WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=600 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=1159 THEN  \'2-MAÑANA\'
                      WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=1200 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=1759 THEN \'3-TARDE\'
                      WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=1800 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=2359 THEN \'4-NOCHE\'
                      ELSE \'X\'
                   END) as TURNO,
                   CAST(MAX("RSYNC2"."VELOCIDAD")as NUMERIC(10,2)) "TrackerVelocidad" 
              FROM "SISGESPATMI"."TH_RUTA_SYNC" "RSYNC2"
              LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA"
                ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
              LEFT JOIN "SISGESPATMI"."TM_VEHICULO" "VH"
                ON "VH"."IDVEHICULO" = "RSYNC2"."IDVEHICULO"
              LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C"
                ON "C"."IDCOMISARIA" = NVL("RA"."IDCOMISARIA", "VH"."IDCOMISARIA")
              LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" 
                ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
             INNER JOIN (SELECT (NVL("RSYNC1"."IDRADIO", \'0\') || \'_\' ||
                                NVL("RSYNC1"."IDVEHICULO", \'0\')) AS "IDRAVH",
                                MAX("RSYNC1"."IDRUTASYNC") as "IDRUTASYNC"
                           FROM "SISGESPATMI"."TH_RUTA_SYNC" "RSYNC1"
                          WHERE "RSYNC1"."FECHALOC" BETWEEN  
                                TO_DATE(\''.$fechaini.'\', \'YYYY-MM-DD HH24:MI:SS\') AND
                                TO_DATE(\''.$fechafin.'\', \'YYYY-MM-DD HH24:MI:SS\')
                            AND "RSYNC1"."FLGCORRECTO" = 1
                            AND "RSYNC1"."FLGCORRECTO" = 1
                          GROUP BY (NVL("RSYNC1"."IDRADIO", \'0\') || \'_\' ||
                                   NVL("RSYNC1"."IDVEHICULO", \'0\'))) "G"
                ON "G"."IDRAVH" = (NVL("RSYNC2"."IDRADIO", \'0\') || \'_\' ||
                   NVL("RSYNC2"."IDVEHICULO", \'0\'))
               AND "G"."IDRUTASYNC" = "RSYNC2"."IDRUTASYNC"
             WHERE 1 = 1
               AND ( nvl("RA".FLGACTIVO, nvl("VH".FLGACTIVO,0)) =1 OR nvl("VH".FLGACTIVO, nvl("RA".FLGACTIVO,0))  = 1)
               AND NVL("VH"."PLACA", "RA"."PLACA") IS NOT NULL
               AND "RSYNC2"."FLGCORRECTO" = 1
               '.$q_where.'
             GROUP BY NVL("U"."DEPARTAMENTO",\'\'),
                      NVL("U"."PROVINCIA",\'\'),
                      NVL("U"."DISTRITO",\'\'),
                      NVL("C"."NOMBRE", \'\'),
                      NVL("VH"."PLACA", "RA"."PLACA"),
                      NVL("C"."IDCOMISARIA", 0),
                      "RSYNC2"."FECHALOC",
                      "RSYNC2"."VELOCIDAD",
                      (CASE 
                          WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=0 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=559 THEN \'1-MADRUGADA\'
                          WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=600 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=1159 THEN  \'2-MAÑANA\'
                          WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=1200 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=1759 THEN \'3-TARDE\'
                          WHEN cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER)>=1800 AND cast(TO_CHAR("RSYNC2"."FECHALOC",\'HH24MI\') as INTEGER) <=2359 THEN \'4-NOCHE\'
                          ELSE \'X\'
                       END)
                     ORDER BY 1,2,3,4,5,6,7';


      // AND NVL(NVL("RA"."FLGACTIVO", "VH"."FLGACTIVO"), 0) = 1
       // inicio
       // $query = $this->db->query($sql);

       // $array  = $query->result_array();

        echo $sql;
        // return $query->result_array();   
    }

     function get_DetalleVehiculo($fecha,$hora_ini,$hora_fin, $idradio, $idvehiculo)
    {
        
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':59';
        }


        $sql = 'SELECT  NVL("U"."DEPARTAMENTO",\'\') as "UbigeoDepartamento",
                        NVL("U"."PROVINCIA",\'\') as "UbigeoProvincia", 
                        NVL("U"."DISTRITO",\'\') as "UbigeoDistrito", 
                        NVL("C"."NOMBRE",\'\') as "ComisariaNombre",
                        NVL("VH"."PLACA", "RA"."PLACA") as "VehiculoPlaca",   
                        NVL("RA"."ETIQUETA", \'\') as "RadioEtiqueta",       
                        CAST(MAX("RSYNC2"."VELOCIDAD")as NUMERIC(10,2)) "TrackerVelocidad" ,              
                        TO_CHAR("RSYNC2"."FECHALOC", \'YYYY-MM-DD\') as "TrackerFecha",
                        TO_CHAR(MIN("RSYNC2"."FECHALOC"),\'HH24:MI:SS\') as "TrackerHoraIni",
                        TO_CHAR(MAX("RSYNC2"."FECHALOC"),\'HH24:MI:SS\') as "TrackerHoraFin",
                        CAST("SISGESPATMI".UFUN_GETDISTANCIASYNC(max("RSYNC2".IDRADIO),max("RSYNC2".IDVEHICULO),MIN("RSYNC2"."FECHALOC"), MAX("RSYNC2"."FECHALOC"))/1000 as NUMERIC(10,2)) as "TrackerKm"
               FROM "SISGESPATMI"."TH_RUTA_SYNC" "RSYNC2"
               LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
               LEFT JOIN "SISGESPATMI"."TM_VEHICULO" "VH" ON "VH"."IDVEHICULO" = "RSYNC2"."IDVEHICULO"
               LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = NVL("RA"."IDCOMISARIA", "VH"."IDCOMISARIA") 
               LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
                WHERE 1=1  
                AND NVL("RSYNC2"."IDRADIO",0) = '.(int)$idradio.'   AND NVL("RSYNC2"."IDVEHICULO",0) = '.(int)$idvehiculo.'                 
                AND "RSYNC2"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\', \'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\', \'YYYY-MM-DD HH24:MI:SS\')
                AND "RSYNC2".FLGCORRECTO = 1         

                GROUP BY    NVL("U"."DEPARTAMENTO",\'\'), 
                            NVL("U"."PROVINCIA",\'\'), 
                            NVL("U"."DISTRITO",\'\'), 
                            NVL("C"."NOMBRE",\'\'),
                            NVL("VH"."PLACA", "RA"."PLACA"),
                            NVL("RA"."ETIQUETA", \'\'), 
                            TO_CHAR("RSYNC2"."FECHALOC", \'YYYY-MM-DD\')';

  
        // $query = $this->db->query($sql);
        // $array  = $query->result_array();
        // $rows = $query->result_array();
        // return $rows[0];   
        echo $sql;
    }
}