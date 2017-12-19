var map_api = {
    map: null,
    iniLatitud: -9.7213829,
    iniLongitud: -73.5501206,
    iniZoom: 5,

    filtro: {
        fecha: '', //
        horaini:'',
        horafin:'',
        tipo_filtro: 0,
        dispogps:0,
        dependencia:0,
        institucion:0,
        descripcion:'',
        placa:'',
        idradio:'',
        serie:'',
        reset: 0,
    },

    mapacalor_capa: null,
    mapacalor_data: [],

    clusterDenuncia: null,

    aMarkers : [],


    taskError:[],

    usu_jurisdiccion: [],

    init: function(k_it, v_it){
        this.k_it=k_it;
        this.v_it=v_it;
        this.filtro[k_it] = v_it;
       
        this.map = new google.maps.Map($('#cnv_map')[0], {
            center: {
                lat: map_api.iniLatitud,
                lng: map_api.iniLongitud
            },
            zoom: map_api.iniZoom,
            streetViewControl:false,
            mapTypeControl: false

        });

        this.aMarkers = [];

        this.usu_jurisdiccion = [];


        google.maps.event.addListener(map_api.map, 'zoom_changed', function() {
          if (map_api.map.getZoom() < 5) map_api.map.setZoom(5);
          if(typeof denuncia_api!='undefined' && denuncia_api!=null){
            denuncia_api.zoom_event(map_api.map.getZoom());
          }
        });

        
        google.maps.event.addListener(map_api.map, 'zoom_changed', function() {
          if (map_api.map.getZoom() < 5) map_api.map.setZoom(5);
          if(typeof map_api!='undefined' && map_api!=null){
            map_api.zoom_event(map_api.map.getZoom());
          }
        });

        google.maps.event.addListenerOnce(this.map, 'idle', function() {
           google.maps.event.trigger(map_api.map, 'resize');
        });

        this.clusterDenuncia = new MarkerClusterer(this.map, this.markersComisaria, {
            imagePath: 'assets/sipcop/img/m_comi_'
        });

        this.add_TaskLoader();
        this.aplicar_filtro(function(resp_data){
            map_api.end_TaskLoader();
            jurisdiccion_api.centrarJurisdiccion();
        });

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
            map_api.taskError = [];
        }

        map_api.taskError.put(msj);
    },





    get_ubigeo: function (){
        var depa = $('#txtDepartamento').val();
        var prov = $('#txtProvincia').val();
        var dist = $('#txtDistrito').val();

        var ubigeo = '';

        if(dist!='0'){
            ubigeo = dist;
        }else if(prov!='0'){
            ubigeo = prov;
        }else if(depa!='0'){
            ubigeo = depa;
        }

        return ubigeo;
    },

    get_dependencia: function (){
        var macroreg = $('#txtMacroreg').val();
        var regpol = $('#txtRegpol').val();
        var divter = $('#txtDivter').val();

        var dependencia = '';

        if(divter!='0'){
            dependencia = divter;
        }else if(regpol!='0'){
            dependencia = regpol;
        }else if(macroreg!='0'){
            dependencia = macroreg;
        }
        return dependencia;
    },



    

    limpiarMapa: function (){
            $.each(map_api.aMarkers, function(idx, objx) {
                if (typeof objx === 'DenunciaMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.clusterDenuncia.removeMarkers(map_api.aMarkers,true);
            map_api.aMarkers.length = 0;
            delete map_api.aMarkers;
            map_api.aMarkers = [];
            map_api.clusterDenuncia.repaint();

            if(map_api.mapacalor_capa!=null){
                map_api.mapacalor_capa.setMap(null);                
            }

            if(map_api.mapacalor_data!=null){
                map_api.mapacalor_data.length = 0;
                delete map_api.mapacalor_data;
                map_api.mapacalor_data = [];              
            }
    },


    agregarMarker: function(obj) {

        var htmlResult;
        var color = '#3d3d3d';

        var overlay = new DenunciaMrkr(new google.maps.LatLng(obj.DenunciaLat, obj.DenunciaLong), map_api.map, {
            marker_id: 'Denuncia_'+obj.DenunciaID,
            oDenuncia: obj,
            oResult: htmlResult,
            color: color,
            callback: function(mrk) {
                map_api.mostrarInfoDenuncia(mrk);  
            }
        });

        this.clusterDenuncia.addMarker(overlay);
        this.aMarkers.push(overlay);          
        overlay.color = color;
        return overlay;
    },

    mostrarInfoDenuncia: function(obj_val, callback){
        var mrk = obj_val;
        
        //map_api.centrarMapa(mrk.latlng, 22);
        var denuncia = mrk.args.oDenuncia;
        var info_html = '<div style="width:100%;text-align:left;">';

    
            info_html += '<strong>Tipo:</strong> '+denuncia.TipoNombre + '<br>';
            info_html += '<strong>Sub tipo:</strong> '+denuncia.SubTipoNombre + '<br>';
            info_html += '<strong>Tipificación:</strong> '+denuncia.TipifNombre + '<br>';
            info_html += '<strong>Fecha Denuncia:</strong> '+denuncia.DenunciaFecha + '<br>';
            info_html += '<strong>Hora Denuncia:</strong> '+denuncia.DenunciaHora + '<br>';
            info_html += '<strong>Departamento:</strong> '+denuncia.UbigeoDepartamento + '<br>';
            info_html += '<strong>Provincia:</strong> '+denuncia.UbigeoProvincia + '<br>';
            info_html += '<strong>Distrito:</strong> '+denuncia.UbigeoDistrito + '<br>';
            info_html += '<strong>Comisaria:</strong> '+denuncia.ComisariaNombre + '<br>';
            info_html += '<strong>Macro Region:</strong> '+denuncia.MacroregNombre + '<br>';
            info_html += '<strong>Regpol:</strong> '+denuncia.RegpolNombre + '<br>';
            info_html += '<strong>Divter:</strong> '+denuncia.DivterNombre + '<br>';

        info_html += '</div>';

           swal({
              title: "Detalle denuncia",
              text: info_html,
              html: true
            });


        if(callback){
            callback({resp:'ok', status:'1'});
        }
    },

    buscarDireccion: function(lat, lng, callback){

        $.get('https://maps.googleapis.com/maps/api/geocode/json', {latlng: lat+','+lng}, function(data){
            if(typeof callback !=='undefined' && data && data.results && data.results.length > 0){
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

        this.limpiarMapa();

        if(map_api.ajaxPost!= null)
        {
            map_api.ajaxPost.abort();
            map_api.ajaxPost = null;
        }

        map_api.filtro.dependencia = this.get_dependencia();
        map_api.filtro.institucion = 0;
        map_api.filtro.fechaini = $('#txtFechaIni').val();
        map_api.filtro.fechafin = $('#txtFechaFin').val();
        map_api.filtro.horaini = $('#txtHoraIni').val();
        map_api.filtro.horafin = $('#txtHoraFin').val();
        map_api.filtro.modo = $('#cboModo').val();

        map_api.ajaxPost =  $.post('admin/mapa_delito/json_consultar',map_api.filtro, function(data){
            if(map_api.filtro.modo=='1'){
                $.each(data.denuncias, function(idx, objx){
                    map_api.mapacalor_data.push(new google.maps.LatLng(objx.DenunciaLat,objx.DenunciaLong));
                });

                map_api.mapacalor_capa = new google.maps.visualization.HeatmapLayer({
                  data: map_api.mapacalor_data,
                  radius: 11,
                  opacity: 0.7,
                  dissipating: true,
                  maxIntensity: 100
                });
                map_api.mapacalor_capa.setMap(map_api.map);

            }else{
                $.each(data.denuncias, function(idx, objx){
                    map_api.agregarMarker(objx);
                });
            }

            if(callback){
                callback({resp:'ok', status:'1'});
            }

        },'json').fail(function(err){
            if(callback){
                callback({resp:err, status:'0'});
            }
        });

        

    },

    centrarDiv: function(div){
        var child = $(div);
        var parent = $($(div).parent());
        child.css("position","absolute");
        child.css("top", ((parent.height() - child.outerHeight()) / 2) + parent.scrollTop() + "px");
        child.css("left", ((parent.width() - child.outerWidth()) / 2) + parent.scrollLeft() + "px");
    },

    zoom_event: function(z){
        if(typeof map_api.mapacalor_capa!='undefined' && map_api.mapacalor_capa!=null){
            if(z<=7){
                map_api.mapacalor_capa.setOptions({maxIntensity:100, radius: 10});
            }else if(z>=8 && z<=9){
                map_api.mapacalor_capa.setOptions({maxIntensity:150, radius: 10});
            }else if(z>=10 && z<=11){
                map_api.mapacalor_capa.setOptions({maxIntensity:200, radius: 15});
            }else if(z>=12 && z<=13){
                map_api.mapacalor_capa.setOptions({maxIntensity:30, radius: 20});
            }else if(z>=14 && z<=15){
                map_api.mapacalor_capa.setOptions({maxIntensity:10, radius: 12});
            }else{
                map_api.mapacalor_capa.setOptions({maxIntensity:2, radius: 15});
            }
        }
    
    }


}  