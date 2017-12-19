<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Require Base controller
 */
require_once 'Base_model.php';

/** 
 * Clase Tracker_Model , entidad de la tabla "tracker"
 */
class Tracker_radio_Model extends Base_model { 
    /**
     * Function __construct , Constructor de clase
     */
    function __construct()
    {
        // Crea referencia a la clase de modelo padre
        parent::__construct('SISGESPATMI.TH_RUTA_RADIO', 'IDRUTA');
    }



    function get_Resumen($fecha, $hora_ini='', $hora_fin='' ,$ubigeo = '', $idcomisaria = 0)
    {
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fecha = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];

        if($idcomisaria > 0){
            $q_where .= ' AND  "c2"."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("c2"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("c2"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "c2"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        $sql = '
            SELECT "IDTURNO", COUNT(DISTINCT "IDRADIO") AS "CANTIDAD", MIN( CASE 
                   WHEN "IDTURNO"=1 THEN \'Madrugada\'
                   WHEN "IDTURNO"=2 THEN \'Mañana\'
                   WHEN "IDTURNO"=3 THEN \'Tarde\'
                   WHEN "IDTURNO"=4 THEN \'Noche\'
            END) AS "TURNO"
            FROM (
            select DISTINCT ( CASE 
                   WHEN TO_CHAR("t2"."FECHALOC",\'hh24miss\')>=\'000000\' AND TO_CHAR("t2"."FECHALOC",\'hh24miss\') <=\'055959\' THEN 1
                   WHEN TO_CHAR("t2"."FECHALOC",\'hh24miss\')>=\'060000\' AND TO_CHAR("t2"."FECHALOC",\'hh24miss\') <=\'115959\' THEN 2
                   WHEN TO_CHAR("t2"."FECHALOC",\'hh24miss\')>=\'120000\' AND TO_CHAR("t2"."FECHALOC",\'hh24miss\') <=\'175959\' THEN 3
                   WHEN TO_CHAR("t2"."FECHALOC",\'hh24miss\')>=\'180000\' AND TO_CHAR("t2"."FECHALOC",\'hh24miss\') <=\'235959\' THEN 4
            END) AS "IDTURNO", "RA2"."IDRADIO"
            FROM "SISGESPATMI"."TH_RUTA_RADIO" "t2" 
            LEFT JOIN "SISGESPATMI"."TM_RADIO" "RA2" ON "RA2"."IDRADIO" = "t2"."IDRADIO"
            LEFT JOIN "SISGESPATMI"."TM_COMISARIA" "c2" ON "c2"."IDCOMISARIA" = "RA2"."IDCOMISARIA"
            WHERE
            "RA2".IDMODELOVH = \'3\'
            AND "t2"."FECHALOC"  
            BETWEEN TO_DATE(\''.$fecha.' '.$hora_ini.':00\',\'YYYY-MM-DD HH24:MI:SS\') AND 
            TO_DATE(\''.$fecha.' '.$hora_fin.':59\',\'YYYY-MM-DD HH24:MI:SS\')            
            '.$q_where.'
            ) "REP" WHERE 1=1 
            GROUP BY "IDTURNO" ORDER BY 1';
        $query = $this->db->query($sql);
        // print_r($this->db->last_query());
        // echo $sql;
        // return print_r($sql);
        return $query->result_array();   
    }
    function get_ResumenTotal($fecha, $ubigeo = '', $idcomisaria = 0)
    {
        
        $q_where = '';


        if($idcomisaria > 0){
            $q_where .= ' AND  "C"."IDCOMISARIA" = '.$idcomisaria.' ';
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

        $sql = ' select COUNT( DISTINCT "REP"."IDRADIO") AS TOTAL  from
        "SISGESPATMI"."TH_RUTA_RADIO" "t2" 
        INNER JOIN  "SISGESPATMI"."TM_RADIO" "REP" ON "REP"."IDRADIO" = "t2"."IDRADIO"
        INNER JOIN "SISGESPATMI"."TM_COMISARIA" "C" ON "C"."IDCOMISARIA" = "REP"."IDCOMISARIA"
        WHERE 1=1 AND "REP".IDMODELOVH = \'3\' AND "REP"."FLGACTIVO" = \'1\' 
        AND TRUNC("t2"."FECHALOC") = TO_DATE(\''.$fecha.'\',\'DD/MM/YYYY\') 
                '.$q_where.'  ';

        $query = $this->db->query($sql);

        // return print_r($query) ;
        $data = $query->row_array();
        //echo $sql;
        
        return @(int)$data['TOTAL'];   
    }


    function get_GPSbyFecha($fecha, $hora_ini='', $hora_fin='', $idradio = 0, $etiqueta = '', $tipo = 0, $ubigeo = '', $idcomisaria = 0)
    {
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].$fecha_arr[1].$fecha_arr[0];
        $fechaini = $fechanum.'000000';
        $fechafin = $fechanum.'000000';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.str_replace(':', '', $hora_ini).'00';
            $fechafin = $fechanum.str_replace(':', '', $hora_fin).'00';
        }

        if($idcomisaria > 0){
            $q_where .= ' AND  "c2"."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("c2"."IDUBIGEO",0,2) like \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("c2"."IDUBIGEO",0,4) like \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "c2"."IDUBIGEO" like \''.trim($ubigeo).'\' ';
            }
        }




        $sql = 'SELECT "t2"."IDRUTA" as "TrackerID", "RA2"."IDRADIO" as "RadioID", "RA2"."ETIQUETA" as "RadioEtiqueta", "t2"."LATITUD" as "TrackerLat", "t2"."LONGITUD" as "TrackerLong", "t2"."VELOCIDAD" as "TrackerVelocidad", TO_CHAR("t2"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha", TO_CHAR("t2"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora", 
            NVL("c2"."IDCOMISARIA",0) AS "ComisariaID", NVL("c2"."NOMBRE",\'\') AS "ComisariaNombre", "RA2"."PLACA" AS "VehiculoPlaca", NVL("RA2"."TIPO",\'0\') as "RadioTipoID", 
            (CASE NVL("RA2"."TIPO",\'0\') WHEN \'1\' THEN \'MÓVIL\' WHEN \'2\' THEN \'PORTÁTIL\' ELSE \'N.D.\' END) as "RadioTipo",
            (CASE 
               WHEN "t2"."FECHALOC"  BETWEEN (SYSDATE - (30/ (24*60))) AND 
               SYSDATE THEN \'1\'
               ELSE \'2\'
            END) AS "Indicador",
            (CASE NVL("RA2"."IDMODELOVH",\'0\') WHEN \'1\' THEN \'SSANGYONG\' WHEN \'2\' THEN \'HYUNDAI\' WHEN \'3\' THEN \'MOTORIZADO\' WHEN \'4\' THEN \'BASE\' ELSE \'OTRO\' END) as "VehiculoModelo", NVL("RA2"."IDMODELOVH",\'0\') AS "VehiculoModeloID", NVL("RA2"."ICONO",0) AS "RadioIcono"
        FROM "SISGESPATMI"."TH_RUTA_RADIO" "t2"
        INNER JOIN "SISGESPATMI"."TM_RADIO" "RA2" ON "RA2"."IDRADIO" = "t2"."IDRADIO"
        INNER JOIN "SISGESPATMI"."TM_COMISARIA" "c2" ON "c2"."IDCOMISARIA" = "RA2"."IDCOMISARIA"
        INNER JOIN (
            SELECT "RA"."IDRADIO", MAX("t"."IDRUTA") as "IDRUTA" FROM "SISGESPATMI"."TH_RUTA_RADIO" "t"
            INNER JOIN "SISGESPATMI"."TM_RADIO" "RA" ON "RA"."IDRADIO" = "t"."IDRADIO"
            WHERE   "RA"."FLGACTIVO" = \'1\' AND "RA".IDMODELOVH = \'3\' AND NVL("t"."LATITUD",\'0\') <> \'0\' AND NVL("t"."LONGITUD",\'0\') <> \'0\' AND 
            CAST(TO_CHAR("t"."FECHALOC", \'YYYYMMDDHH24MISS\') AS NUMBER) BETWEEN '.$fechaini.' AND '.$fechafin.'
            '.(($idradio > 0)?(' AND "t"."IDRADIO" = \''.$idradio.'\''):'').' 
            '.(($etiqueta != '')?(' AND ("RA"."ETIQUETA" LIKE \'%'.$etiqueta.'%\' OR "RA"."PLACA" LIKE \'%'.$etiqueta.'%\')'):'').' 
            '.(($tipo > 0)?(' AND "RA"."TIPO" = \''.$tipo.'\''):'').' 
            GROUP BY "RA"."IDRADIO"
        ) "g" ON "g"."IDRADIO" = "t2"."IDRADIO" AND "g"."IDRUTA" = "t2"."IDRUTA" 
        WHERE "RA2"."FLGACTIVO" = \'1\' '.$q_where.' ORDER BY  NVL("c2"."NOMBRE",\'\'), NVL("c2"."IDCOMISARIA",0), "t2"."FECHALOC" DESC';

         // echo $sql;
         $query = $this->db->query($sql);
        //print_r($this->db->last_query());
        return $query->result_array();   
    }



    function get_RutabyFechaRadio($fecha, $hora_ini='', $hora_fin='', $idradio=0)
    {
        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];
        $fechaini = $fechanum.' 00:00:00';
        $fechafin = $fechanum.' 23:59:59';

        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.' '.$hora_ini.':00';
            $fechafin = $fechanum.' '.$hora_fin.':59';
        }

        $sql = 'SELECT  
                    "t2"."IDRUTA" as "TrackerID",
                    "v2"."IDRADIO" as "RadioID",
                    "v2"."PLACA" as "VehiculoPlaca",
                    "t2"."LATITUD" as "TrackerLat",
                    "t2"."LONGITUD" as "TrackerLong",
                    "t2"."VELOCIDAD" as "TrackerVelocidad",
                    TO_CHAR("t2"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha",
                    TO_CHAR("t2"."FECHALOC", \'HH24:MI:SS\') as "TrackerHora"      
                    FROM "SISGESPATMI"."TH_RUTA_RADIO" "t2"
                    INNER JOIN "SISGESPATMI"."TM_RADIO" "v2" ON "v2"."IDRADIO" = "t2"."IDRADIO"
                    WHERE  "v2"."FLGACTIVO" = \'1\'
                              AND NVL("t2"."LATITUD",\'0\') <> \'0\' 
                              AND NVL("t2"."LONGITUD",\'0\') <> \'0\' 
                              AND "t2"."FECHALOC"
                              BETWEEN TO_DATE(\''.$fechaini.'\', \'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fechafin.'\', \'YYYY-MM-DD HH24:MI:SS\')
                              AND "t2"."IDRADIO" = \''.$idradio.'\' 
                              ORDER BY "t2"."FECHALOC" DESC';
   
         $query = $this->db->query($sql);
         return $query->result_array();   
    }


     function get_DetalleRadio($fecha,$hora_ini='', $hora_fin='', $idradio='0')
    {
        
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fecha = $fecha_arr[2].'-'.$fecha_arr[1].'-'.$fecha_arr[0];

        $sql = ' SELECT  NVL("U"."DEPARTAMENTO",\'\') as "UbigeoDepartamento",
                            NVL("U"."PROVINCIA",\'\') as "UbigeoProvincia", 
                            NVL("U"."DISTRITO",\'\') as "UbigeoDistrito",
                            NVL("C"."NOMBRE",\'\') as "ComisariaNombre",
                            "RA"."IDRADIO" as "RadioID",  
                            "RA"."ETIQUETA" as "RadioEtiqueta",  
                            "RA"."PLACA" as "VehiculoPlaca",
                            "RA"."SERIE" as "RadioSerie",
                            "RA"."ORIGEN" as "RadioOrigen",
                            "RA"."TEI" as "RadioTEI",
                            "RA"."UNIDAD" as "RadioUnidad",
                            "RA"."DIVISION" as "RadioDivision",
                            "RA"."MARCA" as "RadioMarca",
                            "RA"."CATEGORIA" as "RadioCategoria", NVL("RA"."TIPO",\'0\') as "RadioTipoID", 
            (CASE NVL("RA"."TIPO",\'0\') WHEN \'1\' THEN \'MÓVIL\' WHEN \'2\' THEN \'PORTÁTIL\' ELSE \'N.D.\' END) as "RadioTipo",
                            TO_CHAR("R"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha",
                            TO_CHAR(MIN("R"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraIni",
                            TO_CHAR(MAX("R"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraFin",
                            CAST(MAX("R"."VELOCIDAD")as NUMERIC(10,2)) "TrackerVelocidad",
                            (CASE 
                               WHEN MAX("R"."FECHALOC")  BETWEEN (SYSDATE - (180/ (24*60))) AND 
                               SYSDATE THEN \'1\'
                               ELSE \'2\'
                            END) AS "Indicador",
            (CASE NVL(MAX("RA"."IDMODELOVH"),\'0\') WHEN \'1\' THEN \'SSANGYONG\' WHEN \'2\' THEN \'HYUNDAI\' WHEN \'3\' THEN \'MOTORIZADO\' WHEN \'4\' THEN \'BASE\' ELSE \'OTRO\' END) as "VehiculoModelo", NVL(MAX("RA"."IDMODELOVH"),\'0\') AS "VehiculoModeloID", NVL(MAX("RA"."ICONO"),0) AS "RadioIcono"
                            FROM "SISGESPATMI"."TM_RADIO" "RA"
                            LEFT JOIN "SISGESPATMI"."TH_RUTA_RADIO" "R" ON "R"."IDRADIO" = "RA"."IDRADIO"
                            LEFT JOIN "SISGESPATMI"."TM_COMISARIA" C ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA"
                            LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
                            WHERE 1=1  
                            AND "RA"."FLGACTIVO" = \'1\'
                            AND "RA"."IDRADIO" = '.$idradio.'
                            AND NVL("R"."LATITUD",\'0\') <> \'0\' AND NVL("R"."LONGITUD",\'0\') <> \'0\'
                            AND "FECHALOC" BETWEEN TO_DATE(\''.$fecha.' '.$hora_ini.':00\',\'YYYY-MM-DD HH24:MI:SS\') AND TO_DATE(\''.$fecha.' '.$hora_fin.':59\',\'YYYY-MM-DD HH24:MI:SS\')
                            GROUP BY    NVL("U"."DEPARTAMENTO",\'\'), 
                                        NVL("U"."PROVINCIA",\'\'), 
                                        NVL("U"."DISTRITO",\'\'), 
                                        NVL("C"."NOMBRE",\'\'),
                                        "RA"."ETIQUETA",
                                        "RA"."PLACA",
                                        "RA"."IDRADIO",
                                        "RA"."TIPO",
                                        "RA"."SERIE",
                                        "RA"."ORIGEN",
                                        "RA"."TEI",
                                        "RA"."UNIDAD",
                                        "RA"."DIVISION",
                                        "RA"."MARCA",
                                        "RA"."CATEGORIA",
                                        TO_CHAR("R"."FECHALOC", \'DD/MM/YYYY\')
                                        ORDER BY 1,2,3,4,5,6,7

                ';

        $query = $this->db->query($sql);
        // $array  = $query->result_array();
        $rows = $query->result_array();
        return $rows[0];   
    }

    function get_ReporteXLS($fecha,$hora_ini='',$hora_fin='', $ubigeo = '', $radio ='')
       {
        $q_where = '';

        $fecha_arr = explode('/',$fecha);
        $fechanum = $fecha_arr[2].$fecha_arr[1].$fecha_arr[0];
        $fechaini = $fechanum.'000000';
        $fechafin = $fechanum.'000000';
        $placa = strtoupper(utf8_decode($radio));


        if($hora_ini!='' && $hora_fin!=''){
            $fechaini = $fechanum.str_replace(':', '', $hora_ini).'00';
            $fechafin = $fechanum.str_replace(':', '', $hora_fin).'00';
        }

        if($idcomisaria > 0){
            $q_where .= ' AND  "C"."IDCOMISARIA" = '.$idcomisaria.' ';
        }

        if($ubigeo!='' && $ubigeo!='0'){
            if(substr($ubigeo,2,4) == '0000'){ //Departamento
                $q_where .= ' AND SUBSTR("U"."IDUBIGEO",0,2) = \''.substr($ubigeo,0,2).'\' ';
            }elseif(substr($ubigeo,4,2) == '00'){ //Provincia
                $q_where .= ' AND SUBSTR("U"."IDUBIGEO",0,4) = \''.substr($ubigeo,0,4).'\' ';
            }else{ //Distrito
                $q_where .= ' AND "U"."IDUBIGEO" = \''.trim($ubigeo).'\' ';
            }
        }

        if($radio!='' && $radio!='0')
        {
             $q_where .= 'AND "RA"."PLACA"= \''.strtoupper($placa).'\' '; 
        }

        $sql = '  SELECT  NVL("U"."DEPARTAMENTO",\'\') as "UbigeoDepartamento",
                 NVL("U"."PROVINCIA",\'\') as "UbigeoProvincia", 
                 NVL("U"."DISTRITO",\'\') as "UbigeoDistrito",
                 NVL("C"."NOMBRE",\'\') as "ComisariaNombre",
                 "RA"."IDRADIO" as RadioID,
                 "RA"."ETIQUETA" AS RadioEtiqueta,  
                 "RA"."PLACA" AS VehiculoPlaca,
                 (CASE WHEN "RA".TIPO = 1 THEN \'MOVÍL\' 
                       ELSE  \'PORTÁTIL\'
                  END) AS TIPO,
                 "RA"."SERIE" AS RadioSerie,
                 "RA"."ORIGEN" AS RadioOrigen,
                 "RA"."TEI" AS RadioTEI,
                 "RA"."UNIDAD" AS RadioUnidad,
                 "RA"."DIVISION" AS RadioDivision,
                 "RA"."MARCA" AS RadioMarca,
                 "RA"."CATEGORIA" AS RadioCategoria,
                 TO_CHAR("R"."FECHALOC", \'DD/MM/YYYY\') as "TrackerFecha",
                 TO_CHAR(MIN("R"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraIni",
                 TO_CHAR(MAX("R"."FECHALOC"), \'HH24:MI:SS\') as "TrackerHoraFin",
                 (CASE WHEN 
                 cast(TO_CHAR("R"."FECHALOC", \'HH24MI\')as INTEGER)>=0 AND 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\'))as INTEGER) <=559 THEN \'1-MADRUGADA\' WHEN 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=600 AND 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=1159 THEN \'2-MAÑANA\' WHEN 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=1200 AND 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=1759 THEN \'3-TARDE\' WHEN 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=1800 AND 
                 cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=2359 THEN \'4-NOCHE\' ELSE \'X\' END) as TURNO,
                 CAST(MAX("R"."VELOCIDAD")as NUMERIC(10,2)) "TrackerVelocidad" 
                 FROM "SISGESPATMI"."TM_RADIO" "RA"
                 LEFT JOIN "SISGESPATMI"."TH_RUTA_RADIO" "R" ON "R"."IDRADIO" = "RA"."IDRADIO"
                 LEFT JOIN "SISGESPATMI"."TM_COMISARIA" C ON "C"."IDCOMISARIA" = "RA"."IDCOMISARIA" 
                  LEFT JOIN "SISGESPATMI"."TB_UBIGEO" "U" ON "C"."IDUBIGEO" = "U"."IDUBIGEO"    
                 WHERE 1=1  
                 AND "RA".IDMODELOVH = \'3\'
                 AND "RA"."FLGACTIVO" = \'1\'
                 AND "RA"."PLACA" LIKE \'PL-%\'
                 '.$q_where.'
                 AND "U"."DEPARTAMENTO" = \'LIMA\'
                 AND NVL("R"."LATITUD",\'0\') <> \'0\' AND NVL("R"."LONGITUD",\'0\') <> \'0\'
                 AND "FECHALOC" BETWEEN TO_DATE('.$fechaini.',\'YYYY/MM/DD HH24:MI:SS\') AND TO_DATE('.$fechafin.',\'YYYY/MM/DD HH24:MI:SS\')
                 GROUP BY    NVL("U"."DEPARTAMENTO",\'\'), 
                    NVL("U"."PROVINCIA",\'\'), 
                    NVL("U"."DISTRITO",\'\'), 
                    NVL("C"."NOMBRE",\'\'),
                    "RA"."ETIQUETA",
                    "RA"."PLACA",
                    "RA"."IDRADIO",
                    "RA"."TIPO",
                    "RA"."SERIE",
                    "RA"."ORIGEN",
                    "RA"."TEI",
                    "RA"."UNIDAD",
                    "RA"."DIVISION",
                    "RA"."MARCA",
                    "RA"."CATEGORIA",
                    TO_CHAR("R"."FECHALOC", \'DD/MM/YYYY\'),
                    (CASE WHEN cast(TO_CHAR("R"."FECHALOC", \'HH24MI\')as INTEGER)>=0 AND 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=559 THEN \'1-MADRUGADA\' WHEN 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=600 AND 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=1159 THEN \'2-MAÑANA\' WHEN 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=1200 AND 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=1759 THEN \'3-TARDE\' WHEN 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER)>=1800 AND 
                     cast((TO_CHAR(FECHALOC,\'HH24MI\')) as INTEGER) <=2359 THEN \'4-NOCHE\' ELSE \'X\' END)
                    ORDER BY 1,2,3,4,5,6,7
                    ';

        // echo $sql;

        $query = $this->db->query($sql);

        $array  = $query->result_array();
        // return $query->result_array();   
    }

    

}