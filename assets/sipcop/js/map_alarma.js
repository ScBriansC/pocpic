var alarma_api = {
	markers:null,
	imarkers:null,
	cluster:null,
	ubigeo:'-1',
	comisaria:'0',
	municipalidad:'0',
	activo:false,
	tkk:null,
	tkv: null,
	ajax: null,

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
		var mostrar = $('#ckAlarma').is(':checked');

		if(!notificar){
			this.clear();
		}

		if(mostrar != alarma_api.activo){
			this.activar(mostrar);
		}
		
		if(mostrar || notificar){
			alarma_api.dependencia = dependencia;
			alarma_api.institucion = institucion;

			var filtro_alarma = {};
			filtro_alarma.dependencia = alarma_api.dependencia;
			filtro_alarma.institucion = alarma_api.institucion;
			filtro_alarma.notificar = notificar;
			filtro_alarma[map_api.k_it] = map_api.v_it;

			if(alarma_api.ajax == null){
				alarma_api.ajax = $.post('admin/home/json_alarma', filtro_alarma, function(resp){
					alarma_api.ajax = null;

			 		$.each(resp.alarmas, function(idx, alarma){
			 			var marker_id = 'Alarma_'+alarma.AlarmaID;
			 			if(!alarma_api.markers[marker_id]){
			 				var overlay = new AlarmaMrkr(new google.maps.LatLng(alarma.AlarmaLatitud, alarma.AlarmaLongitud), map_api.map, {
					            marker_id: marker_id,
					            alarma: alarma,
					            callback: function(mrk) {
					                alarma_api.encender(mrk);
					            }
					        });
					        overlay.alarma = alarma;
					        alarma_api.markers[marker_id] = overlay;
					        alarma_api.cluster.addMarker(overlay);
	        				alarma_api.imarkers.push(overlay);
			 			}else{
			 				alarma_api.markers[marker_id].alarma = alarma;
			 				alarma_api.markers[marker_id].encender(alarma.AlarmaEncendido);
			 			}					
			 		});
	
			 		if(notificar){
			 			alarma_api.activar(true);
			 		}else{
			 			alarma_api.activar(alarma_api.activo);
			 		}
	
					if(callback){
		                callback({resp:'ok', status:'1',data:resp.alarmas});
		            }
				},'json').fail(function(err){
					if(callback){
		                callback({err:err, status:'0'});
		            }
		            alarma_api.ajax = null;
				});
			}
		}else{
			if(callback){
                callback({resp:'ok', status:'1'});
                alarma_api.ajax = null;
            }
		}
	},

	activar: function(activo){
		if(activo != alarma_api.activo){
			alarma_api.activo = activo;
			if(alarma_api.markers!=null){
				$.each(alarma_api.markers, function(idx, objx){
					if(alarma_api.activo){
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
	},

	encender: function(alarma){
		var idalarma = alarma.alarma.AlarmaID;
		var nombre = alarma.alarma.AlarmaReferencia + " - " + alarma.alarma.AlarmaDescripcion;
		var lat = alarma.alarma.AlarmaLatitud;
		var lon = alarma.alarma.AlarmaLongitud;
		var enc = alarma.alarma.AlarmaEncendido;

		var obj = {alarma:idalarma, flag:((enc=='0')?1:2), lat:lat, lon:lon};

	
		if(enc > 0){
			$('#myModalAlarma').modal('show');
			$('#txtidalarma').val(idalarma);
			$('#txtnombrealarma').val(nombre);
			$('#txtlatalarma').val(lat);
			$('#txtlonalarma').val(lon);
			$('#txtenc').val(enc);
		}
		else{
			swal({
			  title: ((enc=='0')?'Encender':'Apagar')+" alarma",
			  text: "¿Está seguro de "+((enc=='0')?'encender':'apagar')+"la alarma<br><strong style=\"font-weight: bold;\">\""+nombre+"\"</storng>?",
			  type: "warning",
			  showCancelButton: true,
			  confirmButtonColor: "#DD6B55",
			  confirmButtonText: "Si",
			  cancelButtonText: "No",
			  closeOnConfirm: true,
			  html:true
			},
			function(){
					$.ajax('api/alarma/encenderAlarma',{
					    'data': JSON.stringify(obj),
					    'type': 'POST',
					    'processData': false,
					    'contentType': 'application/json'
					}).done(function() {
					  swal("Acción realizada", "La alarma ha sido encendida.", "success");
					  alarma.encender(obj.flag);
					});			
			});
		}

	},

	apagarAlarma: function(){

		var id = $.trim($('#txtidalarma').val());
		var nombre = $.trim($('#txtnombrealarma').val());
		var lat = $.trim($('#txtlatalarma').val());
		var lon = $.trim($('#txtlonalarma').val());
		var enc = $.trim($('#txtenc').val());
		var tipo = $.trim($('#tipoalarma').val());
    	var motivo = $.trim($('#motivoalarma').val());
    	var detalle = $.trim($('#detallealarma').val());


    	var obj = {id:id,enc:enc,lat:lat,lon:lon,tipo:tipo,motivo:motivo,detalle,detalle};

    	var blank = ' ';

    	if(id)
    	{
    		if(tipo > 0 || !motivo==' '){
	       		$("#eTipoA").css("display","none");
	       		if(!motivo==' ' || motivo.search(blank) > 0){
		       		$("#eMotivoA").css("display","none");
		       		if(!detalle==' ' || detalle.search(blank) > 0){
			       		$("#eDetalleA").css("display","none");
							$.ajax('api/alarma/apagarAlarma',{
							    'data': JSON.stringify(obj),
							    'type': 'POST',
							    'processData': false,
							    'contentType': 'application/json'
							}).done(function(data) {
							 $('#txtidalarma').val('');
							 $('#txtnombrealarma').val('');
							 $('#txtlatalarma').val('');
							 $('#txtlonalarma').val('');
							 $('#txtenc').val('');							 
							 $("#tipoalarma").val('0').trigger('change');
            				 $('#motivoalarma').val('');	
            				 $('#detallealarma').val('');		
							 $('#myModalAlarma').modal('hide');
 							  	swal("Acción realizada", "La alarma ha sido apagada.", "success");
					  			var alarma = alarma_api.markers['Alarma_'+id];
					  			if(alarma){
					  				alarma.encender(0);
					  			}
							  
							});			
			       	}else{
				        $("#eDetalleA").css("display","");
				        $( "#eDetalleA" ).html( "Complete Detalle" );
				    }
		       	}else{
			        $("#eMotivoA").css("display","");
			        $( "#eMotivoA" ).html( "complete Motivo" );
			    }
	       	}else{
		        $("#eTipoA").css("display","");
		        $("#eTipoA" ).html( "Seleccione un tipo" );
		    }	
    	}
	
	}
}