<?php
$usu_rol = $obj_usuario['IDROL'];
?>


<script>
var opt;

    function filtrar(){
        $('#dgTabla').dataTable().fnClearTable();
        $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').show();
        $('#Grafico').hide();
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');

        SipcopJS.post('admin/reporte/json_consultar_transmisiones',{
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'),   
            // periodo: $('#txtPeriodo').val(),
            tipo: dependencia.tipo,
            dependencia: dependencia.id
        }, 
          function(data){
            $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            $('#Grafico').show(); 
          cargarData(data);
        });
    }
   function modalDetalle(dependencia,tipo,fecha){
        $('#dgDetalle_filter input').val('');
        
        $('#dgDetalle').dataTable().fnClearTable();
        $('#dgDetalle').dataTable().fnAddData([]);
        $('#dgDetalle').dataTable().fnDraw();

        // $('#btnExportarDet').data('ubigeo', ubigeo).data('comisaria', comisaria).data('fecha',fecha);
        $('#btnExportarDet').data('dependencia', dependencia).data('tipo', tipo).data('fecha',fecha);

        $('#modalDetalle').modal('show');
        $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').show(); 

        SipcopJS.post('admin/reporte/json_consultar_transmisiones_det',{
            dependencia: dependencia,
            tipo: tipo,
            fecha: fecha,
        }, function(data){
            $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            $('#Grafico').show(); 
            $('#dgDetalle').dataTable().fnClearTable();
            $('#dgDetalle').dataTable().fnAddData(data.data);
            $('#dgDetalle').dataTable().fnDraw();
        });
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


    function cargarData(reporte){
        $('#dgTabla').dataTable().fnClearTable();
        $('#dgTabla').dataTable().fnAddData(reporte.data);
        $('#dgTabla').dataTable().fnDraw();

        opt = {
            chart: {
                type: 'bar'
            },
            title: {
                text: 'Reporte de Transmisiones'
            },
            xAxis: {
                categories: reporte.categorias,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '# Transmisiones'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">Periodo: {point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.0f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0
                }
            },
            series: reporte.series
        };
        $('#Grafico').highcharts(opt);

     }

    preCarga = function(){
      $('#dgTabla').dataTable({
          "bSort": true,
          "bFilter": false,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": false,
          "aoColumns": [
            {sTitle : "Ubicación", mData: "RptLocalidad"},
            {sTitle : "Patrullero", mData: "RptPatrullero"},
            {sTitle : "Motorizado", mData: "RptMotorizado"},
            {sTitle : "Patrullaje a Pie", mData: "RptPatpie"},
            {sTitle : "Patrullaje Integrado", mData: "RptPatintegrado"},
            {sTitle : "Radio Base", mData: "RptBase"},
            {sTitle : "Total", mData: "RptTotal"},
             {sTitle : " ", mRender: function(data, type, row){
                return '<a href="javascript:;" onclick="modalDetalle(\''+row.DependenciaID+'\',\''+row.DependenciaNivel+'\',\''+$('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini')+'\')" class="btn btn-primary btn-xs tooltips" data-toggle="button" data-placement="top" data-original-title="Ver"><i class="fa fa-search"></i></a>';
            }}
          ], 
          "bServerSide" : false,
          "bProcessing" : true,
          "fnFooterCallback": function(row, data, start, end, display) {
              var total_col = [];
              var columns = this.fnSettings().aoColumns;
              var len = columns.length;
              var iniCol = 1;
              if(len > 0){
                var footer = $(this).find('tfoot tr');
                if(footer.length==0){
                    footer = $(this).append('<tfoot><tr></tr></tfoot>');
                    footer = $(this).find('tfoot tr')
                    $(footer).append('<td colspan="'+iniCol+'" style="text-align:right">Total</td>');
                    for (var i = iniCol; i < len; i++) {
                        $(footer).append('<td/>');
                    }
                }
              }

              total_col[1] = 0;
              total_col[2] = 0;
              total_col[3] = 0;
              total_col[4] = 0;
              total_col[5] = 0;
              total_col[6] = 0;
              $.each(data, function(idx, objx){
                total_col[1] += parseInt(objx.RptPatrullero);
                total_col[2] += parseInt(objx.RptMotorizado);
                total_col[3] += parseInt(objx.RptPatpie);
                total_col[4] += parseInt(objx.RptPatintegrado);
                total_col[5] += parseInt(objx.RptBase);
                total_col[6] += parseInt(objx.RptTotal);
              });

              $($(footer).find('td')[1]).html((total_col[1]));
              $($(footer).find('td')[2]).html((total_col[2]));
              $($(footer).find('td')[3]).html((total_col[3]));
              $($(footer).find('td')[4]).html((total_col[4]));
              $($(footer).find('td')[5]).html((total_col[5]));
              $($(footer).find('td')[6]).html((total_col[6]));
              $(footer).find('td').css('font-weight','bold');

          }
        });

         $('#dgDetalle').dataTable({
          "bSort": true,
          "bFilter": true,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : "Ubicación", mRender: function(data, type, row){
                return row.macroREGION+', '+row.regionPOLICIAL+', '+row.divisionPOLICIAL;
            }},
            {sTitle : "Comisaría", mData: "dependencia"},
            {sTitle : "Radio", mData: "RadioEtiqueta", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Placa", mData: "VehiculoPlaca", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Tipo", mData: "Vehiculo"},
            {sTitle : "Transmisiones", mRender: function(data, type, row){
                if (row.Transmite == 'X'){
                     return '<span class="label label-success">Transmitió</span>';
                    } else{
                       return '<span class="label label-danger">No transmitió</span>';
                    }
                // return row.Transmite+', '+row.UbigeoProv+', '+row.UbigeoDist;
            }}
          ], 
          "bServerSide" : false,
          "bProcessing" : true
        });

    
        SipcopJS.cargarDependencia('#txtMacroReg',0,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDMACROREG']; ?>', function(){
            SipcopJS.cargarDependencia('#txtRegPol',1,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDREGPOL']; ?>',function(){
                SipcopJS.cargarDependencia('#txtDivTer',2,'<?php echo $obj_usuario['IDREGPOL']; ?>','<?php echo $obj_usuario['IDDIVTER']; ?>',function(){
                    SipcopJS.cargarDependencia('#txtComisaria',3,'<?php echo $obj_usuario['IDDIVTER']; ?>','<?php echo $obj_usuario['IDINSTITUCION']; ?>',function(){
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
            $('#dpFechaIni').data('date', Date.today().toString('dd/MM/yyyy'));
            $('#dpFechaIni input').val(Date.today().toString('dd/MM/yyyy'));
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
            filtrar();
      });

     $('#btnExportarDet').click(function(){
     SipcopJS.autoPostBlank('admin/reporte/xls_transmisiones_det', {
            dependencia: $('#btnExportarDet').data('dependencia'),
            tipo: $('#btnExportarDet').data('tipo'),
            fecha:$('#btnExportarDet').data('fecha')
        });
      });


    $('#btnExportar').click(function(){
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.autoPostBlank('admin/reporte/xls_transmisiones', {
            fecha: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'), 
            tipo: dependencia.tipo,
            dependencia: dependencia.id 
        });
      });

      // $('#btnExportar').click(function(){
      //   $('#btnExportar').html('Exportando...');
      //   $('.highcharts-tooltip').hide();
      //   SipcopJS.generarChartOpt(opt, function(img){
      //       $('#btnExportar').html('Exportar');
      //       $('.highcharts-tooltip').show();
      //       // SipcopJS.reporte_htmlpdf('Reporte de Ingresos en el Periodo '+$('#txtFechaIni').val()+' / '+ $('#txtFechaFin').val(), $('#Reporte').html(), img);
      //   });
      // });
      //filtrar();
    }
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte General de Transmisiones
            </header>
            <div class="panel-body">
            <form id="frmExportar">         
                <div class="row">
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-5" for="txtMacroReg"  >Macro Región: </label>
                        <div class="col-lg-7">
                            <select tname="txtMacroReg" id="txtMacroReg" class="form-control" style="width:100%" <?php echo (($usu_rol == 3)?'disabled':''); ?>>
                                    <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtRegPol" >Región Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtRegPol" id="txtRegPol" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtDivTer" >Div. Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtDivTer" id="txtDivTer" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="txtComisaria" >Comisaria: </label>
                        <div class="col-lg-8">
                            <select tname="txtComisaria" id="txtComisaria" class="form-control" style="width:100%"  <?php echo (($usu_rol == 3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="form-group col-sm-3" style="display:none;">
                        <label class="control-label col-lg-4" for="txtPeriodo">Periodo: </label>
                        <div class="col-lg-8">
                            <select class="form-control" id="txtPeriodo" name="txtPeriodo">
                                <option value="1" selected>Diario</option>
                                <option value="2">Mensual</option>
                                <option value="3">Anual</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3" >
                        <label class="control-label col-lg-4" for="dpFechaIni">Fecha: </label>
                        <div class="col-lg-8">
                            <div id="dpFechaIni" class="input-append date">
                                <input type="text" readonly="" size="16" class="form-control" name="FechaIni" id="txtFechaIni">
                                  <span class="input-group-btn add-on">
                                    <button class="btn btn-primary" type="button"><i class="fa fa-calendar"></i></button>
                                  </span>
                            </div>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3" style="display:none;">
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
                            <button type="button" class="btn btn-info" id="btnFiltrar">Filtrar</button> 
                            <button type="button" class="btn btn-success" id="btnExportar">Exportar</button>      
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


<div id="modalDetalle" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <h4 id="basicModalLabel" class="modal-title">DETALLE DE TRANSMISIONES</h4>
      </div>
      <div class="modal-body">
        <div class="adv-table">
          <a id="btnExportarDet" href="javascript:;" class="btn btn-success btn-xs" data-toggle="button" >
          <i class="fa fa-download"></i> Exportar</a></h4>
          <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgDetalle" width="100%">                   
          </table>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>