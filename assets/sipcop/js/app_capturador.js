var capturador_api = {
    map: null,
    iniLatitud: -9.7213829,
    iniLongitud: -73.5501206,
    iniZoom: 5,

    opcion_ui: 0,
    marker_added: null,

    usu_comisaria:0,

    filtro: {
        fecha: '', //
        horaini:'',
        horafin:'',
        tipo_filtro: 0,
        policia: '',
        placa: '',
        ubigeo:'',
        tipo_vehiculo:'',
        vehiculo:0,
        reset: 0,
        comisaria:0
    },
    filtro_radio: {
        fecha: '', //
        horaini:'',
        horafin:'',
        tipo_filtro: 0,
        etiqueta: '',
        ubigeo:'',
        tipo_radio:'',
        radio:0,
        reset: 0,
        comisaria:0
    },

    filtro_comisaria: {
        nombre:'',
        zona:''
    },

    cluster: null,
    clusterComisaria: null,
    clusterRadio: null,

    mrkSelected:null,
    mrkRadioSelected:null,
    mrkComisariaSelected:null,
    mrkVehiculos: {},
    mrkComisarias: {},
    mrkRadios: {},
    mrkCapturador:{},
    markers: [],
    markersComisaria: [],
    markersRadio: [],
    markersCapturador:[],

    rutaVehiculo: null,
    rutaRadio: null,
    puntosRuta:[],
    centroRuta:null,
    ajaxPost: null,
    ajaxPostComisaria: null,
    ajaxPostRadio: null,
    taskCount: 0,
    k_it:'',

    taskError:[],


    grupoNotificacion:null,

    init: function(k_it, v_it){
        this.k_it=k_it;
        this.v_it=v_it;
        this.filtro[k_it] = v_it;
        this.filtro_comisaria[k_it] = v_it;
        this.filtro_radio[k_it] = v_it;
        this.map = new google.maps.Map($('#cnv_map')[0], {
            center: {
                lat: capturador_api.iniLatitud,
                lng: capturador_api.iniLongitud
            },
            zoom: capturador_api.iniZoom,
            // zoomControl: false,
            // scaleControl: true
            streetViewControl:false,
            mapTypeControl: false

        });

       
        google.maps.event.addListenerOnce(this.map, 'idle', function() {
           google.maps.event.trigger(capturador_api.map, 'resize');
        });

        this.grupoNotificacion = [];

    },

    add_TaskLoader: function(){
        if(!this.taskLoader){
            this.taskLoader=0;
        }
        if(this.taskLoader == 0){
            $('.btn-filtrar').addClass('fil-searching').html('Buscando...'); 
            SipcopJS.center($('.app-bg-loader img'));          
            $('.app-bg-loader').show();
        }
        this.taskLoader++;
    },

    end_TaskLoader: function(){
        if(!this.taskLoader){
            this.taskLoader=0;
        }

        if(this.taskLoader>0){
            this.taskLoader--;
        }

        if(this.taskLoader<=0){
            this.taskLoader = 0;
            $('.app-bg-loader').hide();
            $('.btn-filtrar').removeClass('fil-searching').html('Buscar');

            if(this.taskError.length > 0){
                for(var iErr = 0; iErr < this.taskError.length; iErr++){
                    $.gritter.add({
                                position: 'bottom-right',
                                title: 'Mensaje',
                                text: this.taskError[iErr],
                                class_name: 'gritter-error'
                            });
                }
                this.taskError = null;
                this.taskError = [];
            }
        }
    },

    addTaskError: function(msj){
        if(!taskError){
            capturador_api.taskError = [];
        }

        capturador_api.taskError.put(msj);
    },


    descargar_gps : function(callback){

        clearInterval(capturador_api.interval_demo);
        if(capturador_api.ajaxPostPatrullero!=null && capturador_api.ajaxPostPatrullero.readyState == 1){
            capturador_api.ajaxPostPatrullero.abort();
            capturador_api.ajaxPostPatrullero = null;
        }
        capturador_api.limpiarRuta();


        if((capturador_api.filtro_radio.tipo_filtro == 2 || capturador_api.filtro_radio.tipo_filtro == 3) && capturador_api.mrkComisariaSelected){
            
            capturador_api.clearMarkersRadio();
            if(callback){
                callback({resp:'ok', status:'1'});
            }
           return false; 
        }
        

        capturador_api.ajaxPostPatrullero = $.post('transmisiones/json_patrullaje',capturador_api.filtro, function(data){
           

            capturador_api.centroRuta = new google.maps.LatLngBounds();

            $('.cont-vehiculo').html('');
            $('.restult-vehiculo').html('0');

            var hmtl_vehiculo = '';
            var tab_comisaria = $('.panel-tab-tabs a.tab-comisaria').hasClass('active');

            if(data.patrullaje  && data.patrullaje.length > 0){
                var _idcomisaria_act = -1;
                $.each(data.patrullaje, function(idx, objx){

                    capturador_api.agregarMarcadorCapturador(objx);
                   
                });

                hmtl_vehiculo += '</div></div>';

                $('.cont-vehiculo').html(hmtl_vehiculo);
                $('.restult-vehiculo').html(data.patrullaje.length);

            }

            if(data.comisaria){
                var mrk = capturador_api.mrkComisarias["Comisaria_" + data.comisaria.ComisariaID];
                if (mrk != null && typeof mrk !== 'undefined' && typeof mrk.args !== 'undefined' && typeof mrk.args.oComisaria !== 'undefined') {
                    mrk.latlng = new google.maps.LatLng(parseFloat(data.comisaria.ComisariaLat), parseFloat(data.comisaria.ComisariaLong));                            
                    mrk.draw();
                } else {
                    mrk = capturador_api.agregarMarcadorComisaria(data.comisaria);
                }
                capturador_api.centroRuta.extend(mrk.latlng);
            }

            if(data.ruta && (capturador_api.filtro.tipo_filtro == 2 || capturador_api.filtro.tipo_filtro == 3)){
                capturador_api.dibujarRuta(data.ruta, '#DA3A26');                           
            }

            if(capturador_api.filtro.tipo_filtro == 0 || capturador_api.filtro.tipo_filtro == 4){
                capturador_api.filtro.tipo_filtro = 1;
                if(data.patrullaje.length == 1){
                    var listener = google.maps.event.addListener(capturador_api.map, "idle", function() { 
                      if (capturador_api.map.getZoom() > 12) capturador_api.map.setZoom(12); 
                      google.maps.event.removeListener(listener); 
                    });
                }
                if(!tab_comisaria || data.comisaria){
                    if(!capturador_api.centroRuta.isEmpty()){
                        capturador_api.map.fitBounds(capturador_api.centroRuta);
                    }else{
                        capturador_api.centrarMapa();
                    }
                    
                }

            }

            capturador_api.ajaxPostPatrullero = null;

            if(callback){
                callback({resp:'ok', status:'1', data:data.patrullaje});
            }
        }, 'json').fail(function(err){
            if(callback){
                callback({err:err, status:'0'});
            }
            capturador_api.ajaxPostPatrullero = null;
        });
    },
    limpiarRuta:function(){},

   

    clearMarkers: function() {
       
    },

    clearMarkersRadio: function() {
       
    },

    clearMarkersComisaria: function() {
       
    },



    agregarMarcador: function(obj) {
        var htmlResult;
        var color = '#3d3d3d';
        if(obj.ComisariaID == capturador_api.usu_comisaria){
            color = '#20a8d8'; 
        }
        var marker_id = 'Vehiculo_' +  obj.DispoGPS;
        var overlay = new CapturadorMrkr(new google.maps.LatLng(obj.TrackerLat, obj.TrackerLong), capturador_api.map, {
            marker_id: marker_id,
            colour: 'Red',
            oVehiculo: obj,
            oResult: htmlResult,
            color: color,
            callback: function(mrk) {
                capturador_api.mostrarInfo(mrk);                
                /*if($(window).width() < 500){
                    capturador_api.centrarMapa(mrk.latlng, 18);
                }else{
                    capturador_api.centrarMapa(mrk.latlng, 18);
                }*/
            }
        });
        this.mrkVehiculos[marker_id] = overlay;
        this.cluster.addMarker(overlay);
        this.markers.push(overlay);
        overlay.color = color;
        return overlay;
    },

    agregarMarcadorComisaria: function(obj) {
        var htmlResult;
        var color = '#1bbc9b'; 
        var marker_id = 'Comisaria_' + obj.ComisariaID;
        if(obj.ComisariaID == capturador_api.usu_comisaria){
            color = '#2c6c5e'; 
        }
        var overlay = new ComisariaMrkr(new google.maps.LatLng(obj.ComisariaLat, obj.ComisariaLong), capturador_api.map, {
            marker_id: marker_id,
            colour: 'Red',
            oComisaria: obj,
            oResult: htmlResult,
            color: color,
            callback: function(mrk) {
                capturador_api.mostrarInfoComisaria(mrk);                
                /*if($(window).width() < 500){
                    capturador_api.centrarMapa(mrk.latlng, 10);
                }else{
                    capturador_api.centrarMapa(mrk.latlng, 10);
                }*/
            }
        });
        this.mrkComisarias[marker_id] = overlay;
        this.clusterComisaria.addMarker(overlay);
        this.markersComisaria.push(overlay);
        overlay.color = color;
        return overlay;
    },

    buscarDireccion: function(lat, lng, callback){

        $.get('https://maps.googleapis.com/maps/api/geocode/json', {latlng: lat+','+lng}, function(data){
            if(typeof callback !=='undefined'){
                callback(data.results[0].formatted_address);
            }
        }, 'json');

    },

    colorByString: function(str){
        var hash = 0;
        for (var i = 0; i < str.length; i++) {
           hash = str.charCodeAt(i) + ((hash << 5) - hash);
        }
        var c = (hash & 0x00FFFFFF)
        .toString(16)
        .toUpperCase();
        return "#"+("00000".substring(0, 6 - c.length) + c);
    },


    centrarMapa: function(pos, zoom) {
        var center;
        if(pos!=null && typeof pos !== 'undefined'){
            center = pos;
            this.map.setZoom(zoom);
        }else{
            center = new google.maps.LatLng(this.iniLatitud, this.iniLongitud);
            this.map.setZoom(this.iniZoom);
        }
        this.map.panTo(center);
        
    },
    
    aplicar_filtro: function(callback){


        var mostrar_vehi = $('#ckVehiculos').is(':checked');
        var mostrar_radio = $('#ckRadio').is(':checked');
        var txtPlaca = $('#txtPlaca');
        var txtRadio = $('#txtRadio');

        if(!mostrar_vehi || ($('.panel-tab-tabs a.tab-motorizado').hasClass('active') || ($('.panel-tab-tabs a.tab-comisaria').hasClass('active') && (capturador_api.filtro.comisaria == 0 && capturador_api.filtro_radio.comisaria == 0)))){
            
            capturador_api.clearMarkers();
            
            if(capturador_api.filtro.comisaria == 0){
                capturador_api.clearMarkersComisaria();
            }
            $('.cont-vehiculo').html('');
            $('.restult-vehiculo').html('0');
            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }else{

            if(!((capturador_api.filtro.placa!="" && capturador_api.filtro_radio.etiqueta!="") || (capturador_api.filtro.placa=="" && capturador_api.filtro_radio.etiqueta=="") || (capturador_api.filtro.placa!="" && capturador_api.filtro_radio.etiqueta==""))){
                if(capturador_api.ajaxPostRadio!=null && capturador_api.ajaxPostRadio.readyState == 1){
                    capturador_api.ajaxPostRadio.abort();
                    capturador_api.ajaxPostRadio = null;
                }
            }


            capturador_api.clearMarkers();

            if(capturador_api.filtro.comisaria == 0){
                capturador_api.clearMarkersComisaria();
            }


            capturador_api.filtro.fecha = $('#txtFecha').val();
            capturador_api.filtro.horaini = $('#txtHoraIni').val();
            capturador_api.filtro.horafin = $('#txtHoraFin').val();
            capturador_api.filtro.tipo_filtro = 0;
            capturador_api.filtro.policia = $('#txtPolicia').val();
            capturador_api.filtro.placa = $('#txtPlaca').val();
            capturador_api.filtro.ubigeo =capturador_api.get_ubigeo();
            capturador_api.filtro.tipo_vehiculo = $('#txtTipoVehiculo').val();
            capturador_api.filtro.vehiculo = 0;
            capturador_api.reset = 1;
            

            this.descargar_gps(function(resp_data){
                if(callback){
                    callback(resp_data);
                    
                }
            });
        }

    },
    

    centrarDiv: function(div){
        var child = $(div);
        var parent = $($(div).parent());
        child.css("position","absolute");
        child.css("top", ((parent.height() - child.outerHeight()) / 2) + parent.scrollTop() + "px");
        child.css("left", ((parent.width() - child.outerWidth()) / 2) + parent.scrollLeft() + "px");
    },


    notificarSalidaJurisdiccion: function(oDispoGPS){
        var radio = oDispoGPS.radio;
        var obj = {
            dispogps: radio.DispoGPS,
            fecha: radio.TrackerFecha,
            hora: radio.TrackerHora,
            latitud:radio.TrackerLat,
            longitud: radio.TrackerLong
        };
        capturador_api.grupoNotificacion.push(obj);
    },

    validarRadio: function(oDispoGPS){
        var cond = jurisdiccion_api && jurisdiccion_api.capa!=null && google && google.maps && google.maps.geometry && google.maps.geometry.poly;

        if(cond){
            var capa_juris = jurisdiccion_api.capa['Comisaria_'+oDispoGPS.radio.ComisariaID];     
            
            if(capa_juris){

               this.enJurisdicicon = google.maps.geometry.poly.containsLocation(oDispoGPS.marker.getPosition(),capa_juris);
               if(!this.enJurisdicicon){
                    capturador_api.notificarSalidaJurisdiccion(oDispoGPS);
               }
            }else{
                //console.log(oDispoGPS.ComisariaID); Comisaría sin jurisdicción
            }
        }
    },

    
    agregarMarcadorCapturador: function(obj) {        
        var marker_id = 'Vehiculo_' +  obj.DispoGPS;
        if(!this.mrkVehiculos[marker_id]){
            this.mrkVehiculos[marker_id] = {};
            this.mrkVehiculos[marker_id].radio = obj;
            this.mrkVehiculos[marker_id].marker = new google.maps.Marker(
            {
                position: new google.maps.LatLng(this.mrkVehiculos[marker_id].radio.TrackerLat, this.mrkVehiculos[marker_id].radio.TrackerLong),
                map: capturador_api.map,
                title: this.mrkVehiculos[marker_id].radio.VehiculoPlaca+' / '+this.mrkVehiculos[marker_id].radio.RadioEtiqueta
            });
        }else{
            this.mrkVehiculos[marker_id].radio = obj;
            this.mrkVehiculos[marker_id].marker.setPosition(new google.maps.LatLng(this.mrkVehiculos[marker_id].radio.TrackerLat, this.mrkVehiculos[marker_id].radio.TrackerLong));            
        }

        this.validarRadio(this.mrkVehiculos[marker_id]);
        
        
    },

    tSaleJurisdiccion:null,

    programarSaleJurisdiccion: function(){
        if(capturador_api.grupoNotificacion.length > 0){
            var cant_grupo = 30;
            var arr = capturador_api.grupoNotificacion;
            capturador_api.grupoNotificacion = [];
            var grupos = arr.map( function(e,i){ 
                return i%cant_grupo===0 ? arr.slice(i,i+cant_grupo) : null; 
            })
            .filter(function(e){ return e; });

            $.each(grupos, function(idx, grupo){
                SipcopJS.post('transmisiones/call_salejurisdiccion',{
                    unidades: grupo
                }, function(data){
                    if(data.unidades>0){
                        SipcopJS.msj.success('Notificación', data.unidades+' unidades salieron de su jurisdicción');
                    }
                });
            });
        }
    }

}