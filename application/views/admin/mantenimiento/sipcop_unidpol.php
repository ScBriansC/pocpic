<script>

function filtrar(){
  var dependencia = SipcopJS.get_dependencia('#txtFiltroMacroreg','#txtFiltroRegpol','#txtFiltroDivter');
  var filtro = {};
  filtro.placa = $('#txtFiltroPlaca').val();
  filtro.dependencia = dependencia.id; 
  filtro.institucion = $('#txtFiltroComisaria').val();

  $('#dgTabla').dataTable().fnClearTable();
  $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').show();

  SipcopJS.post('admin/mantenimiento/json_unidpol',filtro,function(data){
      $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide();
      cargarTabla(data);
  });

}


function cargarTabla(data){
    $('#dgDetalle_filter input').val('');
    $('#dgTabla').dataTable().fnClearTable();
    $('#dgTabla').dataTable().fnAddData(data.data);
    $('#dgTabla').dataTable().fnDraw();
}

preCarga = function(){ 


  $('#btnFiltrar').click(function(ev){
    ev.preventDefault();
    filtrar();
  });

  $('#dgTabla').dataTable({
    "bSort": true,
    "bFilter": false,
    "bPaginate": true,
    "pageLength": 10,
    "bInfo": true,
    "aoColumns": [
      {sTitle : "Placa/Ref", mData: "UnidPolPlaca", mRender: function(value, type, row){
        var info_placa = '';

        if(typeof row.UnidPolPlaca!='undefined' && $.trim(row.UnidPolPlaca)!=''){
          info_placa += row.UnidPolPlaca;
        }

        if(typeof row.UnidPolDesc!='undefined' && $.trim(row.UnidPolDesc)!=''){
          info_placa += (info_placa!=''?' /<br>':'')+row.UnidPolDesc;
        }

        return info_placa;
      }},
      {sTitle : "Tipo", mData: "RadioTipoNombre"},
      {sTitle : "Serie", mData: "RadioSerie"},
      {sTitle : "ID Radio", mData: "RadioID"},
      {sTitle : "Comisaría", mData: "ComisariaNombre"},
      {sTitle : "Tipo Pat.", mData: "PatrullajeNombre"},
      {sTitle : "Estado", mData: "UnidPolEstado"},
      {sTitle : "Transmisión", mData: "ProveedorNombre"},
      {sTitle : "Ult. Transmisión", mData: "FechaLoc"},
      {sTitle : "", mRender: function(value, type, row){
        <?php if($obj_modulo['FLG1'] == 2){ ?>
          return  ' <a href="javascript:;" type="button" class="btn btn-info"  title="Editar unidad policial"  onclick="modal(\''+row.DispoGPS+'\',\'0\')"><i class="fa fa-pencil-square-o" aria-hidden="true"></i></a>'   ;
        <?php  }else{ ?>
          return  ' <a href="javascript:;" type="button" class="btn btn-info"  title="Ver unidad policial"  onclick="modal(\''+row.DispoGPS+'\',\'1\')"><i class="fa fa-search-plus" aria-hidden="true"></i></a>'   ;
        <?php  } ?>
      }}
    ],
    "bServerSide" : false,
    "bProcessing" : true
  });

  

  SipcopJS.cargarDependencia('#txtFiltroMacroreg',0,'0','<?php echo (int)$obj_usuario['IDMACROREG']; ?>', function(){
    SipcopJS.cargarDependencia('#txtFiltroRegpol',1,'<?php echo (int)$obj_usuario['IDMACROREG']; ?>','<?php echo (int)$obj_usuario['IDREGPOL']; ?>',function(){
        SipcopJS.cargarDependencia('#txtFiltroDivter',2,'<?php echo (int)$obj_usuario['IDREGPOL']; ?>','<?php echo (int)$obj_usuario['IDDIVTER']; ?>',function(){
            SipcopJS.cargarDependencia('#txtFiltroComisaria',3,'<?php echo (int)$obj_usuario['IDDIVTER']; ?>','<?php echo (int)$obj_usuario['IDCOMISARIA']; ?>',function(){
                filtrar();
            });
        });
    });
  });

  $('#txtFiltroMacroreg').change(function(){
    $('#txtFiltroRegpol').val('0');
    SipcopJS.cargarDependencia('#txtFiltroRegpol',1,$('#txtFiltroMacroreg').val(),'',null);
    $('#txtFiltroDivter').val('0');
    SipcopJS.cargarDependencia('#txtFiltroDivter',2,$('#txtFiltroRegpol').val(),'',null);
    $('#txtFiltroComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFiltroComisaria',3,$('#txtFiltroDivter').val(),'',null);
  });

  $('#txtFiltroRegpol').change(function(){
    $('#txtFiltroDivter').val('0');
    SipcopJS.cargarDependencia('#txtFiltroDivter',2,$('#txtFiltroRegpol').val(),'',null);
    $('#txtFiltroComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFiltroComisaria',3,$('#txtFiltroDivter').val(),'',null);
  }); 

  $('#txtFiltroDivter').change(function(){
    $('#txtFiltroComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFiltroComisaria',3,$('#txtFiltroDivter').val(),'',null);
  }); 

  

  $('#txtFormMacroreg').change(function(){
    $('#txtFormRegpol').val('0');
    SipcopJS.cargarDependencia('#txtFormRegpol',1,$('#txtFormMacroreg').val(),'',null);
    $('#txtFormDivter').val('0');
    SipcopJS.cargarDependencia('#txtFormDivter',2,$('#txtFormRegpol').val(),'',null);
    $('#txtFormComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFormComisaria',3,$('#txtFormDivter').val(),'',null);
  });

  $('#txtFormRegpol').change(function(){
    $('#txtFormDivter').val('0');
    SipcopJS.cargarDependencia('#txtFormDivter',2,$('#txtFormRegpol').val(),'',null);
    $('#txtFormComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFormComisaria',3,$('#txtFormDivter').val(),'',null);
  }); 

  $('#txtFormDivter').change(function(){
    $('#txtFormComisaria').val('0');
    SipcopJS.cargarDependencia('#txtFormComisaria',3,$('#txtFormDivter').val(),'',null);
  }); 


  $('#txtFormProveedor').change(function(){
    var id = parseInt($(this).val());
    if(id == 0){
      $('#dvFormRadioID, #dvFormOtroID').hide();
      $('#dvFormRadioTipo, #dvFormRadioSerie, #dvFormRadioModelo, #dvFormRadioTEI, #dvFormRadioCategoria, #dvFormRadioMarca').hide();
    }else if(id == 1){
      $('#dvFormRadioID').show();
      $('#dvFormOtroID').hide();
      $('#dvFormRadioTipo, #dvFormRadioSerie, #dvFormRadioModelo, #dvFormRadioTEI, #dvFormRadioCategoria, #dvFormRadioMarca').show();
    }else if(id == 2){
      $('#dvFormRadioID, #dvFormOtroID').hide();
      $('#dvFormRadioTipo, #dvFormRadioSerie, #dvFormRadioModelo, #dvFormRadioTEI, #dvFormRadioCategoria, #dvFormRadioMarca').hide();
    }else if(id == 3){
      $('#dvFormRadioID').hide();
      $('#dvFormOtroID').show();
      $('#dvFormRadioTipo, #dvFormRadioSerie, #dvFormRadioModelo, #dvFormRadioTEI, #dvFormRadioCategoria, #dvFormRadioMarca').hide();
    }
  }); 


  $('#txtFormPatrullaje').change(function(){
    var id = parseInt($(this).val());
    if(id == 1 || id == 2 || id == 6 || id == 7){
      $('#dvFormPlaca').show();
      $('#dvFormTipoVH, #dvFormMarcaVH, #dvFormModeloVH').show();
    }else{
      $('#dvFormPlaca').hide();
      $('#dvFormTipoVH, #dvFormMarcaVH, #dvFormModeloVH').hide();
    }
  }); 

  $('#txtFormTipoVH').change(function(){
    var id = parseInt($(this).val());
    $('#txtFormMarcaVH option').hide();
    $.each($('#txtFormMarcaVH option'), function(idx, objx){
      var op = $(objx).attr('tipovh');
      if(typeof op=='undefined' || parseInt(op) == id){
        $(objx).show();
      }
    });
  }); 

  $('#txtFormMarcaVH').change(function(){
    var id = parseInt($(this).val());
    $('#txtFormModeloVH option').hide();
    $.each($('#txtFormModeloVH option'), function(idx, objx){
      var op = $(objx).attr('marcavh');
      if(typeof op=='undefined' || parseInt(op) == id){
        $(objx).show();
      }
    });
  }); 




}



