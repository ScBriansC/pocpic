var region_api = {
	capa:null,
	data:null,
	ubigeo:'-1',
	activo:false,
	tkk:null,
	tkv: null,

	init: function(ubigeo, tkk, tkv){
		this.tkk = tkk;
		this.tkv = tkv;
		this.cargar(ubigeo);
	},

	cargar: function(ubigeo,callback){
		var mostrar = $('#ckRegion').is(':checked');

		this.activar(mostrar);

		if(this.ubigeo != ubigeo && mostrar){
			region_api.ubigeo = ubigeo;

			var filtro_jurisduccion = {};
			filtro_jurisduccion.ubigeo = region_api.ubigeo;
			filtro_jurisduccion[region_api.tkk] = region_api.tkv;

			$.post('admin/home/json_region', filtro_jurisduccion, function(resp){

				if(region_api.capa!=null){
					$.each(region_api.capa, function(idx, objx){
						objx.setMap(null);
					});
				}
				
		 		region_api.capa = null;
		 		region_api.capa = {};

		 		var data_coord = {};


		 		$.each(resp.regiones, function(idx, region){
		 				if(!data_coord['Ubigeo_'+region.UbigeoID]){
		 					data_coord['Ubigeo_'+region.UbigeoID] = [];
		 				}
		 				data_coord['Ubigeo_'+region.UbigeoID].push({lat:parseFloat(region.RegionLat), lng:parseFloat(region.RegionLong)});
		 		});

		 		$.each(data_coord, function(idx, objx){
					region_api.capa[idx] = new google.maps.Polygon({
										          paths: objx,
										          strokeColor: map_api.colorByString(idx),
										          strokeOpacity: 0.8,
										          strokeWeight: 4,
										          fillColor: map_api.colorByString(idx),
										          fillOpacity: 0
										        });
				});

				data_coord = null;

		 		region_api.activar(region_api.activo);

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
		region_api.activo = activo;
		if(region_api.capa!=null){
			$.each(region_api.capa, function(idx, objx){
				if(region_api.activo){
					objx.setMap(map_api.map);
				}else{
					objx.setMap(null);
				}
			});
		}
	}
}