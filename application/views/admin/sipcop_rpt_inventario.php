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

        SipcopJS.post('admin/reporte/json_consultar_inventario',{
            tipo: dependencia.tipo,
            dependencia: dependencia.id
        }, 
            function(data){
                $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            $('#Grafico').show(); 
            cargarData(data);
        });
    }

    function modalDetalle(dependencia, nivel){
        $('#dgDetalle_filter input').val('');
        
        $('#dgDetalle').dataTable().fnClearTable();
        $('#dgDetalle').dataTable().fnAddData([]);
        $('#dgDetalle').dataTable().fnDraw();

        $('#btnExportar').data('dependencia', dependencia).data('nivel', nivel);
        
        $('#modalDetalle').modal('show');
        $('.dataTables_processing', $('#dgDetalle').closest('.dataTables_wrapper')).css('visibility','visible').show(); 

        SipcopJS.post('admin/reporte/json_consultar_inventario_det',{
            dependencia: dependencia,
            tipo: nivel
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
                type: 'column'
            },
            title: {
                text: 'Reporte de Inventario'
            },
            xAxis: {
                categories: reporte.categorias,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: '# Inventario'
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        //color: 'gray'
                    }
                }
            },
            tooltip: {
                headerFormat: '<b>{point.x}</b><br/>',
                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}',
                shared: false,
                useHTML: true
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        //color: 'gray'
                    }
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
            {sTitle : "Patrullero", mData: "RptPatrullero"},
            {sTitle : "Motorizado", mData: "RptMotorizado"},
            {sTitle : "Patrullaje a Pie", mData: "RptPatpie"},
            {sTitle : "Patrullaje Integrado", mData: "RptPatintegrado"},
            {sTitle : "Radio Base", mData: "RptBase"},
            {sTitle : "Total", mData: "RptTotal"},
            {sTitle : " ", mRender: function(data, type, row){
                return '<a href="javascript:;" onclick="modalDetalle(\''+row.DependenciaID+'\',\''+row.DependenciaNivel+'\')" class="btn btn-primary btn-xs tooltips" data-toggle="button" data-placement="top" data-original-title="Ver"><i class="fa fa-search"></i></a>';
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
            {sTitle : "Radio", mData: "RadioEtiqueta" , mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Proveedor", mData: "RadioProveedor"},
            {sTitle : "Placa", mData: "RadioPlaca" , mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Serie", mData: "RadioSerie", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Tipo", mData: "RadioTipo", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
            }},
            {sTitle : "Estado", mData: "RadioEstado", mRender: function(data, type, row){
               if(data){
                return data;
               }
               else{
                return "-";
               }
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

      $('#btnFiltrar').click(function(){
            filtrar();
      });

      $('#btnExportar').click(function(){
        SipcopJS.autoPostBlank('admin/reporte/xls_inventario_det', {
            tipo: $('#btnExportar').data('nivel'),
            dependencia: $('#btnExportar').data('dependencia')
        });
      });

      $('#btnExportarConsolidado').click(function(){
        var dependencia = SipcopJS.get_dependencia('#txtMacroReg', '#txtRegPol', '#txtDivTer', '#txtComisaria');
        SipcopJS.autoPostBlank('admin/reporte/xls_consultar_inventario', {
          tipo: dependencia.tipo,
          dependencia: dependencia.id
        });
      });


    }

    
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte General de Inventario
            </header>
            <div class="panel-body">
            <form id="frmExportar">         
                <div class="row">
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-5" for="txtMacroReg"  >Macro Región: </label>
                        <div class="col-lg-7">
                            <select tname="txtMacroReg" id="txtMacroReg" class="form-control" style="width:100%" <?php echo (($usu_rol ==3)?'disabled':''); ?>>
                                    <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtRegPol" >Región Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtRegPol" id="txtRegPol" class="form-control" style="width:100%"  <?php echo (($usu_rol ==3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtDivTer" >Div. Policial: </label>
                        <div class="col-lg-8">
                            <select tname="txtDivTer" id="txtDivTer" class="form-control" style="width:100%"  <?php echo (($usu_rol ==3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="txtComisaria" >Comisaria: </label>
                        <div class="col-lg-8">
                            <select tname="txtComisaria" id="txtComisaria" class="form-control" style="width:100%"  <?php echo (($usu_rol ==3)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">   
                    <div class="form-group col-sm-3 pull-right text-right">
                            <button type="button" class="btn btn-info" id="btnFiltrar">Filtrar</button>   
                            <button type="button" class="btn btn-success" id="btnExportarConsolidado"><i class="fa fa-download"></i> Exportar</button>
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
                (*) Las cantidades mostradas en el reporte de inventario tiene estado OPERATIVO.
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
        <h4 id="basicModalLabel" class="modal-title">Detalle de inventario &nbsp;&nbsp;&nbsp;
        <a id="btnExportar" href="javascript:;" class="btn btn-success btn-xs" data-toggle="button" >
        <i class="fa fa-download"></i> Exportar</a></h4>
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