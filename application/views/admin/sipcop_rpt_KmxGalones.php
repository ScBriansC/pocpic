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
        SipcopJS.post('admin/reporte/json_calcular_KmxGalones',{
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

    function modalDetalle(dependencia,nivel,fecha,periodo){
        $('#dgDetalle_filter input').val('');
        
        $('#dgDetalle').dataTable().fnClearTable();
        $('#dgDetalle').dataTable().fnAddData([]);
        $('#dgDetalle').dataTable().fnDraw();

        $('#btnExportarDet').data('dependencia', dependencia).data('nivel', nivel).data('fecha',fecha).data('periodo',periodo);

        $('#modalDetalle').modal('show');
        $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').show(); 
        SipcopJS.post('admin/reporte/json_kmxgalones_det',{
            dependencia: dependencia,
            nivel: nivel,
            fecha: fecha,
            periodo: $('#txtPeriodo').val(),
        }, function(data){
            $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
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
                text: 'Reporte de Galones por Kilómetros recorridos'
            },
            xAxis: {
                categories: reporte.categorias,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '# Cant. Galones por Kilómetros recorridos'
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
          "bInfo": true,
          "aoColumns": [
            {sTitle : "Ubicación", mData: "RptLocalidad"},
            {sTitle : "Periodo", mData: "RptPeriodo"},
            {sTitle : "#Vehículo", mData: "CantidadVehiculo"},
            {sTitle : "Kilometros", mData: "KmRecorrido"},
            {sTitle : "#Galones", mData: "Galones"},
            {sTitle : "Ratio Galones", mData: "Ratio", mRender: function(value, type, row){
                return SipcopJS.format.decimal(value,2);
            }},
            {sTitle : " ", mRender: function(data, type, row){
                return '<a href="javascript:;" onclick="modalDetalle(\''+row.DependenciaID+'\',\''+row.DependenciaNivel+'\',\''+row.RptPeriodo+'\',\''+$('#txtPeriodo').val()+'\')" class="btn btn-primary btn-xs tooltips" data-toggle="button" data-placement="top" data-original-title="Ver"><i class="fa fa-search"></i></a>';
            }}
          ], 
          "bServerSide" : false,
          "bProcessing" : true,
          "fnFooterCallback": function(row, data, start, end, display) {
              var total_col = [];
              var columns = this.fnSettings().aoColumns;
              var len = columns.length;
              var iniCol = 2;
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
              $.each(data, function(idx, objx){
                total_col[1] += parseInt(objx.CantidadVehiculo);
                total_col[2] += parseFloat(objx.KmRecorrido);
                total_col[3] += parseFloat(objx.Galones);
              });

              $($(footer).find('td')[1]).html((total_col[1]));
              $($(footer).find('td')[2]).html(SipcopJS.format.decimal(total_col[2],2));
              $($(footer).find('td')[3]).html(SipcopJS.format.decimal(total_col[3],2));
              $($(footer).find('td')[4]).html(SipcopJS.format.decimal(total_col[3]/total_col[1],2));
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
            {sTitle : "Comisaria", mData: "dependencia"},
            {sTitle : "Radio", mData: "radioID", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Placa", mData: "placa", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Kilómetros", mData: "KmRecorrido", mRender: function(value, type, row){
                return SipcopJS.format.decimal(value,2);
            }},
            {sTitle : "Galones", mData: "Galon", mRender: function(value, type, row){
                return SipcopJS.format.decimal(value,2);
            }},
            {sTitle : "Proveedor", mData: "Proveedor"}
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
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');

        SipcopJS.autoPostBlank('admin/reporte/json_kmxgalones_det_xls', {
            dependencia: $('#btnExportarDet').data('dependencia'),
            nivel: $('#btnExportarDet').data('nivel'),
            fecha: $('#btnExportarDet').data('fecha'), 
            periodo: $('#btnExportarDet').data('periodo')
        });
    });
    $('#btnExportar').click(function(){
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.autoPostBlank('admin/reporte/json_calcular_KmxGalones_xls', {
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'), 
            fechafin: $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin'),  
            periodo: $('#txtPeriodo').val(),
            tipo: dependencia.tipo,
            dependencia: dependencia.id
        });
    });


      // filtrar();
    }
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte de Galones por Kilometros recorridos
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
                            <button type="button" class="btn btn-success" id="btnExportar"><i class="fa fa-download"></i> Exportar</button>
                    </div>  
                </div>   
                <div class="clear"></div>
            </form>
            <div id="Reporte">
                <div class="row">
                    <div class="col-sm-12">
                        <div id="Grafico"></div>
                    </div>
                </div>
                <div class="row">
                <div class="col-sm-12">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">                   
                        </table>
                    </div>
                    </div>
                </div>
            </div>
               <!--  <div class="col-sm-7">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">                   
                        </table>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div id="Grafico"></div>
                </div> -->
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
        <h4 id="basicModalLabel" class="modal-title">Detalle de Galones por Kilometros Recorridos AProx. &nbsp;&nbsp;&nbsp;
        <a id="btnExportarDet" href="javascript:;" class="btn btn-success btn-xs" data-toggle="button" ><i class="fa fa-download"></i> Exportar</a>
        </h4>
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