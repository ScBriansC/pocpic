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
<script type="text/javascript" src="assets/sipcop/js/map_jurisdiccion_ruta.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_barrio.js?<?php echo rand(1,1000); ?>"></script>    
<script type="text/javascript" src="assets/sipcop/js/map_incidencia.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_camara.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_alarma.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/IncidenciaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/CamaraMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/AlarmaMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/geocerca.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/comision.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/app_hojaruta.js?<?php echo rand(1,1000); ?>"></script>

<div class="app-bg-loader"><img src="assets/sipcop/img/loader.gif" width="320"></div>
<div id="cnv_map" class="map-full" style="overflow:hidden;"></div>
<div class="viewpanel">	
	<div class="viewpanel_menu">
		<div class="viewpanel_menur">
			CREAR
		</div>
		<div class="viewpanel_menul">
			LISTAR
		</div>
	</div>
	<div class="viewpanel_header" id="header_registrar">
		<div class="container-fluid">
			<div class="form-group col-sm-12">
               <label class="control-label col-sm-12" for="txtFormVehiculo">VEHICULO: </label>
               <div class="col-sm-12">
                    <select name="txtFormVehiculo" id="txtFormVehiculo" class="form-control">
                    	<option value="0">-- Seleccione --</option>
                    	<option value="1">PATRULLERO</option>
                    	<option value="2">MOTORIZADO</option>
                    </select>
                </div>	
             </div>
             <div class="form-group col-sm-12">
             	<label class="control-label col-sm-12" for="txtFormPlaca">PLACA: </label>
                <div class="col-sm-12">
                    <select name="txtFormPlaca" id="txtFormPlaca" class="form-control">
                    	<option value="0">-- Seleccione --</option>
                    </select>
                </div>	
             </div>
             <div class="form-group col-sm-12">
               <label class="control-label col-sm-12" for="txtFormOperador">OPERADOR: </label>
               <div class="col-sm-12">
                    <select name="txtFormOperador" id="txtFormOperador" class="form-control">
                    	<option value="0">-- Seleccione --</option>
                    	<option value="1">CUSTODIO FALLA, JOSÉ OMAR</option>
                    </select>
                </div>	   
             </div>
             <div class="form-group col-sm-12">
             	<label class="control-label col-sm-12" for="txtFormChofer">CHOFER: </label>
                <div class="col-sm-12">
                    <select name="txtFormChofer" id="txtFormChofer" class="form-control">
                    	<option value="0">-- Seleccione --</option>
                    	<option value="1">CORNEJO FOX, NESTOR MARTIN</option>
                    </select>
                </div>	  
             </div>

             <div class="form-group col-sm-12">
             	<label class="control-label col-sm-12" for="txtFormFecha">FECHA: </label>
                <div class="col-sm-12">
                    <input type="text" name="datefilter" class="form-control" id="txtFormFecha" />
                </div>
             </div>
             <input type="checkbox" id="ckJurisdiccion" value="1" checked style="display: none" />
		</div>
	</div>
	<div class="viewpanel_header" id="header_listar" style="display:none;">
		<div class="container-fluid">
             <div class="form-group col-sm-12">
             	<label class="control-label col-sm-12" for="txtFormFecha">FECHA: </label>
                <div class="col-sm-12">
                    <input type="text" name="datelistar" class="form-control" id="txtFromFechalistar" />
                </div>
             </div>
		</div>
	</div>
	<div class="viewpanel_route" id="route_registrar">
		<div class="container-fluid">
			<div class="row">
				<center><label class="control-label col-sm-12">OPCIONES DE RUTA:</label></center>
			</div>
			<center>
				<button class="btn btn-warning" id="generarRuta">
	  			    GENERAR RUTA
	  			</button>
	  			<button class="btn btn-danger" id="deleteRuta">
	  			    	X
	  			</button>
			</center>
		</div>
	</div>
	<div class="viewpanel_route" id="route_listar" style="display:none;">
		<div class="container-fluid">
			<center><div class="form-group col-sm-12">
             	<center><button class="btn btn-primary" id="btnBuscar">BUSCAR</button></center>
             </div></center>
		</div>
	</div>

	<div class="viewpanel_content" id="content_registrar">
		<table class="table table-responsive table-bordered" id="myTable">
			 <thead>
		      <tr>
		        <th style="text-align: center">#</th>
		        <th style="text-align: center">Hora</th>
		        <th style="text-align: center">Zona/Lugar</th>
		        <th style="text-align: center">Motivo</th>
		      </tr>
		    </thead>
		    <tbody id="cuerpo">
		    </tbody>
		</table>
	</div>

	<div class="viewpanel_content" id="content_listar" style="display:none;">
		<table class="table table-responsive table-bordered" id="tbl-hojaruta">
			 <thead>
		      <tr>
		        <th style="text-align: center">Placa</th>
		        <th style="text-align: center">Fecha</th>
		        <th style="text-align: center">Chofer</th>
		        <th style="text-align: center">Operador</th>
		        <th style="text-align: center">Opciones</th>
		      </tr>
		    </thead>
		    <tbody id="cuerpo">
		    </tbody>
		</table>
	</div>


