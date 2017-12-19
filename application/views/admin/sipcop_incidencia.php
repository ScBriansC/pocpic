
<?php
$usu_departamento = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,2).'0000'):'');
$usu_provincia = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,4).'00'):'');
$usu_distrito = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,6)):'');
$tipo_ubigeo = 0;
if(@$usu_ubigeo){
    if($usu_departamento == $usu_ubigeo){
        $tipo_ubigeo = 1;
    }elseif($usu_provincia == $usu_ubigeo){
        $tipo_ubigeo = 2;
    }else{
        $tipo_ubigeo = 3;
    }
}

if($usu_comisaria > 0){
    $tipo_ubigeo = 4;
}

?>

<style>
      #map_incidencia {
        height: 400px;
        width: 100%;
      }
</style>

<script>
var map_incidencia;
var marker_incidepncia;
    
    function initMap(latitud,longitud) {

      var lat = -12.0446;
      var long = -77.0203;

      if(latitud && longitud)
      {
        var lat = Number(latitud);
        var long = Number(longitud);
      }
 
      var lima = {lat: lat, lng: long};
      
        map_incidencia = new google.maps.Map(document.getElementById('map_incidencia'), {
          zoom: 14,
          center: lima
        });

        map_incidencia.addListener('click', function(e) {
            marker_incidencia.setPosition(e.latLng);
            $.get('https://maps.googleapis.com/maps/api/geocode/json', {latlng: e.latLng.lat()+','+e.latLng.lng()}, function(data){
                if(data){
                    $('#txt_mapa_direccion').val(data.results[0].formatted_address);
                }
            }, 'json');
            $('#txtlatitud').val(e.latLng.lat());
            $('#txtlongitud').val(e.latLng.lng());

            /*var geocoder = new google.maps.Geocoder();
            geocoder.geocode({
                'latLng': e.latLng
              }, function(results, status) {
                console.log(status);
                if (status == google.maps.GeocoderStatus.OK) {
                  if (results[0]) {
                    alert(results[0].formatted_address);
                  }
                }
              });*/
          });

        marker_incidencia = new google.maps.Marker({
          position: lima,
          map: map_incidencia
        });

    }

    function load_Markers(lat,long){
      initMap(lat,long)
    }

    function refresh_mapa(){
        setTimeout(function(){
            google.maps.event.trigger(map_incidencia, 'resize');
            map_incidencia.setCenter(marker_incidencia.getPosition())
        },500);
    }

    function filtrar(){
        $('#dgTabla').dataTable().fnClearTable();
        $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').show();

        SipcopJS.post('admin/incidencia/json_incidencias',{
        }, 
            function(data){
            $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            cargarData(data);
        });
    }

    function deleteRow(id){
        SipcopJS.post('admin/incidencia/json_deleteArchivoById',{
          id: id,
          idincidencia: $('#txtid').val(),
        },
        function(data){
           $('#dgDetalle_filter input').val('');
            $('#dgArchivos').dataTable().fnClearTable();            
            $('#dgArchivos').dataTable().fnAddData(data.archivos, true);
            $('#dgArchivos').dataTable().fnDraw();
        });


  
    }

    function get_ById(id){
       SipcopJS.post('admin/incidencia/json_getById',{
          id: id
        }, 
        function(data){
          $('#modal').modal('show');
          $('#txtTitulo').val(data.data.Incidencia_Titulo); 
          $('#txtDetalle').val(data.data.Incidencia_Detalle);
          $("#txtTipo").val(data.data.Incidecincia_Tipo).trigger('change');
          $("#txtEstado").val(data.data.Incidencia_EstadoPublic).trigger('change');
          $("#txtTipo").val(data.data.Incidecincia_Tipo).trigger('change');
          $('#txt_mapa_direccion').val(data.data.Incidencia_Direccion);
          $('#txtid').val(data.data.Incidencia_ID);
          $('#txtlatitud').val(data.data.Incidencia_Latitud);
          $('#txtlongitud').val(data.data.Incidencia_Longitud);


          latitud =data.data.Incidencia_Latitud;
          longitud=data.data.Incidencia_Longitud;
          load_Markers(latitud,longitud);

          // $('.nav-tabs a[href="#tabUsuario"]').tab('show');
          if(data.data.Usuario_DNI != null)
          {
            $('#tabUsuarioTab').show();   
            $('#txtDNI').val(data.data.Usuario_DNI);
            $('#txtCorreo').val(data.data.Usuario_Correo);
            $('#txtAlias').val(data.data.Usuario_Alias);
            $('#txtCelular').val(data.data.Usuario_Celular); 
            if(data.data.Usuario_Sexo == 'M')
            {
              $('#txtSexo').val('Masculino');
            } else{
              $('#txtSexo').val('Feminino');
            }
          }else{
            $('#tabUsuarioTab').hide(); 
          }
          $('#dgArchivos').dataTable().fnAddData(data.archivos, true);
          console.log(data.archivos)
        });

    }

    function cargarData(reporte){
        $('#dgDetalle_filter input').val('');
        $('#dgTabla').dataTable().fnClearTable();
        $('#dgTabla').dataTable().fnAddData(reporte.data);
        $('#dgTabla').dataTable().fnDraw();
     }


    preCarga = function(){
      $('#dgTabla').dataTable({
          "bSort": true,
          "bFilter": false,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : "ID", mData: "Incidencia_ID"},
            {sTitle : "TÍTULO", mData: "Incidencia_Titulo"},
            {sTitle : "DETALLE", mData: "Incidencia_Detalle"},
            {sTitle : "TIPO", mData: "Incidecincia_Tipo"},
            {sTitle : "DIRECCIÓN", mData: "Incidencia_Direccion"},
            // {sTitle : "DNI", mData: "Incidencia_DNI"},
            {sTitle : "ESTADO", mData: "Incidencia_EstadoPublic"},
            {sTitle : "FECHA REGISTRO", mData: "Incidencia_FechaReg"},
            {sTitle : "ACCIONES", mData: "Incidencia_ID", mRender: function(value, type, row){
                return '<a href="javascript:;" type="button" class="btn btn-info"  onclick="modal('+value+',2)"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>';
            }}

          ], 
          "bServerSide" : false,
          "bProcessing" : true
        });

      $('#dgArchivos').dataTable({
          "bSort": true,
          "bFilter": false,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : "Archivo", mData: "ArchivoNombre"},
            {sTitle : "Tipo", mData: "ArchivoTipo"},
            {sTitle : "Acción", mRender: function(value, type, row){
                return '<a type="button" class="btn btn-success" target="_blank"  href="archivos/soytestigo/'+row.ArchivoNombre+'" ><i class="fa fa-search" aria-hidden="true"></i></a> ' +
                ' <a href="javascript:;" onclick="deleteRow('+row.ArchivoId+')" type="button" class="btn btn-danger"><i class="fa fa-trash-o" aria-hidden="true"></i></a>';
            }}

          ], 
          "bServerSide" : false,
          "bProcessing" : true
        });

      $('#btnAgregarArchivo').click(function(){
        var archivo = {
          ArchivoID:0,
          ArchivoNombre:$('#txtArchivo').val(),
          ArchivoTipo: $('#txtArchivo').val().substr(($('#txtArchivo').val().lastIndexOf('.') + 1)),
        };
        $('#dgArchivos').data('nuevos').push(archivo);
        $('#dgArchivos').dataTable().fnAddData([archivo], true);
      });
      
      filtrar()
    }

    function guardar(){

      var id = $('#txtid').val();
      var titulo = $('#txtTitulo').val();
      var detalle= $('#txtDetalle').val();
      var tipo= $('#txtTipo').val();
      var estado= $('#txtEstado').val();
      var direccion= $('#txt_mapa_direccion').val();
      var latitud= $('#txtlatitud').val();
      var longitud= $('#txtlongitud').val();

      if(id > 0){
        actualizar();
      }
      else {
        SipcopJS.post('admin/incidencia/json_addIncidencia',{
              id: $('#txtid').val(),
              titulo: $('#txtTitulo').val(),
              detalle: $('#txtDetalle').val(),
              tipo: $('#txtTipo').val(),
              estado: $('#txtEstado').val(),
              direccion: $('#txt_mapa_direccion').val(),
              latitud: $('#txtlatitud').val(),
              longitud: $('#txtlongitud').val(),
              archivos: $('#dgArchivos').data('nuevos')
          }, function(data){
              if(data.status=='success'){
                $('#modal').modal('hide');
                limpiar();
                filtrar();
                swal("Correcto", data.msj, "success");        
              }
              else{
                swal("Cancelado", data.msj, "error");
              }
          });
      }

      

    }

    function actualizar(){
        SipcopJS.post('admin/incidencia/json_updateIncidencia',{
            id: $('#txtid').val(),
            titulo: $('#txtTitulo').val(),
            detalle: $('#txtDetalle').val(),
            tipo: $('#txtTipo').val(),
            estado: $('#txtEstado').val(),
            direccion: $('#txt_mapa_direccion').val(),
            latitud: $('#txtlatitud').val(),
            longitud: $('#txtlongitud').val(),
            archivos: $('#dgArchivos').data('nuevos')
        }, function(data){
            if(data.status=='success'){
              $('#modal').modal('hide');
              limpiar();
              filtrar();
              swal("Correcto", data.msj, "success");        
            }
            else{
              swal("Cancelado", data.msj, "error");
            }
        });
    }


    function limpiar(){
      $('.nav-tabs a[href="#tabIncidencia"]').tab('show');
      $('#txtTitulo').val('');
      $('#txtDetalle').val('');
      $("#txtTipo").val('0').trigger('change');
      $("#txtEstado").val('0').trigger('change');
      $('#txt_mapa_direccion').val('');
      $('#txtlatitud').val('');
      $('#txtlongitud').val('');
      $('#txtArchivo').val('');
      $('#txtid').val('');

      $('#fCargaArchivo').html('');

      $('#dgArchivos').dataTable().fnClearTable();

      $('#txtTitulo').parent().attr("class", "form-group");
      $('#txtTitulo').parent().children('span').text("").show();
      $('#txtTitulo').parent().children('#glypcntxtTitulo').remove();
      $('#txtDetalle').parent().attr("class", "form-group");
      $('#txtDetalle').parent().children('span').text("").show();
      $('#txtDetalle').parent().children('#glypcntxtDetalle').remove();
      $('#txt_mapa_direccion').parent().attr("class", "form-group");
      $('#txt_mapa_direccion').parent().children('span').text("").show();
      $('#txt_mapa_direccion').parent().children('#glypcntxt_mapa_direccion').remove();
      $('#txtTipo').parent().attr("class", "form-group");
      $('#txtEstado').parent().attr("class", "form-group");
    }

    function modal(id,tipo){
      $('#dgArchivos').data('nuevos', []);

        if(!id && tipo==1){
            $('#modal').modal('show');
            $('#basicModalLabel1').show();
            $('#basicModalLabel2').hide(); 
            $('#btnGuardar').show();
            // $('#btnActualizar').hide();   
        }
        else{
            $('#modal').modal('show');
            // $('#dgArchivos').data('nuevos', []);
            get_ById(id);
            $('#basicModalLabel2').show();
            $('#basicModalLabel1').hide(); 
            // $('#btnActualizar').show();
            // $('#btnGuardar').hide(); 
            $('#btnGuardar').show();
        }
        refresh_mapa();
    }

 
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Listado de Incidencias
            </header>
            <div class="panel-body">
            <div id="Reporte">
                <a href="javascript:;" type="button" class="btn btn-success"  onclick="modal('',1)"><i class="fa fa-plus" aria-hidden="true"></i></a>
                <div class="col-sm-12">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">                   
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </div>
</div>


