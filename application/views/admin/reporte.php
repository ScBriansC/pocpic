
<script type="text/javascript" src="assets/js/date.js"></script>
<script type="text/javascript" src="assets/js/reportes.js"></script>
<script type="text/javascript" src="assets/components/animate/dynamics.min.js"></script>
<style>
.lblmarker {
    color: #000;
    background-color: white;
    font-family: "Lucida Grande", "Arial", sans-serif;
    font-size: 10px;
    text-align: center; 
    border: 1px solid #CCC;
    white-space: nowrap;
    padding: 2px;
    border-radius: 6px;
    width: 90px;
    z-index: 1050!important;
}
.lblmarker-hide{
    display: none!important;
}
.gmnoprint {
    z-index: 5040!important;
}
a.fil-searching {
    background: #368cb5!important;
    border: 1px solid #29843d!important;
}
</style>
<div class="container-fluid">
    <div class="panel-tab" >
                <div class="panel-tab-tabs">
                    <a href="javascript:;" class="tab-general active">Reporte de Sesiones</a>
                    <a href="javascript:;" class="tab-comisaria">Reporte de Inventario Radios </a>
                    <a href="javascript:;" class="tab-patrullero">Reporte de Transmiciones</a>
                    <a href="javascript:;" class="tab-motorizado">Reporte Distancía Recorrida</a>
                </div>
                <div class="panel-tab-group">
                    <div class="panel-tab-content active">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="item">
                            <label>Fecha:</label>
                            <div class="field"><input type="text" name="txtFecha" data-date-end-date="0d" id="txtFecha" class="input-fil" style="width: 85%" readonly=""><a id="btnFecha" href="javascript:;" class="btn-picker"><img src="assets/img/ic-date.png" height="20"></a></div>
                        </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Ini.:</label>
                                <div class="field"><input type="text" name="txtHoraIni" id="txtHoraIni" class="input-fil" style="width: 85%"><a id="btnHoraIni" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Fin.:</label>
                                <div class="field"><input type="text" name="txtHoraFin" id="txtHoraFin" class="input-fil" style="width: 85%"><a id="btnHoraFin" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                        </div><hr>
                        <div class="row">
                          <div class="col-md-3">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Departam.</label>
                                <div class="field">
                                    <select tname="txtDepartamento" id="txtDepartamento" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                        <?php foreach ($ubigeo_depa as $u_depa) { ?>
                                        <option value="<?php echo $u_depa['IDUBIGEO']; ?>"><?php echo $u_depa['DEPARTAMENTO']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Provincia:</label>
                                <div class="field">
                                    <select tname="txtProvincia" id="txtProvincia" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Distrito:</label>
                                <div class="field">
                                    <select tname="txtDistrito" id="txtDistrito" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Comisaria:</label>
                                <div class="field">
                                    <select tname="txtComisaria" id="txtComisaria" class="select-fil" style="width:90%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                        </div><hr>
                        <div class="row">
                          <div class="col-md-12">
                            <table id="example" class="display" cellspacing="0" width="100%">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start date</th>
                                        <th>Salary</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Tiger Nixon</td>
                                        <td>System Architect</td>
                                        <td>Edinburgh</td>
                                        <td>61</td>
                                        <td>2011/04/25</td>
                                        <td>$320,800</td>
                                    </tr>
                                    <tr>
                                        <td>Garrett Winters</td>
                                        <td>Accountant</td>
                                        <td>Tokyo</td>
                                        <td>63</td>
                                        <td>2011/07/25</td>
                                        <td>$170,750</td>
                                    </tr>
                                    <tr>
                                        <td>Ashton Cox</td>
                                        <td>Junior Technical Author</td>
                                        <td>San Francisco</td>
                                        <td>66</td>
                                        <td>2009/01/12</td>
                                        <td>$86,000</td>
                                    </tr>
                                </tbody>
                            </table>
                          </div>
                        </div>
                    </div>
                    <div class="panel-tab-content">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="item">
                            <label>Fecha:</label>
                            <div class="field"><input type="text" name="txtFecha1" data-date-end-date="0d" id="txtFecha1" class="input-fil" style="width: 85%" readonly=""><a id="btnFecha" href="javascript:;" class="btn-picker"><img src="assets/img/ic-date.png" height="20"></a></div>
                        </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Ini.:</label>
                                <div class="field"><input type="text" name="txtHoraIni1" id="txtHoraIni1" class="input-fil" style="width: 85%"><a id="btnHoraIni" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Fin.:</label>
                                <div class="field"><input type="text" name="txtHoraFin1" id="txtHoraFin1" class="input-fil" style="width: 85%"><a id="btnHoraFin" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Departam.</label>
                                <div class="field">
                                    <select tname="txtDepartamento" id="txtDepartamento" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                        <?php foreach ($ubigeo_depa as $u_depa) { ?>
                                        <option value="<?php echo $u_depa['IDUBIGEO']; ?>"><?php echo $u_depa['DEPARTAMENTO']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Provincia:</label>
                                <div class="field">
                                    <select tname="txtProvincia" id="txtProvincia" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Distrito:</label>
                                <div class="field">
                                    <select tname="txtDistrito" id="txtDistrito" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">.col-md-4</div>
                        </div>
                    </div>
                    <div class="panel-tab-content">
                        <div class="row">
                          <div class="col-md-4">
                            <div class="item">
                            <label>Fecha:</label>
                            <div class="field"><input type="text" name="txtFecha2" data-date-end-date="0d" id="txtFecha2" class="input-fil" style="width: 85%" readonly=""><a id="btnFecha" href="javascript:;" class="btn-picker"><img src="assets/img/ic-date.png" height="20"></a></div>
                        </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Ini.:</label>
                                <div class="field"><input type="text" name="txtHoraIni2" id="txtHoraIni2" class="input-fil" style="width: 85%"><a id="btnHoraIni" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Fin.:</label>
                                <div class="field"><input type="text" name="txtHoraFin2" id="txtHoraFin2" class="input-fil" style="width: 85%"><a id="btnHoraFin" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Departam.</label>
                                <div class="field">
                                    <select tname="txtDepartamento" id="txtDepartamento" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                        <?php foreach ($ubigeo_depa as $u_depa) { ?>
                                        <option value="<?php echo $u_depa['IDUBIGEO']; ?>"><?php echo $u_depa['DEPARTAMENTO']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Provincia:</label>
                                <div class="field">
                                    <select tname="txtProvincia" id="txtProvincia" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Distrito:</label>
                                <div class="field">
                                    <select tname="txtDistrito" id="txtDistrito" class="select-fil">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                        </div>
                        <div class="row">
                          <div class="col-md-12">.col-md-4</div>
                        </div>
                    </div>
                    <div class="panel-tab-content ">
                        <div class="row">
                          <div class="col-md-4">
                                <div class="item">
                                <label>Fecha:</label>
                                <div class="field"><input type="text" name="txtFecha3" data-date-end-date="0d" id="txtFecha3" class="input-fil" style="width: 85%" readonly=""><a id="btnFecha" href="javascript:;" class="btn-picker"><img src="assets/img/ic-date.png" height="20"></a></div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Ini.:</label>
                                <div class="field"><input type="text" name="txtHoraIni3" id="txtHoraIni3" class="input-fil" style="width: 85%"><a id="btnHoraIni" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                          <div class="col-md-4">
                            <div class="item">
                                <label>Hora Fin.:</label>
                                <div class="field"><input type="text" name="txtHoraFin3" id="txtHoraFin3" class="input-fil" style="width: 85%"><a id="btnHoraFin" href="javascript:;" class="btn-picker"><img src="assets/img/ic-time.png" height="20"></a></div>
                            </div>
                          </div>
                        </div><hr>
                        <div class="row">
                          <div class="col-md-2">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Departam.</label>
                                <div class="field">
                                    <select tname="txtDepartamento3" id="txtDepartamento3" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                        <?php foreach ($ubigeo_depa as $u_depa) { ?>
                                        <option value="<?php echo $u_depa['IDUBIGEO']; ?>"><?php echo $u_depa['DEPARTAMENTO']; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Provincia:</label>
                                <div class="field">
                                    <select tname="txtProvincia3" id="txtProvincia3" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-2">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Distrito:</label>
                                <div class="field">
                                    <select tname="txtDistrito3" id="txtDistrito3" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <div class="item" style="<?php echo (($rolcomisario == 3)?'display:none':''); ?>">
                                <label>Comisaria:</label>
                                <div class="field">
                                    <select tname="txtComisaria3" id="txtComisaria3" class="select-fil" style="width:100%">
                                        <option value="0">-- Todos --</option>
                                    </select>
                                </div>
                            </div>
                          </div>
                          <div class="col-md-3">
                            <a id="generar3" class="btn btn-primary" href="javascript:;" style="margin-top: 20px;">GENERAR REPORTE</a> 
                          </div>
                        </div><hr>
                        <div class="row">
                          <div class="col-md-12">
                           <div class="adv-table">
                                <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">                   
                                </table>
                            </div>
                          </div>
                        </div><hr>
                        <div class="row">
                            <div class="col-md-12">
                                <div id="Grafico"></div>
                            </div>
                        </div>
                    </div>          
                </div>
    </div>
</div>
<script>
var fecha_actual = (new Date()).toString('dd/MM/yyyy');

js_task = function(){
  
    // $(document).ready(function() {
    //     $('#example').DataTable();
    //     $('#example3').DataTable();
    // } );

    reporte_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');

    $('#generar3').click(function(){
        if($.trim($('#txtHoraIni3').val()).length < 4 || $.trim($('#txtHoraFin3').val()).length < 4) {
            $.gritter.add({
                position: 'bottom-right',
                title: 'Mensaje',
                text: 'Seleccione una hora válida',
                class_name: 'gritter-error'
            });
            return false;
        }
        var hi = $('#txtHoraIni3').val();
        var hii = hi.replace(":","");
        var hf = $('#txtHoraFin3').val();
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
                reporte_api.add_TaskLoader();
                reporte_api.cargar(reporte_api.get_ubigeo(),function(resp_data){
                   $("#example3").DataTable();
                    reporte_api.end_TaskLoader();

                });  
            }
        }
        
    });

    $('#txtFecha').val((new Date()).toString('dd/MM/yyyy'));
    $('#txtFecha1').val((new Date()).toString('dd/MM/yyyy'));
    $('#txtFecha2').val((new Date()).toString('dd/MM/yyyy'));
    $('#txtFecha3').val((new Date()).toString('dd/MM/yyyy'));

    //$('#txtHoraFin').val((new Date()).toString('H:mm:ss'));
    //$('#txtHoraIni').val(((new Date()).add({ hours: -1 })).toString('H:mm:ss'));
    $('#txtHoraIni').val('00:00');
    $('#txtHoraIni1').val('00:00');
    $('#txtHoraIni2').val('00:00');
    $('#txtHoraIni3').val('00:00');

    $('#txtHoraFin').val('23:59');
    $('#txtHoraFin1').val('23:59');
    $('#txtHoraFin2').val('23:59');
    $('#txtHoraFin3').val('23:59');

    $('#txtComisariaDependencia').val(0);

      var vehi = $('#ckVehiculos').is(':checked');
      var comi = $('#ckComisaria').is(':checked');
      var radio = $('#ckRadio').is(':checked');
      var mapa = $('#ckMapaDenuncia').is(':checked');

    $('#btnFecha').click(function(ev){
        ev.preventDefault();
        $('#txtFecha').datepicker('show');
    });
     $('#btnFecha1').click(function(ev){
        ev.preventDefault();
        $('#txtFecha').datepicker('show');
    });
      $('#btnFecha2').click(function(ev){
        ev.preventDefault();
        $('#txtFecha').datepicker('show');
    });
       $('#btnFecha3').click(function(ev){
        ev.preventDefault();
        $('#txtFecha').datepicker('show');
    });

    $('#btnHoraFin').click(function(ev){
        ev.preventDefault();
        $('#txtHoraFin').timepicker('show');
    });
    $('#btnHoraFin1').click(function(ev){
        ev.preventDefault();
        $('#txtHoraFin').timepicker('show');
    });
    $('#btnHoraFin2').click(function(ev){
        ev.preventDefault();
        $('#txtHoraFin').timepicker('show');
    });
    $('#btnHoraFin3').click(function(ev){
        ev.preventDefault();
        $('#txtHoraFin').timepicker('show');
    });


    $('#btnHoraIni').click(function(ev){
        ev.preventDefault();
        $('#txtHoraIni').timepicker('show');
    });
    $('#btnHoraIni1').click(function(ev){
        ev.preventDefault();
        $('#txtHoraIni').timepicker('show');
    });
    $('#btnHoraIni2').click(function(ev){
        ev.preventDefault();
        $('#txtHoraIni').timepicker('show');
    });
    $('#btnHoraIni3').click(function(ev){
        ev.preventDefault();
        $('#txtHoraIni').timepicker('show');
    });

    $('#txtHoraIni,#txtHoraIni1,#txtHoraIni2,#txtHoraIni3, #txtHoraFin,#txtHoraFin1,#txtHoraFin2,#txtHoraFin3').timepicker({
        'showDuration': true,
        'timeFormat': 'H:i'
    });

    $('#txtFecha,#txtFecha1,#txtFecha2,#txtFecha3').datepicker({
        'format': 'dd/mm/yyyy',
        'autoclose': true
    });

    $('#txtDepartamento').change(function(){
        $('#txtProvincia').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        $('#txtDistrito').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:1, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.provincias, function(idx, obj){
                    $('#txtProvincia').append('<option value="'+obj.IDUBIGEO+'">'+obj.PROVINCIA+'</option>');
                });
            });
        }
    });

    $('#txtProvincia').change(function(){
        $('#txtDistrito').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:2, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.distritos, function(idx, obj){
                    $('#txtDistrito').append('<option value="'+obj.IDUBIGEO+'">'+obj.DISTRITO+'</option>');
                });
            });
        }
    });

    $('#txtDistrito').change(function(){
        $('#txtComisaria').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:3, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.comisarias, function(idx, obj){
                    $('#txtComisaria').append('<option value="'+obj.IDCOMISARIA+'">'+obj.NOMBRE+'</option>');
                });
            });
        }
    });
    $('#txtDepartamento3').change(function(){
        $('#txtProvincia3').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        $('#txtDistrito3').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:1, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.provincias, function(idx, obj){
                    $('#txtProvincia3').append('<option value="'+obj.IDUBIGEO+'">'+obj.PROVINCIA+'</option>');
                });
            });
        }
    });

    $('#txtProvincia3').change(function(){
        $('#txtDistrito3').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:2, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.distritos, function(idx, obj){
                    $('#txtDistrito3').append('<option value="'+obj.IDUBIGEO+'">'+obj.DISTRITO+'</option>');
                });
            });
        }
    });
    $('#txtDistrito3').change(function(){
        $('#txtComisaria3').html('<option value="0">-- Todos --</option>').val('0').trigger('change');
        if($(this).val()!='0'){
            $.post('admin/reporte/json_ubigeo',{tipo:3, ubigeo:$(this).val(), <?php echo $this->security->get_csrf_token_name(); ?>:'<?php echo $this->security->get_csrf_hash(); ?>'},function(data){
                $.each(data.comisarias, function(idx, obj){
                    $('#txtComisaria3').append('<option value="'+obj.IDCOMISARIA+'">'+obj.NOMBRE+'</option>');
                });
            });
        }
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

    $('.panel-tab-tabs a').click(function(){
        $('.panel-tab-tabs a, .panel-tab-content').removeClass('active');
        $('.panel-tab-tabs a, .panel-tab-result').removeClass('active');
        $(this).addClass('active');
        if($('.panel-tab-tabs a.tab-comisaria').hasClass('active')){
            $($('.panel-tab-content')[1]).addClass('active');
            $($('.panel-tab-result')[1]).addClass('active');
            // $('.btn-exportar').hide();
        }else if($('.panel-tab-tabs a.tab-patrullero').hasClass('active')){
            $($('.panel-tab-content')[2]).addClass('active');
            $($('.panel-tab-result')[2]).addClass('active');
            // $('.btn-exportar').hide();
        }else if($('.panel-tab-tabs a.tab-motorizado').hasClass('active')){
            $($('.panel-tab-content')[3]).addClass('active');
            $($('.panel-tab-result')[3]).addClass('active');
            // $('.btn-exportar').hide();
        }else{
            $($('.panel-tab-content')[0]).addClass('active');
            $($('.panel-tab-result')[0]).addClass('active');
            // $('.btn-exportar').show();
        }
        $('.select-fil').select2();
    });

    $('.select-fil').select2();


    $('#txtHoraIni, #txtHoraFin').mask('HH:MM', {'translation': {
                                        H: {pattern: /[0-9]/},
                                        M: {pattern: /[0-9]/}
                                      }
                                });






}
</script>