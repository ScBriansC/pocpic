<?php
$usu_rol = $obj_usuario['IDROL'];
?>

<script>
var opt;
    function addDaysNum(date, days) {
        var dat = new Date(date.valueOf())
        dat.setDate(dat.getDate() + days);
        return dat;
    }

    function filtrar(){
        $('#dgTabla').dataTable().fnClearTable();
        $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').show();
        $('#Grafico').hide();
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.post('admin/reporte/json_consultar_conexiones',{
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'), 
            fechafin: $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin'),  
            periodo: $('#txtPeriodo').val(),
            tipo: dependencia.tipo,
            dependencia: dependencia.id
        }, 
            function(data){
           $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            $('#Grafico').show(); 
            cargarData(data);
        });
    }
    
    function cargarData(reporte){
        $('#dgTabla').dataTable().fnClearTable();
        $('#dgTabla').dataTable().fnAddData(reporte.data);
        $('#dgTabla').dataTable().fnDraw();
     }

    function getFechasRango(startDate, stopDate) {
        var dateArray = new Array();
        var currentDate = startDate;
        while (currentDate <= stopDate) {
            dateArray.push( new Date (currentDate) )
            currentDate = addDaysNum(currentDate,1);
        }
        return dateArray;
    }

    function modalDetalle(tipo, dependencia, fechaini, fechafin){
        $('#dgDetalle_filter input').val('');


        $('#dgDetalle').dataTable().fnClearTable();
        $('#dgDetalle').dataTable().fnAddData([]);
        $('#dgDetalle').dataTable().fnDraw();

        $('#btnExportarDet').data('tipo', tipo).data('dependencia', dependencia).data('fechaini', fechaini).data('fechafin', fechafin);

        $('#modalDetalle').modal('show');
        $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').show(); 

        SipcopJS.post('admin/reporte/json_consultar_conexiones_det',{
            tipo: tipo,
            dependencia: dependencia,
            fechaini: fechaini,
            fechafin: fechafin
        }, function(data){
             $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            $('#dgDetalle').dataTable().fnClearTable();
            $('#dgDetalle').dataTable().fnAddData(data.data);
            $('#dgDetalle').dataTable().fnDraw();
        });
    }

    var tblData = null;

    function generarTablaEstructura(){
        var columnas = [{sTitle : "Ubicación", mData: "RptLocalidad"},
            /*{sTitle : "Periodo", mData: "RptPeriodo"},
            {sTitle : "Cantidad", mData: "RptCantidad"}*/
            ];

        var fechaini = $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'); 
        var fechafin = $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin');
        var fechaHoy = Date.parseExact((new Date()).toString("dd/MM/yyyy"), "dd/MM/yyyy" );

        var listaFecha = getFechasRango(Date.parseExact(fechaini, "dd/MM/yyyy" ),Date.parseExact(fechafin, "dd/MM/yyyy" ));
        var listaFechaCol = [];
        $.each(listaFecha, function(idx, oFecha){
            listaFecha[idx] = oFecha.toString("dd/MM/yyyy");
            listaFechaCol[idx] = "Rpt_"+oFecha.toString("yyyyMMdd");
            columnas.push({sTitle : listaFecha[idx], mData: listaFechaCol[idx], mRender:function(value, type, row){
                var fecha = oFecha;
                var val = '-';
                if(value){
                    if(value>0){
                        var txtconectado = '';
                        if(parseInt(value)>1){
                            txtconectado = ' usuarios';
                        }else{
                            txtconectado = ' usuario';
                        }
                        val = '<a href="javascript:;" class="label label-success tooltips" data-placement="top" data-toggle="tooltip " class="btn btn-default tooltips" type="button" data-original-title="'+value+txtconectado+'" onclick="modalDetalle(\''+row.DependenciaNivel+'\',\''+row.DependenciaID+'\',\''+fecha.toString("dd/MM/yyyy")+'\',\''+fecha.toString("dd/MM/yyyy")+'\')">SI</a>';
                    }else{
                        val = '<span class="label label-danger">NO</span>';
                    }
                }else{
                    if(oFecha.getTime() > fechaHoy.getTime()){
                        val = '-';
                    }else{
                        val = '<span class="label label-danger">NO</span>';
                    }
                }
                return val;
            }});
        });

        if(tblData){
            $('#dgTabla').DataTable().fnDestroy();
            $('#dgTabla').html('');
            tblData = null;
        }


        tblData = $('#dgTabla').dataTable({
          "bSort": true,
          "bFilter": false,
          "bPaginate": false,
          //"pageLength": 10,
          "bInfo": false,
          "aoColumns": columnas, 
          "bServerSide" : false,
          "bProcessing" : true,
          "fnDrawCallback": function(){
            $('.tooltips').tooltip();
          }
        });
    }

    function colorByString(str){
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
           hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        var c = (hash & 0x00FFFFFF)
        .toString(16)
        .toUpperCase();
        return "#"+("00000".substring(0, 6 - c.length) + c);
    }

    preCarga = function(){SipcopJS.logEnabled = true;

        $('#dgDetalle').dataTable({
          "bSort": true,
          "bFilter": true,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : " ", mRender: function(data, type, row){
                return '&nbsp;';
            }},
            {sTitle : "Dependencia", mRender: function(data, type, row){
                return row.MacRegNombre+', '+row.RegPolNombre+', '+row.DivTerNombre;
            }},
            {sTitle : "Comisaría", mData: "ComisariaNombre"},
            {sTitle : "Usuario", mData: "UsuarioCodigo", mRender: function(data, type, row){
                return row.UsuarioNombre+' '+row.UsuarioApellido;
            }},
            {sTitle : "F. Inicio", mData: "SesionFechaInicio"},
            {sTitle : "F. Fin", mData: "SesionFechaFin"},
            {sTitle : "Estado", mData: "SesionEstado", mRender: function(data, type, row){
                if(parseInt(data)==2){
                    return '<span class="label label-danger">'+row.SesionEstadoTexto+'</span>';
                }else{
                    return '<span class="label label-success">'+row.SesionEstadoTexto+'</span>';
                }
            }}
          ], 
          "bServerSide" : false,
          "bProcessing" : true,

          "fnCreatedRow": function ( row, data, index ) {
                var fIni = data.SesionFechaInicio.split(' ');
                $($('td',row)[0]).style('background',colorByString(data.UsuarioNombre+' '+data.UsuarioApellido + ' ' +fIni[0]),'important');
            }
        });
      
      SipcopJS.cargarDependencia('#txtMacroReg',0,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDMACROREG']; ?>', function(){
        SipcopJS.cargarDependencia('#txtRegPol',1,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDREGPOL']; ?>',function(){
            SipcopJS.cargarDependencia('#txtDivTer',2,'<?php echo $obj_usuario['IDREGPOL']; ?>','<?php echo $obj_usuario['IDDIVTER']; ?>',function(){
                SipcopJS.cargarDependencia('#txtComisaria',3,'<?php echo $obj_usuario['IDDIVTER']; ?>','<?php echo $obj_usuario['IDINSTITUCION']; ?>',function(){
                    generarTablaEstructura();
                    filtrar();
                });
            });
        });
      });      
      
      
      


      $('#txtMacroReg').change(function(){
        $('#txtRegPol').val('0');
        SipcopJS.cargarDependencia('#txtRegPol',1,$('#txtMacroReg').val(),'',null);
        $('#txtDivTer').val('0');
        SipcopJS.cargarDependencia('#txtDivTer',2,$('#txtRegPol').val(),'',null);
        $('#txtComisaria').val('0');
        SipcopJS.cargarComisarias('#txtComisaria',$('#txtDivTer').val(),'',null);
      });

      $('#txtRegPol').change(function(){
        $('#txtDivTer').val('0');
        SipcopJS.cargarDependencia('#txtDivTer',2,$('#txtRegPol').val(),'',null);
        $('#txtComisaria').val('0');
        SipcopJS.cargarDependencia('#txtComisaria',3,$('#txtDivTer').val(),'',null);
      });

      $('#txtDivTer').change(function(){
        $('#txtComisaria').val('0');
        SipcopJS.cargarDependencia('#txtComisaria',3,$('#txtDivTer').val(),'',null);
      });

      $('#txtPeriodo').change(function(){
        var tipo = $(this).val();

        if(tipo==1){
            $('#dpFechaIni').data('date', Date.today().moveToFirstDayOfMonth().toString('dd/MM/yyyy'));
            $('#dpFechaIni input').val(Date.today().moveToFirstDayOfMonth().toString('dd/MM/yyyy'));
            $('#dpFechaFin').data('date', Date.today().toString('dd/MM/yyyy'));
            $('#dpFechaFin input').val(Date.today().toString('dd/MM/yyyy'));

            $('#dpFechaIni, #dpFechaFin').data('date-format', 'dd/mm/yyyy');
            $('#dpFechaIni, #dpFechaFin').data('date-minviewmode', '');
            $('#dpFechaIni, #dpFechaFin').data('date-viewmode', '');
        }else if(tipo == 2){
            $('#dpFechaIni').data('date', Date.today().moveToFirstDayOfMonth().toString('MM/yyyy'));
            $('#dpFechaIni input').val(Date.today().moveToFirstDayOfMonth().toString('MM/yyyy'));
            $('#dpFechaFin').data('date', Date.today().moveToLastDayOfMonth().toString('MM/yyyy'));
            $('#dpFechaFin input').val(Date.today().moveToLastDayOfMonth().toString('MM/yyyy'));

            $('#dpFechaIni, #dpFechaFin').data('date-format', 'mm/yyyy');
            $('#dpFechaIni, #dpFechaFin').data('date-minviewmode', 'months');
            $('#dpFechaIni, #dpFechaFin').data('date-viewmode', 'months');            
        }else if(tipo == 3){
            $('#dpFechaIni').data('date', Date.today().moveToFirstDayOfMonth().toString('yyyy'));
            $('#dpFechaIni input').val(Date.today().moveToFirstDayOfMonth().toString('yyyy'));
            $('#dpFechaFin').data('date', Date.today().moveToLastDayOfMonth().toString('yyyy'));
            $('#dpFechaFin input').val(Date.today().moveToLastDayOfMonth().toString('yyyy'));

            $('#dpFechaIni, #dpFechaFin').data('date-format', 'yyyy');
            $('#dpFechaIni, #dpFechaFin').data('date-minviewmode', 'years');
            $('#dpFechaIni, #dpFechaFin').data('date-viewmode', 'years');    
        }
        if($('#dpFechaIni').data('datepicker')!=null){
            $('#dpFechaIni').data('datepicker').constructor($('#dpFechaIni'),{});
            $('#dpFechaFin').data('datepicker').constructor($('#dpFechaFin'),{});
        }else{
            $('#dpFechaIni, #dpFechaFin').datepicker();
        }
      });

      $('#txtPeriodo').change();

      $('#btnFiltrar').click(function(){
            generarTablaEstructura();
            filtrar();
      });

      $('#btnExportar').click(function(){
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.autoPostBlank('admin/reporte/xls_reporte_conexiones', {
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'), 
            fechafin: $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin'),  
            periodo: $('#txtPeriodo').val(),
            tipo: dependencia.tipo,
            dependencia: dependencia.id
        });
      });

      $('#btnExportarDet').click(function(){
        SipcopJS.autoPostBlank('admin/reporte/xls_conexiones_det', {
            tipo: $('#btnExportarDet').data('tipo'),
            dependencia: $('#btnExportarDet').data('dependencia'),
            fechaini: $('#btnExportarDet').data('fechaini'),
            fechafin: $('#btnExportarDet').data('fechafin')
        });
      });

      $('#btnExportarDet2').click(function(){
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.autoPostBlank('admin/reporte/xls_conexiones_det', {
            tipo: dependencia.tipo,
            dependencia: dependencia.id,
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'),
            fechafin: $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin')
        });
      });
      //filtrar();
    }
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte de Conexiones
            </header>
            <div class="panel-body">
            <form id="frmExportar">         
                <div class="row">
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-5" for="txtMacroReg"  >Macro Región: </label>
                        <div class="col-lg-7">
                            <select tname="txtMacroReg" id="txtMacroReg" class="form-control" style="width:100%" <?php echo (($usu_rol == 3)?'disabled':''); ?>  >
                                    <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtRegPol" >Región Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtRegPol" id="txtRegPol" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>  >
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtDivTer" >Div. Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtDivTer" id="txtDivTer" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>  >
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="txtComisaria" >Comisaria: </label>
                        <div class="col-lg-8">
                            <select tname="txtComisaria" id="txtComisaria" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>  >
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="txtPeriodo">Periodo: </label>
                        <div class="col-lg-8">
                            <select class="form-control" id="txtPeriodo" name="txtPeriodo">
                                <option value="1" selected>Diario</option>
                                <option value="2">Mensual</option>
                                <option value="3">Anual</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="dpFechaIni">F. Inicio: </label>
                        <div class="col-lg-8">
                            <div id="dpFechaIni" class="input-append date">
                                <input type="text" readonly="" size="16" class="form-control" name="FechaIni" id="txtFechaIni">
                                  <span class="input-group-btn add-on">
                                    <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                  </span>
                            </div>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="dpFechaFin">F. Fin: </label>
                        <div class="col-lg-8">
                            <div id="dpFechaFin" class="input-append date">
                                <input type="text" readonly="" size="16" class="form-control" name="FechaFin" id="txtFechaFin">
                                  <span class="input-group-btn add-on">
                                    <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                  </span>
                            </div>
                        </div>
                    </div>   
                    <div class="form-group col-sm-3">
                            <button type="button" class="btn btn-info" id="btnFiltrar"><i class="fa fa-search"></i> Filtrar</button> 
                            <div class="btn-group">
                                <button data-toggle="dropdown" class="btn btn-success dropdown-toggle" type="button">Exportar <span class="caret"></span></button>
                                <ul role="menu" class="dropdown-menu">
                                    <li><a href="javascript:;" id="btnExportar">General</a></li>
                                    <li><a href="javascript:;" id="btnExportarDet2">Detallado</a></li>
                                </ul>
                            </div>
                    </div>  
                </div>   
                <div class="clear"></div>
            </form><br><br>
            <div id="Reporte">
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


<div id="modalDetalle" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <h4 id="basicModalLabel" class="modal-title">Detalle de Conexión &nbsp;&nbsp;&nbsp;<a id="btnExportarDet" href="javascript:;" class="btn btn-success btn-xs" data-toggle="button" ><i class="fa fa-download"></i> Exportar</a></h4>
      </div>
      <div class="modal-body">
        <div class="adv-table">
          <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgDetalle" width="100%">                   
          </table>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>