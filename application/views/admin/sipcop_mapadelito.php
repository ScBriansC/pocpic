<?php
$usu_rol = $obj_usuario['IDROL'];
?>

<link href="assets/sipcop/css/home.css" rel="stylesheet">

<script type="text/javascript" src="https://maps.google.com/maps/api/js?v=3.exp&key=AIzaSyAQGNCgXhTrE7TJROgFSOftaosTVUtqXY8&libraries=visualization,drawing"></script>

<script type="text/javascript" src="assets/sipcop/js/markerclusterer.js"></script>
<script type="text/javascript" src="assets/sipcop/js/DenunciaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_delito.js"></script>
<script type="text/javascript" src="assets/sipcop/js/map_jurisdiccion.js"></script>

<style>
.denuncia-marker {
  position: absolute;
  cursor: pointer;
  width: 38px;
  height: 45px;
  z-index: 3000;
  }

.denuncia-marker:hover{
  z-index: 5000; 
}


.denuncia-marker .bloque1 {
  background: #FFF;
  border-radius: 30px;
  text-align: center;
  padding: 4px;
  z-index: 1050;
  position: relative; }

.denuncia-marker .bloque2 {
  overflow: hidden;
  border-radius: 30px;
  width: 30px;
  height: 30px; }

  .denuncia-marker .denuncia-tipo {
  display: none;
  position: absolute;
  bottom: 40px;
  left: -30px;
  padding: 4px 10px 4px 10px;
  background: #FFF;
  border-radius: 30px;
  border: 1px solid #FFFFFF;
  z-index: 1010;
  width: 100px;
  text-align: center;
  }

.denuncia-marker:hover .denuncia-tipo {
  display: block; }

.denuncia-marker .pie {
  display: block;
  width: 0;
  height: 0;
  border-style: solid;
  border-width: 10px 10px 0 10px;
  border-color: #FFF transparent transparent transparent;
  margin: -3px auto auto; }
</style>

<div id="cnv_map" class="map-full" style="overflow:hidden;"></div>
<div class="logos"><img src="assets/img/logos.png"></div>

<div class="filtro-info">
    <div class="content">
        <div class="panel-tab">
            <div class="panel-tab-group">
                <div class="panel-tab-content active">
                    <div class="item">
                        <label>Fecha Ini.:</label>
                        <div class="field"><input type="text" name="txtFechaIni" data-date-end-date="0d"  id="txtFechaIni" class="input-fil" style="width: 100%; background: url(assets/sipcop/img/ic-date.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;" readonly> </div>
                    </div>
                    <div class="item">
                        <label>Fecha Fin:</label>
                        <div class="field"><input type="text" name="txtFechaFin" data-date-end-date="0d"  id="txtFechaFin" class="input-fil" style="width: 100%; background: url(assets/sipcop/img/ic-date.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;" readonly ></div>
                    </div>
                    <div class="item">
                        <label>Hora Ini.:</label>
                        <div class="field"><input type="text" name="txtHoraIni" id="txtHoraIni" class="input-fil" style="width: 100%; background: url(assets/img/ic-time.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;"></div>
                    </div>
                    <div class="item">
                        <label>Hora Fin:</label>
                        <div class="field"><input type="text" name="txtHoraFin" id="txtHoraFin" class="input-fil" style="width: 100%; background: url(assets/img/ic-time.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;"></div>
                    </div>
                    <div class="item" style="<?php echo (($usu_rol == 3)?'display:none':''); ?>">
                        <label>MACROREG</label>
                        <div class="field">
                            <select tname="txtMacroreg" id="txtMacroreg" class="form-control">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="item" style="<?php echo (($usu_rol == 3)?'display:none':''); ?>">
                        <label>REGPOL:</label>
                        <div class="field">
                            <select tname="txtRegpol" id="txtRegpol" class="form-control">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="item" style="<?php echo (($usu_rol == 3)?'display:none':''); ?>">
                        <label>DIVPOL:</label>
                        <div class="field">
                            <select tname="txtDivter" id="txtDivter" class="form-control">
                                <option value="0">-- Seleccione --</option>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <label>Modo</label>
                        <div class="field">
                            <select tname="cboModo" id="cboModo" class="form-control">
                                <option value="1">Mapa de calor</option>
                                <option value="2">Íconos</option>
                            </select>
                        </div>
                    </div>
                    <!--<div class="item">
                        <label>Tipos:</label>
                        <div class="mapa-capas" style="padding-top: 5px;">
                        <div><input type="checkbox" id="ckTipo1" value="1" checked /> Tipo</div> 
                        <span class="clear"></span>
                        </div>
                    </div>-->
                    <div style="display: none"><input type="checkbox" id="ckJurisdiccion" value="1" checked /> Jurisdicción</div>
                </div>
                
            
            </div>
        </div>
        <div class="clear"></div>
            <a class="btn-filtrar" href="javascript:;">Buscar</a>            
            <a href="javascript:;" class="btn-limpiar"><img src="./assets/img/reset/reset-3.png" width="30"/></a>
        <div class="clear"></div>

        <!--<div class="dv-resultado">
            <div class="panel-tab-result active">
                <div style="text-align: center"><strong>Resumen General</strong><br><span class="resumen-tiempo"></span></div>
                <div class="cont-resumen"></div>
            </div>
        </div>-->
    </div>
    <a class="btn-hide" href="javascript:;"><i class="fa fa-minus fa-lg"></i></a>
    <a class="btn-show" href="javascript:;"><i class="fa fa-filter fa-lg"></i></a>