function limpiarForm(){  

  $('#txtFormDispoGPS').val('0');
  $('#txtFormDesc').val('');
  $('#txtFormPlaca').val('');
  $('#txtFormPlaca').prop('readonly',false);
  $('#txtFormPatrullaje').val('0').trigger('change');
  $('#txtFormProveedor').val('0').trigger('change');

  $('#txtFormTipoVH').val('0').trigger('change');
  $('#txtFormMarcaVH').val('0').trigger('change');
  $('#txtFormModeloVH').val('0').trigger('change');
  
  $('#txtFormRadioID').val('');
  $('#txtFormOtroID').val('');
  $('#txtFormRadioTipo').val('0');
  $('#txtFormRadioSerie').val('');
  $('#txtFormRadioModelo').val('');
  $('#txtFormRadioOrigen').val('');
  $('#txtFormRadioTEI').val('');
  $('#txtFormObservacion').val('');
  $('#txtFormRadioCategoria').val('');
  $('#txtFormRadioMarca').val('');
  $('#txtFormEstado').val('0');
  $('#txtFormMotivoAnt').val('');
  $('#txtFormMotivo').val('');
  $('#dvFormMotivo').hide();
}

function modal(dispogps, ver){
  limpiarForm();
  if(dispogps > 0){
    SipcopJS.post('admin/mantenimiento/json_unidpol',{dispogps:dispogps}, function(data){

      var unidpol = data.data[0];
      $('#txtFormDispoGPS').val(unidpol.DispoGPS);
      $('#txtFormDesc').val(unidpol.UnidPolDesc);
      $('#txtFormPlaca').val(unidpol.UnidPolPlaca).prop('readonly', true);
      $('#txtFormPatrullaje').val(unidpol.PatrullajeID).trigger('change');
      $('#txtFormProveedor').val(unidpol.ProveedorID).trigger('change');

      $('#txtFormTipoVH').val(unidpol.TipoVHID).trigger('change');
      $('#txtFormMarcaVH').val(unidpol.MarcaVHID).trigger('change');
      $('#txtFormModeloVH').val(unidpol.ModeloVHID).trigger('change');
      
      $('#txtFormRadioID').val(unidpol.RadioID);
      $('#txtFormOtroID').val(unidpol.OtroID);
      $('#txtFormRadioTipo').val(unidpol.RadioTipo);
      $('#txtFormRadioSerie').val(unidpol.RadioSerie);
      $('#txtFormRadioModelo').val(unidpol.RadioModelo);
      $('#txtFormRadioOrigen').val(unidpol.RadioOrigen);
      $('#txtFormRadioTEI').val(unidpol.RadioTEI);
      $('#txtFormObservacion').val(unidpol.UnidPolObs);
      $('#txtFormRadioCategoria').val(unidpol.RadioCategoria);
      $('#txtFormRadioMarca').val(unidpol.RadioMarca);
      $('#txtFormEstado').val(unidpol.UnidPolFlagActivo);
      $('#txtFormMotivoAnt').val(unidpol.UnidPolMotivo);
      $('#txtFormMotivo').val('');
      $('#dvFormMotivo').show();

      $('#modalTitulo').html('Editar unidad policial');

      SipcopJS.cargarDependencia('#txtFormMacroreg',0,'0',unidpol.MacroregID, function(){
        SipcopJS.cargarDependencia('#txtFormRegpol',1,unidpol.MacroregID,unidpol.RegpolID,function(){
            SipcopJS.cargarDependencia('#txtFormDivter',2,unidpol.RegpolID,unidpol.DivterID,function(){
                SipcopJS.cargarDependencia('#txtFormComisaria',3,unidpol.DivterID,unidpol.ComisariaID,function(){
                    $('#modalForm').modal('show');
                    if(ver == '1'){
                      $('#modalForm input').prop('readonly', true);
                      $('#modalForm select').prop('disabled', true);
                    }else{
                      $('#modalForm input').prop('readonly', false);
                      $('#modalForm select').prop('disabled', false);

                      $('#txtFormPlaca').prop('readonly',true);
                    }
                });
            });
        });
      });

      
    });
  }else{
    SipcopJS.cargarDependencia('#txtFormMacroreg',0,'0','<?php echo (int)$obj_usuario['IDMACROREG']; ?>', function(){
      SipcopJS.cargarDependencia('#txtFormRegpol',1,'<?php echo (int)$obj_usuario['IDMACROREG']; ?>','<?php echo (int)$obj_usuario['IDREGPOL']; ?>',function(){
          SipcopJS.cargarDependencia('#txtFormDivter',2,'<?php echo (int)$obj_usuario['IDREGPOL']; ?>','<?php echo (int)$obj_usuario['IDDIVTER']; ?>',function(){
              SipcopJS.cargarDependencia('#txtFormComisaria',3,'<?php echo (int)$obj_usuario['IDDIVTER']; ?>','<?php echo (int)$obj_usuario['IDCOMISARIA']; ?>',function(){
                  $('#modalTitulo').html('Nueva unidad policial');
                  $('#modalForm').modal('show');

                   $('#txtFormPlaca').prop('readonly',false);
              });
          });
      });
    });
    
  }
}

