var denuncia_api = {
	capa:null,
	data:null,
	ubigeo:'-1',
	activo:false,
	tkk:null,
	tkv: null,

	init: function(dependencia, institucion, tkk, tkv){
		this.tkk = tkk;
		this.tkv = tkv;
		this.cargar(dependencia, institucion);
	},

	cargar: function(dependencia, institucion, callback){
		var mostrar = $('#ckMapaDenuncia').is(':checked');

		this.activar(mostrar);

		if(this.dependencia != dependencia && mostrar){
			denuncia_api.dependencia = dependencia;
			denuncia_api.institucion = institucion;

			var filtro_denuncia = {};
			filtro_denuncia.dependencia = denuncia_api.dependencia;
			filtro_denuncia.institucion = denuncia_api.institucion;
			filtro_denuncia[denuncia_api.tkk] = denuncia_api.tkv;

			$.post('admin/home/json_denuncia', filtro_denuncia, function(resp){
				denuncia_api.data = null;
		 		denuncia_api.data = [];
		 		if(denuncia_api.capa!=null){
		 			denuncia_api.capa.setMap(null);
		 			denuncia_api.capa = null;
		 		}


		 		$.each(resp.denuncias, function(idx, denuncia){
		 			//for(var i=0; i<denuncia.DenunciaCant; i++){
		 				denuncia_api.data.push(new google.maps.LatLng(denuncia.DenunciaLat,denuncia.DenunciaLong));
		 			//}
		 		});

		 		denuncia_api.capa = new google.maps.visualization.HeatmapLayer({
				  data: denuncia_api.data,
				  radius: 11,
				  opacity: 0.7,
				  dissipating: true,
				  maxIntensity: 100
				});

				if(denuncia_api.activo){
					denuncia_api.capa.setMap(map_api.map)
				}

				if(callback){
	                callback({resp:'ok', status:'1',data:resp.denuncias});
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
		this.activo = activo;
		if(this.capa!=null){
			if(this.activo){
				this.capa.setMap(map_api.map);
			}else{
				this.capa.setMap(null);
			}
		}
	},

	zoom_event: function(z){
		if(typeof this.capa!='undefined' && this.capa!=null){
			if(this.ubigeo==''){
				if(z<=7){
					denuncia_api.capa.setOptions({maxIntensity:100, radius: 10});
				}else if(z>=8 && z<=9){
					denuncia_api.capa.setOptions({maxIntensity:150, radius: 10});
				}else if(z>=10 && z<=11){
					denuncia_api.capa.setOptions({maxIntensity:200, radius: 15});
				}else if(z>=12 && z<=13){
					denuncia_api.capa.setOptions({maxIntensity:30, radius: 20});
				}else if(z>=14 && z<=15){
					denuncia_api.capa.setOptions({maxIntensity:10, radius: 12});
				}else{
					denuncia_api.capa.setOptions({maxIntensity:2, radius: 15});
				}
			}else{
				var depa = this.ubigeo.substr(1,2);
				var prov = this.ubigeo.substr(2,2);
				var dist = this.ubigeo.substr(4,2);

				if(prov=='00' && dist=='00'){
					if(z<=7){
						denuncia_api.capa.setOptions({maxIntensity:100, radius: 10});
					}else if(z>=8 && z<=9){
						denuncia_api.capa.setOptions({maxIntensity:150, radius: 10});
					}else if(z>=10 && z<=11){
						denuncia_api.capa.setOptions({maxIntensity:200, radius: 15});
					}else if(z>=12 && z<=13){
						denuncia_api.capa.setOptions({maxIntensity:30, radius: 20});
					}else if(z>=14 && z<=15){
						denuncia_api.capa.setOptions({maxIntensity:10, radius: 12});
					}else{
						denuncia_api.capa.setOptions({maxIntensity:2, radius: 15});
					}
				}else if(prov!='00' && dist=='00'){
					if(z<=7){
						denuncia_api.capa.setOptions({maxIntensity:100, radius: 10});
					}else if(z>=8 && z<=9){
						denuncia_api.capa.setOptions({maxIntensity:150, radius: 10});
					}else if(z>=10 && z<=11){
						denuncia_api.capa.setOptions({maxIntensity:200, radius: 15});
					}else if(z>=12 && z<=13){
						denuncia_api.capa.setOptions({maxIntensity:30, radius: 20});
					}else if(z>=14 && z<=15){
						denuncia_api.capa.setOptions({maxIntensity:10, radius: 12});
					}else{
						denuncia_api.capa.setOptions({maxIntensity:2, radius: 15});
					}
				}else{
					if(z<=7){
						denuncia_api.capa.setOptions({maxIntensity:100, radius: 10});
					}else if(z>=8 && z<=9){
						denuncia_api.capa.setOptions({maxIntensity:150, radius: 10});
					}else if(z>=10 && z<=11){
						denuncia_api.capa.setOptions({maxIntensity:200, radius: 15});
					}else if(z>=12 && z<=13){
						denuncia_api.capa.setOptions({maxIntensity:30, radius: 20});
					}else if(z>=14 && z<=15){
						denuncia_api.capa.setOptions({maxIntensity:10, radius: 12});
					}else{
						denuncia_api.capa.setOptions({maxIntensity:2, radius: 15});
					}
				}


				depa = null, prov = null, dist = null;

			}
		}

		
	}
}





/*var gradientScheme = [
'rgba(255, 0, 255, 0)',
'rgba(255, 0, 0, 1)',
'rgba(255, 0, 0, 1)',
'rgba(125, 125, 125, 0)',
'rgba(125, 120, 60, 0)',
'rgba(200, 60, 0, 0)',
'rgba(255, 0, 0, 1)'
];

capaDenuncia.set('gradient', capaDenuncia.get('gradient') ? null : gradientScheme);*/

