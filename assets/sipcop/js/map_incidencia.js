var incidencia_api = {
	markers:null,
	imarkers:null,
	cluster:null,
	ubigeo:'-1',
	comisaria:'0',
	municipalidad:'0',
	activo:false,
	tkk:null,
	tkv: null,
	utlimo:0,
	ajax: null,
	filtro: {
        fecha: '',
        horaini:'',
        horafin:'',
        tipo_filtro: 0
    },

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
		this.cargar(dependencia, institucion, false);
	},

	cargar: function(dependencia, institucion, notificar,callback){
		var mostrar = $('#ckIncidencia').is(':checked');
		if(!notificar){
			this.clear();
		}

		if(mostrar != incidencia_api.activo){
			this.activar(mostrar);
		}
		
		if(mostrar || notificar){
			incidencia_api.dependencia = dependencia;
			incidencia_api.institucion = institucion;

			incidencia_api.filtro.fecha = $('#txtFecha').val();
			incidencia_api.filtro.horaini = $('#txtHoraIni').val();
			incidencia_api.filtro.horafin = $('#txtHoraFin').val();



			var filtro_incidencia = {};
			filtro_incidencia.dependencia = incidencia_api.dependencia;
			filtro_incidencia.institucion = incidencia_api.institucion;
			filtro_incidencia[incidencia_api.tkk] = incidencia_api.tkv;
			filtro_incidencia.fecha = incidencia_api.filtro.fecha;
			filtro_incidencia.horaini = incidencia_api.filtro.horaini;
			filtro_incidencia.horafin = incidencia_api.filtro.horafin;
			filtro_incidencia.notificar = notificar;

			if(notificar){
				filtro_incidencia.ultimo = incidencia_api.ultimo;
			}

			if(incidencia_api.ajax == null){
				incidencia_api.ajax = $.post('admin/home/json_incidencia', filtro_incidencia, function(resp){
					incidencia_api.ajax = null;

					if(resp.ultimo > 0){
						incidencia_api.ultimo = resp.ultimo;
					}

					$.each(resp.incidencias, function(idx, incidencia){
			 			var marker_id = 'Indicencia_'+incidencia.IncidenciaID;
			 			if(!incidencia_api.markers[marker_id]){
			 				var overlay = new IncidenciaMrkr(new google.maps.LatLng(incidencia.IncidenciaLatitud, incidencia.IncidenciaLongitud), map_api.map, {
					            marker_id: marker_id,
					            incidencia: incidencia,
					            callback: function(mrk) {
					                map_api.mostrarInfoIncidencia(mrk);
					            }
					        });
					        overlay.incidencia = incidencia;
					        incidencia_api.markers[marker_id] = overlay;
					        incidencia_api.cluster.addMarker(overlay);
	        				incidencia_api.imarkers.push(overlay);
	        				incidencia_api.ultimo = incidencia.IncidenciaID;

			 			}
			 		});

			 		if(notificar){
			 			incidencia_api.activar(true);
			 		}else{
			 			incidencia_api.activar(incidencia_api.activo);
			 		}

					if(callback){
		                callback({resp:'ok', status:'1', data:resp.incidencias});
		            }
				},'json').fail(function(err){
					if(callback){
		                callback({err:err, status:'0'});
		            }
		            incidencia_api.ajax = null;
				});
			}
		}else{
			if(callback){
                callback({resp:'ok', status:'1'});
                incidencia_api.ajax = null;
            }
		}
	},

	activar: function(activo){
		if(incidencia_api.activo != activo){
			incidencia_api.activo = activo;
			if(incidencia_api.markers!=null){
				$.each(incidencia_api.markers, function(idx, objx){
					if(incidencia_api.activo){
						objx.setMap(map_api.map);
					}else{
						objx.setMap(null);
					}
				});
			}
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