function guardar(){
  var msj_err = '';

  var obj = {};
  obj.dependencia = SipcopJS.get_dependencia('#txtFiltroMacroreg','#txtFiltroRegpol','#txtFiltroDivter');
  obj.dependencia = obj.dependencia.id;
  obj.comisaria = parseInt($('#txtFormComisaria').val());

  obj.dispogps = parseInt($('#txtFormDispoGPS').val());
  obj.descripcion = $.trim($('#txtFormDesc').val()).toUpperCase();
  obj.placa = $.trim($('#txtFormPlaca').val()).toUpperCase();
  obj.patrullaje = parseInt($('#txtFormPatrullaje').val());
  obj.proveedor = parseInt($('#txtFormProveedor').val());

  obj.tipovh = parseInt($('#txtFormTipoVH').val());
  obj.marcavh = parseInt($('#txtFormMarcaVH').val());
  obj.modelovh = parseInt($('#txtFormModeloVH').val());

  obj.idradio = parseInt($('#txtFormRadioID').val());
  obj.idotro = $.trim($('#txtFormOtroID').val()).toUpperCase();
  obj.tipo = parseInt($('#txtFormRadioTipo').val());
  obj.serie = $.trim($('#txtFormRadioSerie').val()).toUpperCase();
  obj.modelo = $.trim($('#txtFormRadioModelo').val()).toUpperCase();
  obj.origen = $.trim($('#txtFormRadioOrigen').val()).toUpperCase();
  obj.tei = $.trim($('#txtFormRadioTEI').val()).toUpperCase();
  obj.observacion = $.trim($('#txtFormObservacion').val()).toUpperCase();
  obj.categoria = $.trim($('#txtFormRadioCategoria').val()).toUpperCase();
  obj.marca = $.trim($('#txtFormRadioMarca').val()).toUpperCase();
  obj.estado = parseInt($('#txtFormEstado').val());
  obj.motivo = $.trim($('#txtFormMotivo').val()).toUpperCase();

  if(obj.comisaria == 0 || !obj.comisaria){
    msj_err += 'Seleccione comisaría.<br>';
  }

  if(obj.patrullaje > 0){
    if(obj.patrullaje == 1 || obj.patrullaje == 2 || obj.patrullaje == 6 || obj.patrullaje == 7){

      if(obj.placa == ''){
        msj_err += 'Ingrese placa.<br>';
      }

      if(obj.tipovh == 0){
        msj_err += 'Ingrese tipo de vehículo.<br>';
      }

      if(obj.marcavh == 0){
        msj_err += 'Ingrese marca de vehículo.<br>';
      }

      if(obj.modelovh == 0){
        msj_err += 'Ingrese modelo de vehículo.<br>';
      }

    }else{
      obj.placa = '';      
      obj.tipovh = '';
      obj.modelovh = '';
      obj.marcavh = '';

      if(obj.descripcion == ''){
        msj_err += 'Ingrese referencia.<br>';
      }

    }

  }else{
    msj_err += 'Seleccione tipo de patrullaje.<br>';
  }

  if(obj.origen == ''){
    msj_err += 'Ingrese origen.<br>';
  }

  if(obj.proveedor == 0){
    msj_err += 'Seleccione el medio de transmisión.<br>';
  }else if(obj.proveedor == 1){

    if(obj.idradio == '' || obj.idradio.length < 4){
      msj_err += 'Ingrese un ID de radio válido.<br>';
    }

    if(obj.tipo == 0){
      msj_err += 'Seleccione tipo de radio.<br>';
    }

    if(obj.serie == '' || obj.serie.length < 4){
      msj_err += 'Ingrese una serie válida.<br>';
    }

    if(obj.modelo == ''){
      msj_err += 'Ingrese modelo de radio.<br>';
    }

    if(obj.categoria == ''){
      msj_err += 'Ingrese categoría de radio.<br>';
    }

    if(obj.marca == ''){
      msj_err += 'Ingrese marca de radio.<br>';
    }

  }else{
    obj.idradio = '';
    obj.tipo = '';
    obj.serie = '';
    obj.modelo = '';
    obj.categoria = '';
    obj.marca = '';
  }


  if(obj.motivo == '' && obj.dispogps > 0){
    msj_err += 'Ingrese motivo del cambio realizado.<br>';
  }



  if(msj_err!=''){
    SipcopJS.msj.error('Error',msj_err);
  }else{
   SipcopJS.msj.confirm('Confirmar', '¿Está seguro de guardar cambios?', function(resp){
    if(resp){
       SipcopJS.post('admin/mantenimiento/call_guardar_unidpol', obj, function(data){
        if(data.status == 'success'){
          SipcopJS.msj.success('Mensaje',data.msg);
          limpiarForm();
          $('#modalForm').modal('hide');
        }else{
          SipcopJS.msj.error('Error',data.msg);
        }   
      });
    }
   });
  }
  
}

