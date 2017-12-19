var barrio_api = {
	capa:null,
	data:null,
	ubigeo:'-1',
	comisaria:'0',
	dependencia:null,
	activo:false,
	tkk:null,
	tkv: null,

	init: function(tkk, tkv){
		this.tkk = tkk;
		this.tkv = tkv;
		this.cargar(0,0,'');
	},

	cargar: function(institucion,dependencia,ubigeo,callback){
		var mostrar = $('#ckBarrioSeguro').is(':checked');

		this.activar(mostrar);

		if(this.dependencia != dependencia && mostrar){

			var filtro_barrio = {};
			filtro_barrio.ubigeo = 		ubigeo;
			filtro_barrio.institucion = institucion;
			filtro_barrio.dependencia = dependencia;
			filtro_barrio.ubigeo = 		ubigeo;
			filtro_barrio[map_api.k_it] = map_api.v_it;

			this.dependencia = dependencia;
			//jco buscar en otro lado admin/home/json_jurisdiccion

			$.post('admin/home/json_barrio', filtro_barrio, function(resp){

				if(barrio_api.capa!=null){
					$.each(barrio_api.capa, function(idx, objx){
						objx.polygon.setMap(null);
					});
				}
				
		 		barrio_api.capa = null;
		 		barrio_api.capa = {};

		 		var data_coord = {};


		 		$.each(resp.barrio, function(idx, barrio){
		 				if(!data_coord['Comisaria_'+barrio.ComisariaID]){
		 					data_coord['Comisaria_'+barrio.ComisariaID] = {flg:0,puntos:[]};
		 					data_coord['Comisaria_'+barrio.ComisariaID].flg = barrio.BarrioFlg;
		 				}
		 				data_coord['Comisaria_'+barrio.ComisariaID].puntos.push({lat:parseFloat(barrio.BarrioLat), lng:parseFloat(barrio.BarrioLong)});
		 		});

		 		$.each(data_coord, function(idx, objx){
		 	

		 		var color ='';
		 		var z_index = 0;	

		 			if (parseInt(objx.flg) == 1) {
		 				color= '#088A85'; 
		 				z_index = 1000;
		 			}else{
						color= '#5882FA'; 	
		 				z_index = 100;
		 			}
		 			barrio_api.capa[idx] = {};
		 			barrio_api.capa[idx].flg = objx.flg;
					barrio_api.capa[idx].polygon = new google.maps.Polygon({
										          paths: objx.puntos,
										          strokeColor: color,
										          strokeOpacity: 0.8,
										          strokeWeight: 4,
										          //fillColor: color,
										          fillOpacity: 0,
										          zIndex:z_index
										        });

					/*if(z_index == 1000){
						jurisdiccion_api.jurisdiccion = jurisdiccion_api.capa[idx];
						jurisdiccion_api.centrarJurisdiccion();						
					}*/

				});

				data_coord = null;

		 		barrio_api.activar(barrio_api.activo);

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
		barrio_api.activo = activo;
		if(barrio_api.capa!=null){
			$.each(barrio_api.capa, function(idx, objx){
				if(barrio_api.activo){
					objx.polygon.setMap(map_api.map);
				}else{
					objx.polygon.setMap(null);
				}
			});
		}
	},
	centrarBarrio: function(comisaria){
		if(!comisaria){
			comisaria = map_api.usu_comisaria;
		}

		if(!this.capa){
			return false;
		}

		if(this.capa['Comisaria_'+comisaria]){
			var bounds = new google.maps.LatLngBounds();
		    barrio_api.capa['Comisaria_'+comisaria].polygon.getPath().forEach(function (path, index) {    
		        bounds.extend(path);
		    });
		    map_api.map.fitBounds(bounds);
		}
	}
}