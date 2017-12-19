var camara_api = {
	markers:null,
	imarkers:null,
	cluster:null,
	ubigeo:'-1',
	comisaria:'0',
	municipalidad:'0',
	activo:false,
	tkk:null,
	tkv: null,

	init: function(dependencia, institucion, tkk, tkv){
		this.tkk = tkk;
		this.tkv = tkv;

		if(!this.markers){
			this.markers = {};
		}

		if(!this.imarkers){
			this.imarkers = [];
		}

		this.cluster = new MarkerClusterer(map_api.map, this.imarkers, {
            imagePath: 'assets/sipcop/img/m_poli_'
        });

		this.cargar(dependencia, institucion);
	},

	cargar: function(dependencia,institucion,callback){
		var mostrar = $('#ckCamara').is(':checked');

		this.clear();

		this.activar(mostrar);

		if(mostrar){
			camara_api.dependencia = dependencia;
			camara_api.institucion = institucion;

			var filtro_camara = {};
			filtro_camara.dependencia = camara_api.dependencia;
			filtro_camara.institucion = camara_api.institucion;
			filtro_camara[camara_api.tkk] = camara_api.tkv;

			$.post('admin/home/json_camara', filtro_camara, function(resp){

		 		$.each(resp.camaras, function(idx, camara){
		 			var marker_id = 'Camara_'+camara.CamaraID;
		 			if(!camara_api.markers[marker_id]){
		 				var overlay = new CamaraMrkr(new google.maps.LatLng(camara.CamaraLatitud, camara.CamaraLongitud), map_api.map, {
				            marker_id: marker_id,
				            camara: camara,
				            callback: function(mrk) {
				                map_api.mostrarInfoCamara(mrk);
				            }
				        });
				        overlay.camara = camara;
				        camara_api.markers[marker_id] = overlay;
				        camara_api.cluster.addMarker(overlay);
        				camara_api.imarkers.push(overlay);
		 			}
		 				
		 		});

		 		camara_api.activar(camara_api.activo);

				if(callback){
	                callback({resp:'ok', status:'1', data:resp.camaras});
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
		camara_api.activo = activo;
		if(camara_api.markers!=null){
			$.each(camara_api.markers, function(idx, objx){
				if(camara_api.activo){
					objx.setMap(map_api.map);
				}else{
					try{
						objx.setMap(null);
					}catch(err){}
				}
			});
		}
	},

	clear: function(){
        $.each(this.markers, function(idx, objx) {
            objx.remove();
        });
        this.markers = {};
        this.cluster.removeMarkers(this.imarkers,true);
        this.imarkers.length = 0;
        delete this.imarkers;
        this.imarkers = [];
        this.cluster.repaint();
	}
}