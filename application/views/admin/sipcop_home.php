<?php
$usu_rol = $obj_usuario['IDROL'];
?>

<link href="assets/sipcop/css/home.css?<?php echo rand(1,1000); ?>" rel="stylesheet">

<script type="text/javascript" src="https://maps.google.com/maps/api/js?v=3.exp&key=AIzaSyAQGNCgXhTrE7TJROgFSOftaosTVUtqXY8&libraries=visualization,drawing"></script>

<script type="text/javascript" src="assets/sipcop/js/markerclusterer.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/MarkerWithLabel.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/VehiculoMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/ComisariaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/app_home.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_denuncias.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_jurisdiccion.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_barrio.js?<?php echo rand(1,1000); ?>"></script>    
<script type="text/javascript" src="assets/sipcop/js/map_incidencia.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_camara.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_alarma.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/IncidenciaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/CamaraMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/AlarmaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/geocerca.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/comision.js?<?php echo rand(1,1000); ?>"></script>


<div id="cnv_map" class="map-full" style="overflow:hidden;"></div>
<div class="logos"><img src="assets/img/logos.png"></div>

<div class="leyenda_modal">
    <a href="javascript:;" onclick="modalLeyenda()" class="btn btn-warning tooltips" data-toggle="button" data-placement="left" data-original-title="Leyenda">
        Leyenda
    </a>
</div>

<div class="geocerca">
    <a href="javascript:;" onclick="fijarMapa()" class="btn btn-primary" data-toggle="button"  id="btnFijarMapa">
    Fijar Mapa
    </a>
    <a href="javascript:;" onclick="dibujarGeocerca()" class="btn btn-primary" data-toggle="button" id="btnGeoCerca">
    Dibujar GeoCerca
    </a>
</div>

<script type="text/javascript">
      function modalLeyenda(){
         $('#modalLeyenda').modal('show');
      }
</script>