</div>

<div class="viewpanel_footer">
	<center><a class="btn btn-sucess" style="color:white;" id="guardarRuta"><b>GUARDAR</b></a></center>
</div>

<div class="logos"><img src="assets/img/logos.png"></div>

<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog modal-sm">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Detalles de Ruta</h4>
      </div>
      <div class="modal-body">
       		<div class="form-group">
			    <label for="txthora">Hora:</label>
			    <input type="text" class="form-control" id="txthora">
			</div>
			<select class="form-control" id="txtmotivo">
				<option value="0">--SELECCIONE--</option>
				<option value="1">COMISARIA</option>
				<option value="2">PATRULLAJE</option>
				<option value="3">EST.TACTICO</option>
			</select>

      </div>
      <div class="modal-footer">
        <button type="button" id="guardarDatos" class="btn btn-success">Guardar</button>
      </div>
    </div>

  </div>
</div>

<script>
var fecha_actual;

preCarga = function(){SipcopJS.logEnabled = true;
	fecha_actual = (new Date()).toString('dd/MM/yyyy');
    $('#txtFecha').val(fecha_actual);

     $('input[name="datefilter"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }); 


    $('input[name="datelistar"]').daterangepicker({
        singleDatePicker: true,
        showDropdowns: true,
        locale: {
            format: 'DD/MM/YYYY'
        }
    }); 

	var htmlautomatic = 
					  '<div class="form-group col-sm-12">'+
			          '<label class="control-label col-sm-3" for="txtFormGenRuta" style="margin-top: 3px">INICIO:</label>'+
			          '<div class="col-sm-9">'+
			          '<input type="text"  id="inicioRuta" class="form-control" /></div></div>'+
			          '<div class="form-group col-sm-12">'+
			          '<label class="control-label col-sm-3" for="txtFormGenRuta" style="margin-top: 3px">FIN:</label>'+
			          '<div class="col-sm-9"><input type="text"  class="form-control" id="finRuta" ></div>'+
			          '</div>'
			          ;

	$('#generarRuta').click(function(){
			hojaruta_api.generarRuta();
		});

	$('#guardarRuta').click(function(){
		hojaruta_api.guardarRuta(hojaruta_api.usu_jurisdiccion[0]);
	});
	$('#guardarDatos').click(function(){
		hojaruta_api.guardarDatos();
	});

	$('.viewpanel_menur').click(function(){
		$('#header_listar').hide();
		$('#route_listar').hide();
		$('#content_listar').hide();

		$('#header_registrar').show();
		$('#route_registrar').show();
		$('#content_registrar').show();
		$('.viewpanel_footer').show();
	});

	$('.viewpanel_menul').click(function(){
		$('#header_registrar').hide();
		$('#route_registrar').hide();
		$('#content_registrar').hide();
		$('.viewpanel_footer').hide();
		$('#header_listar').show();
		$('#route_listar').show();
		$('#content_listar').show();
	});


    $('#btnBuscar').click(function(){
    	// $('#tbl-hojaruta').append('');s
    	$('#tbl-hojaruta tbody').html('');
    	hojaruta_api.buscarHojaRuta();
    });





	$('#txtFormVehiculo').change(function(){
        var modeloVehi = $('#txtFormVehiculo').val();
		var institucion = hojaruta_api.usu_jurisdiccion[0];
       	hojaruta_api.get_vehiculo(modeloVehi,institucion);
        // SipcopJS.cargarDependencia('#txtDivter',2,$('#txtRegpol').val(),'',null);
    }); 

	<?php foreach ($usu_jurisdiccion as $jurisd) { ?>
        hojaruta_api.usu_jurisdiccion.push('<?php echo $jurisd['IDINSTITUCION']; ?>');
    <?php } ?>

    function initJS(){
    	hojaruta_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        jurisdiccion_ruta_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>',function(){
        	 jurisdiccion_ruta_api.centrarJurisdiccion(hojaruta_api.usu_jurisdiccion[0]);
      		 hojaruta_api.clickJurisdiccion();   
        });
    }

    initJS();

    $(window).resize(function(){
        $('#cnv_map').css('width','100%');
        $('#cnv_map').height($(window).height()-$('header.header').height() - 6);
        $('.viewpanel').height($(window).height()-$('.viewpanel_footer').height()-51);
    });

    $(window).resize();
}


</script>