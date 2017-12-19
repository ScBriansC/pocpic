var map_api = {
    map: null,
    iniLatitud: -9.7213829,
    iniLongitud: -73.5501206,
    iniZoom: 5,

    marker_added: null,


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

    usu_jurisdiccion:[],

    filtro_comisaria: {
        nombre:'',
        zona:''
    },

    clusterComisaria: null,
    clusterPatrullero: null,
    clusterMotorizado: null,
    clusterPatPie: null,

    
    mrkComisariaSelected:null,
    mrkComisarias: {},
    markersComisaria: [],

    mrkPatrullajeSelected:null,
    oMrkPatrullero: {},
    aMrkPatrullero: [],
    oMrkMotorizado: {},
    aMrkMotorizado: [],
    oMrkPatPie: {},
    aMrkPatPie: [],
    oMrkPuestoFijo: {},
    aMrkPuestoFijo: [],
    oMrkBarrioSeg: {},
    aMrkBarrioSeg: [],

    rutaPatrullaje: null,
    puntosRuta:[],
    centroRuta:null,
    ajaxPostPatrullaje: null,
    ajaxPostComisaria: null,
    taskCount: 0,
    k_it:'',

    taskError:[],

    fijar_coord: null,

    init: function(k_it, v_it){
        this.k_it=k_it;
        this.v_it=v_it;
        this.filtro[k_it] = v_it;
        this.filtro_comisaria[k_it] = v_it;
        this.map = new google.maps.Map($('#cnv_map')[0], {
            center: {
                lat: map_api.iniLatitud,
                lng: map_api.iniLongitud
            },
            zoom: map_api.iniZoom,
            // zoomControl: false,
            // scaleControl: true
            streetViewControl:false,
            mapTypeControl: false

        });

        this.oMrkPatrullero = {};
        this.aMrkPatrullero = [];
        this.oMrkMotorizado = {};
        this.aMrkMotorizado = [];
        this.oMrkPatPie = {};
        this.aMrkPatPie = [];
        this.oMrkPuestoFijo = {};
        this.aMrkPuestoFijo = [];
        this.oMrkBarrioSeg = {};
        this.aMrkBarrioSeg = [];

  

        google.maps.event.addListener(map_api.map, 'zoom_changed', function() {
          if (map_api.map.getZoom() < 5) map_api.map.setZoom(5);
          if(typeof denuncia_api!='undefined' && denuncia_api!=null){
            denuncia_api.zoom_event(map_api.map.getZoom());
          }
        });

        google.maps.event.addListenerOnce(this.map, 'idle', function() {
           google.maps.event.trigger(map_api.map, 'resize');
        });

        this.clusterComisaria = new MarkerClusterer(this.map, this.markersComisaria, {
            imagePath: 'assets/sipcop/img/m_comi_'
        });
         

        this.clusterPatrullero = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'assets/sipcop/img/m_poli_'
        });
        this.clusterMotorizado = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'assets/sipcop/img/m_radio_'
        });
        this.clusterPatPie = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'assets/sipcop/img/m_pie_'
        });
        this.clusterPuestoFijo = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'assets/sipcop/img/m_puestofijo_'
        });
        this.clusterBarrioSeg = new MarkerClusterer(this.map, this.markers, {
            imagePath: 'assets/sipcop/img/m_barrioseg_'
        });

        this.add_TaskLoader();
        this.aplicar_filtro(function(resp_data){
            map_api.end_TaskLoader();
            jurisdiccion_api.centrarJurisdiccion();
        });
        this.add_TaskLoader();
        this.aplicar_filtro_comisaria(function(resp_data){
            map_api.end_TaskLoader();
            jurisdiccion_api.centrarJurisdiccion();
        });
        this.add_TaskLoader();
        this.get_resumen('.cont-resumen',function(resp_data){
            map_api.end_TaskLoader();
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

    get_resumen: function(contenedor, callback){
        var filtro = false;

        if($('#ckPatrullero').is(':checked') || $('#ckMotorizado').is(':checked') || $('#ckPatpie').is(':checked') || $('#ckBarrioSeguro').is(':checked') || $('#ckPuestoFijo').is(':checked')){
            filtro = map_api.filtro;
            filtro.fecha = $('#txtFecha').val();
            filtro.horaini = $('#txtHoraIni').val();
            filtro.horafin = $('#txtHoraFin').val();
            filtro.institucion = 0;
        }


        if(filtro){
             $.post('admin/home/json_resumen',filtro, function(data){

                var cad = '<table width="100%" border="0">'+
                            '<thead><tr><th>Turno</th>';
                            if($('#ckPatrullero').is(':checked')){
                                cad+='<th style="text-align:center">Patrulleros</th>';
                            }
                            if($('#ckMotorizado').is(':checked')){
                                cad+='<th style="text-align:center">Motorizados</th>';
                            }
                            if($('#ckPatpie').is(':checked')){
                                cad+='<th style="text-align:center">Pat. Pie</th>';
                            }
                            if($('#ckPuestoFijo').is(':checked')){
                                cad+='<th style="text-align:center">P. Fijo</th>';
                            }
                            if($('#ckBarrioSeguro').is(':checked')){
                                cad+='<th style="text-align:center">B. Seg.</th>';
                            }

                            cad+='</tr></thead>'+
                            '<tbody>';
                 $.each(data.resumen_turno, function(idx, obj){
                        cad += '<tr><td>'+obj.TURNO+'</td>';
                        if($('#ckPatrullero').is(':checked')){
                            cad += '<td align="center">'+(parseInt(obj.TotalPatrullero)+parseInt(obj.TotalPatInt))+'/'+(parseInt(data.resumen_total.TotalPatrullero)+parseInt(data.resumen_total.TotalPatInt))+'</td>';
                        }
                        if($('#ckMotorizado').is(':checked')){
                            cad += '<td align="center">'+obj.TotalMotorizado+'/'+data.resumen_total.TotalMotorizado+'</td>';
                        }
                        if($('#ckPatpie').is(':checked')){
                            cad += '<td align="center">'+obj.TotalPatPie+'/'+data.resumen_total.TotalPatPie+'</td>';
                        }
                        if($('#ckPuestoFijo').is(':checked')){
                            cad += '<td align="center">'+obj.TotalPuestoFijo+'/'+data.resumen_total.TotalPuestoFijo+'</td>';
                        }
                        if($('#ckBarrioSeguro').is(':checked')){
                            cad += '<td align="center">'+obj.TotalBarrioSeg+'/'+data.resumen_total.TotalBarrioSeg+'</td>';
                        }
                        cad += '</tr>';
                });

                cad += '</tbody></table>';
                $(contenedor).html(cad);
                if(callback){
                    callback({resp:'ok', status:'1'});
                }

            },'json').fail(function(err){
                if(callback){
                    callback({err:err, status:'0'});
                }
            });
        }else{
            var cad = '<table width="100%" border="0"><tbody>';
            cad += '<tr><td align="center">No tiene activa alguna capa de las unidades</td></tr>';
            cad += '</tbody></table>';
            $(contenedor).html(cad);

            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }
    },


    get_resumen_comisaria: function(institucion, contenedor, callback){
        var filtro = false;

        if($('#ckPatrullero').is(':checked') || $('#ckMotorizado').is(':checked') || $('#ckPatpie').is(':checked') || $('#ckBarrioSeguro').is(':checked') || $('#ckPuestoFijo').is(':checked')){
            filtro = map_api.filtro;
        }

        filtro.fecha = $('#txtFecha').val();
        filtro.horaini = $('#txtHoraIni').val();
        filtro.horafin = $('#txtHoraFin').val();
        filtro.institucion = institucion;

        $(contenedor).html('');

        if(filtro){
             $.post('admin/home/json_resumen',filtro, function(data){

                var cad = '<table width="100%" border="0">'+
                            '<thead><tr><th>Turno</th>';
                            if($('#ckPatrullero').is(':checked')){
                                cad+='<th style="text-align:center">Patrulleros</th>';
                            }
                            if($('#ckMotorizado').is(':checked')){
                                cad+='<th style="text-align:center">Motorizados</th>';
                            }
                            if($('#ckPatpie').is(':checked')){
                                cad+='<th style="text-align:center">Pat. Pie</th>';
                            }
                            if($('#ckPuestoFijo').is(':checked')){
                                cad+='<th style="text-align:center">P. Fijo</th>';
                            }
                            if($('#ckBarrioSeguro').is(':checked')){
                                cad+='<th style="text-align:center">B. Seg.</th>';
                            }

                            cad+='</tr></thead>'+
                            '<tbody>';
                 $.each(data.resumen_turno, function(idx, obj){
                        cad += '<tr><td>'+obj.TURNO+'</td>';
                        if($('#ckPatrullero').is(':checked')){
                            cad += '<td align="center">'+(parseInt(obj.TotalPatrullero)+parseInt(obj.TotalPatInt))+'/'+(parseInt(data.resumen_total.TotalPatrullero)+parseInt(data.resumen_total.TotalPatInt))+'</td>';
                        }
                        if($('#ckMotorizado').is(':checked')){
                            cad += '<td align="center">'+obj.TotalMotorizado+'/'+data.resumen_total.TotalMotorizado+'</td>';
                        }
                        if($('#ckPatpie').is(':checked')){
                            cad += '<td align="center">'+obj.TotalPatPie+'/'+data.resumen_total.TotalPatPie+'</td>';
                        }
                        if($('#ckPuestoFijo').is(':checked')){
                            cad += '<td align="center">'+obj.TotalPuestoFijo+'/'+data.resumen_total.TotalPuestoFijo+'</td>';
                        }
                        if($('#ckBarrioSeguro').is(':checked')){
                            cad += '<td align="center">'+obj.TotalBarrioSeg+'/'+data.resumen_total.TotalBarrioSeg+'</td>';
                        }
                        cad += '</tr>';
                });

                cad += '</tbody></table>';
                $(contenedor).html(cad);
                if(callback){
                    callback({resp:'ok', status:'1'});
                }

            },'json').fail(function(err){
                if(callback){
                    callback({err:err, status:'0'});
                }
            });
        }else{
            var cad = '<table width="100%" border="0"><tbody>';
            cad += '<tr><td align="center">No tiene activa alguna capa de las unidades</td></tr>';
            cad += '</tbody></table>';
            $(contenedor).html(cad);

            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }

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


    descargar_patrullaje : function(callback){
        if(map_api.ajaxPostPatrullaje!= null && map_api.ajaxPostPatrullaje.readyState ==1)
        {
            map_api.ajaxPostPatrullaje.abort();
            map_api.ajaxPostPatrullaje = null;
        }


        map_api.limpiarRuta();

       /*if(map_api.mrkComisariaSelected){
            
            map_api.clearMarkersPatrullero();
            map_api.clearMarkersMotorizado();
            map_api.clearMarkersPatpie();

            if(callback){
                callback({resp:'ok', status:'1'});
            }
           return false; 
        }*/
        

        map_api.filtro.flgpatrullero = $('#ckPatrullero').is(':checked');
        map_api.filtro.flgmotorizado = $('#ckMotorizado').is(':checked');
        map_api.filtro.flgpatpie = $('#ckPatpie').is(':checked');
        map_api.filtro.flgpuestofijo = $('#ckPuestoFijo').is(':checked');
        map_api.filtro.flgbarrioseg = $('#ckBarrioSeguro').is(':checked');

        map_api.ajaxPostPatrullaje = $.post('admin/home/json_patrullaje',map_api.filtro, function(data){

            var mostrar_vehi = $('#ckPatrullero').is(':checked');
            var mostrar_moto = $('#ckMotorizado').is(':checked');
            var mostrar_patpie = $('#ckPatpie').is(':checked');
            var mostrar_PuestoFijo = $('#ckPuestoFijo').is(':checked');
            var mostrar_barrioseg = $('#ckBarrioSeguro').is(':checked');
            if(!mostrar_vehi && !mostrar_moto && !mostrar_patpie && !mostrar_PuestoFijo && !mostrar_barrioseg){
                return false;
            }

            map_api.centroRuta = new google.maps.LatLngBounds();

            $('.cont-vehiculo').html('');
            $('.restult-vehiculo').html('0');
            
            var hmtl_vehiculo = '';
            var tab_comisaria = $('.panel-tab-tabs a.tab-comisaria').hasClass('active');
            
            if(data.patrullaje  && data.patrullaje.length > 0){
                var _idcomisaria_act = -1;
                $.each(data.patrullaje, function(idx, objx){

                    var mrk_validado = ((objx.PatrullajeID == 1 || objx.PatrullajeID == 4) && $('#ckPatrullero').is(':checked')==true) || 
                                       ((objx.PatrullajeID == 2) && $('#ckMotorizado').is(':checked')==true) || 
                                       ((objx.PatrullajeID == 3) && $('#ckPatpie').is(':checked')==true)|| 
                                       ((objx.PatrullajeID == 6) && $('#ckBarrioSeguro').is(':checked')==true)|| 
                                       ((objx.PatrullajeID == 7) && $('#ckPuestoFijo').is(':checked')==true);



                    if(mrk_validado){
                        if(_idcomisaria_act!=objx.ComisariaID){
                            if(_idcomisaria_act!=objx.ComisariaID && _idcomisaria_act!=-1){
                                hmtl_vehiculo += '</div></div>';
                            }
                            hmtl_vehiculo += '<div class="panel"><div class="panel-vehi-cabecera"> <span class="cab_vehi">'+objx.ComisariaNombre+'</span> </div> <div class="panel-body">';
                            _idcomisaria_act=objx.ComisariaID;
                        }

                        var mrk = null;
                        var mrk1 = map_api.oMrkPatrullero['Patrullaje_' + objx.DispoGPS];
                        var mrk2 = map_api.oMrkMotorizado['Patrullaje_' + objx.DispoGPS];
                        var mrk3 = map_api.oMrkPatPie['Patrullaje_' + objx.DispoGPS];
                        var mrk4 = map_api.oMrkPuestoFijo['Patrullaje_' + objx.DispoGPS];
                        var mrk5 = map_api.oMrkBarrioSeg['Patrullaje_' + objx.DispoGPS];

                        if(mrk1){
                            mrk = mrk1;
                        }else if(mrk2){
                            mrk = mrk2;
                        }else if(mrk3){
                            mrk = mrk3;
                        }else if(mrk4){
                            mrk = mrk4;
                        }else if(mrk5){
                            mrk = mrk5;
                        }

                        if (mrk != null && typeof mrk !== 'undefined' && typeof mrk.args !== 'undefined' && typeof mrk.args.oPatrullaje !== 'undefined') {
                            mrk.latlng = new google.maps.LatLng(parseFloat(objx.TrackerLat), parseFloat(objx.TrackerLong));       
                            mrk.args.oPatrullaje =  objx;                    
                            mrk.draw();
                        } 
                        else {
                            mrk = map_api.agregarMarcadorPatrullaje(objx);
                        }

                        mrk.act();

                        if((map_api.filtro.tipo_filtro == 0 || map_api.filtro.tipo_filtro == 4) && (!tab_comisaria || data.comisaria)){
                            map_api.centroRuta.extend(mrk.latlng);
                        }

                        var placa_lbl = '';

                        if(objx.VehiculoPlaca && $.trim(objx.VehiculoPlaca)!=''){
                            placa_lbl += objx.VehiculoPlaca;
                        }

                        if(objx.DispoDescripcion && $.trim(objx.DispoDescripcion)!=''){
                            placa_lbl += (placa_lbl!=''?' /<br>':'') + objx.DispoDescripcion;
                        }

                        hmtl_vehiculo += '<a class="itm-vehiculo" href="javascript:;" id="Patrullaje_'+ objx.DispoGPS +'"><span class="lbl-placa">'+'<img src="'+mrk.getIcoImg()+'" style="vertical-align:middle;margin-right:4px;" width="15" height="15" />'+placa_lbl+'</span><span class="lbl-hora">'+Date.parseExact(objx.TrackerHora,'HH:mm:ss').toString('hh:mm tt')+'</span><div class="clear"></div></a>';
                       
                    }
                });

                hmtl_vehiculo += '</div></div>';

                $('.cont-vehiculo').html(hmtl_vehiculo);
                $('.restult-vehiculo').html(data.patrullaje.length);
            }
                  
            if(data.ruta && (map_api.filtro.tipo_filtro == 2 || map_api.filtro.tipo_filtro == 3)){
                map_api.dibujarRuta(data.ruta, '#DA3A26');                           
            }

            if(!map_api.fijar_coord){
                if(map_api.filtro.tipo_filtro == 0 || map_api.filtro.tipo_filtro == 4){
                    map_api.filtro.tipo_filtro = 1;
                    if(data.patrullaje.length == 1){
                        var listener = google.maps.event.addListener(map_api.map, "idle", function() { 
                          if (map_api.map.getZoom() > 12) map_api.map.setZoom(12); 
                          google.maps.event.removeListener(listener); 
                        });
                    }
                    if(!tab_comisaria || data.comisaria){
                        if(!map_api.centroRuta.isEmpty()){
                            map_api.map.fitBounds(map_api.centroRuta);
                        }else{
                            map_api.centrarMapa();
                        }                    
                    }
                }
            }else{
                map_api.map.setZoom(map_api.fijar_coord.zoom);
                map_api.map.setCenter(map_api.fijar_coord.latlng);
            }

            map_api.ajaxPostPatrullaje = null;

            if(callback){
                callback({resp:'ok', status:'1', data:data.patrullaje});
            }
         
        }, 'json').fail(function(err){
            if(callback){
                callback({err:err, status:'0'});
            }
            map_api.ajaxPostPatrullaje = null;
        });      

    },

    ruta_demo:null,
    dibujarRuta: function(ruta, color){
        var coordenadas = [];
        map_api.ruta_demo = ruta;
        map_api.pos_demo = 0;
        map_api.centroRuta = new google.maps.LatLngBounds(); 
        var item_count = 1;
        $.each(ruta, function(idx, objx){
            map_api.centroRuta.extend(new google.maps.LatLng(parseFloat(objx.TrackerLat), parseFloat(objx.TrackerLong)));
            coordenadas.push({lat: parseFloat(objx.TrackerLat), lng: parseFloat(objx.TrackerLong)});
            var marker =  new MarkerWithLabel({
                position: new google.maps.LatLng(parseFloat(objx.TrackerLat), parseFloat(objx.TrackerLong)),
                map: map_api.map,
                labelContent: '<b>'+objx.TrackerHora+'</b>',
                labelAnchor: new google.maps.Point(0, 0),
                labelClass: "lblmarker-hide",
                labelStyle: {opacity: 1},
                icon:((idx == map_api.ruta_demo.length - 1)?'assets/sipcop/img/transp_ini.png':'assets/sipcop/img/transp.png')
              });

            google.maps.event.addListener(marker, "click", function (e) { 
                if(map_api.pickHoraLabel){
                    map_api.pickHoraLabel.set('labelClass', 'lblmarker-hide ');
                    map_api.pickHoraLabel = null;
                }
                this.set('labelClass', 'lblmarker');
                map_api.pickHoraLabel = this;
                map_api.mostrarStreetView(this.position.lat(),this.position.lng());
             });
            google.maps.event.addListener(marker, "mouseover", function (e) { 
                if(map_api.pickHoraLabel){
                    map_api.pickHoraLabel.set('labelClass', 'lblmarker-hide ');
                    map_api.pickHoraLabel = null;
                }
                this.set('labelClass', 'lblmarker');
                map_api.pickHoraLabel = this;
            });
            google.maps.event.addListener(marker, "mouseout", function (e) { 
                this.set('labelClass', 'lblmarker-hide '); 
                map_api.pickHoraLabel = null;
            });
            
            map_api.puntosRuta.push(marker);
            item_count++;
        });


        map_api.rutaPatrullaje = new google.maps.Polyline({
            path: coordenadas,
            geodesic: true,
            strokeColor: color,
            strokeOpacity: 1.0,
            strokeWeight: 4
        });

        map_api.rutaPatrullaje.setMap(this.map);

        if(!map_api.fijar_coord){
            if(map_api.filtro.tipo_filtro == 2){
                map_api.map.fitBounds(map_api.centroRuta);
                map_api.filtro.tipo_filtro = 3;
            }
        }else{
            map_api.map.setZoom(map_api.fijar_coord.zoom);
            map_api.map.setCenter(map_api.fijar_coord.latlng);
        }
        
        $(map_api.mrkPatrullajeSelected.args.div).show();
    },

    pickHoraLabel:null,

    descargar_comisaria : function(callback){

        var mostrar_result_comi = $('#ckComisaria').is(':checked');

        if(map_api.ajaxPostComisaria!=null && map_api.ajaxPostComisaria.readyState == 1){
            map_api.ajaxPostComisaria.abort();
            map_api.ajaxPostComisaria = null;
        }


        $('.cont-comisaria').html('');
        $('.restult-comisaria').html('0');
        var hmtl_comisaria = '';
        var tab_comisaria = $('.panel-tab-tabs a.tab-comisaria').hasClass('active') || $('#ckComisaria').is(':checked');


        map_api.limpiarRuta();
        map_api.ajaxPostComisaria = $.post('admin/home/json_comisaria',map_api.filtro_comisaria, function(data){
            map_api.ajaxPostComisaria = null;
            map_api.centroRutaComisaria = new google.maps.LatLngBounds();
            if(data.comisarias  && data.comisarias.length > 0){
                $.each(data.comisarias, function(idx, objx){
                    var mrk = map_api.mrkComisarias["Comisaria_" + objx.ComisariaID];
                    if (mrk != null && typeof mrk !== 'undefined' && typeof mrk.args !== 'undefined' && typeof mrk.args.oComisaria !== 'undefined') {
                        mrk.latlng = new google.maps.LatLng(parseFloat(objx.ComisariaLat), parseFloat(objx.ComisariaLong));                            
                        mrk.draw();
                    } else {
                        mrk = map_api.agregarMarcadorComisaria(objx);
                    }

                    var cant_dispo = 0;
                    var cant_dispo_total = 0;

                    if($('#ckPatrullero').is(':checked')){
                        cant_dispo += parseInt(objx.ActualPatrullero) + parseInt(objx.ActualPatInt);
                        cant_dispo_total += parseInt(objx.TotalPatrullero) + parseInt(objx.TotalPatInt);
                    }

                    if($('#ckMotorizado').is(':checked')){
                        cant_dispo += parseInt(objx.ActualMotorizado);
                        cant_dispo_total += parseInt(objx.TotalMotorizado);
                    }

                    if($('#ckPatpie').is(':checked')){
                        cant_dispo += parseInt(objx.ActualPatPie);
                        cant_dispo_total += parseInt(objx.TotalPatPie);
                    }

                    if($('#ckPuestoFijo').is(':checked')){
                        cant_dispo += parseInt(objx.ActualPuestoFijo);
                        cant_dispo_total += parseInt(objx.TotalPuestoFijo);
                    }

                    if($('#ckBarrioSeguro').is(':checked')){
                        cant_dispo += parseInt(objx.ActualBarrioSeg);
                        cant_dispo_total += parseInt(objx.TotalBarrioSeg);
                    }


                    var pertenece = false;
                    $.each(map_api.usu_jurisdiccion, function(i_juris, o_juris){
                        if(parseInt(o_juris) == parseInt(objx.ComisariaID)){
                            pertenece = true;
                        }
                    });

                    hmtl_comisaria += '<a class="itm-comisaria '+((pertenece)?'comisaria-activa':'')+'" id="Comisaria_' + objx.ComisariaID+ '" href="javascript:;">'+objx.ComisariaNombre+' ('+(cant_dispo)+'/'+(cant_dispo_total)+')</a>';                         
                    if(tab_comisaria){
                        map_api.centroRutaComisaria.extend(mrk.latlng);
                    }
                });
                $('.cont-comisaria').html(hmtl_comisaria);
                $('.restult-comisaria').html(data.comisarias.length);
            }

            if(!map_api.fijar_coord){
                if(data.comisarias.length <300 && tab_comisaria){
                    if(map_api.centroRuta && !map_api.centroRuta.isEmpty()){
                        map_api.map.fitBounds(map_api.centroRutaComisaria);
                    }else{
                        map_api.centrarMapa();
                    }
                }else{
                    map_api.centrarMapa();
                }
            }else{
                map_api.map.setZoom(map_api.fijar_coord.zoom);
                map_api.map.setCenter(map_api.fijar_coord.latlng);
            }
        

            map_api.taskCount++;
            if(callback){
                callback({resp:'ok', status:'1'});
            }

        }, 'json').fail(function(err){

            if(callback){
                callback({err:err, status:'0'});
            }

        });
    },


    clearMarkersPatrullero: function (){
        if(map_api.filtro.tipo_filtro!=3 &&  map_api.clusterPatrullero){
            $.each(map_api.oMrkPatrullero, function(idx, objx) {
                if (typeof objx === 'VehiculoMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.oMrkPatrullero = {};
            map_api.clusterPatrullero.removeMarkers(this.aMrkPatrullero,true);
            map_api.aMrkPatrullero.length = 0;
            delete map_api.aMrkPatrullero;
            map_api.aMrkPatrullero = [];
            map_api.clusterPatrullero.repaint();
        }
    },
    clearMarkersMotorizado: function (){
        if(map_api.filtro.tipo_filtro!=3 && map_api.clusterMotorizado){
            $.each(map_api.oMrkMotorizado, function(idx, objx) {
                if (typeof objx === 'VehiculoMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.oMrkMotorizado = {};
            map_api.clusterMotorizado.removeMarkers(this.aMrkMotorizado,true);
            map_api.aMrkMotorizado.length = 0;
            delete map_api.aMrkMotorizado;
            map_api.aMrkMotorizado = [];
            map_api.clusterMotorizado.repaint();
        }
    },
    clearMarkersPatpie: function (){
        if(map_api.filtro.tipo_filtro!=3 && map_api.clusterPatPie){
            $.each(map_api.oMrkPatPie, function(idx, objx) {
                if (typeof objx === 'VehiculoMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.oMrkPatPie = {};
            map_api.clusterPatPie.removeMarkers(this.aMrkPatPie,true);
            map_api.aMrkPatPie.length = 0;
            delete map_api.aMrkPatPie;
            map_api.aMrkPatPie = [];
            map_api.clusterPatPie.repaint();
        }
    },
    clearMarkersPuestoFijo: function (){
        if(map_api.filtro.tipo_filtro!=3 && map_api.clusterPuestoFijo){
            $.each(map_api.oMrkPuestoFijo, function(idx, objx) {
                if (typeof objx === 'VehiculoMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.oMrkPuestoFijo = {};
            map_api.clusterPuestoFijo.removeMarkers(this.aMrkPuestoFijo,true);
            map_api.aMrkPuestoFijo.length = 0;
            delete map_api.aMrkPuestoFijo;
            map_api.aMrkPuestoFijo = [];
            map_api.clusterPuestoFijo.repaint();
        }
    },
    clearMarkersBarrioSeg: function (){
        if(map_api.filtro.tipo_filtro!=3 && map_api.clusterBarrioSeg){
            $.each(map_api.oMrkBarrioSeg, function(idx, objx) {
                if (typeof objx === 'VehiculoMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.oMrkBarrioSeg = {};
            map_api.clusterBarrioSeg.removeMarkers(this.aMrkBarrioSeg,true);
            map_api.aMrkBarrioSeg.length = 0;
            delete map_api.aMrkBarrioSeg;
            map_api.aMrkBarrioSeg = [];
            map_api.clusterBarrioSeg.repaint();
        }
    },

    clearMarkersComisaria: function() {
        if(map_api.clusterComisaria){
            $.each(map_api.mrkComisarias, function(idx, objx) {
                if (typeof objx === 'ComisariaMrkr') {
                    objx.remove();
                } else {
                    objx.setMap(null);
                }
            });
            map_api.mrkComisarias = {};
            map_api.clusterComisaria.removeMarkers(this.markersComisaria,true);
            map_api.markersComisaria.length = 0;
            delete map_api.markersComisaria;
            map_api.markersComisaria = [];
            map_api.clusterComisaria.repaint();
        }
    },


    limpiarRuta: function(){
        if(map_api.filtro.tipo_filtro!=3){
            if(map_api.rutaPatrullaje!=null && typeof map_api.rutaPatrullaje!=='undefined'){
                map_api.rutaPatrullaje.setMap(null);
                $.each(map_api.puntosRuta, function(idx, objx){
                    objx.setMap(null);
                });
                map_api.rutaPatrullaje = null;
                delete map_api.puntosRuta;
                map_api.puntosRuta = [];
            }
        }
    },

    agregarMarcadorPatrullaje: function(obj) {

        var htmlResult;
        var color = '#3d3d3d';

        var pertenece = false;

        $.each(map_api.usu_jurisdiccion, function(i_juris, o_juris){
            if(parseInt(o_juris) == parseInt(obj.ComisariaID)){
                pertenece = true;
            }
        });

        if(pertenece){
            color = '#20a8d8'; 
            // color= '#b70f5d';
        }

        var marker_id = 'Patrullaje_' +  obj.DispoGPS;
        var overlay = new VehiculoMrkr(new google.maps.LatLng(obj.TrackerLat, obj.TrackerLong), map_api.map, {
            marker_id: marker_id,
            colour: 'Red',
            oPatrullaje: obj,
            oResult: htmlResult,
            color: color,
            callback: function(mrk) {
                map_api.mostrarInfoPatrullaje(mrk);                
                /*if($(window).width() < 500){
                    map_api.centrarMapa(mrk.latlng, 18);
                }else{
                    map_api.centrarMapa(mrk.latlng, 18);
                }*/
            }
        });

        
        if(obj.PatrullajeID == 1 || obj.PatrullajeID == 4)
        {
            this.oMrkPatrullero[marker_id] = overlay;
            this.clusterPatrullero.addMarker(overlay);
            this.aMrkPatrullero.push(overlay);
        }else if(obj.PatrullajeID == 2)
        {
            this.oMrkMotorizado[marker_id] = overlay;
            this.clusterMotorizado.addMarker(overlay);
            this.aMrkMotorizado.push(overlay);
        }else if(obj.PatrullajeID == 3){
            this.oMrkPatPie[marker_id] = overlay;
            this.clusterPatPie.addMarker(overlay);
            this.aMrkPatPie.push(overlay);
        }else if(obj.PatrullajeID == 6){
            this.oMrkBarrioSeg[marker_id] = overlay;
            this.clusterBarrioSeg.addMarker(overlay);
            this.aMrkBarrioSeg.push(overlay);
        }else if(obj.PatrullajeID == 7){
            this.oMrkPuestoFijo[marker_id] = overlay;
            this.clusterPuestoFijo.addMarker(overlay);
            this.aMrkPuestoFijo.push(overlay);
        }
        
        overlay.color = color;
        return overlay;
    },

    agregarMarcadorComisaria: function(obj) {
        var htmlResult;
        var color = '#1bbc9b'; 
        var marker_id = 'Comisaria_' + obj.ComisariaID;

        var pertenece = false;
        $.each(map_api.usu_jurisdiccion, function(i_juris, o_juris){
            if(parseInt(o_juris) == parseInt(obj.ComisariaID)){
                pertenece = true;
            }
        });

        if(pertenece){
            color = '#2c6c5e'; 
        }


        var overlay = new ComisariaMrkr(new google.maps.LatLng(obj.ComisariaLat, obj.ComisariaLong), map_api.map, {
            marker_id: marker_id,
            colour: 'Red',
            oComisaria: obj,
            oResult: htmlResult,
            color: color,
            callback: function(mrk) {
                map_api.mostrarInfoComisaria(mrk);                
                /*if($(window).width() < 500){
                    map_api.centrarMapa(mrk.latlng, 10);
                }else{
                    map_api.centrarMapa(mrk.latlng, 10);
                }*/
            }
        });
        this.mrkComisarias[marker_id] = overlay;
        this.clusterComisaria.addMarker(overlay);
        this.markersComisaria.push(overlay);
        overlay.color = color;
        return overlay;
    },

    mostrarInfoPatrullaje: function(obj_val, callback){
        var mrk = null;
        if(typeof obj_val == 'string'){
            var mrk1 = map_api.oMrkPatrullero[obj_val];
            var mrk2 = map_api.oMrkMotorizado[obj_val];
            var mrk3 = map_api.oMrkPatPie[obj_val];
            var mrk4 = map_api.oMrkPuestoFijo[obj_val];
            var mrk5 = map_api.oMrkBarrioSeg[obj_val];
            if(mrk1 != null){
                mrk = mrk1;
            }else if(mrk2 != null){
                mrk = mrk2;
            }else if(mrk3 != null){
                mrk = mrk3;
            }else if(mrk4 != null){
                mrk = mrk4;
            }else if(mrk5 != null){
                mrk = mrk5;
            }
        }else{
            mrk = obj_val;
        }
        
        
        if(!map_api.fijar_coord){
            map_api.centrarMapa(mrk.latlng, 22);
        }else{
            map_api.map.setZoom(map_api.fijar_coord.zoom);
            map_api.map.setCenter(map_api.fijar_coord.latlng);
        }
        var vh = mrk.args.oPatrullaje;
        var filtro_vh = {};
        filtro_vh.dispogps = vh.DispoGPS;
        filtro_vh.fecha =map_api.filtro.fecha;
        filtro_vh.horaini =map_api.filtro.horaini;
        filtro_vh.horafin =map_api.filtro.horafin;
        filtro_vh[this.k_it]=map_api.filtro[this.k_it];


        $('.filtro-info').hide();
        $('.vehiculo-info').show();
        $('.comisaria-info').hide();

        map_api.mrkPatrullajeSelected = mrk;


            if(vh.DispoDescripcion && vh.DispoDescripcion!='' && vh.DispoDescripcion!='null'){
                $('.vehiculo-info .ref').show();
                $('.vehiculo-info .ref span').html(vh.DispoDescripcion);
            }else{
                $('.vehiculo-info .ref').hide();
                $('.vehiculo-info .ref span').html('');
            }

            if(vh.VehiculoPlaca && vh.VehiculoPlaca!='' && vh.VehiculoPlaca!='null'){
                $('.vehiculo-info .placa').show();
                $('.vehiculo-info .placa span').html(vh.VehiculoPlaca);
            }else{
                $('.vehiculo-info .placa').hide();
                $('.vehiculo-info .placa span').html('');
            }

            if(vh.RadioID && vh.RadioID!='' && vh.RadioID!='null'){
                $('.vehiculo-info .etiqueta').show();
                $('.vehiculo-info .etiqueta span').html(vh.RadioID);
            }else{
                $('.vehiculo-info .etiqueta').hide();
                $('.vehiculo-info .etiqueta span').html('');
            }

            $('.vehiculo-info .comisaria span').html(vh.ComisariaNombre);


            $('.vehiculo-info .departamento span').html('').parent().hide();
            $('.vehiculo-info .provincia span').html('').parent().hide();
            $('.vehiculo-info .distrito span').html('').parent().hide();
            $('.vehiculo-info .fecha span').html(vh.TrackerFecha);
            $('.vehiculo-info .horaini span').html(Date.parseExact($.trim(vh.TrackerHoraIni),$.trim('HH:mm:ss')).toString($.trim('hh:mm tt'))).parent().show();
            $('.vehiculo-info .horafin span').html(Date.parseExact($.trim(vh.TrackerHora),$.trim('HH:mm:ss')).toString($.trim('hh:mm tt')));

            
            $('.vehiculo-info .velocidad span').html('').parent().hide();
            $('.vehiculo-info .distancia span').html('').parent().hide();

            $('.vehiculo-info .direccion').hide();
            $('.vehiculo-info .direccion span').html('...');
            
            map_api.buscarDireccion(vh.TrackerLat, vh.TrackerLong, function(direccion){
                if(direccion && direccion!=''){
                    $('.vehiculo-info .direccion').show();
                    $('.vehiculo-info .direccion span').html(direccion);
                }
            });

            if(vh.Indicador)
            {
                if(vh.Indicador == 2){
                    $('.vehiculo-info .div-comision').html('<center><a class="btn-comision" id="btn-comisionon" href="javascript:;"><i class="fa fa-wheelchair" aria-hidden="true"></i> Activar Comision</a></center>');
                }
                else{
                    $('.vehiculo-info .div-comision').html('<center><a class="btn-comision" id="btn-comisionoff" href="javascript:;"><i class="fa fa-wheelchair" aria-hidden="true"></i> Activar Comision</a></center>');
                }
            }





        $.post('admin/home/json_info_dispogps',filtro_vh, function(data){
           
            $('.vehiculo-info .departamento span').html(data.UbigeoDepartamento).parent().show();
            $('.vehiculo-info .provincia span').html(data.UbigeoProvincia).parent().show();
            $('.vehiculo-info .distrito span').html(data.UbigeoDistrito).parent().show();
           // $('.vehiculo-info .fecha span').html(Date.parseExact($.trim(data.TrackerFecha),$.trim('yyyy-MM-dd')).toString($.trim('dd/MM/yyyy'))).parent().show();
          //  $('.vehiculo-info .horaini span').html(Date.parseExact($.trim(data.TrackerHoraIni),$.trim('HH:mm:ss')).toString($.trim('hh:mm tt'))).parent().show();
           // $('.vehiculo-info .horafin span').html(Date.parseExact($.trim(data.TrackerHoraFin),$.trim('HH:mm:ss')).toString($.trim('hh:mm tt'))).parent().show();

            
           // $('.vehiculo-info .velocidad span').html(data.TrackerVelocidad).parent().show();
            $('.vehiculo-info .distancia span').html(data.TrackerKm).parent().show();
            

            if(callback){
                callback({resp:'ok', status:'1'});
            }
        },'json').fail(function(err){
            if(callback){
                callback({err:err, status:'0'});
            }
        });
    },

    mostrarInfoComisaria: function(mrk,callback){

        map_api.filtro.dispogps = 0;

        var comi = mrk.args.oComisaria;
        var filtro_comi = {};
        filtro_comi.institucion = mrk.args.oComisaria.ComisariaID;
        filtro_comi[this.k_it]=map_api.filtro_comisaria[this.k_it];


        $.post('admin/home/json_info_comisaria',filtro_comi, function(data){

            $('.filtro-info').hide();
            $('.vehiculo-info').hide();
            $('.comisaria-info').show();

           
            if(data.ComisariaNombre && data.ComisariaNombre!='' && data.ComisariaNombre!='null'){
                $('.comisaria-info .nombre').show();
                $('.comisaria-info .nombre span').html(data.ComisariaNombre);
            }else{
                $('.comisaria-info .nombre').hide();
                $('.comisaria-info .nombre span').html('');
            }

            if(data.ComisariaTelf && data.ComisariaTelf!='' && data.ComisariaTelf!='null'){
                $('.comisaria-info .telefono').show();
                $('.comisaria-info .telefono span').html(data.ComisariaTelf);
            }else{
                $('.comisaria-info .telefono').hide();
                $('.comisaria-info .telefono span').html('');
            }

            if(data.ComisariaDependencia && data.ComisariaDependencia!='' && data.ComisariaDependencia!='null'){
                $('.comisaria-info .dependencia').show();
                $('.comisaria-info .dependencia span').html(data.ComisariaDependencia);
            }else{
                $('.comisaria-info .dependencia').hide();
                $('.comisaria-info .dependencia span').html('');
            }


            if(data.ComisariaClase && data.ComisariaClase!='' && data.ComisariaClase!='null'){
                $('.comisaria-info .clase').show();
                $('.comisaria-info .clase span').html(data.ComisariaClase);
            }else{
                $('.comisaria-info .clase').hide();
                $('.comisaria-info .clase span').html('');
            }


            if(data.ComisariaTipo && data.ComisariaTipo!='' && data.ComisariaTipo!='null'){
                $('.comisaria-info .tipo').show();
                $('.comisaria-info .tipo span').html(data.ComisariaTipo);
            }else{
                $('.comisaria-info .tipo').hide();
                $('.comisaria-info .tipo span').html('');
            }

            if(data.ComisariaCategoria && data.ComisariaCategoria!='' && data.ComisariaCategoria!='null'){
                $('.comisaria-info .categoria').show();
                $('.comisaria-info .categoria span').html(data.ComisariaCategoria);
            }else{
                $('.comisaria-info .categoria').hide();
                $('.comisaria-info .categoria span').html('');
            }

            if(data.MacroregNombre && data.MacroregNombre!='' && data.MacroregNombre!='null'){
                $('.comisaria-info .macroreg').show();
                $('.comisaria-info .macroreg span').html(data.MacroregNombre);
            }else{
                $('.comisaria-info .macroreg').hide();
                $('.comisaria-info .macroreg span').html('');
            }

            if(data.RegpolNombre && data.RegpolNombre!='' && data.RegpolNombre!='null'){
                $('.comisaria-info .regpol').show();
                $('.comisaria-info .regpol span').html(data.RegpolNombre);
            }else{
                $('.comisaria-info .regpol').hide();
                $('.comisaria-info .regpol span').html('');
            }

            if(data.DivterNombre && data.DivterNombre!='' && data.DivterNombre!='null'){
                $('.comisaria-info .divter').show();
                $('.comisaria-info .divter span').html(data.DivterNombre);
            }else{
                $('.comisaria-info .divter').hide();
                $('.comisaria-info .divter span').html('');
            }


            var obj = mrk.args.oComisaria;
             map_api.mrkComisariaSelected = mrk;
            map_api.get_resumen_comisaria(obj.ComisariaID, '.cont-resumen-comisaria', function(resp_data){

            });

           if(callback){
                    callback({resp:'ok', status:'1'});
                }

            },'json').fail(function(err){
            if(callback){
                callback({err:err, status:'0'});
            }
        });

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


        var mostrar_vehi = $('#ckPatrullero').is(':checked') || $('#ckMotorizado').is(':checked') || $('#ckPatpie').is(':checked') || $('#ckPuestoFijo').is(':checked') || $('#ckBarrioSeguro').is(':checked');
        var txtPlaca = $('#txtPlaca');
        var txtEtiqueta = $('#txtEtiqueta');
        var txtSerie = $('#txtSerie');


        if(!mostrar_vehi || ( ($('.panel-tab-tabs a.tab-comisaria').hasClass('active') && (map_api.filtro.institucion == 0)))){

            // map_api.clearMarkers();
            map_api.clearMarkersPatrullero();
            map_api.clearMarkersMotorizado();
            map_api.clearMarkersPatpie();
            map_api.clearMarkersPuestoFijo();
            map_api.clearMarkersBarrioSeg();
            if(map_api.filtro.institucion == 0){
                map_api.clearMarkersComisaria();
            }
            $('.cont-vehiculo').html('');
            $('.restult-vehiculo').html('0');
            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }else{
            // map_api.clearMarkers();
            map_api.clearMarkersPatrullero();
            map_api.clearMarkersMotorizado();
            map_api.clearMarkersPatpie();
            map_api.clearMarkersPuestoFijo();
            map_api.clearMarkersBarrioSeg();

            if(map_api.filtro.institucion == 0){
                map_api.clearMarkersComisaria();
            }

            map_api.filtro.fecha = $('#txtFecha').val();
            map_api.filtro.horaini = $('#txtHoraIni').val();
            map_api.filtro.horafin = $('#txtHoraFin').val();
            map_api.filtro.tipo_filtro = 0;
            map_api.filtro.dispogps = 0;
            map_api.filtro.dependencia = map_api.get_dependencia();
            //map_api.filtro.institucion = 0;
            map_api.filtro.descripcion = $('#txtDescripcion').val();
            map_api.filtro.placa = $('#txtPlaca').val();
            map_api.filtro.idradio = $('#txtRadio').val();
            map_api.filtro.serie = $('#txtSerie').val();
            map_api.filtro.reset = 1;

            this.descargar_patrullaje(function(resp_data){
                if(callback){
                    callback(resp_data);
                }
            });
        }

    },
    
    aplicar_filtro_comisaria: function(callback){
        var mostrar = $('#ckComisaria').is(':checked');
        if(!mostrar || ($('.panel-tab-tabs a.tab-patrullero').hasClass('active') || $('.panel-tab-tabs a.tab-motorizado').hasClass('active'))){
            map_api.clearMarkersComisaria();
            $('.cont-comisaria').html('');
            $('.restult-comisaria').html('0');
            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }else{
            // map_api.clearMarkers();
            map_api.clearMarkersPatrullero();
            map_api.clearMarkersMotorizado();
            map_api.clearMarkersPatpie();
            map_api.clearMarkersPuestoFijo();
            map_api.clearMarkersBarrioSeg();
            map_api.clearMarkersComisaria();
            map_api.filtro_comisaria.fecha = $('#txtFecha').val();
            map_api.filtro_comisaria.horaini = $('#txtHoraIni').val();
            map_api.filtro_comisaria.horafin = $('#txtHoraFin').val();
            map_api.filtro_comisaria.nombre = $('#txtComisariaNombre').val();
            map_api.filtro_comisaria.idtipodepen = $('#txtComisariaDependencia').val();
            map_api.filtro_comisaria.dependencia = map_api.get_dependencia();
            map_api.filtro_comisaria.zona = $('#txtComisariaZona').val();
            map_api.filtro_comisaria.division = $('#txtComisariaDivision').val();
            map_api.filtro_comisaria.clase = $('#txtComisariaClase').val();
            map_api.filtro_comisaria.tipo = $('#txtComisariaTipo').val();
            map_api.filtro_comisaria.categoria = $('#txtComisariaCategoria').val();
            map_api.filtro_comisaria.tipo_filtro = 0;
            map_api.filtro_comisaria.ubigeo =map_api.get_ubigeo();
            this.descargar_comisaria(function(resp_data){
                if(callback){
                    callback(resp_data);
                }
            });
        }
    },

    aplicar_ruta: function(callback){
        map_api.clearMarkersPatrullero();
        map_api.clearMarkersMotorizado();
        map_api.clearMarkersPatpie();
        map_api.clearMarkersBarrioSeg();
        map_api.clearMarkersPuestoFijo();

        var obj = map_api.mrkPatrullajeSelected.args.oPatrullaje;

        map_api.filtro.fecha = map_api.filtro.fecha;
        map_api.filtro.horaini = map_api.filtro.horaini;
        map_api.filtro.horafin = map_api.filtro.horafin;
        map_api.filtro.tipo_filtro = 2;
        map_api.filtro.dispogps = obj.DispoGPS;
        map_api.filtro.dependencia = map_api.get_dependencia();
        map_api.filtro.institucion = 0;
        map_api.filtro.descripcion = '';
        map_api.filtro.placa = '';
        map_api.filtro.idradio = '';
        map_api.filtro.serie = '';
        map_api.filtro.reset = 1;

        $('#btn-inirecorrido').show();
        $('#btn-finrecorrido').hide();

        
         $('#btnprimero').hide();
         $('#btnsegundo').show();


        this.descargar_patrullaje(function(resp_data){
            if(callback){
                callback(resp_data);
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

    mostrarStreetView: function(lat,lng){
        $('.sipcop-ui-modal-item').hide();
        $('.sipcop-ui-modal').fadeIn('fast');
        map_api.centrarDiv($('.sipcop-streetview').show());

        var ruta_sv = 'admin/home/v_StreetView?latlng='+lat+','+lng;
        $('.sipcop-streetview-frame iframe')[0].src = ruta_sv;
    },

    notificarJurisdiccionPatrullaje: function(oPatrullero, miJurisdiccion, anterior, actual, mute){
        var mrk = null;

        if(this.oMrkPatrullero['Patrullaje_'+oPatrullero.DispoGPS]){
            mrk = this.oMrkPatrullero['Patrullaje_'+oPatrullero.DispoGPS];
        }

        if(this.oMrkMotorizado['Patrullaje_'+oPatrullero.DispoGPS]){
            mrk = this.oMrkMotorizado['Patrullaje_'+oPatrullero.DispoGPS];
        }

        if(this.oMrkPatPie['Patrullaje_'+oPatrullero.DispoGPS]){
            mrk = this.oMrkPatPie['Patrullaje_'+oPatrullero.DispoGPS];
        }

        if(this.oMrkPuestoFijo['Patrullaje_'+oPatrullero.DispoGPS]){
            mrk = this.oMrkPuestoFijo['Patrullaje_'+oPatrullero.DispoGPS];
        }

        if(this.oMrkPuestoFijo['Patrullaje_'+oPatrullero.DispoGPS]){
            mrk = this.oMrkBarrioSeg['Patrullaje_'+oPatrullero.DispoGPS];
        }

        if(mrk){
            var dvMsj = $(mrk.args.div).find('.patrullero-msj');       
        
            //if(anterior!=actual){
                var texto = "";
                var txtColor = "";
                var txtBg = "";
                if(miJurisdiccion){
                    if(actual){
                        //texto = "Regresó";
                        txtColor = "#FFFFFF";
                        txtBg = "#94bd4d";
                    }else{
                        texto = "Salió";
                        txtColor = "#FFFFFF";
                        txtBg = "#ec8c08";
                        if(!mute){map_api.playAlarma1();}
                    }
                }else{
                    if(actual){
                        texto = "Entró";
                        txtColor = "#FFFFFF";
                        txtBg = "#333333";
                        if(!mute){map_api.playAlarma2();}
                    }else{
                        //texto = "Salió";
                        txtColor = "#333333";
                        txtBg = "#FFFFFF";
                    }
                }
                
                if(texto!=""){
                    $(dvMsj).text(texto).css({color:txtColor, background:txtBg}).show();
                }else{
                    $(dvMsj).hide();
                }
            //}
        }
    },
    playAlarma1: function(){
        document.getElementById('sndAlarma1').play();
    },

    playAlarma2: function(){
        document.getElementById('sndAlarma2').play();
    },

    playAlarma3: function(){
        document.getElementById('sndAlarma3').play();

    },

    tipoMapa: function(tipo){
        if(tipo == 0){
            map_api.map.setOptions({mapTypeId: 'roadmap'})
        }else if(tipo == 1){
            map_api.map.setOptions({mapTypeId: 'satellite'})
        }else if(tipo == 2){
            map_api.map.setOptions({mapTypeId: 'hybrid'})
        }
    },
    mostrarInfoCamara: function(mrk){
        $('.sipcop-ui-modal-item').hide();
        $('.sipcop-ui-modal').fadeIn('fast');
        map_api.centrarDiv($('.sipcop-streetview').show());

        var ruta_sv = 'admin/home/v_StreetView?latlng='+mrk.camara.CamaraLatitud+','+mrk.camara.CamaraLongitud;
        $('.sipcop-streetview-frame iframe')[0].src = ruta_sv;
    },

  mostrarInfoIncidencia: function(mrk){
        swal({
          title: "Detalle incidencia",
          text:  

            '<table class="table table-bordered">'+
            '<tr><td><b>Descripción:</b></td><td>'+mrk.incidencia.IncidenciaDescripcion+'</td></tr>'+
            '<tr><td><b>Dirección:</b></td><td>'+mrk.incidencia.IncidenciaDireccion+'</td></tr>'+
            '<tr><td><b>Tipo:</b></td><td>'+mrk.incidencia.IncidenciaTipo+'</td></tr>'+
            '<tr><td><b>Subtipo:</b></td><td>'+mrk.incidencia.IncidenciaSubtipo+'</td></tr>'+
            '<tr><td><b>Fecha:</b></td><td>'+mrk.incidencia.IncidenciaFecha+'</td></tr>'+
            '</table>',
          html: true
        });
    },
    cambiarCursor: function(op){
        if(op == 0){
            map_api.map.setOptions({draggableCursor:'pointer'});
        }else if(op == 1){
            map_api.map.setOptions({draggableCursor:'crosshair'});
        }
    },

    google_km: function(){
        var path = [];
        var polylineLength = 0;
        for (var i = 0; i < map_api.puntosRuta.length; i++) {
          var pointPath = map_api.puntosRuta[i].position;
          path.push(pointPath);
          if (i > 0) polylineLength += google.maps.geometry.spherical.computeDistanceBetween(path[i], path[i-1]);
        }
        return (polylineLength/1000);
    }


}  