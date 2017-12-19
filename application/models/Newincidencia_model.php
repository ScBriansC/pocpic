<?php if (!defined('BASEPATH'))
	exit('No direct script access allowed');

require_once 'Base_model.php';

/**
 * Clase Incidencia_Model , entidad de la tabla "TH_INCIDENCIA"
 */
class Newincidencia_Model extends Base_model {
	
	function __construct() {

      parent::__construct('SISTESTIGOMI.INCIDENCIA', 'IDUSUARIO');
      // parent::__construct('SISTESTIGOMI.newincidencia_model', 'IDINCIDENCIA'); 
      // parent::__construct();
      // $this->load->model('newincidencia_model', 'm_incidencia');  

	}

    function get_NuevoID(){
        $query = $this->db->query('SELECT SISTESTIGOMI.USEQ_INCIDENCIA_IDINCIDENCIA.NEXTVAL as IDINCIDENCIA from dual');
        $id = $query->row_array();
        return (int)@$id['IDINCIDENCIA'];
    }

    function get_MaxID(){
        $query = $this->db->query('SELECT MAX(IDINCIDENCIA) as IDINCIDENCIA FROM '.$this->model_name.' WHERE FECHAREG IS NOT NULL');
        $id = $query->row_array();
        return (int)@$id['IDINCIDENCIA'];
    }

    function add_incidencias($titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq){
        $id= $this->get_NuevoID();
        if($id > 0){
            $this->db->set('IDINCIDENCIA',$id);
            $this->db->set('TITULO', $titulo);
            $this->db->set('DETALLE', $detalle);
            $this->db->set('IDTIPO', $idtipo);            
            $this->db->set('IDESTADO',$estado);    
            $this->db->set('LATITUD', $latitud);
            $this->db->set('LONGITUD', $longitud);            
            $this->db->set('DIRECCION',$direccion);
            $this->db->set('IDUSUAPP',null);
            $this->db->set('FLGACTIVO',1);      
            $this->db->set('IDUSUREG',$idusuario);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            $this->db->set('IPMAQREG',$ipmaq);
            if($this->db->insert($this->model_name)){
                return $id;
            }
            // print_r($this->db->last_query());


        }
        return FALSE;

       // return  'as';
       
    }

    function update_indicendia($id,$titulo,$detalle,$idtipo,$estado,$direccion,$latitud,$longitud,$idusuario,$ipmaq){

      if($id > 0){
            $this->db->set('TITULO', $titulo);
            $this->db->set('DETALLE', $detalle);
            $this->db->set('IDTIPO', $idtipo);
            $this->db->set('IDESTADO', $estado);
            $this->db->set('LATITUD', $latitud);
            $this->db->set('LONGITUD', $longitud);
            $this->db->set('DIRECCION', $direccion);
            $this->db->set('IDUSUMOD', $idusuario);
            $this->db->set('FECHAMOD',"(SYSDATE)",false);
            $this->db->where('IDINCIDENCIA', $id);
            if($this->db->update($this->model_name)){
                return $id;
            }
 
      }
      return FALSE;

    }

    function get_incidencias(){

        $sql = 'SELECT I.IDINCIDENCIA  as "Incidencia_ID", 
       I.TITULO as "Incidencia_Titulo",
       I.DETALLE as "Incidencia_Detalle",
       T.NOMBRE as "Incidecincia_Tipo",                       
       I.DIRECCION as "Incidencia_Direccion",
       I.LATITUD as "Incidencia_Latitud",
       I.LONGITUD as "Incidencia_Longitud",
       E.NOMBRE as "Incidencia_EstadoPublic",
       TO_CHAR(I.FECHAREG,\'DD/MM/YYYY\') as "Incidencia_FechaReg"
       FROM SISTESTIGOMI.INCIDENCIA I
       INNER JOIN SISTESTIGOMI.TIPO T ON I.IDTIPO = T.IDTIPO
       INNER JOIN SISTESTIGOMI.ESTADO E ON I.IDESTADO = E.IDESTADO
       WHERE I.FLGACTIVO= 1 AND T.FLGACTIVO= 1 ';
        $query = $this->db->query($sql);
        return $query->result_array();   

        // echo $sql;
    }

    function get_incidenciaById($id){

      $sql = 'SELECT I.IDINCIDENCIA as "Incidencia_ID",
       I.TITULO as "Incidencia_Titulo",
       I.DETALLE as "Incidencia_Detalle",
       I.IDTIPO as "Incidecincia_Tipo",
       I.DIRECCION as "Incidencia_Direccion",
       I.LATITUD as "Incidencia_Latitud",
       I.LONGITUD as "Incidencia_Longitud",
       I.IDESTADO as "Incidencia_EstadoPublic",
       TO_CHAR(I.FECHAREG, \'DD/MM/YYYY\') as "Incidencia_FechaReg",
       U.NRODOC as "Usuario_DNI",
       U.CORREO as "Usuario_Correo",
       U.ALIAS as "Usuario_Alias",
       U.CELULAR as "Usuario_Celular",
       U.SEXO as "Usuario_Sexo"
       FROM SISTESTIGOMI.INCIDENCIA I
       LEFT JOIN SISTESTIGOMI.USUAPP U ON I.IDUSUAPP = U.IDUSUAPP
       WHERE I.FLGACTIVO = 1
       AND I.IDINCIDENCIA='.$id.'';
          $query = $this->db->query($sql);
          $rows = $query->result_array();
          return $rows[0]; 

        // echo $sql; 

    }

    function get_NuevoIDArchivo(){
        $query = $this->db->query('SELECT SISTESTIGOMI.USEQ_ARCHIVO_IDARCHIVO.NEXTVAL as IDARCHIVO from dual');
        $id = $query->row_array();
        return (int)@$id['IDARCHIVO'];
    }

    function get_MaxIDArchivo(){
        $query = $this->db->query('SELECT MAX(IDARCHIVO) as IDARCHIVO FROM SISTESTIGOMI.ARCHIVO WHERE FECHAREG IS NOT NULL');
        $id = $query->row_array();
        return (int)@$id['IDINCIDENCIA'];
    }

    function add_Archivos($idIncidencia,$idusuario,$ruta,$tipo){
        $id= $this->get_NuevoIDArchivo();

        switch ($tipo) {
          case 'jpg':
             $tipo = '1';
          break;
          case 'jpeg':
             $tipo = '2';
          break;
          case 'png':
             $tipo = '3';
          break;
          case 'gif':
             $tipo = '4';
          break;
          case 'pdf':
             $tipo = '5';
          break;
          case 'mp4':
             $tipo = '6';
          break;
          case 'mp3':
             $tipo = '7';
          break;
          case 'wav':
             $tipo = '8';
          break;
          case 'amr':
             $tipo = '9';
          break;

          default:
            return false;
          break;
        }

        if($id > 0){
            $this->db->set('IDARCHIVO',$id);
            $this->db->set('IDINCIDENCIA', $idIncidencia);
            $this->db->set('RUTA', $ruta);
            $this->db->set('TIPO', $tipo);
            $this->db->set('FLGACTIVO',1);      
            $this->db->set('IDUSUREG',$idusuario);
            $this->db->set('FECHAREG',"(SYSDATE)",false);
            // $this->db->set('IPMAQREG',$ipmaq);
            if($this->db->insert('SISTESTIGOMI.ARCHIVO')){
                return $id;
            }
        }
        return FALSE;
    }
    



}