<div id="modal" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close" onclick="limpiar()"><span aria-hidden="true">×</span></button>
            <h4 id="basicModalLabel1" class="modal-title" style="display: none;">  Crear Registro de Incidencia&nbsp;&nbsp;&nbsp;</h4>
            <h4 id="basicModalLabel2" class="modal-title" style="display: none;">  Actualizar Registro  de Incidencia&nbsp;&nbsp;&nbsp;</h4>
      </div>
      <div class="modal-body">
        <div class="adv-table">
            <!-- <div class="row"></div> -->
            <div>
                  <!-- Nav tabs -->
                  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#tabIncidencia" aria-controls="tabIncidencia" role="tab" data-toggle="tab">Incidencia</a></li>
                    <li role="presentation"><a href="#tabArchivo" aria-controls="tabArchivo" role="tab" data-toggle="tab">Archivo</a></li>
                    <li role="presentation"><a href="#tabUsuario" id="tabUsuarioTab" aria-controls="tabUsuario" role="tab" data-toggle="tab" style="display:none;">Usuario</a></li>
                  </ul>

                  <!-- Tab panes -->
                  <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="tabIncidencia">
                        <div class="container-fluid">            
                            <div class="row">
                            <form id="myform">
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label for="txtTitulo">Título:</label>
                                        <input type="text" class="form-control" name="txtTitulo" id="txtTitulo" onkeyup="validacion('txtTitulo');" placeholder="Escriba Título">
                                        <span class="help-block"></span>
                                   </div>
                                   <div class="form-group">
                                        <label for="txtDetalle">Detalle:</label>                                    
                                        <textarea class="form-control" name="txtDetalle" id="txtDetalle" rows="7" required maxlength="357" style="resize: none" onkeyup="validacion('txtDetalle');" placeholder="Escriba Detalle, Maximo 357 Caracteres"></textarea>
                                        <span class="help-block"></span>
                                   </div>
                                    <div class="form-group">
                                        <label for="txtTipo">Tipo:</label>      
                                        <select name="txtTipo" id="txtTipo" class="select-fil form-control" required>
                                            <option value="0">-- Seleccione --</option>
                                            <?php foreach ($incidencia_tipo as $tipo) { ?>
                                            <option value="<?php echo $tipo['IDTIPO']; ?>"><?php echo $tipo['NOMBRE']; ?></option>
                                            <?php } ?>
                                        </select> 
                                    </div>
                                    <div class="form-group">
                                        <label for="txtEstado">Estado:</label>          
                                        <select name="txtEstado" id="txtEstado" class="select-fil form-control" required>                                                                 
                                            <option value="0">-- Seleccione --</option>
                                            <?php foreach ($incidencia_estado as $estado) { ?>
                                            <option value="<?php echo $estado['IDESTADO']; ?>"><?php echo $estado['NOMBRE']; ?></option>
                                            <?php } ?>
                                        </select> 
                                    </div>
                                    <div class="form-group">
                                        <label for="txt_mapa_direccion">Dirección:</label>                                    
                                        <input type="text" class="form-control" id="txt_mapa_direccion" placeholder="Seleccione Dirección usando el Mapa" required onkeyup="validacion('txt_mapa_direccion');" placeholder="Escriba Dirección" disabled="">
                                        <span class="help-block"></span>
                                    </div>
                                    <input type="hidden" name="txtid" id="txtid" >
                                    <input type="hidden" name="txtlatitud" id="txtlatitud" >
                                    <input type="hidden" name="txtlongitud" id="txtlongitud" >
                              </div>
                            </form>
                              <div class="col-md-6">
                                    <div id="map_incidencia"></div>
                              </div>
                            </div>
                        </div>                   
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabArchivo">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-9">
                                    <div class="form-group">
                                        <label for="archivos">Archivos:</label>
                                        <input type="file" class="form-control" id="fArchivo" name="fArchivo" onchange="subirArchivo(this,'#txtArchivo', '#fCargaArchivo')">
                                        <input type="hidden" class="form-control" id="txtArchivo" name="txtArchivo">
                                        <div id="fCargaArchivo"></div>
                                    </div>           
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">          
                                        <label for="archivos">&nbsp;</label><br>
                                        <a id="btnAgregarArchivo" href="javascript:;" type="button" class="btn btn-info"> Cargar</a>       
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <table id="dgArchivos" class="table table-bordered">
                                </table>
                            </div>
                        </div>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="tabUsuario">
                        <div class="container-fluid">            
                            <div class="row">
                            <form id="myform">
                              <div class="col-md-6">
                                  <div class="form-group">
                                        <label for="txtDNI">DNI:</label>
                                        <input type="text" class="form-control" name="txtDNI" id="txtDNI" disabled>
                                        <span class="help-block"></span>
                                   </div>
                                   <div class="form-group">
                                        <label for="txtCorreo">Correo:</label>    
                                        <input type="text" class="form-control" name="txtCorreo" id="txtCorreo" disabled>                                
                                        <span class="help-block"></span>
                                   </div>
                                   <div class="form-group">
                                        <label for="txtAlias">Alias:</label>    
                                        <input type="text" class="form-control" name="txtAlias" id="txtAlias" disabled>                                
                                        <span class="help-block"></span>
                                   </div>
                                   <div class="form-group">
                                        <label for="txtCelular">Celular:</label>    
                                        <input type="text" class="form-control" name="txtCelular" id="txtCelular" disabled>                                
                                        <span class="help-block"></span>
                                   </div>
                                   <div class="form-group">
                                        <label for="txtSexo">Sexo:</label>    
                                        <input type="text" class="form-control" name="txtSexo" id="txtSexo" disabled>                                
                                        <span class="help-block"></span>
                                   </div>
                              </div>
                            </form>
                            </div>
                        </div>                   
                    </div>
                  </div>
            </div>
        </div><br>
        <center><a id="btnGuardar" class="btn btn-success" href="javascript:;" style="display: none;" onclick="verificar();">GUARDAR</a></center>
        <center><a id="btnActualizar" class="btn btn-info" href="javascript:;" style="display: none;" onclick="actualizar();verificar();">ACTUALIZAR</a></center>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>
<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyCveFrTaYBExvmUGD8FWXfB_tP4JW4AV4I&callback=initMap"></script>
<script src="assets/sipcop/js/validar_Incidencia.js"></script>
<script type="text/javascript"> 
  subirArchivo = function(fileInput, sValor, fCarga){
      $(fileInput).simpleUpload("admin/incidencia/subir_archivo", {
          allowedExts: ["jpg", "jpeg", "png", "gif", "pdf", "mp4", "mp3", "wav", "amr"],
          maxFileSize: 2000000,
          start: function(file){
            $(fCarga).html('Preparando archivo...');
          },
          progress: function(progress){
            $(fCarga).html('Subiendo archivo... '+Math.round(progress * 100) / 100+'%');
          },
          success: function(data){
            var obj = JSON.parse(data);
            if(obj.status == 'success'){
              $(sValor).val(obj.archivo);
              $(fCarga).html(obj.archivo);
            }else{
              SipcopJS.msj.error("Error", obj.msj);
            }
          },
          error: function(error){
            $(fCarga).html('');
            SipcopJS.msj.error("Error", error.message);
          }

      });
  }
</script>
