var jurisdiccion_api = {
	capa:null,
	data:null,
	ubigeo:'-1',
	comisaria:'0',
	activo:false,
	tkk:null,
	tkv: null,

	jurisdiccion:null,

	init: function(ubigeo, comisaria, tkk, tkv){
		this.tkk = tkk;
		this.tkv = tkv;
		this.cargar(ubigeo, comisaria);
	},

	cargar: function(ubigeo,comisaria,callback){
		var mostrar = true;

		this.activar(mostrar);

		if(this.ubigeo != ubigeo && mostrar){
			jurisdiccion_api.ubigeo = ubigeo;
			jurisdiccion_api.comisaria = comisaria;

			var filtro_jurisduccion = {};
			filtro_jurisduccion.ubigeo = jurisdiccion_api.ubigeo;
			//filtro_jurisduccion.comisaria = jurisdiccion_api.comisaria;
			filtro_jurisduccion[jurisdiccion_api.tkk] = jurisdiccion_api.tkv;

			$.post('transmisiones/json_jurisdiccion', filtro_jurisduccion, function(resp){

				if(jurisdiccion_api.capa!=null){
					$.each(jurisdiccion_api.capa, function(idx, objx){
						objx.setMap(null);
					});
				}
				
		 		jurisdiccion_api.capa = null;
		 		jurisdiccion_api.capa = {};

		 		var data_coord = {};
		 		var data_ubi = {};


		 		$.each(resp.jurisdicciones, function(idx, jurisdiccion){
		 				if(!data_coord['Comisaria_'+jurisdiccion.ComisariaID]){
		 					data_coord['Comisaria_'+jurisdiccion.ComisariaID] = [];
		 				}
		 				data_coord['Comisaria_'+jurisdiccion.ComisariaID].push({lat:parseFloat(jurisdiccion.JurisdiccionLat), lng:parseFloat(jurisdiccion.JurisdiccionLong)});
		 				data_ubi['Comisaria_'+jurisdiccion.ComisariaID] = jurisdiccion.UbigeoID;
		 		});

		 		$.each(data_coord, function(idx, objx){
		 	

		 		var color ='';
		 		var z_index = 0;	

		 			if (idx=='Comisaria_'+jurisdiccion_api.comisaria) {
		 				color= '#FF5733'; 
		 				z_index = 1000;
		 			}else{
						color= '#A26610'; 	
		 				z_index = 100;
		 			}

					jurisdiccion_api.capa[idx] = new google.maps.Polygon({
										          paths: objx,
										          strokeColor: color,
										          strokeOpacity: 0.8,
										          strokeWeight: 4,
										          //fillColor: color,
										          fillOpacity: 0,
										          zIndex:z_index
										        });
					jurisdiccion_api.capa[idx].ubigeo = data_ubi[idx];

					if(z_index == 1000){
						jurisdiccion_api.jurisdiccion = jurisdiccion_api.capa[idx];
						jurisdiccion_api.centrarJurisdiccion();						
					}

					google.maps.event.addListener(jurisdiccion_api.capa[idx], 'click', function (event) {
					  if(capturador_api.opcion_ui > 0){
			            capturador_api.agregarMarker(event.latLng, this.ubigeo);
			           }
					});  

					google.maps.event.addListener(jurisdiccion_api.capa[idx], 'mousemove',
					    function(e) {
					        if (!isNaN(e.edge) || !isNaN(e.vertex))
					            capturador_api.map.className = '';
					        else if(capturador_api.opcion_ui > 0)
					            capturador_api.map.className = 'moving-map';  
					        else
					            capturador_api.map.className = '';        
					    }
					);

					google.maps.event.addListener(jurisdiccion_api.capa[idx], 'mouseout',
					    function() {
					        capturador_api.map.className = '';
					    }
					);

				});

				data_coord = null;

		 		jurisdiccion_api.activar(jurisdiccion_api.activo);

				if(callback){
	                callback({resp:'ok', status:'1'});
	            }
			},'json').fail(function(err){
				if(callback){
	                callback({err:err, status:'0'});
	            }
			});
		}else{
			if(callback){
                callback({resp:'ok', status:'1'});
            }
		}
	},

	activar: function(activo){
		jurisdiccion_api.activo = activo;
		if(jurisdiccion_api.capa!=null){
			$.each(jurisdiccion_api.capa, function(idx, objx){
				if(jurisdiccion_api.activo){
					objx.setMap(capturador_api.map);
				}else{
					objx.setMap(null);
				}
			});
		}
	},
	
	centrarJurisdiccion: function(comisaria){
		if(!comisaria){
			comisaria = capturador_api.usu_comisaria;
		}

		if(!this.capa){
			return false;
		}

		if(this.capa['Comisaria_'+comisaria]){
			var bounds = new google.maps.LatLngBounds();
		    jurisdiccion_api.capa['Comisaria_'+comisaria].getPath().forEach(function (path, index) {    
		        bounds.extend(path);
		    });
		    capturador_api.map.fitBounds(bounds);
		}
	}
}