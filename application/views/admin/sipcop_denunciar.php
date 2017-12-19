<script src="https://maps.googleapis.com/maps/api/js?libraries=places&key=AIzaSyAQGNCgXhTrE7TJROgFSOftaosTVUtqXY8"></script>

<div style="text-align:center;" id="modalInei" class="ineiModal"></div>
		
<table  style="margin: 0 auto;" width = 50%>
	<tr>
		<td colspan=2><input type="text" id="direccion" name="direccion" placeholder="Dirección" style="width:90%;"></td> <br>
		<td rowspan=2><button id="ineiGeoButton" style="height:100%; width:100%;">Geolocalizar</button></td>
	</tr>
	<tr>
		<td><input type="text" id="latitud" name="latitud" placeholder="Latitud" style="width:85%;"></td>
		<td><input type="text" id="longitud" name="longitud" placeholder="Longitud" style="width:80%;"></td>
	</tr>
	<tr>			
		<td colspan=2 align="center"><button id="enviar" style="width:50%;">Registrar</button></td>
	</tr>
</table>

<script>

preCarga = function(){
	initMap();

	//Añadir evento al botón que abre el módulo de geo referenciacion.
	document.getElementById("ineiGeoButton").onclick = function() {
		IneiGeoref.showMap();
	};

	document.getElementById("enviar").onclick = function() {
		var denuncia = {};
		denuncia.direccion = $('#direccion').val();
		denuncia.latitud = $('#latitud').val();
		denuncia.longitud = $('#longitud').val();
		
		
		SipcopJS.post('admin/denuncia/registrar',denuncia, function (data){
			if(data.status == 'success'){
				SipcopJS.msj.success('Éxito', data.msj);
			}else{
				SipcopJS.msj.error('Error', data.msj);
			}
		});
	};


}
				
//Esta función corre después que termina de cargarse el API de GoogleMaps.
function initMap() {

	//Indicar a qué campos se enviarán los valores de dirección, lat y lng respectivamente.
	IneiGeoref.setInputs('direccion', 'latitud', 'longitud');			
	
	//Construir la venta emergente. Indicar elemento contenedor.
	IneiGeoref.setModal('modalInei');			
	
	//Construir el mapa 
	IneiGeoref.createGeorefMap();
	
};


	
				
	</script>