</script>
 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Unidades policiales
            </header>
            <div class="panel-body">
            <form id="frInstitucion">
                <div class="row" id="rowInstitucion" >
                    <div class="form-group col-sm-4" id="divMacro" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                        <label class="control-label col-lg-5" for="txtFiltroMacroreg" > MACROREG: </label>
                        <div class="col-lg-7">
                            <select name="txtFiltroMacroreg" id="txtFiltroMacroreg" class="form-control" style="width:100%" >
                                    <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4" id="divRegPol" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                        <label class="control-label col-lg-5" for="txtFiltroRegpol" > REGPOL: </label>
                        <div class="col-lg-7">
                            <select name="txtFiltroRegpol" id="txtFiltroRegpol" class="form-control" style="width:100%">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4" id="divDivTer" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                        <label class="control-label col-lg-5" for="txtFiltroDivter" > DIVPOL: </label>
                        <div class="col-lg-7">
                            <select name="txtFiltroDivter" id="txtFiltroDivter" class="form-control" style="width:100%">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4" id="divComisaria" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                        <label class="control-label col-lg-5" for="txtFiltroComisaria" > Comisaria: </label>
                        <div class="col-lg-7">
                            <select name="txtFiltroComisaria" id="txtFiltroComisaria" class="form-control" style="width:100%">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-sm-4" id="divComisaria">
                        <label class="control-label col-lg-5" for="txtFiltroComisaria" > Placa/Ref: </label>
                        <div class="col-lg-7">
                            <input name="txtFiltroPlaca" id="txtFiltroPlaca" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group col-sm-4 pull-right text-right" id="divBoton">
                        <button type="button" class="btn btn-info" id="btnFiltrar"><i class="fa fa-filter"></i> Filtrar</button>
                        <?php if($obj_modulo['FLG1'] == 2){ ?>
                        <button type="button" class="btn btn-success" id="btnNuevo" onclick="modal('0','0')"><i class="fa fa-file"></i> Nuevo</button>
                        <?php } ?>
                    </div>
                </div>
                <div class="clear"></div>
            </form>
            <div id="Reporte">
                <div class="col-sm-12">
                    <div id="Grafico"></div>
                </div>
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