</div>




<div class="app-bg-loader"><img src="assets/sipcop/img/loader.gif" width="320"></div>


<script>


var fecha_actual;
preCarga = function(){
    fecha_actual = (new Date()).toString('dd/MM/yyyy');
    fecha_ini = (new Date(new Date().getFullYear(), new Date().getMonth(), 1)).toString('dd/MM/yyyy');
    //$('#txtFecha').val('2017-02-21');
    $('#txtFechaIni').val(fecha_ini);
    $('#txtFechaFin').val(fecha_actual);

    <?php foreach ($usu_jurisdiccion as $jurisd) { ?>
        map_api.usu_jurisdiccion.push('<?php echo $jurisd['IDINSTITUCION']; ?>');
    <?php } ?>

    function initJS(){
        map_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        jurisdiccion_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
    }

      SipcopJS.cargarDependencia('#txtMacroreg',0,'0','0', function(){
        SipcopJS.cargarDependencia('#txtRegpol',1,'0','0',function(){
            SipcopJS.cargarDependencia('#txtDivter',2,'0','0',function(){
                initJS();
            });
        });
      });

    

    $('.btn-limpiar').click(function(ev){
            ev.preventDefault();
            $('#txtFechaIni').val((new Date()).toString('dd/MM/yyyy'));
            $('#txtFechaFin').val((new Date()).toString('dd/MM/yyyy'));
            $('#txtHoraIni').val('00:00');
            $('#txtHoraFin').val('23:59');        

            map_api.filtro.institucion = 0;
            map_api.map.setZoom(5);
            jurisdiccion_api.activar(false);

            $('.filtro-info .btn-filtrar').click();
    });


    $('#ckJurisdiccion').change(function() {
          jurisdiccion_api.activar($(this).prop('checked'));
    });


    $('#btnFechaIni').click(function(ev){
        ev.preventDefault();
        var mostrar_dp = $('#txtFechaIni').data('mostrar_dp');
        if(!mostrar_dp){
            $('#txtFechaIni').datepicker('show');
        }else{
            $('#txtFechaIni').datepicker('hide');
        }
        $('#txtFechaIni').data('mostrar_dp',!mostrar_dp);
        
    });



    $('#btnFechaFin').click(function(ev){
        ev.preventDefault();
        var mostrar_dp = $('#txtFechaFin').data('mostrar_dp');
        if(!mostrar_dp){
            $('#txtFechaFin').datepicker('show');
        }else{
            $('#txtFechaFin').datepicker('hide');
        }
        $('#txtFechaFin').data('mostrar_dp',!mostrar_dp);
        
    });

    $('#btnHoraFin').click(function(ev){
        ev.preventDefault();
        //$('#txtHoraFin').timepicker('showWidget');
    });

    $('#btnHoraIni').click(function(ev){
        ev.preventDefault();
        //$('#txtHoraIni').timepicker('showWidget');
    });

    $('#txtHoraIni, #txtHoraFin').timepicker({
        showMeridian: false
    });

    $('#txtHoraIni').val('00:00');
    $('#txtHoraFin').val('23:59');

   
    $('#txtFechaIni,#txtFechaFin').datepicker({
        'format': 'dd/mm/yyyy',
        'autoclose': true
    });


    $('#txtMacroreg').change(function(){
        $('#txtRegpol').val('0');
        SipcopJS.cargarDependencia('#txtRegpol',1,$('#txtMacroreg').val(),'',null);
        $('#txtDivter').val('0');
        SipcopJS.cargarDependencia('#txtDivter',2,$('#txtRegpol').val(),'',null);
      });

      $('#txtRegpol').change(function(){
        $('#txtDivter').val('0');
        SipcopJS.cargarDependencia('#txtDivter',2,$('#txtRegpol').val(),'',null);
      }); 

    

    $(window).resize(function(){
        $('#cnv_map').css('width','100%');
        $('#cnv_map').height($(window).height()-$('header.header').height() - 6);
       // $('.select-fil form-control').select2();
    });

    $(window).resize();


    

    $('.filtro-info .btn-hide').click(function(){
        $('.filtro-info').addClass('hidex');
    });

    $('.filtro-info .btn-show').click(function(){
        $('.filtro-info').removeClass('hidex');
    });

    $("#txtHoraIni").keyup(function(){
            var hi = $('#txtHoraIni').val();
            var hii = hi.split(":");
            var val = $(this).val().length === 0;
            if( hii[1]>59 ){
                $('#txtHoraIni').val(hii[0]+':59');
            }
    });

    $("#txtHoraFin").keyup(function(){
            var hf = $('#txtHoraFin').val();
            var hff = hf.split(":");
            if( hff[1]>59 ){
                $('#txtHoraFin').val(hff[0]+':59');
            }
    });

    $('.filtro-info .btn-filtrar').click(function(){
        if($.trim($('#txtHoraIni').val()).length < 4 || $.trim($('#txtHoraFin').val()).length < 4) {
            $.gritter.add({
                position: 'bottom-right',
                title: 'Mensaje',
                text: 'Seleccione una hora válida',
                class_name: 'gritter-error'
            });
            return false;
        }
        var hi = $('#txtHoraIni').val();
        var hii = hi.replace(":","");
        var hf = $('#txtHoraFin').val();
        var hff = hf.replace(":","");

        if(hii>hff)
        {
            $.gritter.add({
                position: 'bottom-right',
                title: 'Mensaje',
                text: 'El rando de horas es inválido',
                class_name: 'gritter-error'
            });
        }  
        else{

            if($(this).hasClass('fil-searching') == false){
                console.log('aaa');
                map_api.add_TaskLoader();
                jurisdiccion_api.cargar(0, map_api.get_dependencia(), null,function(resp_data){
                    map_api.end_TaskLoader();
                    console.log('bbb');
                    console.log(resp_data);
                });
console.log('ccc');
                map_api.add_TaskLoader();
                map_api.aplicar_filtro(function(resp_data){
                    jurisdiccion_api.centrarJurisdiccion(); 

                    if(resp_data.data){
                        if(resp_data.data.length == 0){
                            $.gritter.add({
                                    position: 'bottom-right',
                                    title: 'Mensaje',
                                    text: 'No se encontraron denuncias',
                                    class_name: 'gritter-error'
                                });
                        }
                    }
                    map_api.map.setZoom(5);
                    map_api.centrarMapa();
                    map_api.end_TaskLoader();
                    console.log('ddd');
                    console.log(resp_data);
                });



            }
        }
        
    });


    $('#txtHoraIni, #txtHoraFin').mask('HH:MM', {'translation': {
                                        H: {pattern: /[0-9]/},
                                        M: {pattern: /[0-9]/}
                                      }
                                });


    $('body').on('click','.sipcop-closable, .sipcop-streetview-close',function(){
        $($('.sipcop-ui-modal-item').parent()).fadeOut('fast');
        $('.sipcop-ui-modal-item').hide();
    });

}
</script>
<div class="app-bg-loader" style="display: block;"><img src="assets/sipcop/img/loader.gif" width="320" style="position: absolute; top: 337px; left: 800px;"></div>