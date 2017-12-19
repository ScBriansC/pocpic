IneiGeoref = window.IneiGeoref || {};

IneiGeoref = function () {

	var gmap, marker, geocoder, direccion, modal, label, addressInput, latInput, lngInput;
		
	setInputs = function (address, lat, lng){
		addressInput =  document.getElementById(address);
		latInput =  document.getElementById(lat);
		lngInput =  document.getElementById(lng);
	};
	
	setModal = function (element){
		
		modal =  document.getElementById(element);
		
		modal.innerHTML = '<div class="ineiModal-content"><div class="ineiModal-header"><span class="ineiCloseIcon">&times;</span><h2>Módulo de Geo Referenciación</h2></div>'
		+ '<div class="ineiModal-body"><div id="ineiGeoDialog"><div id="ineiGeoMap"></div><input id="ineiGeoSearch" class="ineiGeoControls" type="text" placeholder="Buscar..."></input></div></div>'
		+ '<div class="ineiModal-footer">'
		+ '<table width=100%" style="table-layout: fixed;">'
		+ '<colgroup><col span="1" style="width: 70%;"><col span="1" style="width: 15%;"><col span="1" style="width: 15%;"></colgroup>'		
		+ '<tr><td id="ineiGeoLabel" style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><h4>DIRECCIÓN:<h4></td>'
		+ '<td><button id="ineiGeoLoad" class="ineiButton" style="width:100%;" >Aceptar</button></td></td>'
		+ '<td><button id="ineiGeoClose" class="ineiButton" style="width:100%;" >Cancelar</button></td></td></tr><table></div></div>';
		
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		};
		
		label = document.getElementById('ineiGeoLabel');
		
		var span = document.getElementsByClassName("ineiCloseIcon")[0];
		span.onclick = function() { modal.style.display = "none"; };
		
		var close = document.getElementById("ineiGeoClose");
		close.onclick = function() { modal.style.display = "none"; };
						
		var load = document.getElementById("ineiGeoLoad");
		load.onclick = function() { returnGeoref(); };
		
	};
	
	createGeorefMap = function () {	
			
		//Map
		gmap = new google.maps.Map(document.getElementById('ineiGeoMap'), {
			center: {lat: -12.0666612376922, lng: -77.04518861520364},
			zoom: 16,
			transitionEffect: 'resize'
		});
		
		//Geocoder
		geocoder  = new google.maps.Geocoder();
		
		//Initial Marker			
		marker = new google.maps.Marker({
			position: new google.maps.LatLng(-12.0666612376922,-77.04518861520364),
			map: gmap,
			draggable: true
		});
		geocodePosition(marker.getPosition());
		
		//Events
		google.maps.event.addListener(gmap, 'click', function(event) {
			updateMarker(event.latLng);
		});	
		google.maps.event.addListener(marker, 'dragend', function(event){
			geocodePosition(event.latLng);
		});
		
		var options = {
			componentRestrictions: {country: "pe"}
		};
				
		// Create the search box and link it to the UI element.	
		var input = document.getElementById('ineiGeoSearch');
		var searchBox = new google.maps.places.Autocomplete(input);
		searchBox.setBounds(gmap.getBounds());
		
		gmap.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
		
		var address = document.getElementById('ineiGeoSearch');
		
		gmap.addListener('bounds_changed', function() {
			searchBox.setBounds(gmap.getBounds());
		});
		
		gmap.addListener('bounds_changed', function() {
			searchBox.setBounds(gmap.getBounds());
		});
		
		searchBox.addListener('place_changed', function() {                    
			var place = searchBox.getPlace();        
			var bounds = new google.maps.LatLngBounds();        
			updateMarker(place.geometry.location);		
			if(place.geometry){
				if (place.geometry.viewport) {
					bounds.union(place.geometry.viewport);
				} else {
					bounds.extend(place.geometry.location);
				}
				gmap.fitBounds(bounds);
			}               
		});
		
		//Current Geolocation	
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				var pos = {
					lat: position.coords.latitude,
					lng: position.coords.longitude
				};
				updateMarker(pos);
				//map.setCenter(pos);
			}, function() {
				//handleLocationError(true, infoWindow, map.getCenter());
			});
		} else {
			//handleLocationError(false, infoWindow, map.getCenter());
		}
		
	};	
	
	updateMarker = function (location) {
		marker.setPosition(location);
		geocodePosition(marker.getPosition());
	};
	  
	geocodePosition = function (pos) {
		geocoder.geocode({latLng: pos}, function(responses) {
			if (responses && responses.length > 0) {
				direccion = responses[0].formatted_address;	
				label.innerHTML = '<h4>DIRECCIÓN: '+direccion+'</h4>';
			} else {
				direccion = 'No puedo encontrar esta dirección.';
			}
		});	
	};
	
	showMap = function (){
		modal.style.display = "block";
		google.maps.event.trigger(gmap, "resize");	
		gmap.setCenter(marker.getPosition());		
	};
	
	returnGeoref = function (){		
		if(marker != null){
			addressInput.value = direccion;
			latInput.value = marker.position.lat();
			lngInput.value = marker.position.lng();
		}		
		modal.style.display = "none";
	};
	
	closeModal = function() {
		modal.style.display = "none";
	};
	
	return {
		"showMap" :  showMap,
		"setModal" : setModal,
		"setInputs" : setInputs,
		"createGeorefMap" : createGeorefMap
	};

}();