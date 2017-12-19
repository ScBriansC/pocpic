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

<script>

var data_general = 
        {
          "data": 
                [
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "07/07/2017",
                      "RptDistancia": "8"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "07/07/2017",
                      "RptDistancia": "12"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "08/07/2017",
                      "RptDistancia": "13"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "08/07/2017",
                      "RptDistancia": "7"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "09/07/2017",
                      "RptDistancia": "9"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "09/07/2017",
                      "RptDistancia": "24"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "10/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "10/07/2017",
                      "RptDistancia": "10"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "11/07/2017",
                      "RptDistancia": "11"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "11/07/2017",
                      "RptDistancia": "14"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "12/07/2017",
                      "RptDistancia": "16"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "12/07/2017",
                      "RptDistancia": "16"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "13/07/2017",
                      "RptDistancia": "15"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "13/07/2017",
                      "RptDistancia": "14"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "14/07/2017",
                      "RptDistancia": "21"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "14/07/2017",
                      "RptDistancia": "22"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "15/07/2017",
                      "RptDistancia": "7"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "15/07/2017",
                      "RptDistancia": "10"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "16/07/2017",
                      "RptDistancia": "16"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "16/07/2017",
                      "RptDistancia": "13"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "17/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "17/07/2017",
                      "RptDistancia": "27"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "18/07/2017",
                      "RptDistancia": "19"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "18/07/2017",
                      "RptDistancia": "24"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "19/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "19/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "20/07/2017",
                      "RptDistancia": "21"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "20/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "21/07/2017",
                      "RptDistancia": "22"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "21/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "22/07/2017",
                      "RptDistancia": "18"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "22/07/2017",
                      "RptDistancia": "17"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "23/07/2017",
                      "RptDistancia": "21"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "23/07/2017",
                      "RptDistancia": "22"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "24/07/2017",
                      "RptDistancia": "23"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "24/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "25/07/2017",
                      "RptDistancia": "19"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "25/07/2017",
                      "RptDistancia": "27"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "26/07/2017",
                      "RptDistancia": "25"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "26/07/2017",
                      "RptDistancia": "17"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "27/07/2017",
                      "RptDistancia": "16"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "27/07/2017",
                      "RptDistancia": "23"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "28/07/2017",
                      "RptDistancia": "26"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "28/07/2017",
                      "RptDistancia": "27"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "29/07/2017",
                      "RptDistancia": "12"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "29/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "30/07/2017",
                      "RptDistancia": "25"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "30/07/2017",
                      "RptDistancia": "20"
                    },
                    {
                      "RptLocalidad": "CALLAO",
                      "RptPeriodo": "31/07/2017",
                      "RptDistancia": "21"
                    },
                    {
                      "RptLocalidad": "LIMA",
                      "RptPeriodo": "31/07/2017",
                      "RptDistancia": "27"
                    }
                ],
          "categorias": 
                [
                    "07/07/2017",
                    "08/07/2017",
                    "09/07/2017",
                    "10/07/2017",
                    "11/07/2017",
                    "12/07/2017",
                    "13/07/2017",
                    "14/07/2017",
                    "15/07/2017",
                    "16/07/2017",
                    "17/07/2017",
                    "18/07/2017",
                    "19/07/2017",
                    "20/07/2017",
                    "21/07/2017",
                    "22/07/2017",
                    "23/07/2017",
                    "24/07/2017",
                    "25/07/2017",
                    "26/07/2017",
                    "27/07/2017",
                    "28/07/2017",
                    "29/07/2017",
                    "30/07/2017",
                    "31/07/2017"
                  ],
          "series": [
                {
                  "name": "CALLAO",
                  "data": [
                    8,
                    13,
                    9,
                    20,
                    11,
                    16,
                    15,
                    21,
                    7,
                    16,
                    20,
                    19,
                    20,
                    21,
                    22,
                    18,
                    21,
                    23,
                    19,
                    25,
                    16,
                    26,
                    12,
                    25,
                    21
                  ]
                },
                {
                  "name": "LIMA",
                  "data": [
                    12,
                    7,
                    24,
                    10,
                    14,
                    16,
                    14,
                    22,
                    10,
                    13,
                    27,
                    24,
                    20,
                    20,
                    20,
                    17,
                    22,
                    20,
                    27,
                    17,
                    23,
                    27,
                    20,
                    20,
                    27
                  ]
                }
          ]
        };