<div id="modalForm" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h4 id="modalTitulo" class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
                <input type="hidden" name="txtFormDispoGPS" id="txtFormDispoGPS" value="0">

                <div class="form-group col-sm-4" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                   <label class="control-label col-sm-5" for="txtFormMacroreg">MACROREG: </label>
                   <div class="col-sm-7">
                        <select name="txtFormMacroreg" id="txtFormMacroreg" class="form-control">
                          <option value="0">-- Seleccione --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                   <label class="control-label col-sm-5" for="txtFormRegpol">REGPOL: </label>
                   <div class="col-sm-7">
                        <select name="txtFormRegpol" id="txtFormRegpol" class="form-control">
                          <option value="0">-- Seleccione --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                   <label class="control-label col-sm-5" for="txtFormDivter">DIVPOL: </label>
                   <div class="col-sm-7">
                        <select name="txtFormDivter" id="txtFormDivter" class="form-control">
                          <option value="0">-- Seleccione --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" style="<?php echo ((int)$obj_usuario['IDROL'])==3?'display: none;':''; ?>">
                   <label class="control-label col-sm-5" for="txtFormComisaria">Comisaría: </label>
                   <div class="col-sm-7">
                        <select name="txtFormComisaria" id="txtFormComisaria" class="form-control">
                          <option value="0">-- Seleccione --</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                   <label class="control-label col-sm-5" for="txtFormPatrullaje">Tipo Patrullaje: </label>
                   <div class="col-sm-7">
                        <select name="txtFormPatrullaje" id="txtFormPatrullaje" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <?php foreach ($patrullaje as $item) { ?>
                            <option value="<?php echo $item['IDPATRULLAJE']; ?>"><?php echo $item['NOMBRE']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormPlaca">
                   <label class="control-label col-sm-5" for="txtFormPlaca">Placa: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormPlaca" id="txtFormPlaca" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                   <label class="control-label col-sm-5" for="txtFormDesc">Referencia: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormDesc" id="txtFormDesc" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormTipoVH">
                   <label class="control-label col-sm-5" for="txtFormTipoVH">Tipo de Veh.: </label>
                   <div class="col-sm-7">
                        <select name="txtFormTipoVH" id="txtFormTipoVH" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <?php foreach ($tipovh as $item) { ?>
                            <option value="<?php echo $item['IDTIPOVH']; ?>"><?php echo $item['NOMBRE']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormMarcaVH">
                   <label class="control-label col-sm-5" for="txtFormMarcaVH">Marca de Veh.: </label>
                   <div class="col-sm-7">
                        <select name="txtFormMarcaVH" id="txtFormMarcaVH" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <?php foreach ($marcavh as $item) { ?>
                            <option value="<?php echo $item['IDMARCAVH']; ?>" tipovh="<?php echo $item['IDTIPOVH']; ?>"><?php echo $item['NOMBRE']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormModeloVH">
                   <label class="control-label col-sm-5" for="txtFormModeloVH">Modelo de Veh.: </label>
                   <div class="col-sm-7">
                        <select name="txtFormModeloVH" id="txtFormModeloVH" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <?php foreach ($modelovh as $item) { ?>
                            <option value="<?php echo $item['IDMODELOVH']; ?>" marcavh="<?php echo $item['IDMARCAVH']; ?>"><?php echo $item['NOMBRE']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4">
                   <label class="control-label col-sm-5" for="txtFormRadioOrigen">Origen: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioOrigen" id="txtFormRadioOrigen" class="form-control"/>
                    </div>
                </div>

                <div class="clear"></div>
                <hr>
                <div class="clear"></div>

                <div class="form-group col-sm-4">
                   <label class="control-label col-sm-5" for="txtFormProveedor">Transmite por: </label>
                   <div class="col-sm-7">
                        <select name="txtFormProveedor" id="txtFormProveedor" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <?php foreach ($proveedores as $item) { ?>
                            <option value="<?php echo $item['IDPROVEEDOR']; ?>"><?php echo $item['NOMBRE']; ?></option>
                          <?php } ?>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioID">
                   <label class="control-label col-sm-5" for="txtFormRadioID">ID Radio: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioID" id="txtFormRadioID" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormOtroID">
                   <label class="control-label col-sm-5" for="txtFormOtroID">ID de Transmisión: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormOtroID" id="txtFormOtroID" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioTipo">
                   <label class="control-label col-sm-5" for="txtFormRadioTipo">Tipo de radio: </label>
                   <div class="col-sm-7">
                        <select name="txtFormRadioTipo" id="txtFormRadioTipo" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <option value="1">MOVIL</option>
                          <option value="2">PORTATIL</option>
                          <option value="3">OTRO</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioSerie">
                   <label class="control-label col-sm-5" for="txtFormRadioSerie">Serie: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioSerie" id="txtFormRadioSerie" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioModelo">
                   <label class="control-label col-sm-5" for="txtFormRadioModelo">Modelo: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioModelo" id="txtFormRadioModelo" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioTEI">
                   <label class="control-label col-sm-5" for="txtFormRadioTEI">TEI: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioTEI" id="txtFormRadioTEI" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioCategoria">
                   <label class="control-label col-sm-5" for="txtFormRadioCategoria">Categoría: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioCategoria" id="txtFormRadioCategoria" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormRadioMarca">
                   <label class="control-label col-sm-5" for="txtFormRadioMarca">Marca de radio: </label>
                   <div class="col-sm-7">
                        <input type="text" name="txtFormRadioMarca" id="txtFormRadioMarca" class="form-control"/>
                    </div>
                </div>

                <div class="clear"></div>
                <hr>
                <div class="clear"></div>

                <div class="form-group col-sm-4">
                   <label class="control-label col-sm-12" for="txtFormEstado">Estado: </label>
                   <div class="col-sm-12">
                        <select name="txtFormEstado" id="txtFormEstado" class="form-control">
                          <option value="0">-- Seleccione --</option>
                          <option value="1">Operativo</option>
                          <option value="3">Inoperativo</option>
                        </select>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormObservacion">
                   <label class="control-label col-sm-12" for="txtFormObservacion">Observación: </label>
                   <div class="col-sm-12">
                        <input type="text" name="txtFormObservacion" id="txtFormObservacion" class="form-control"/>
                    </div>
                </div>

                <div class="form-group col-sm-4" id="dvFormMotivo">
                   <label class="control-label col-sm-12" for="txtFormMotivo">Motivo del cambio: </label>
                   <div class="col-sm-12">
                        <input type="hidden" name="txtFormMotivoAnt" id="txtFormMotivoAnt" class="form-control"/>
                        <input type="text" name="txtFormMotivo" id="txtFormMotivo" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>

        </div>
      <div class="modal-footer">
          
          <?php if($obj_modulo['FLG1'] == 2){ ?>
          <button data-dismiss="modal" class="btn btn-default" type="button">Cancelar</button>
          <button class="btn btn-success" type="button" id="btnFormGuardar" onclick="guardar()">Guardar</button>
          <?php }else{ ?>
          <button data-dismiss="modal" class="btn btn-default" type="button">Cerrar</button>
          <?php } ?>
      </div>
      </div>
    </div>
  </div>
</div>

