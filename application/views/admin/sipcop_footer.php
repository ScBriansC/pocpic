<div id="changePassword" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <h4 id="basicModalLabel" class="modal-title">CAMBIO DE CONTRASEÑA &nbsp;&nbsp;&nbsp;
        </h4>
      </div>
        <div class="modal-body">
            <div id="dbcontra">
              <div class="form-group">
                <label for="passActual">CONTRASEÑA ACTUAL:</label>
                <label id="errorPass" style="color:red;"></label>
                <input type="password" class="form-control" id="passActual" required>
              </div>
              <div class="form-group">
                <label for="passNuevo">NUEVA CONTRASEÑA:</label>
                <label id="errorPassN" style="color:red;"></label>
                <input type="password" class="form-control" id="passNuevo" required >
              </div>
              <div class="form-group">
                <label for="passNuevoAgain">REPITA CONTRASEÑA:</label>
                <label id="errorPassNN" style="color:red;"></label>
                <input type="password" class="form-control" id="passNuevoAgain" required>
              </div>
              <br>
              <label id="nosame" style="color:red;"></label>
              <br>
              <center><a href="javascript:;" onclick="changePassword()" type="button" class="btn btn-success">CAMBIAR</a></center>
          </div>
          <div id="dbvalido" style="display: none;">
                 <div class="form-group">
                    <label for="lbtoken">INGRESE CÓDIGO</label>
                    <label id="errortoken" style="color:red;"></label>
                    <input type="text" class="form-control" id="lbtoken" required>
                  </div>
                  <center><a href="javascript:;" onclick="validartoken()" type="button" class="btn btn-success">CAMBIAR</a></center>
          </div>

      </div>
      


      <div class="clear"></div>
    </div>
  </div>
</div>

<div id="modalAyuda" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-sm">
    <div class="modal-content">
      <div class="modal-header" style="background-color: #57c8f1">
        <button type="button" data-dismiss="modal" aria-label="Cerrar" class="close"><span aria-hidden="true">×</span></button>
        <center><span style="font-size: medium;color: white; font-weight: bold;">SOPORTE</p></center>
      </div>
      <div class="modal-body">
        <div class="container-fluid">
            <div class="row">
            <div class="prf-contacts">
                  <h2> <span><i class="fa fa-phone"></i></span> SIPCOP - MININTER</h2>
                  <div class="location-info">
                      <p>Correo  : soportesipcop@mininter.gob.pe <br>
                        Celular    : 980 122 819</p>
                  </div>
                  <h2> <span><i class="fa fa-phone"></i></span> DIRTIC - PNP</h2>
                  <div class="location-info">
                      <p>Teléfono  : 225 3655 <br>
                          Celular    : 980 122 293</p>
                  </div>
              </div>
            </div>
        </div>

        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>

</section>
</section>
<!--main content end-->

</section>
<!-- Placed js at the end of the document so the pages load faster -->
<!--Core js-->

<script src="assets/js/date.js"></script>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery.mask.min.js"></script>
<script src="assets/js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
<script src="assets/bs3/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/morris-chart/morris.js"></script>
<script src="assets/js/sweetalert/sweet-alert.js"></script>
<script src="assets/js/morris-chart/raphael-min.js"></script>
<script src="assets/js/jquery.customSelect.min.js" ></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/js/gritter/js/jquery.gritter.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datepicker/js/bootstrap-datepicker.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-timepicker/js/bootstrap-timepicker.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js"></script>
<script type="text/javascript" src="assets/js/select2/select2.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.js"></script>
<script type="text/javascript" src="assets/js/bootstrap-inputmask/bootstrap-inputmask.min.js"></script>
<script type="text/javascript" src="assets/js/bootbox.js"></script>
<script type="text/javascript" src="assets/js/daterangepicker/js/moment.js"></script>
<script type="text/javascript" src="assets/js/daterangepicker/js/daterangepicker.js"></script>

<!-- <script src="https://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.0/additional-methods.js"></script> -->

<!--common script init for all pages-->

<script src="assets/sipcop/js/SipcopJS.js?<?php echo rand(1,1000); ?>"></script>
<script src="assets/js/scripts.js?<?php echo rand(1,1000); ?>"></script>
<script>
SipcopJS.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
</script>
<!--script for this page-->
<?php
if(isset($jslib)){
	foreach ($jslib as $kjs=>$js) {
		echo '<script src="'.$js.'"></script>';
	}
}
?>
<?php
if(isset($jsInit)){
	echo "<script>$(document).ready(function(){".$jsInit."});</script>";
}
?>
<script>
$(function(){
	try{
		
		if(typeof preCarga != 'undefined'){
			preCarga();
		}
	}catch(err){SipcopJS.log(err);}
  var ses_tiempo = <?php echo $ses_duracion['TIEMPO']; ?>;
  
  setInterval(function(){
    var hours   = Math.floor(ses_tiempo / 3600);
    var minutes = Math.floor((ses_tiempo - (hours * 3600)) / 60);
    var seconds = ses_tiempo - (hours * 3600) - (minutes * 60);

    if (hours   < 10) {hours   = "0"+hours;}
    if (minutes < 10) {minutes = "0"+minutes;}
    if (seconds < 10) {seconds = "0"+seconds;}
    $('#txtSesTiempo').html(hours+':'+minutes+':'+seconds);
    if(ses_tiempo>0){
      ses_tiempo--;
    }else{
      document.location.reload()
    }
  },1000);
});

function modalAyuda(){
  $('#modalAyuda').modal('show');
}
</script>


</body>
</html>