<div id="modalLeyenda" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #f3ca3f">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <center><span style="font-size: medium;color: white; font-weight: bold;">LEYENDA</p></center>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
            <center>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/m_poli_1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">                   
                        Grupo Patrulleros
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                        <img src="assets/sipcop/img/m_radio_1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Grupo Motorizado
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/m_comi_1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Grupo Comisarias
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/icon-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Vehículos de Mi Jurisdicción
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/icon-3.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">                   
                            Vehículos de Otra Juridicción
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                        <img src="assets/sipcop/img/ico-car-1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Patrullero Emitiendo Señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/ico-car-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Patrullero Sin Emitir Señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/ico-patin-1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Patrullero Patrullaje Integrado Emitiendo Señal
                        </div>
                    </div>
                </div>            
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                           <img src="assets/sipcop/img/ico-patin-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">                   
                            Patrullero Patrullaje Integrado Sin Emitir Señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                        <img src="assets/sipcop/img/ico-radio_3-1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Motorizados Emitiendo señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/ico-radio_3-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Motorizados Sin Emitir Señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                        <img src="assets/sipcop/img/ico-pie-1.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Patrullaje a Pie Emitiendo señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6  col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                            <img src="assets/sipcop/img/ico-pie-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Patrullaje a Pie Sin Emitir Señal
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                           <img src="assets/sipcop/img/ico-comisaria-white-2.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Comisaría
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/ic-camara.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Cámara
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/alarma-on.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Alarma
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mijurisdiccion.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Mi Jurisdicción
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/jurisdiccion.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                        Otras Jurisdicciones
                        </div>
                    </div>
                </div>

                  <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/hurto_vehiculo.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Hurto Vehículo
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/hurto_domicilio.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Hurto Domicilio
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/hurto_persona.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Hurto a Persona
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/hurto_localcomercial.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Hurto Local Comercial
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/robo_persona.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Robo a Persona
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/estafa.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Estafa
                        </div>
                    </div>
                </div>


                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/estafa_llamadatelefonica.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Estafa Llamada telefónica
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/estafa_actoscontraelpudor.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Estafa Llamada actos contra el pudor
                        </div>
                    </div>
                </div>
                <div class="col-xs-12 col-sm-6 col-md-4">
                    <div class="card_leyenda">
                        <div class="image_leyenda pull-left">
                          <img src="assets/sipcop/img/mapa_delito/estafa_violenciasexual.png" class="detalle_leyenda">
                        </div>
                        <div class="content_leyenda pull-left">
                            Incidencia Estafa violencia sexual
                        </div>
                    </div>
                </div>

            </center>
            </div>
      
    </div>

        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<div class="filtro-info">
    <div class="content">
        <div class="panel-tab">
            <div class="panel-tab-tabs">
                <a href="javascript:;" class="tab-general active">General</a>
                <a href="javascript:;" class="tab-comisaria">Comisarías</a>
                <a href="javascript:;" class="tab-patrullero">Patrullaje</a>
            </div>
            <div class="panel-tab-group">
                <div class="panel-tab-content active">
                    <div class="item">
                        <label>Fecha:</label>
                        <div class="field"><input type="text" name="txtFecha" data-date-end-date="0d"  id="txtFecha" class="input-fil" style="width: 100%; background: url(assets/sipcop/img/ic-date.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;" readonly ></div>
                    </div>
                    <div class="item">
                        <label>Hora Ini.:</label>
                        <div class="field"><input type="text" name="txtHoraIni" id="txtHoraIni" class="input-fil" style="width: 100%; background: url(assets/img/ic-time.png); background-size: auto 100%; background-position: center right; background-repeat: no-repeat;"></div>
                    </div>
                    <div class="item">
                        <label>Hora Fin.:</label>
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
                        <label>Capas:</label>
                        <div class="mapa-capas" style="padding-top: 5px;">
                        <div><input type="checkbox" id="ckComisaria" value="1" checked /> Comisarías</div>
                        <div><input type="checkbox" id="ckJurisdiccion" value="1" checked /> Jurisdicción</div> 
                        <div><input type="checkbox" id="ckPatrullero" value="1" checked  /> Patrulleros</div> 
                        <div><input type="checkbox" id="ckMotorizado" value="1" checked /> Motorizados</div>  
                        <div><input type="checkbox" id="ckPatpie" value="1" /> Pat. Pie</div>  
                        <div><input type="checkbox" id="ckPuestoFijo" value="1" /> Puesto Fijo</div>  
                        <div><input type="checkbox" id="ckCamara" value="1" /> Cámaras</div> 
                        <div><input type="checkbox" id="ckAlarma" value="1" /> Alarma</div>     
                        <div><input type="checkbox" id="ckIncidencia" value="1" /> Incidencias</div>          
                        <div><input type="checkbox" id="ckMapaDenuncia" value="1"/> Mapa de Calor</div>
                        <div><input type="checkbox" id="ckBarrioSeguro" value="1"/> Barrio Seguro</div>  
                        <span class="clear"></span>
                        </div>
                    </div>
                </div>
                <!-- TAB COMISARIA -->
                <div class="panel-tab-content">
                    <div class="item">
                        <label>Nombre:</label>
                        <div class="field"><input type="text" name="txtComisariaNombre" id="txtComisariaNombre" class="input-fil"></div>
                    </div>
                    <div class="item">
                        <label>Depend.:</label>
                        <div class="field">
                            <select name="txtComisariaDependencia" id="txtComisariaDependencia" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_dependencia as $c_dependencia) { ?>
                                <option value="<?php echo $c_dependencia['IDDEPENDENCIA']; ?>"><?php echo $c_dependencia['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="item" style="display: none">
                        <label>Zona:</label>
                        <div class="field">
                            <select name="txtComisariaZona" id="txtComisariaZona" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_zona as $c_zona) { ?>
                                <option value="<?php echo $c_zona['IDZONA']; ?>"><?php echo $c_zona['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="item" style="display: none;">
                        <label>División:</label>
                        <div class="field">
                            <select name="txtComisariaDivision" id="txtComisariaDivision" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_division as $c_division) { ?>
                                <option value="<?php echo $c_division['IDDIVISION']; ?>"><?php echo $c_division['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="item" style="display: none;">
                        <label>Clase:</label>
                        <div class="field">
                            <select name="txtComisariaClase" id="txtComisariaClase" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_clase as $c_clase) { ?>
                                <option value="<?php echo $c_clase['IDCLASE']; ?>"><?php echo $c_clase['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <label>Tipo:</label>
                        <div class="field">
                            <select name="txtComisariaTipo" id="txtComisariaTipo" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_tipo as $c_tipo) { ?>
                                <option value="<?php echo $c_tipo['IDTIPO']; ?>"><?php echo $c_tipo['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="item">
                        <label>Categoría:</label>
                        <div class="field">
                            <select name="txtComisariaCategoria" id="txtComisariaCategoria" class="select-fil form-control">
                                <option value="0">-- Seleccione --</option>
                                <?php foreach ($comisaria_categoria as $c_categoria) { ?>
                                <option value="<?php echo $c_categoria['IDCATEGORIA']; ?>"><?php echo $c_categoria['NOMBRE']; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                </div>
                <!-- TAB PATRULLERO -->
                <div class="panel-tab-content">
                    <div class="item">
                        <label>Placa:</label>
                        <div class="field"><input type="text" name="txtPlaca" id="txtPlaca" class="input-fil" style="text-transform: uppercase;"></div>
                    </div>
                    <!--<div class="item">
                        <label>Tipo:</label>
                        <div class="field">
                            <select tname="txtTipoVehiculo" id="txtTipoVehiculo" class="select-fil form-control">
                                <option value="">-- Seleccione --</option>
                                <option value="1">Patrullero</option>
                                <option value="2">Motorizado</option>
                            </select>
                        </div>
                    </div>-->


                    <div class="item">
                        <label>Radio:</label>
                        <div class="field"><input type="text" name="txtRadio" id="txtRadio" class="input-fil"></div>
                    </div>
                    <div class="item">
                        <label>Serie:</label>
                        <div class="field"><input type="text" name="txtSerie" id="txtSerie" class="input-fil"></div>
                    </div>

                    <div class="item" style="display: none;">
                        <label>Policía:</label>
                        <div class="field"><input type="text" name="txtPolicia" id="txtPolicia" class="input-fil"></div>
                    </div>
            
                </div>     
            
            </div>
        </div>
        <div class="clear"></div>
            <a class="btn-filtrar" href="javascript:;">Buscar</a>            
            <a href="javascript:;" class="btn-limpiar"><img src="./assets/img/reset/reset-3.png" width="30"/></a>
        <div class="clear"></div>

        <div class="dv-resultado">
            <div class="panel-tab-result active">
                <div style="text-align: center"><strong>Resumen General</strong><br><span class="resumen-tiempo"></span></div>
                <div class="cont-resumen"></div>
            </div>
            <div class="panel-tab-result">
                <div style="text-align: center"><strong>Resultados (<span class="restult-comisaria">0</span>)</strong></div>
                <div class="cont-comisaria"></div>
            </div>
            <div class="panel-tab-result">
                <div style="text-align: center"><strong>Resultados (<span class="restult-vehiculo">0</span>)</strong></div>
                <div class="cont-vehiculo"></div>
            </div>
        </div>
    </div>
    <a class="btn-hide" href="javascript:;"><i class="fa fa-minus fa-lg"></i></a>
    <a class="btn-show" href="javascript:;"><i class="fa fa-filter fa-lg"></i></a>
</div>

<!-- <div class="hoja-ruta"></div> -->

<div class="vehiculo-info">
    <div class="content">
        <div class="info etiqueta"><strong>Radio: </strong> <span></span></div>
        <div class="info placa"><strong>Placa: </strong> <span></span></div>
        <div class="info ref"><strong>Referencia: </strong> <span></span></div>
        <div class="info comisaria"><strong>Comisaria: </strong> <span></span></div>
        <div class="info departamento"><strong>Departamento: </strong> <span></span></div>
        <div class="info provincia"><strong>Provincia: </strong> <span></span></div>
        <div class="info distrito"><strong>Distrito: </strong> <span></span></div>
        <div class="info direccion"><strong>Ubicación: </strong> <span></span></div>
        <div class="info fecha"><strong>Fecha: </strong> <span></span></div>
        <div class="info horaini"><strong>Hora Inicio: </strong> <span></span></div>
        <div class="info horafin"><strong>Hora Fin: </strong> <span></span></div>
        <!--<div class="info velocidad"><strong>Velocidad Aprox.(Km/h): </strong> <span></span></div>-->
        <div class="info distancia"><strong>Distancia Recorrida Aprox.(Km): </strong> <span></span></div>
        <center>
            <a class="btn-ruta" href="javascript:;" id="btnprimero">Mostrar Ruta</a>
            <a class="btn-ruta" href="javascript:;" id="btnsegundo" style="display: none;">Mostrar Ruta</a>
            <a class="btn-inirecorrido" id="btn-inirecorrido" href="javascript:;" style="display: none;"><i class="fa fa-play" aria-hidden="true"></i> Iniciar</a>
            <a class="btn-finrecorrido" id="btn-finrecorrido" href="javascript:;" style="display: none;"><i class="fa fa-stop" aria-hidden="true"></i> Detener</a>
        <!-- <div class="div-comision"></div> -->
        </center>

    </div>
    <a class="btn-close" href="javascript:;" ><i class="fa fa-times fa-lg"></i></a>

</div>

<div class="comisaria-info">
    <div class="content">
        <div class="info nombre"><strong>Nombre: </strong> <span></span></div>
        <div class="info jefe" style="display:none"><strong>Jefe: </strong> <span></span></div>
        <div class="info telefono"><strong>Teléfono: </strong> <span></span></div>
        <div class="info dependencia"><strong>Dependencia: </strong> <span></span></div>
        <div class="info zona" style="display:none"><strong>Zona: </strong> <span></span></div>
        <div class="info macroreg"><strong>MACROREG: </strong> <span></span></div>
        <div class="info regpol"><strong>REGPOL: </strong> <span></span></div>
        <div class="info divter"><strong>DIVPOL: </strong> <span></span></div>
        <div class="info clase"><strong>Clase: </strong> <span></span></div>
        <div class="info tipo"><strong>Tipo: </strong> <span></span></div>
        <div class="info categoria"><strong>Categoría: </strong> <span></span></div>
        <a class="btn-mostrar-veh" style="width: 80%;" id="btn-mostrar-veh" href="javascript:;">Mostrar unidades</a>
        <div class="cont-resumen-comisaria"></div>

    </div>
    <a class="btn-close" href="javascript:;"><i class="fa fa-times fa-lg"></i></a>

</div>


<div class="sipcop-ui-modal">
    <div class="sipcop-closable"></div>
    <div class="sipcop-ui-modal-item sipcop-streetview">
        <a class="sipcop-streetview-close" href="javascript:;"><i class="fa fa-times fa-lg"></i></a>
        <div class="sipcop-streetview-frame" ><iframe frameborder="0" src="" width="100%" height="100%"></iframe></div>
    </div>
</div>
 
<audio id="sndAlarma1" src="assets/sipcop/sound/button-09.wav"></audio>
<audio id="sndAlarma2" src="assets/sipcop/sound/button-03.wav"></audio>
<audio id="sndAlarma3" src="assets/sipcop/sound/alarma.mp3"></audio>

<div class="modal fade" id="myModalAlarma" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header" style="background: #d61313;">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <center><h4 class="modal-title" id="myModalLabel" style="font-weight: 700;color: white;">Apágar Alarma</h4></center>
      </div>
      <div class="modal-body">

        <form>
          <input type="hidden" id="txtidalarma" name="txtidalarma" value="">
          <input type="hidden" id="txtnombrealarma" name="txtnombrealarma" value="">
          <input type="hidden" id="txtlatalarma" name="txtlatalarma" value="">
          <input type="hidden" id="txtlonalarma" name="txtlonalarma" value="">
          <input type="hidden" id="txtenc" name="txtenc" value="">
          <div class="form-group">
            <label for="tipoalarma"><b>Tipo de alarma:</b> </label>
            <label id="eTipoA" style="color:red;"></label>
            <select class="form-control" id="tipoalarma" required>
                <option value="0">-- Seleccione --</option>
                <?php foreach ($alarma_tipos as $tipo) { ?>
                <option value="<?php echo $tipo['IDALARMATIPO']; ?>"><?php echo $tipo['NOMBRE']; ?></option>
                <?php } ?>
            </select>
          </div>
          <div class="form-group">
            <label for="motivo"><b>Motivo:</b> </label>
            <label id="eMotivoA" style="color:red;"></label>
            <input type="text" class="form-control" id="motivoalarma" required>
          </div>
          <div class="form-group">
            <label for="detalle"><b>Detalle:</b> </label>
            <label id="eDetalleA" style="color:red;"></label>
            <textarea class="form-control" rows="3" id="detallealarma" required></textarea>
          </div>
          <center><a href="javascript:;" onclick="alarma_api.apagarAlarma()" type="button" class="btn btn-success" style="background: #d61313;">APÁGAR ALARMA</a></center>
        </form>

      </div>
    </div>
  </div>
</div>


<div class="app-bg-loader"><img src="assets/sipcop/img/loader.gif" width="320"></div>

<div id="modalGeocerca" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <h4 id="basicModalLabel" class="modal-title">UNIDADES IDENTIFICADAS EN LA ZONA &nbsp;&nbsp;&nbsp;
        </h4>
      </div>
      <div class="modal-body">
        <div class="adv-table">
          <table  class="display table table-bordered table-striped table-condensed cf dgGeocerca" id="dgGeocerca" width="100%">                   
          </table>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

<script>


var fecha_actual;
preCarga = function(){SipcopJS.logEnabled = false;
    fecha_actual = (new Date()).toString('dd/MM/yyyy');
    //$('#txtFecha').val('2017-02-21');
    $('#txtFecha').val(fecha_actual);

     comision_api.init();

    <?php foreach ($usu_jurisdiccion as $jurisd) { ?>
        map_api.usu_jurisdiccion.push('<?php echo $jurisd['IDINSTITUCION']; ?>');
    <?php } ?>

    function initJS(){
        map_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        denuncia_api.init(map_api.get_dependencia(),0,'<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        jurisdiccion_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        incidencia_api.init(map_api.get_dependencia(), 0,'<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        camara_api.init(map_api.get_dependencia(),0,'<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        alarma_api.init(map_api.get_dependencia(), 0,'<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
    }

    /*SipcopJS.cargarDependencia('#txtMacroreg',0,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDMACROREG']; ?>', function(){
        SipcopJS.cargarDependencia('#txtRegpol',1,'<?php echo $obj_usuario['IDMACROREG']; ?>','<?php echo $obj_usuario['IDREGPOL']; ?>',function(){
            SipcopJS.cargarDependencia('#txtDivter',2,'<?php echo $obj_usuario['IDREGPOL']; ?>','<?php echo $obj_usuario['IDDIVTER']; ?>',function(){
                initJS();
            });
        });
      });*/

      SipcopJS.cargarDependencia('#txtMacroreg',0,'0','0', function(){
        SipcopJS.cargarDependencia('#txtRegpol',1,'0','0',function(){
            SipcopJS.cargarDependencia('#txtDivter',2,'0','0',function(){
                initJS();
            });
        });
      });

         $('#dgGeocerca').dataTable({
          "bSort": true,
          "bFilter": false,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : "COMISARIA", mData: "ComisariaNombre"},
            {sTitle : "PLACA", mData: "Placa"},
            {sTitle : "IDRADIO", mData: "RadioID"},
            {sTitle : "VEHICULO", mData: "VehiculoModelo"}

          ], 
          "bServerSide" : false,
          "bProcessing" : true
        });


    //$('#txtHoraFin').val((new Date()).toString('H:mm:ss'));
    //$('#txtHoraIni').val(((new Date()).add({ hours: -1 })).toString('H:mm:ss'));
    
    $('#txtComisariaDependencia').val(0);

    $('#btn-finrecorrido').click(function(ev){
        clearInterval(map_api.seq_demo);
         $('#btn-inirecorrido').show();
         $('#btn-finrecorrido').hide();

         $('#btnprimero').hide();
         $('#btnsegundo').show();
    });
    
    $('#btn-inirecorrido').click(function(ev){

        var puntos = null;
        var obj = null;
        var idradio = null;
        if(map_api.puntosRuta && map_api.puntosRuta.length > 0){
            puntos = map_api.puntosRuta;
            obj = map_api.mrkPatrullajeSelected;
            idradio = obj.args.oPatrullaje.DispoGPS;

        }

        // else if(map_api.puntosRuta && map_api.puntosRuta.length > 0){
        //     puntos = map_api.puntosRuta;
        //     obj = map_api.mrkSelected;
        //     idradio = obj.args.oVehiculo.RadioID;
        // }


         $('#btnprimero').hide();
         $('#btnsegundo').show();

         $('#btn-inirecorrido').hide();
         $('#btn-finrecorrido').show();


        if(map_api.seq_demo){
            clearInterval(map_api.seq_demo);
        }


        if(puntos && puntos.length > 0 && obj){
            // console.log(obj);
            map_api.seq_demo_pos = puntos.length-1;
            // console.log(map_api.seq_demo_pos);
            map_api.seq_demo = setInterval(function(){


                try{
                    var obj2 = map_api.oMrkMotorizado['Patrullaje_' + idradio];
                    if(!obj2){
                        obj2 = map_api.oMrkPatrullero['Patrullaje_' + idradio];
                    }
                    if(!obj2){
                        obj2 = map_api.oMrkBarrioSeg['Patrullaje_' + idradio];
                    }
                    if(!obj2){
                        obj2 = map_api.oMrkPatPie['Patrullaje_' + idradio];
                    }
                    if(!obj2){
                        obj2 = map_api.oMrkPuestoFijo['Patrullaje_' + idradio];
                    }

                    if(map_api.seq_demo_pos > 0){
                        obj2.latlng = puntos[map_api.seq_demo_pos].position;
                        obj2.draw();
                        obj2.setMsj(puntos[map_api.seq_demo_pos].labelContent);
                        map_api.seq_demo_pos--;
                    }else{
                        clearInterval(map_api.seq_demo);
                        $('#btn-inirecorrido').show();
                        $('#btn-finrecorrido').hide();
                        obj2.draw();
                        obj2.setMsj('');
                    }
                }catch(ex){
                    console.log(ex);
                    clearInterval(map_api.seq_demo);
                    $('#btn-inirecorrido').show();
                    $('#btn-finrecorrido').hide();
                    obj2.draw();
                    obj2.setMsj('');
                }


            },200);
        }
    });

    

    $('.btn-limpiar').click(function(ev){
            ev.preventDefault();
            $('#txtFecha').val((new Date()).toString('dd/MM/yyyy'));
            $('#txtHoraIni').val('00:00');
            $('#txtHoraFin').val('23:59');        
            // $('#ckPatrullero').prop('checked', false);
            $('#ckComisaria').prop('checked', true);
            $('#ckMotorizado').prop('checked', true);
            $('#ckPatrullero').prop('checked', true);
            $('#txtComisariaDependencia').val(0);
            $('.resumen-tiempo').html('('+$('#txtHoraIni').val()+' - '+$('#txtHoraFin').val()+')');
            $("#ckMapaDenuncia").attr('checked', false);
            // $("#ckPatrullero").attr('checked', true);
            // $("#ckMotorizado").attr('checked', false);
            $("#txtMacroreg").val('0').trigger('change');
            $("#txtRegpol").val('0').trigger('change');
            $("#txtDivter").val('0').trigger('change');
            $('#txtComisariaNombre').val('');
            $('#txtPlaca').val('');
            $("#txtComisariaClase").val('0').trigger('change');
            $("#txtComisariaTipo").val('0').trigger('change');
            $("#txtComisariaCategoria").val('0').trigger('change');
            $('#txtRadio').val('');
            $('#txtSerie').val('');
            $('#txtEtiqueta').val('');
            $('#txtEtiquetaMoto').val('');
            $('#txtSerieMoto').val('');
            map_api.filtro.institucion = 0;
            map_api.map.setZoom(5);
            denuncia_api.activar(false);
            jurisdiccion_api.activar(false);
            incidencia_api.activar(false);
            camara_api.activar(false);
            alarma_api.activar(false);
            barrio_api.activar(false);
            $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive'); 
            $($('.panel-tab-tabs a.tab-comisaria')).removeClass('inactive'); 
            $($('.panel-tab-tabs a.tab-motorizado')).removeClass('inactive'); 
            $('.filtro-info .btn-filtrar').click();
    });

    $(".leyenda").mouseover(function(){
          $($('.leyenda-contenido')).removeClass('inactivo');
          $($('.leyenda-contenido')).addClass('active');
    });
    $(".leyenda").mouseout(function(){
         $($('.leyenda-contenido')).removeClass('active');
         $($('.leyenda-contenido')).addClass('inactivo');
    });

    var vehi =  $('#ckPatrullero').is(':checked');
    var comi =  $('#ckComisaria').is(':checked');
    var radio = $('#ckMotorizado').is(':checked');
    var pie =   $('#ckPatpie').is(':checked');
    var mapa =  $('#ckMapaDenuncia').is(':checked');
    var barrio =$('#ckBarrioSeguro').is(':checked');
    var puestofijo =$('#ckPuestoFijo').is(':checked');

      // alert(radio);denuncia_api

    $('#ckPatrullero').change(function() {
        var mostrar = $('#ckPatrullero').prop('checked') || $('#ckMotorizado').prop('checked') || $('#ckPatpie').prop('checked') || $('#ckBarrioSeguro').prop('checked') || $('#ckPuestoFijo').prop('checked');
              map_api.clearMarkersPatrullero();            
          if (!mostrar) {    
              $($('.panel-tab-tabs a.tab-patrullero')).addClass('inactive');     
              $('.cont-vehiculo').html('');
              $('.restult-vehiculo').html('0');
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
          else {
              $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive');     
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
    });

    $('#ckMotorizado').change(function() {
        var mostrar = $('#ckPatrullero').prop('checked') || $('#ckMotorizado').prop('checked') || $('#ckPatpie').prop('checked') || $('#ckBarrioSeguro').prop('checked') || $('#ckPuestoFijo').prop('checked');
              map_api.clearMarkersMotorizado();     
        if (!mostrar) {    
              $($('.panel-tab-tabs a.tab-patrullero')).addClass('inactive');      
              $('.cont-vehiculo').html('');
              $('.restult-vehiculo').html('0');
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
          else {
              $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive');     
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
    });

    $('#ckPatpie').change(function() {
        var mostrar = $('#ckPatrullero').prop('checked') || $('#ckMotorizado').prop('checked') || $('#ckPatpie').prop('checked') || $('#ckBarrioSeguro').prop('checked') || $('#ckPuestoFijo').prop('checked');
              map_api.clearMarkersPatpie();      
        if (!mostrar) {      
              $($('.panel-tab-tabs a.tab-patrullero')).addClass('inactive');           
              $('.cont-vehiculo').html('');
              $('.restult-vehiculo').html('0');
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
          else {
              $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive');     
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
    });

    $('#ckPuestoFijo').change(function() {
        var mostrar = $('#ckPatrullero').prop('checked') || $('#ckMotorizado').prop('checked') || $('#ckPatpie').prop('checked') || $('#ckBarrioSeguro').prop('checked') || $('#ckPuestoFijo').prop('checked');
              map_api.clearMarkersPuestoFijo();      
        if (!mostrar) {      
              $($('.panel-tab-tabs a.tab-patrullero')).addClass('inactive');           
              $('.cont-vehiculo').html('');
              $('.restult-vehiculo').html('0');
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
          else {
              $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive');     
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
    });

    $('#ckBarrioSeguro').change(function() {
        var mostrar = $('#ckPatrullero').prop('checked') || $('#ckMotorizado').prop('checked') || $('#ckPatpie').prop('checked') || $('#ckBarrioSeguro').prop('checked') || $('#ckPuestoFijo').prop('checked');
              map_api.clearMarkersBarrioSeg(); 
              barrio_api.activar($(this).prop('checked'));     
        if (!mostrar) {      
              $($('.panel-tab-tabs a.tab-patrullero')).addClass('inactive');           
              $('.cont-vehiculo').html('');
              $('.restult-vehiculo').html('0');
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
          else {
              $($('.panel-tab-tabs a.tab-patrullero')).removeClass('inactive');     
              $('#txtPlaca').val('');
              $('#txtEtiqueta').val('');
              $('#txtSerie').val('');
          }
    });


    $('#ckMapaDenuncia').change(function() {
          denuncia_api.activar($(this).prop('checked'));
    });

    $('#ckJurisdiccion').change(function() {
          jurisdiccion_api.activar($(this).prop('checked'));
    });



    $('#ckCamara').change(function() {
            if(!$(this).prop('checked')){
                camara_api.clear();
            }
          camara_api.activar($(this).prop('checked'));
    });

    $('#ckAlarma').change(function() {

        if(!$(this).prop('checked')){
            alarma_api.clear();
        }

          alarma_api.activar($(this).prop('checked'));
    });

    $('#ckIncidencia').change(function() {
        if(!$(this).prop('checked')){
            incidencia_api.clear();
        }

        incidencia_api.activar($(this).prop('checked'));
    });

    $('#ckComisaria').change(function() {
          if (!$(this).prop('checked')) {     
              $($('.panel-tab-tabs a.tab-comisaria')).addClass('inactive');
              $('#txtComisariaNombre').val('');
              $("#txtComisariaClase").val('0').trigger('change');
              $("#txtComisariaTipo").val('0').trigger('change');
              $("#txtComisariaCategoria").val('0').trigger('change');
              map_api.clearMarkersComisaria();
              $('.cont-comisaria').html('');
              $('.restult-comisaria').html('0');
          }
          else {
              $($('.panel-tab-tabs a.tab-comisaria')).removeClass('inactive');
              $('#txtComisariaNombre').val('');
              $("#txtComisariaClase").val('0').trigger('change');
              $("#txtComisariaTipo").val('0').trigger('change');
              $("#txtComisariaCategoria").val('0').trigger('change');
          }
    });

    $('#btnFecha').click(function(ev){
        ev.preventDefault();
        var mostrar_dp = $('#txtFecha').data('mostrar_dp');
        if(!mostrar_dp){
            $('#txtFecha').datepicker('show');
        }else{
            $('#txtFecha').datepicker('hide');
        }
        $('#txtFecha').data('mostrar_dp',!mostrar_dp);
        
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

    $('.resumen-tiempo').html('('+$('#txtHoraIni').val()+' - '+$('#txtHoraFin').val()+')');

    $('#txtFecha').datepicker({
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


    $('body').on('click', '.itm-comisaria', function(el){

        if(!map_api.fijar_coord){
            map_api.centrarMapa(map_api.mrkComisarias[$(this).attr('id')].latlng, 20);
            jurisdiccion_api.centrarJurisdiccion(map_api.mrkComisarias[$(this).attr('id')].args.oComisaria.ComisariaID);
            barrio_api.centrarBarrio(map_api.mrkComisarias[$(this).attr('id')].args.oComisaria.ComisariaID); //JCO     
        }else{
            map_api.map.setZoom(map_api.fijar_coord.zoom);
            map_api.map.setCenter(map_api.fijar_coord.latlng);
        }
        
        map_api.mostrarInfoComisaria(map_api.mrkComisarias[$(this).attr('id')]);                                                                                                 

    });

    $('body').on('click', '.itm-vehiculo', function(el){        
        map_api.mostrarInfoPatrullaje($(this).attr('id'));
    });

    $('.vehiculo-info .btn-close').click(function(){
        $('.vehiculo-info').hide();
        $('.filtro-info').show();


         $('#btnprimero').show();
         $('#btnsegundo').hide();

         $('#btn-inirecorrido').hide();
         $('#btn-finrecorrido').hide();

        if(map_api.mrkPatrullajeSelected!=null && (map_api.filtro.dispogps > 0)){
            map_api.filtro.dispogps = 0;
            map_api.filtro.tipo_filtro = 0;
            $('.btn-filtrar').click();
        }

        map_api.mrkPatrullajeSelected = null;
        map_api.mrkComisariaSelected = null;
    });

    $('.comisaria-info .btn-close').click(function(){
        $('.comisaria-info').hide();
        $('.filtro-info').show();

        if(map_api.mrkComisariaSelected!=null && (map_api.filtro.institucion > 0)){
            map_api.filtro.dispogps = 0;
            map_api.filtro.tipo_filtro = 0;
            map_api.filtro.institucion = 0;
            $('.btn-filtrar').click();
        }

        map_api.mrkPatrullajeSelected = null;
        map_api.mrkComisariaSelected = null;
    });

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
            map_api.mrkSelected = null;
            map_api.mrkComisariaSelected = null;

            if($(this).hasClass('fil-searching') == false){
            map_api.add_TaskLoader();
            denuncia_api.cargar(map_api.get_dependencia(),0,function(resp_data){
                if(resp_data.data){
                    if(resp_data.data.length == 0 && $('#ckMapaDenuncia').is(':checked')){
                        $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: 'No se encontraron denuncias',
                                class_name: 'gritter-error'
                        });
                        // map_api.addTaskError('No se encontraron Cámaras.');
                    }
                }
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            jurisdiccion_api.cargar(0, map_api.get_dependencia(), null,function(resp_data){
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            barrio_api.cargar(0, map_api.get_dependencia(), null,function(resp_data){
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            incidencia_api.cargar(map_api.get_dependencia(), 0, false,function(resp_data){
                 if(resp_data.data){
                    if(resp_data.data.length == 0 && $('#ckIncidencia').is(':checked')){
                        $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: 'No se encontraron Incidencias',
                                class_name: 'gritter-error'
                        });
                    }
                }
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            camara_api.cargar(map_api.get_dependencia(), 0,function(resp_data){
                if(resp_data.data){
                    if(resp_data.data.length == 0 && $('#ckCamara').is(':checked')){
                        $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: 'No se encontraron Cámaras',
                                class_name: 'gritter-error'
                        });
                        // map_api.addTaskError('No se encontraron Cámaras.');
                    }
                }
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            alarma_api.cargar(map_api.get_dependencia(),0,false,function(resp_data){
                if(resp_data.data){
                    if(resp_data.data.length == 0 && $('#ckAlarma').is(':checked')){
                        $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: 'No se encontraron Alarmas',
                                class_name: 'gritter-error'
                        });
                        // map_api.addTaskError('No se encontraron Cámaras.');
                    }
                }
                map_api.end_TaskLoader();
            });

            if($('#txtHoraIni').val()!='' && $('#txtHoraFin').val()!=''){
                $('.resumen-tiempo').html('('+$('#txtHoraIni').val()+' - '+$('#txtHoraFin').val()+')');
            }
            map_api.filtro.comisaria = 0;
            map_api.add_TaskLoader();


            map_api.aplicar_filtro(function(resp_data){
                if(!map_api.fijar_coord){
                    jurisdiccion_api.centrarJurisdiccion();   
                }else{
                    map_api.map.setZoom(map_api.fijar_coord.zoom);
                    map_api.map.setCenter(map_api.fijar_coord.latlng);
                }

                // if(resp_data.data){
                //     if(resp_data.data.length == 0 && ($('#ckPatrullero').is(':checked') == true || $('#ckMotorizado').is(':checked') == true || $('#ckPatpie').is(':checked') == true)){
                //         $.gritter.add({
                //                 position: 'bottom-right',
                //                 title: 'Mensaje',
                //                 text: 'No se encontraron vehículos',
                //                 class_name: 'gritter-error'
                //             });
                //     }
                // }
                if(resp_data.data){
                    if(resp_data.data.length == 0 && ($('#ckPatrullero').is(':checked') == true )){
                        $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: 'No se encontraron vehículos',
                                class_name: 'gritter-error'
                            });
                    }
                }
                map_api.end_TaskLoader();
            });



            map_api.add_TaskLoader();
            map_api.get_resumen('.cont-resumen',function(resp_data){
                map_api.end_TaskLoader();
            });

            map_api.add_TaskLoader();
            map_api.aplicar_filtro_comisaria(function(resp_data){
                if(!map_api.fijar_coord){
                    jurisdiccion_api.centrarJurisdiccion();   
                }else{
                    map_api.map.setZoom(map_api.fijar_coord.zoom);
                    map_api.map.setCenter(map_api.fijar_coord.latlng);
                }
                
                map_api.end_TaskLoader();
            });

         
        }
        }
        
    });

    $('.vehiculo-info .btn-ruta').click(function(){
        if($(this).hasClass('fil-searching') == false){
            map_api.filtro.institucion = 0;
            map_api.add_TaskLoader();
            map_api.aplicar_ruta(function(resp_data){
                    map_api.end_TaskLoader();
                });
        }
    });


    $('.comisaria-info .btn-mostrar-veh').click(function(){
        if((parseInt(map_api.mrkComisariaSelected.args.oComisaria.ActualPatrullero) + parseInt(map_api.mrkComisariaSelected.args.oComisaria.ActualPatInt) + parseInt(map_api.mrkComisariaSelected.args.oComisaria.ActualMotorizado)
             + parseInt(map_api.mrkComisariaSelected.args.oComisaria.ActualBarrioSeg) + parseInt(map_api.mrkComisariaSelected.args.oComisaria.ActualPuestoFijo))>0){
           
            if($(this).hasClass('fil-searching') == false){
         
                map_api.filtro.institucion = map_api.mrkComisariaSelected.args.oComisaria.ComisariaID;
                
                map_api.add_TaskLoader();
                map_api.aplicar_filtro(function(resp_data){
                    map_api.end_TaskLoader();
                    if(!map_api.fijar_coord){
                        jurisdiccion_api.centrarJurisdiccion(map_api.filtro.institucion);
                    }else{
                        map_api.map.setZoom(map_api.fijar_coord.zoom);
                        map_api.map.setCenter(map_api.fijar_coord.latlng);
                    }
              
                });
            }
        }else if((parseInt(map_api.mrkComisariaSelected.args.oComisaria.TotalPatrullero) + parseInt(map_api.mrkComisariaSelected.args.oComisaria.TotalPatInt) + parseInt(map_api.mrkComisariaSelected.args.oComisaria.TotalMotorizado)
             + parseInt(map_api.mrkComisariaSelected.args.oComisaria.TotalBarrioSeg)  + parseInt(map_api.mrkComisariaSelected.args.oComisaria.TotalPuestoFijo))>0){
            $.gritter.add({
                    position: 'bottom-right',
                    title: 'Mensaje',
                    text: 'Las unidades  de esta comisaría no están transmitiendo',
                    class_name: 'gritter-error'
                });
        }else{
            $.gritter.add({
                    position: 'bottom-right',
                    title: 'Mensaje',
                    text: 'La comisaría no tiene unidades asignadas ',
                    class_name: 'gritter-error'
                });
        }
    });

    $('.panel-tab-tabs a').click(function(){
        $('.panel-tab-tabs a, .panel-tab-content').removeClass('active');
        $('.panel-tab-tabs a, .panel-tab-result').removeClass('active');
        $(this).addClass('active');
        if($('.panel-tab-tabs a.tab-comisaria').hasClass('active')){
            $($('.panel-tab-content')[1]).addClass('active');
            $($('.panel-tab-result')[1]).addClass('active');
        }else if($('.panel-tab-tabs a.tab-patrullero').hasClass('active')){
            $($('.panel-tab-content')[2]).addClass('active');
            $($('.panel-tab-result')[2]).addClass('active');
        }
        else{
            $($('.panel-tab-content')[0]).addClass('active');
            $($('.panel-tab-result')[0]).addClass('active');
        }
    });

    $('#txtPlaca').mask('AA-YYYYY', {'translation': {
                                        A: {pattern: /[A-Za-z]/},
                                        Y: {pattern: /[0-9]/}
                                      }
                                });

    $('#txtHoraIni, #txtHoraFin').mask('HH:MM', {'translation': {
                                        H: {pattern: /[0-9]/},
                                        M: {pattern: /[0-9]/}
                                      }
                                });


    function actualizar_posiciones(){
        var mostrar_ruta_fil = map_api.filtro.tipo_filtro!=2 && map_api.filtro.tipo_filtro!=3;



        var mostrar_vehi = ($('#ckPatrullero').is(':checked') || $('#ckMotorizado').is(':checked') || $('#ckPatpie').is(':checked')) &&
                           // $('#ckMotorizado').is(':checked') && $('#ckPatpie').is(':checked') && 
                            (
                                (map_api.filtro.placa!="") || 
                                (map_api.filtro.placa=="") || 
                                (map_api.filtro.placa!="")
                            )  

                            &&
                            (
                                (map_api.filtro.etiqueta!="") || 
                                (map_api.filtro.etiqueta=="") || 
                                (map_api.filtro.etiqueta!="")
                            ) 

                            ;
        if(mostrar_ruta_fil && mostrar_vehi){
            if(fecha_actual == map_api.filtro.fecha){
                if(map_api.ajaxPostPatrullaje==null){
                    map_api.descargar_patrullaje();
                }
            }
        }

    }


    function obtener_incidencias(){
        incidencia_api.cargar(map_api.get_dependencia(), 0, true,function(resp_data){
        }); 
    }

    function obtener_alarmas(){
        alarma_api.cargar(map_api.get_dependencia(),0, true,function(resp_data){
        });
    }

    setInterval(function(){
        actualizar_posiciones();
        //obtener_incidencias();
        //obtener_alarmas();
    }, 15000);

    setInterval(function(){
        //obtener_incidencias();
        obtener_alarmas();
    }, 3000);

    $('body').on('click','.sipcop-closable, .sipcop-streetview-close',function(){
        $($('.sipcop-ui-modal-item').parent()).fadeOut('fast');
        $('.sipcop-ui-modal-item').hide();
    });
    $('#ckPatrullero').change();
    $('#ckComisaria').change();
    $('#ckPatpie').change();
    $('#ckMotorizado').change();
    $('#ckPuestoFijo').change();
    $('#ckBarrioSeguro').change();


}
</script>