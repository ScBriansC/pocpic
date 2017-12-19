var jurisdiccion_ruta_api = {
	capa:null,
	data:null,
	ubigeo:'-1',
	comisaria:'0',
	dependencia:null,
	activo:false,
	tkk:null,
	tkv: null,

	init: function(tkk, tkv, callback){
		this.tkk = tkk;
		this.tkv = tkv;
		this.cargar(0,0,'',callback);
	},

	cargar: function(institucion,dependencia,ubigeo,callback){

		this.activar(true);

		if(this.dependencia != dependencia){

			var filtro_jurisduccion = {};
			filtro_jurisduccion.ubigeo = ubigeo;
			filtro_jurisduccion.institucion = institucion;
			filtro_jurisduccion.dependencia = dependencia;
			filtro_jurisduccion.ubigeo = ubigeo;
			filtro_jurisduccion[jurisdiccion_ruta_api.tkk] = jurisdiccion_ruta_api.tkv;

			this.dependencia = dependencia;

			$.post('admin/home/json_jurisdiccion', filtro_jurisduccion, function(resp){

				if(jurisdiccion_ruta_api.capa!=null){
					$.each(jurisdiccion_ruta_api.capa, function(idx, objx){
						objx.polygon.setMap(null);
					});
				}
				
		 		jurisdiccion_ruta_api.capa = null;
		 		jurisdiccion_ruta_api.capa = {};

		 		var data_coord = {};


		 		$.each(resp.jurisdicciones, function(idx, jurisdiccion){
		 				if(!data_coord['Comisaria_'+jurisdiccion.ComisariaID]){
		 					data_coord['Comisaria_'+jurisdiccion.ComisariaID] = {flg:0,puntos:[]};
		 					data_coord['Comisaria_'+jurisdiccion.ComisariaID].flg = jurisdiccion.JurisdiccionFlg;
		 				}
		 				data_coord['Comisaria_'+jurisdiccion.ComisariaID].puntos.push({lat:parseFloat(jurisdiccion.JurisdiccionLat), lng:parseFloat(jurisdiccion.JurisdiccionLong)});
		 		});

		 		$.each(data_coord, function(idx, objx){
		 	

		 		var color ='';
		 		var z_index = 0;	

		 			if (parseInt(objx.flg) == 1) {
		 				color= '#FF5733'; 
		 				z_index = 1000;
		 			}else{
						color= '#A26610'; 	
		 				z_index = 100;
		 			}
		 			jurisdiccion_ruta_api.capa[idx] = {};
		 			jurisdiccion_ruta_api.capa[idx].flg = objx.flg;
					jurisdiccion_ruta_api.capa[idx].polygon = new google.maps.Polygon({
										          paths: objx.puntos,
										          strokeColor: color,
										          strokeOpacity: 0.8,
										          strokeWeight: 4,
										          //fillColor: color,
										          fillOpacity: 0,
										          zIndex:z_index
										        });

					// if(z_index == 1000){
					// 	jurisdiccion_ruta_api.jurisdiccion = jurisdiccion_ruta_api.capa[idx];
					// 	jurisdiccion_ruta_api.centrarJurisdiccion();						
					// }

				});

				data_coord = null;

		 		jurisdiccion_ruta_api.activar(jurisdiccion_ruta_api.activo);

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
		jurisdiccion_ruta_api.activo = activo;
		if(jurisdiccion_ruta_api.capa!=null){
			$.each(jurisdiccion_ruta_api.capa, function(idx, objx){
				if(jurisdiccion_ruta_api.activo){
					objx.polygon.setMap(hojaruta_api.map);
				}else{
					objx.polygon.setMap(null);
				}
			});
		}
	},
	centrarJurisdiccion: function(comisaria){
		if(!comisaria){
			comisaria = hojaruta_api.usu_comisaria;
		}

		if(!this.capa){
			return false;
		}

		if(this.capa['Comisaria_'+comisaria]){
			var bounds = new google.maps.LatLngBounds();
		    jurisdiccion_ruta_api.capa['Comisaria_'+comisaria].polygon.getPath().forEach(function (path, index) {    
		        bounds.extend(path);
		    });
		    hojaruta_api.map.fitBounds(bounds);
		}
	}
}