var opt;

    function filtrar(){
        /*SipcopJS.post('admin/reporte/json_consultar_distancias',{
            fechaini: $('#dpFechaIni').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'ini'), 
            fechafin: $('#dpFechaFin').data('datepicker').obtenerFecha($('#txtPeriodo').val(),'fin'),  
            periodo: $('#txtPeriodo').val(),
            ubigeo: SipcopJS.get_ubigeo('#txtDepartamento', '#txtProvincia', '#txtDistrito'),
            comisaria: $('#txtComisaria').val()
        }, 
            function(data){
            cargarData(data);
        });*/
        cargarData(data_general);
    }
    function cargarData(reporte){
        $('#dgTabla').dataTable().fnClearTable();
        $('#dgTabla').dataTable().fnAddData(reporte.data);
        $('#dgTabla').dataTable().fnDraw();

        opt = {
            chart: {
                type: 'line'
            },
            title: {
                text: 'Reporte de Distancia Recorrida Aprox.'
            },
            xAxis: {
                categories: reporte.categorias,
                crosshair: true
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Km Recorridos'
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
            {sTitle : "Localidad", mData: "RptLocalidad"},
            {sTitle : "Periodo", mData: "RptPeriodo"},
            {sTitle : "Km Recorrido", mData: "RptDistancia"}
          ], 
          "bServerSide" : false,
          "bProcessing" : false
        });

      SipcopJS.cargarUbigeo('#txtDepartamento',0,'<?php echo $usu_departamento; ?>','<?php echo $usu_departamento; ?>', function(){
        SipcopJS.cargarUbigeo('#txtProvincia',1,'<?php echo $usu_departamento; ?>','<?php echo $usu_provincia; ?>',function(){
            SipcopJS.cargarUbigeo('#txtDistrito',2,'<?php echo $usu_provincia; ?>','<?php echo $usu_distrito; ?>',function(){
                SipcopJS.cargarComisarias('#txtComisaria','<?php echo $usu_distrito; ?>','<?php echo @$usu_comisaria; ?>',function(){
                    filtrar();
                });
            });
        });
      });      
      
      
      


      $('#txtDepartamento').change(function(){
        $('#txtProvincia').val('0');
        SipcopJS.cargarUbigeo('#txtProvincia',1,$('#txtDepartamento').val(),'',null);
        $('#txtDistrito').val('0');
        SipcopJS.cargarUbigeo('#txtDistrito',2,$('#txtProvincia').val(),'',null);
        $('#txtComisaria').val('0');
        SipcopJS.cargarComisarias('#txtComisaria',$('#txtDistrito').val(),'',null);
      });

      $('#txtProvincia').change(function(){
        $('#txtDistrito').val('0');
        SipcopJS.cargarUbigeo('#txtDistrito',2,$('#txtProvincia').val(),'',null);
        $('#txtComisaria').val('0');
        SipcopJS.cargarComisarias('#txtComisaria',$('#txtDistrito').val(),'',null);
      });

      $('#txtDistrito').change(function(){
        $('#txtComisaria').val('0');
        SipcopJS.cargarComisarias('#txtComisaria',$('#txtDistrito').val(),'',null);
      });

      $('#txtPeriodo').change(function(){
        var tipo = $(this).val();

        if(tipo==1){
            $('#dpFechaIni').data('date', Date.today().moveToFirstDayOfMonth().toString('dd/MM/yyyy'));
            $('#dpFechaIni input').val(Date.today().moveToFirstDayOfMonth().toString('dd/MM/yyyy'));
            $('#dpFechaFin').data('date', Date.today().moveToLastDayOfMonth().toString('dd/MM/yyyy'));
            $('#dpFechaFin input').val(Date.today().moveToLastDayOfMonth().toString('dd/MM/yyyy'));

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

      $('#btnExportar').click(function(){
        /*$('#btnExportar').html('Exportando...');
        $('.highcharts-tooltip').hide();
        SipcopJS.generarChartOpt(opt, function(img){
            $('#btnExportar').html('Exportar');
            $('.highcharts-tooltip').show();
            SipcopJS.reporte_htmlpdf('Reporte de Ingresos en el Periodo '+$('#txtFechaIni').val()+' / '+ $('#txtFechaFin').val(), $('#Reporte').html(), img);
        });*/
      });
      //filtrar();
    }
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte de Distancia Recorrida Aproximada
            </header>
            <div class="panel-body">
            <form id="frmExportar">         
                <div class="row">
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-5" for="txtDepartamento"  >Departamento: </label>
                        <div class="col-lg-7">
                            <select tname="txtDepartamento" id="txtDepartamento" class="form-control" style="width:100%" <?php echo (($tipo_ubigeo > 0)?'disabled':''); ?>>
                                    <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>                     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtProvincia" >Provincia: </label>
                        <div class="col-lg-8">
                            <select tname="txtProvincia" id="txtProvincia" class="form-control" style="width:100%"  <?php echo (($tipo_ubigeo > 1)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>     
                    <div class="form-group col-sm-3" style="">
                        <label class="control-label col-lg-4" for="txtDistrito" >Distrito: </label>
                        <div class="col-lg-8">
                            <select tname="txtDistrito" id="txtDistrito" class="form-control" style="width:100%"  <?php echo (($tipo_ubigeo > 2)?'disabled':''); ?>>
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>  
                    <div class="form-group col-sm-3">
                        <label class="control-label col-lg-4" for="txtComisaria" >Comisaria: </label>
                        <div class="col-lg-8">
                            <select tname="txtComisaria" id="txtComisaria" class="form-control" style="width:100%"  <?php echo (($tipo_ubigeo > 3)?'disabled':''); ?>>
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
                            <button type="button" class="btn btn-info" id="btnFiltrar">Filtrar</button> 
                            <!--<button type="button" class="btn btn-success" id="btnExportar">Exportar</button>   -->   
                    </div>  
                </div>   
                <div class="clear"></div>
            </form>
            <div id="Reporte">
                <div class="col-sm-7">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">                   
                        </table>
                    </div>
                </div>
                <div class="col-sm-5">
                    <div id="Grafico"></div>
                </div>
            </div>
            </div>
        </section>
    </div>
</div>