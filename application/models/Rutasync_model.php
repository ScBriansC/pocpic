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

        $sql = ' SELECT "IDTURNO", SUM(VE) AS "CANTIDAD_VE",
       SUM(MT) AS "CANTIDAD_MT", MIN( CASE 
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
            END) AS "IDTURNO", "RA"."IDRADIO", CASE WHEN "RA"."IDMODELOVH" IN(1, 2, 5) THEN 1 ELSE 0 END VE,
                        CASE WHEN "RA"."IDMODELOVH" = 3 THEN 1 ELSE 0 END MT
            FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RUSYNC"
            INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RUSYNC"."IDRADIO"
            INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
            where 1=1 '.$q_where.' AND "RA"."PLACA" IS NOT NULL AND "RUSYNC"."FLGCORRECTO" = 1
            AND "RUSYNC"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
            AND "RUSYNC"."PROVEEDOR" = 1
            AND "RA"."FLGACTIVO" = 1
            ) "REP"
            GROUP BY "IDTURNO" ORDER BY 1';
        
        //$d1 = date('Y-m-d H:i:s');
        $query = $this->db->query($sql);
        //$d2 = date('Y-m-d H:i:s');
        //echo $d1.'-/-'.$d2;
         //echo $sql;
        // 
         //return print_r($sql);
        return $query->result_array();   
        
    }


    function get_ResumenAct($fecha, $hora_ini='', $hora_fin='' ,$ubigeo = '', $idcomisaria = 0, $motorizado = false)
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

        $sql = ' SELECT "IDTURNO", SUM(VE) AS "CANTIDAD_VE",
       SUM(MT) AS "CANTIDAD_MT", MIN( CASE 
                   WHEN "IDTURNO"=1 THEN \'Madrugada\'
                   WHEN "IDTURNO"=2 THEN \'Mañana\'
                   WHEN "IDTURNO"=3 THEN \'Tarde\'
                   WHEN "IDTURNO"=4 THEN \'Noche\'
            END) AS "TURNO"
            FROM (
            select "T"."TURNO" AS "IDTURNO", "RA"."IDRADIO", CASE WHEN "RA"."IDMODELOVH" IN(1, 2, 5) THEN 1 ELSE 0 END VE,
                        CASE WHEN "RA"."IDMODELOVH" = 3 THEN 1 ELSE 0 END MT
            FROM "SISGESPATMI"."TH_RESUMEN_TURNO" "T"
            INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "T"."IDRADIO"
            INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
            where 1=1  '.$q_where.' AND "RA"."PLACA" IS NOT NULL
            AND "T"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
            AND "RA"."FLGACTIVO" = 1
            ) "REP"
            GROUP BY "IDTURNO" ORDER BY 1';
        
        //$d1 = date('Y-m-d H:i:s');
        $query = $this->db->query($sql);
        //$d2 = date('Y-m-d H:i:s');
        //echo $d1.'-/-'.$d2;
         //echo $sql;
        // 
         //return print_r($sql);
        return $query->result_array();   
        
    }



    function get_ResumenTotal($fecha, $ubigeo = '', $idcomisaria = 0, $motorizado = false)
    {
        
        $q_where1 = '';

        if($idcomisaria > 0){
            $q_where1 .= ' AND  RA."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where1 .= ' AND SUBSTR(C1."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where1 .= ' AND SUBSTR(C1."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where1 .= ' AND C1."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        $sql = 'SELECT  count(distinct REP.IDRADIO) AS TOTAL FROM(
    SELECT RA.IDRADIO FROM SISGESPATMI.TM_RADIO RA INNER JOIN SISGESPATMI.TM_COMISARIA C1 ON C1.IDCOMISARIA = RA.IDCOMISARIA
    WHERE RA.FLGACTIVO = 1  '.$q_where1.' AND RA.IDMODELOVH IN('.($motorizado?'3':'1,2,5').') AND RA.PLACA IS NOT NULL
    ) REP WHERE 1=1';

        $query = $this->db->query($sql);

        // return print_r($query) ;
        $data = $query->row_array();
         //echo $sql;
        return @(int)$data['TOTAL'];   
    }


    function get_GPSbyFecha($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $idvehiculo = 0, $motorizado = false, $placa = '', $etiqueta = '', $serie = '', $ubigeo = '', $idcomisaria = 0)
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

         if($idradio > 0){
          $q_where .= ' AND "RA"."IDRADIO" = \''.trim($idradio).'\' ';
        }

        if($placa != ''){
          $q_where .= ' AND "RA"."PLACA"  LIKE \'%'.trim($placa).'%\' ';
        }

        if($etiqueta != ''){
          $q_where .= ' AND "RA"."ETIQUETA"  LIKE \'%'.trim($etiqueta).'%\' ';
        }

        if($serie != ''){
          $q_where .= ' AND "RA"."SERIE"  LIKE \'%'.trim($serie).'%\' ';
        }

        if($motorizado){
          $q_where .= ' AND "RA"."IDMODELOVH" IN(\'3\') ';
        }else{
          $q_where .= ' AND "RA"."IDMODELOVH" IN(\'1\',\'2\',\'5\') ';
        }


        $sql = '
        SELECT "RSYNC2"."IDRUTASYNC" as "TrackerID", "RA"."IDRADIO" as "RadioID",
        "RA"."ETIQUETA" as "RadioEtiqueta", "RA"."PLACA" as "VehiculoPlaca", 
        "RSYNC2"."LATITUD" as "TrackerLat", "RSYNC2"."LONGITUD" as "TrackerLong", "RSYNC2"."VELOCIDAD" as "TrackerVelocidad", 
        TO_CHAR("RSYNC2"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", 
        TO_CHAR("G"."HORAINI", \'HH24:MI:SS\') as "TrackerHoraIni",  
        TO_CHAR("RSYNC2"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora",        
        (CASE WHEN "RSYNC2"."FECHALOC"  >= (SYSDATE - (5/ (24*60))) THEN \'1\' ELSE \'2\' END) AS "Indicador", "RA"."ICONO" AS "Icono",        
        "C"."IDCOMISARIA" AS "ComisariaID", "C"."NOMBRE" AS "ComisariaNombre"
        
        FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC2"
        INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
        INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
        INNER JOIN (
            SELECT "RSYNC1"."IDRADIO", MAX("RSYNC1"."IDRUTASYNC") as "IDRUTASYNC", MIN("RSYNC1"."FECHALOC") AS "HORAINI" FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC1"
            WHERE "RSYNC1"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')  
                  AND "RSYNC1"."FLGCORRECTO" = 1
                  AND "RSYNC1"."PROVEEDOR" = 1  
                  AND "RSYNC1"."IDRADIO" IS NOT NULL
            GROUP BY "RSYNC1"."IDRADIO"
        ) "G" ON "G"."IDRADIO"="RA"."IDRADIO" AND "G"."IDRUTASYNC" = "RSYNC2"."IDRUTASYNC"
        WHERE 1=1 AND "RA"."FLGACTIVO" = 1 '.$q_where.' AND "RA"."PLACA" IS NOT NULL
        ORDER BY  "C"."NOMBRE", "C"."IDCOMISARIA", "RSYNC2"."FECHALOC" DESC
        ';

      // NVL(NVL("RA"."FLGACTIVO","VH"."FLGACTIVO"),0) = 1
       $query = $this->db->query($sql);
      // echo $sql;
       return $query->result_array();   
    }



    function get_GPSbyFechaAct($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $idvehiculo = 0, $tipo_patrullaje = false, $placa = '', $etiqueta = '', $serie = '', $ubigeo = '', $idcomisaria = 0)
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

        if($idradio > 0){
          $q_where .= ' AND "RA"."IDRADIO" = \''.trim($idradio).'\' ';
        }

        if($placa != ''){
          $q_where .= ' AND "RA"."PLACA"  LIKE \'%'.trim($placa).'%\' ';
        }

        if($etiqueta != ''){
          $q_where .= ' AND "RA"."ETIQUETA"  LIKE \'%'.trim($etiqueta).'%\' ';
        }

        if($serie != ''){
          $q_where .= ' AND "RA"."SERIE"  LIKE \'%'.trim($serie).'%\' ';
        }

        $sql_tipopat = '';

        foreach ($tipo_patrullaje as $tipopat) {
            // print_r($tipopat);
            if($tipopat =='patrullero')
            {
                // $sql_tipopat .= ($sql_tipopat!=''?',':'').'\'1\',\'2\',\'5\'';
                // echo 'primero';
                $sql_tipopat .= ($sql_tipopat!=''?',':'').'\'1\',\'2\',\'5\'';
                // echo $sql_tipopat;
            }
            if($tipopat =='motorizado')
            {
                // echo 'segundo';
                $sql_tipopat .= ($sql_tipopat!=''?',':'').'\'3\'';
                // echo $sql_tipopat;
            }
            if($tipopat =='patpie')
            {   
                // echo 'tercero';
                $sql_tipopat .= ($sql_tipopat!=''?',':'').'\'4\'';
              
            }
        }

        if($tipo_patrullaje != ''){
          $q_where .= 'AND "RA"."IDMODELOVH" IN('.$sql_tipopat.')';
        }

        


        // if($motorizado){
        //   $q_where .= ' AND "RA"."IDMODELOVH" IN(\'3\') ';
        // }else{
        //   $q_where .= ' AND "RA"."IDMODELOVH" IN(\'1\',\'2\',\'5\') ';
        // }


        $sql = '
        SELECT "RA"."IDRADIO" as "RadioID",
        "RA"."ETIQUETA" as "RadioEtiqueta", "RA"."PLACA" as "VehiculoPlaca", 
        "RA"."LATITUD" as "TrackerLat", "RA"."LONGITUD" as "TrackerLong", "RA"."VELOCIDAD" as "TrackerVelocidad", 
        TO_CHAR("RA"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", 
        TO_CHAR("RA"."FECHALOCINI", \'HH24:MI:SS\') as "TrackerHoraIni",  
        TO_CHAR("RA"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora",        
        (CASE WHEN "RA"."FECHALOC"  >= (SYSDATE - (5/ (24*60))) THEN \'1\' ELSE \'2\' END) AS "Indicador", "RA"."ICONO" AS "Icono",          
        "C"."IDCOMISARIA" AS "ComisariaID", "C"."NOMBRE" AS "ComisariaNombre"        
        FROM "SISGESPATMI"."TM_RADIO" "RA"
        INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
        WHERE 1=1 AND "RA"."FLGACTIVO" = 1 '.$q_where.' AND "RA"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\') AND "RA"."PLACA" IS NOT NULL
        ORDER BY  "C"."NOMBRE", "C"."IDCOMISARIA", "RA"."FECHALOC" DESC
        ';


       $query = $this->db->query($sql);      
       return $query->result_array();   
       // echo $sql;
    }





    // function get_GPSbyFecha($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $idvehiculo = 0, $motorizado = false, $placa = '', $etiqueta = '', $serie = '', $ubigeo = '', $idcomisaria = 0)
    // {
    //     $q_where = '';

    //     $fecha_arr = explode('/',$fecha);
    //     $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
    //     $fechaini = $fechanum.' 00:00:00';
    //     $fechafin = $fechanum.' 00:00:00';

    //     if($hora_ini!='' && $hora_fin!=''){
    //         $fechaini = $fechanum.' '.$hora_ini.':00';
    //         $fechafin = $fechanum.' '.$hora_fin.':00';
    //     }

    //     if($idcomisaria > 0){
    //         $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
    //     }

    //     if($ubigeo!='' && $ubigeo!='0'){
    //         if(substr($ubigeo,2,4) == '0000'){ //Departamento
    //             $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
    //         }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
    //             $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
    //         }else{ //Distrito
    //             $q_where .= ' AND "C"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
    //         }
    //     }

    //      if($idradio > 0){
    //       $q_where .= ' AND "RA"."IDRADIO" = \''.trim($idradio).'\' ';
    //     }

    //     if($placa != ''){
    //       $q_where .= ' AND "RA"."PLACA"  LIKE \'%'.trim($placa).'%\' ';
    //     }

    //     if($etiqueta != ''){
    //       $q_where .= ' AND "RA"."ETIQUETA"  LIKE \'%'.trim($etiqueta).'%\' ';
    //     }

    //     if($serie != ''){
    //       $q_where .= ' AND "RA"."SERIE"  LIKE \'%'.trim($serie).'%\' ';
    //     }

    //     if($motorizado){
    //       $q_where .= ' AND "RA"."IDMODELOVH" IN(\'3\') ';
    //     }else{
    //       $q_where .= ' AND "RA"."IDMODELOVH" IN(\'1\',\'2\',\'5\') ';
    //     }


    //     $sql = '
    //     SELECT "RSYNC2"."IDRUTASYNC" as "TrackerID", "RA"."IDRADIO" as "RadioID",
    //     "RA"."ETIQUETA" as "RadioEtiqueta", "RA"."PLACA" as "VehiculoPlaca", 
    //     "RSYNC2"."LATITUD" as "TrackerLat", "RSYNC2"."LONGITUD" as "TrackerLong", "RSYNC2"."VELOCIDAD" as "TrackerVelocidad", 
    //     TO_CHAR("RSYNC2"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", 
    //     TO_CHAR("G"."HORAINI", \'HH24:MI:SS\') as "TrackerHoraIni",  
    //     TO_CHAR("RSYNC2"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora",        
    //     (CASE WHEN "RSYNC2"."FECHALOC"  >= (SYSDATE - (5/ (24*60))) THEN \'1\' ELSE \'2\' END) AS "Indicador", "RA"."ICONO" AS "Icono",        
    //     "C"."IDCOMISARIA" AS "ComisariaID", "C"."NOMBRE" AS "ComisariaNombre"
        
    //     FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC2"
    //     INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
    //     INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
    //     INNER JOIN (
    //         SELECT "RSYNC1"."IDRADIO", MAX("RSYNC1"."IDRUTASYNC") as "IDRUTASYNC", MIN("RSYNC1"."FECHALOC") AS "HORAINI" FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC1"
    //         WHERE "RSYNC1"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')  
    //               AND "RSYNC1"."FLGCORRECTO" = 1
    //               AND "RSYNC1"."PROVEEDOR" = 1  
    //               AND "RSYNC1"."IDRADIO" IS NOT NULL
    //         GROUP BY "RSYNC1"."IDRADIO"
    //     ) "G" ON "G"."IDRADIO"="RA"."IDRADIO" AND "G"."IDRUTASYNC" = "RSYNC2"."IDRUTASYNC"
    //     WHERE 1=1 AND "RA"."FLGACTIVO" = 1 '.$q_where.' AND "RA"."PLACA" IS NOT NULL
    //     ORDER BY  "C"."NOMBRE", "C"."IDCOMISARIA", "RSYNC2"."FECHALOC" DESC
    //     ';

    //   // NVL(NVL("RA"."FLGACTIVO","VH"."FLGACTIVO"),0) = 1
    //    $query = $this->db->query($sql);
    //   // echo $sql;
    //    return $query->result_array();   
    // }



    // function get_GPSbyFechaAct($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $idvehiculo = 0, $motorizado = false, $placa = '', $etiqueta = '', $serie = '', $ubigeo = '', $idcomisaria = 0)
    // {
    //     $q_where = '';

    //     $fecha_arr = explode('/',$fecha);
    //     $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
    //     $fechaini = $fechanum.' 00:00:00';
    //     $fechafin = $fechanum.' 00:00:00';

    //     if($hora_ini!='' && $hora_fin!=''){
    //         $fechaini = $fechanum.' '.$hora_ini.':00';
    //         $fechafin = $fechanum.' '.$hora_fin.':00';
    //     }

    //     if($idcomisaria > 0){
    //         $q_where .= ' AND "C"."IDCOMISARIA" = '.$idcomisaria.' ';
    //     }

    //     if($ubigeo!='' && $ubigeo!='0'){
    //         if(substr($ubigeo,2,4) == '0000'){ //Departamento
    //             $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
    //         }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
    //             $q_where .= ' AND SUBSTR("C"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
    //         }else{ //Distrito
    //             $q_where .= ' AND "C"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
    //         }
    //     }

    //     if($idradio > 0){
    //       $q_where .= ' AND "RA"."IDRADIO" = \''.trim($idradio).'\' ';
    //     }

    //     if($placa != ''){
    //       $q_where .= ' AND "RA"."PLACA"  LIKE \'%'.trim($placa).'%\' ';
    //     }

    //     if($etiqueta != ''){
    //       $q_where .= ' AND "RA"."ETIQUETA"  LIKE \'%'.trim($etiqueta).'%\' ';
    //     }

    //     if($serie != ''){
    //       $q_where .= ' AND "RA"."SERIE"  LIKE \'%'.trim($serie).'%\' ';
    //     }


    //     if($motorizado){
    //       $q_where .= ' AND "RA"."IDMODELOVH" IN(\'3\') ';
    //     }else{
    //       $q_where .= ' AND "RA"."IDMODELOVH" IN(\'1\',\'2\',\'5\') ';
    //     }


    //     $sql = '
    //     SELECT "RA"."IDRADIO" as "RadioID",
    //     "RA"."ETIQUETA" as "RadioEtiqueta", "RA"."PLACA" as "VehiculoPlaca", 
    //     "RA"."LATITUD" as "TrackerLat", "RA"."LONGITUD" as "TrackerLong", "RA"."VELOCIDAD" as "TrackerVelocidad", 
    //     TO_CHAR("RA"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", 
    //     TO_CHAR("RA"."FECHALOCINI", \'HH24:MI:SS\') as "TrackerHoraIni",  
    //     TO_CHAR("RA"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora",        
    //     (CASE WHEN "RA"."FECHALOC"  >= (SYSDATE - (5/ (24*60))) THEN \'1\' ELSE \'2\' END) AS "Indicador", "RA"."ICONO" AS "Icono",          
    //     "C"."IDCOMISARIA" AS "ComisariaID", "C"."NOMBRE" AS "ComisariaNombre"
        
    //     FROM "SISGESPATMI"."TM_RADIO" "RA"
    //     INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
    //     WHERE 1=1 AND "RA"."FLGACTIVO" = 1 '.$q_where.' AND "RA"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\') AND "RA"."PLACA" IS NOT NULL
    //     ORDER BY  "C"."NOMBRE", "C"."IDCOMISARIA", "RA"."FECHALOC" DESC
    //     ';

    //   // NVL(NVL("RA"."FLGACTIVO","VH"."FLGACTIVO"),0) = 1
    //    $query = $this->db->query($sql);      
    //    return $query->result_array();   
    //    // echo $sql;
    // }



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
            SELECT "TrackerLat","TrackerLong","TrackerVelocidad","TrackerHora"  FROM(
            SELECT DISTINCT "RUSYNC"."LATITUD" as "TrackerLat", "RUSYNC"."LONGITUD" as "TrackerLong", "RUSYNC"."VELOCIDAD" as "TrackerVelocidad", TO_CHAR("RUSYNC"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora", "RUSYNC"."FECHALOC"
            FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RUSYNC"
            INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RUSYNC"."IDRADIO"
            WHERE "RUSYNC"."FLGCORRECTO" = 1 AND "RUSYNC"."PROVEEDOR" = 1
             AND "RA".FLGACTIVO = 1 
             AND "RUSYNC"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\',\'YYYY-MM-DD HH24:MI:SS\')
            AND "RUSYNC"."IDRADIO" = \''.$idradio.'\'
            ) "REP"
            ORDER BY "FECHALOC" DESC';
        //echo $sql;
        $query = $this->db->query($sql);
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
          $q_where .= ' AND "RA"."IDMODELOVH" IN(\'3\') ';
              if($placa_radio!='' && $placa_radio!='0')
              {
                   $q_where .= 'AND "RA"."PLACA" like \'%'.strtoupper($placa_radio).'%\' '; 
              }
        }else{
          $q_where .= ' AND "RA"."IDMODELOVH" IN(\'1\',\'2\',\'5\') ';
           if($placa!='' && $placa!='0')
            {
                 $q_where .= 'AND "RA"."PLACA" like \'%'.strtoupper($placa).'%\' '; 
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

        $sql = 'SELECT "U"."DEPARTAMENTO" as "DEPARTAMENTO",
                   "U"."PROVINCIA" as "PROVINCIA", 
                   "U"."DISTRITO" as "DISTRITO",
                   "C"."NOMBRE" AS "ComisariaNombre",
                   "RA"."IDRADIO" as "RadioID",
                   "RA"."PLACA" as "VehiculoPlaca",
                   "RA"."ETIQUETA" as "RadioEtiqueta",
                   TO_CHAR("RSYNC2"."FECHALOC",\'DD/MM/YYYY\') as "Fecha",
                   TO_CHAR("RSYNC2"."FECHALOC",\'HH24:MI:SS\') as "Hora",
                   TO_CHAR(MIN("RSYNC2"."FECHAREG"), \'HH24:MI:SS\') as "TrackerHoraIni",
                   TO_CHAR(MAX("RSYNC2"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraIni",
                   (CASE 
                           WHEN "RSYNC2"."HORALOC">=\'000000\' AND "RSYNC2"."HORALOC" <=\'055959\' THEN \'1-MADRUGADA\'
                           WHEN "RSYNC2"."HORALOC">=\'060000\' AND "RSYNC2"."HORALOC" <=\'115959\' THEN \'2-MAÑANA\'
                           WHEN "RSYNC2"."HORALOC">=\'120000\' AND "RSYNC2"."HORALOC" <=\'175959\' THEN \'3-TARDE\'
                           WHEN "RSYNC2"."HORALOC">=\'180000\' AND "RSYNC2"."HORALOC" <=\'235959\' THEN \'4-NOCHE\'
                           ELSE \'X\'
                    END) as TURNO,
                   CAST(MAX("RSYNC2"."VELOCIDAD")as NUMERIC(10,2)) "TrackerVelocidad" ,
                   "SISGESPATMI".UFUN_GETDISTANCIASYNC("RA"."IDRADIO", NULL, MIN("RSYNC2"."FECHALOC"),MAX("RSYNC2"."FECHALOC")) AS "TrackerDistancia"
              FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC2"
              INNER JOIN "SISGESPATMI"."TM_RADIO" "RA"
                ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
              INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C"
                ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
              LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" 
                ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
             INNER JOIN (SELECT "RSYNC1"."IDRADIO",
                                MAX("RSYNC1"."IDRUTASYNC") as "IDRUTASYNC"
                           FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC1"
                          WHERE "RSYNC1"."FECHALOC" BETWEEN  
                                TO_DATE(\''.$fechaini.'\', \'YYYY-MM-DD HH24:MI:SS\') AND
                                TO_DATE(\''.$fechafin.'\', \'YYYY-MM-DD HH24:MI:SS\')
                            AND "RSYNC1"."FLGCORRECTO" = 1
                            AND "RSYNC1"."PROVEEDOR" = 1
                          GROUP BY "RSYNC1"."IDRADIO"
                          ) "G"
                ON "G"."IDRADIO" = "RSYNC2"."IDRADIO"
               AND "G"."IDRUTASYNC" = "RSYNC2"."IDRUTASYNC"
             WHERE 1 = 1
               AND "RA".FLGACTIVO =1 
               AND "RA"."PLACA" IS NOT NULL
               AND "RSYNC2"."FLGCORRECTO" = 1
               AND "RSYNC2"."PROVEEDOR" = 1
               '.$q_where.'
             GROUP BY "U"."DEPARTAMENTO",
                      "U"."PROVINCIA",
                      "U"."DISTRITO",
                      "C"."NOMBRE",
                      "RA"."IDRADIO",
                      "RA"."PLACA",
                      "RA"."ETIQUETA",
                      "C"."IDCOMISARIA",
                      "RSYNC2"."FECHALOC",
                      "RSYNC2"."VELOCIDAD",
                      (CASE 
                           WHEN "RSYNC2"."HORALOC">=\'000000\' AND "RSYNC2"."HORALOC" <=\'055959\' THEN \'1-MADRUGADA\'
                           WHEN "RSYNC2"."HORALOC">=\'060000\' AND "RSYNC2"."HORALOC" <=\'115959\' THEN \'2-MAÑANA\'
                           WHEN "RSYNC2"."HORALOC">=\'120000\' AND "RSYNC2"."HORALOC" <=\'175959\' THEN \'3-TARDE\'
                           WHEN "RSYNC2"."HORALOC">=\'180000\' AND "RSYNC2"."HORALOC" <=\'235959\' THEN \'4-NOCHE\'
                           ELSE \'X\'
                    END)
                     ORDER BY 1,2,3,4,5,6,7';


       // inicio
        $query = $this->db->query($sql);


        //echo $sql;
         return $query->result_array();   
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


        $sql = 'SELECT  "U"."DEPARTAMENTO" as "UbigeoDepartamento",
                        "U"."PROVINCIA" as "UbigeoProvincia", 
                        "U"."DISTRITO" as "UbigeoDistrito", 
                        "C"."NOMBRE" as "ComisariaNombre",
                        "RA"."IDRADIO" AS "RadioID",
                        "RA"."PLACA" as "VehiculoPlaca",   
                        "RA"."ETIQUETA" as "RadioEtiqueta",                 
                        TO_CHAR("RSYNC2"."FECHALOC", \'YYYY-MM-DD\') as "TrackerFecha",   
                        TO_CHAR(MIN("RSYNC2"."FECHALOC"),\'HH24:MI:SS\') as "TrackerHoraIni",
                        TO_CHAR(MAX("RSYNC2"."FECHALOC"),\'HH24:MI:SS\') as "TrackerHoraFin",
                        "SISGESPATMI".UFUN_GETDISTANCIASYNC("RA"."IDRADIO", NULL, MIN("RSYNC2"."FECHALOC"),MAX("RSYNC2"."FECHALOC")) AS "TrackerKm"
               FROM "SISGESPATMI"."'.$this->_tbl_ruta($fecha).'" "RSYNC2"
               INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "RSYNC2"."IDRADIO"
               LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA" 
               LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
                WHERE 1=1  
                AND "RSYNC2"."PROVEEDOR" = 1
                AND "RSYNC2"."IDRADIO" = '.(int)$idradio.'                 
                AND "RSYNC2"."FECHALOC" BETWEEN TO_DATE(\''.$fechaini.'\', \'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\', \'YYYY-MM-DD HH24:MI:SS\')
                AND "RSYNC2".FLGCORRECTO = 1
                GROUP BY  "U"."DEPARTAMENTO",
                        "U"."PROVINCIA", 
                        "U"."DISTRITO", 
                        "C"."NOMBRE",
                        "RA"."IDRADIO",
                        "RA"."PLACA",   
                        "RA"."ETIQUETA",                
                        TO_CHAR("RSYNC2"."FECHALOC", \'YYYY-MM-DD\')';

        /*$sql = 'SELECT  "U"."DEPARTAMENTO" as "UbigeoDepartamento",
                        "U"."PROVINCIA" as "UbigeoProvincia", 
                        "U"."DISTRITO" as "UbigeoDistrito", 
                        "C"."NOMBRE" as "ComisariaNombre",
                        "RA"."IDRADIO" AS "RadioID",
                        "RA"."PLACA" as "VehiculoPlaca",   
                        "RA"."ETIQUETA" as "RadioEtiqueta"
               FROM "SISGESPATMI"."TM_RADIO" "RA" 
               LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA" 
               LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
                WHERE 1=1  
                AND "RA"."IDRADIO" = '.(int)$idradio.' ';*/

  
         $query = $this->db->query($sql);
         $array  = $query->result_array();
         $rows = $query->result_array();
         return $rows[0];   
        // echo $rows[0];
         // echo $sql;
    }
}