var hojaruta_api = {

    map: null,
    iniLatitud: -9.7213829,
    iniLongitud: -73.5501206,
    iniZoom: 5,
    polyline_region: null,
    tipo_archivo: null,
    k_it:'',
    markers_dibujo : [],
    polyline_base : [],
    countcoords: null,
    lastpoint: null,
    usu_jurisdiccion:[],
    mrkMiPosicion :[],
    arrTxtHora:[],
    arrTxtMotivo:[],
    mrkDatos :[],
    arrTotal:[],
    arrayDatos:[],
    cnt:0,

    init: function(k_it, v_it){
        this.k_it=k_it;
        this.v_it=v_it;
        this.map = new google.maps.Map($('#cnv_map')[0], {
            center: {
                lat: hojaruta_api.iniLatitud,
                lng: hojaruta_api.iniLongitud
            },
            zoom: hojaruta_api.iniZoom,
            streetViewControl:false,
            mapTypeControl: false
        });

      google.maps.event.addListener(hojaruta_api.map, 'zoom_changed', function() {
          if (hojaruta_api.map.getZoom() < 5) hojaruta_api.map.setZoom(5);
          if(typeof denuncia_api!='undefined' && denuncia_api!=null){
            denuncia_api.zoom_event(hojaruta_api.map.getZoom());
          }
      });

      google.maps.event.addListenerOnce(this.map, 'idle', function() {
           google.maps.event.trigger(hojaruta_api.map, 'resize');
      });

      // google.maps.event.addListener(jurisdiccion_ruta_api.capa['Comisaria_'+hojaruta_api.usu_jurisdiccion].polygon, 'click', function (ev) {
      //   hojaruta_api.addMarcador(ev.latLng);
      // });  
    },



   clickJurisdiccion: function(){
      google.maps.event.addListener(jurisdiccion_ruta_api.capa['Comisaria_'+hojaruta_api.usu_jurisdiccion].polygon, 'click', function (ev) {
          // hojaruta_api.addMarcador(ev.latLng);
          // hojaruta_api.guardarDatos(ev.latLng);

          $('#myModal').modal('show');
          hojaruta_api.localat = ev.latLng.lat();
          hojaruta_api.localng = ev.latLng.lng();
          // console.log(ev.latLng);
      });
    },


    guardarDatos: function(location){

      hojaruta_api.hora = $('#txthora').val();
      hojaruta_api.motivo = $('#txtmotivo').val();

      if(hojaruta_api.hora != '' && hojaruta_api.motivo !='' && hojaruta_api.motivo!='0')
      {
        var myLatlng = new google.maps.LatLng(hojaruta_api.localat,hojaruta_api.localng);

         hojaruta_api.addMarcador(myLatlng,hojaruta_api.hora,hojaruta_api.motivo);

        $('#myModal').modal('hide');
        $('#txthora').val('');
        $('#txtmotivo').val('0');
      }
      else{
        $('#txthora').val('');
        $('#txtmotivo').val('0');
        $.gritter.add({
            position: 'bottom-right',
            title: 'Mensaje',
            text: 'Debe completar los campos',
            class_name: 'gritter-error'
        });
      }

    },


    add_TaskLoader: function(){
        if(!this.taskLoader){
            this.taskLoader=0;
        }
        if(this.taskLoader == 0){
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

    generarRuta: function (){
      
      if(!hojaruta_api.renderer){

            hojaruta_api.directionsService = new google.maps.DirectionsService();

          for (var i = 0, parts = [], max = 8 - 1; i < hojaruta_api.mrkDatos.length; i = i + max)
            parts.push(hojaruta_api.mrkDatos.slice(i, i + max + 1));

          var service_callback = function(response, status) {
          
              if (status != 'OK') {
                  console.log('Directions request failed due to ' + status);
                  return;
              }
              hojaruta_api.renderer = new google.maps.DirectionsRenderer;
              hojaruta_api.renderer.setMap(hojaruta_api.map);
              hojaruta_api.renderer.setOptions({ suppressMarkers: true, preserveViewport: true });
              hojaruta_api.renderer.setDirections(response);
          };
          // Send requests to service to get route (for stations count <= 25 only one request will be sent)
          for (var i = 0; i < parts.length; i++) {
            // Waypoints does not include first station (origin) and last station (destination)
            var waypoints = [];
              for (var j = 1; j < parts[i].length - 1; j++)
                  waypoints.push({location: parts[i][j], stopover: false});
              // Service options
              var service_options = {
                  origin: parts[i][0],
                  destination: parts[i][parts[i].length - 1],
                  waypoints: waypoints,
                  travelMode: 'DRIVING'
              };
              // Send request
              hojaruta_api.directionsService.route(service_options, service_callback);
          }
      }
      else{
              hojaruta_api.renderer.setMap(null);
              delete hojaruta_api.renderer;
              hojaruta_api.renderer = null;
              hojaruta_api.generarRuta();
      }

    },


    get_vehiculo: function (modeloVehi,institucion, selected , callback){

        SipcopJS.post('admin/home/json_modelvehplaca',{modelo:modeloVehi,institucion:institucion}, function(data){
          $('#txtFormPlaca').html('<option value="0">-- Seleccione --</option>');
          $.each(data.placas, function(index, obj){
            var seleccionado = "";
            $('#txtFormPlaca').append('<option value="'+obj.PLACA+'">'+obj.PLACA+'</option>');
          });
          if(callback){
            callback();
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

   guardarRuta: function(idinstitucion){
      
      var placa = $('#txtFormPlaca').val();
      var operador = $('#txtFormOperador').val();
      var chofer = $('#txtFormChofer').val();
      var fecha = $('#txtFormFecha').val();
      var idinstitucion =idinstitucion;

      var objDatos = {placa:placa,operador:operador,chofer:chofer,fecha:fecha,idinstitucion:idinstitucion};

      var rutas = hojaruta_api.mrkDatos;

      SipcopJS.post('admin/ruta/json_save',{objdatos:objDatos,rutas:rutas}, function(data){

                console.log(data);
                // if(data > 0){
                //   swal(
                //     'Good job!',
                //     'You clicked the button!',
                //     'success'
                //   )
                // }
                // if(callback){
                //   callback({resp:'ok', status:'1'});
                // }
                //   var cad = '<table width="100%" border="0">'+
                //               '<thead><tr><th>Turno</th>';
                //               if($('#ckPatrullero').is(':checked')){
                //                   cad+='<th style="text-align:center">Patrulleros</th>';
                //               }
                //               if($('#ckMotorizado').is(':checked')){
                //                   cad+='<th style="text-align:center">Motorizados</th>';
                //               }
                //               if($('#ckPatpie').is(':checked')){
                //                   cad+='<th style="text-align:center">Pat. Pie</th>';
                //               }
                //               if($('#ckPuestoFijo').is(':checked')){
                //                   cad+='<th style="text-align:center">P. Fijo</th>';
                //               }
                //               if($('#ckBarrioSeguro').is(':checked')){
                //                   cad+='<th style="text-align:center">B. Seg.</th>';
                //               }

                //               cad+='</tr></thead>'+
                //               '<tbody>';
                //    $.each(data.resumen_turno, function(idx, obj){
                //           cad += '<tr><td>'+obj.TURNO+'</td>';
                //           if($('#ckPatrullero').is(':checked')){
                //               cad += '<td align="center">'+(parseInt(obj.TotalPatrullero)+parseInt(obj.TotalPatInt))+'/'+(parseInt(data.resumen_total.TotalPatrullero)+parseInt(data.resumen_total.TotalPatInt))+'</td>';
                //           }
                //           if($('#ckMotorizado').is(':checked')){
                //               cad += '<td align="center">'+obj.TotalMotorizado+'/'+data.resumen_total.TotalMotorizado+'</td>';
                //           }
                //           if($('#ckPatpie').is(':checked')){
                //               cad += '<td align="center">'+obj.TotalPatPie+'/'+data.resumen_total.TotalPatPie+'</td>';
                //           }
                //           if($('#ckPuestoFijo').is(':checked')){
                //               cad += '<td align="center">'+obj.TotalPuestoFijo+'/'+data.resumen_total.TotalPuestoFijo+'</td>';
                //           }
                //           if($('#ckBarrioSeguro').is(':checked')){
                //               cad += '<td align="center">'+obj.TotalBarrioSeg+'/'+data.resumen_total.TotalBarrioSeg+'</td>';
                //           }
                //           cad += '</tr>';
                //   });
                // cad += '</tbody></table>';
                // $(contenedor).html(cad);


                if(callback){
                    callback({resp:'ok', status:'1'});
                }
            },'json').fail(function(err){
                if(callback){
                    callback({err:err, status:'0'});
                }
            });

    },

   buscarHojaRuta: function(){
      
      var fecha = $('#txtFromFechalistar').val();

      SipcopJS.post('admin/ruta/json_hojaruta',{fecha:fecha}, function(data){
            if(data.hoja_ruta !=''){
              $.each(data.hoja_ruta, function(index, obj){
                  $('#tbl-hojaruta').append('<tr><td>'+ obj.placa+'</td><td>'+obj.fecha+'</td><td>'+ obj.chofer +'</td><td>'+obj.operador+'</td><td><a href="javascript:;" onclick="hojaruta_api.modalDetalle('+obj.idhojaruta+')" class="btn btn-primary btn-xs tooltips" data-toggle="button" data-placement="top" data-original-title="Ver"><i class="fa fa-search"></i></a></td></tr>');
              });
            }
            else{
              $('#tbl-hojaruta').append('<tr><td colspan="5" style="text-align:center;">NO HAY DATOS</td></tr>');
            } 


     
      });


    },

  modalDetalle: function($idhojaruta){
    alert($idhojaruta);
  },

  removeA:function (txt) {
       // var index = hojaruta_api.mrkDatos.indexOf(txt);
       // var index = map_api.mrkDatos.findIndex((item) => item.name === txt);
       // if(index !== -1){
       //    hojaruta_api.mrkDatos.splice([index],1);
       // }else{
       //    console.log('no se realizo nada');
       // }

       var index = hojaruta_api.mrkDatos.findIndex((item) => item.name === txt);
       if(index !== -1){
          hojaruta_api.mrkDatos.splice([index],1);
            hojaruta_api.renderer.setMap(null);
              delete hojaruta_api.renderer;
              hojaruta_api.renderer = null;
              hojaruta_api.generarRuta();


       }else{
          console.log('no se realizo nada');
       }
        // hojaruta_api.renderer.setMap(null);
        // delete hojaruta_api.renderer;
        // hojaruta_api.renderer = null;
        // hojaruta_api.generarRuta();
  },


  addMarcador:function (location,hora,motivo) {

          hojaruta_api.buscarDireccion(location.lat(),location.lng(), function(direccion){
            if(direccion && direccion!=''){
                hojaruta_api.direccion= direccion;
            }

            if(motivo ==1){
              var motivoShow = 'COMISARIA';
            }else if(motivo ==2){
              var motivoShow = 'PATRULLAJE';
            }else if(motivo ==3){
              var motivoShow = 'EST. TACTICO';
            }

             var marker = new google.maps.Marker({
               position: location,
               map: hojaruta_api.map,
               // draggableCursor:'crosshair',
                draggable: false,
                title: "" + ++hojaruta_api.cnt,
                id: "marker"+ hojaruta_api.cnt,
                label: ""+hojaruta_api.cnt,
                hora: hora,
                motivo: motivo,
                direccion: hojaruta_api.direccion,
            });

            google.maps.event.addListener(marker, "dblclick", function (e) { 
                 var itm_self = this;
                   if(confirm('¿Desea Eliminar marker?')){
                       marker.setMap(null)
                       
                      hojaruta_api.removeA(marker.title);

                      $('.'+marker.id).html('');
                      // hojaruta_api.removeA(itm_self);
                   }
                  
             });

            // $('#myTable').append('<tr class='+marker.id+'><td>'+ marker.title +'</td><td><input type="text" class="form-control" name="txtHora" id="txtHora" placeholder="Hora"  style="width: 50px;"></td><td>'+ marker.direccion +'</td><td><select class="form-control" id="txtmotivo"><option value="0">--SELECCIONE--</option><option value="1">COMISARIA</option><option value="2">PATRULLAJE</option><option value="3">EST.TACTICO</option></select></td></tr>');
             
            $('#myTable').append('<tr class='+marker.id+'><td>'+ marker.title +'</td><td>'+marker.hora+'</td><td>'+ marker.direccion +'</td><td>'+motivoShow+'</td></tr>');
     

            hojaruta_api.mrkMiPosicion.push(marker);

            for (var i = 0; i < hojaruta_api.mrkMiPosicion.length; i++) {
                var objDatos = {lat:marker.position.lat(),lng:marker.position.lng(),name:marker.title,direccion:hojaruta_api.direccion,hora:hojaruta_api.hora,motivo:hojaruta_api.motivo};
            }

            hojaruta_api.mrkDatos.push(objDatos);

        });
    },

    removerMarker: function(mrk){
      mrk.setMap(null);
      var i = hojaruta_api.markers_dibujo.indexOf(mrk);
      if(i != -1) {
        hojaruta_api.markers_dibujo.splice(i, 1);
      }
      hojaruta_api.dibujarLinea();
    },

    agregarMarker: function(latlong){
      var marker = new google.maps.Marker({
        position: latlong,
        map: hojaruta_api.map,
        draggable:true,
        // cursor: url("https://maps.gstatic.com/mapfiles/openhand_8_8.cur"),
      }); 


      google.maps.event.addListener(marker, 'dragend', function(){
          hojaruta_api.dibujarLinea();
      });
      
      google.maps.event.addListener(marker, "dblclick", function (e) { 
           var itm_self = this;
           if(confirm('¿Desea Eliminar marker?')){
              hojaruta_api.removerMarker(itm_self);
           }
        });

      var myLatLng = latlong;
      var lat = myLatLng.lat();
      var lng = myLatLng.lng();

      map_api.buscarDireccion(lat,lng, function(direccion){
          if(direccion && direccion!=''){
              
              var htmldireccion = '<div class="row">'+
                                    '<div class="col-md-9"><p>'+
                                        direccion+
                                      '</p>'+
                                    '</div>'+
                                    '<div class="col-md-3">'+
                                        '<input type="text" class="form-control" value="01:00" style="text-align: center">'+
                                    '</div>'+
                                  '</div>'+
                                  '<div class="line"></div>';

              $('#lista_contenido').append(htmldireccion);
              $('#lista_contenido').trigger('create');


              if(direccion=='undefined'){
                  map_api.direccion='Sin Nombre';
              }else{
                  map_api.direccion= direccion;
              }              
          }else{
              map_api.direccion = '';
          }
      });

      hojaruta_api.markers_dibujo.push(marker);
      hojaruta_api.dibujarLinea();
    },

    dibujarLinea: function(){
          if(hojaruta_api.polyline_region!=null){
            hojaruta_api.polyline_region.setMap(null);
            hojaruta_api.polyline_region = null;
          }

          hojaruta_api.polyline_region = new google.maps.Polyline({
            path: hojaruta_api.obtenerCoords(),
            geodesic: true,
            strokeColor: '#FF0000',
            strokeOpacity: 1.0,
            strokeWeight: 2
          });

          hojaruta_api.polyline_region.setMap(hojaruta_api.map);
    },

    obtenerCoords: function(){
          hojaruta_api.coords = [];

          $.each(hojaruta_api.markers_dibujo, function(idx, objx){
            var posicion = objx.getPosition();
            hojaruta_api.coords.push({lat:posicion.lat(), lng:posicion.lng()});
          });

          return hojaruta_api.coords;
    },

    dibujarRuta: function(){
        var markerArray = [];
        var directionsService = new google.maps.DirectionsService;
        var directionsDisplay = new google.maps.DirectionsRenderer({map: hojaruta_api.map});
        var stepDisplay = new google.maps.InfoWindow;

        hojaruta_api.calculateAndDisplayRoute(directionsDisplay, directionsService, markerArray, stepDisplay, hojaruta_api.map);
        // Listen to change events from the start and end lists.
        var onChangeHandler = function() {
          hojaruta_api.calculateAndDisplayRoute(
              directionsDisplay, directionsService, markerArray, stepDisplay, hojaruta_api.map);
        };
        document.getElementById('inicioRuta').addEventListener('change', onChangeHandler);
        document.getElementById('finRuta').addEventListener('change', onChangeHandler);
    },


    calculateAndDisplayRoute: function(directionsDisplay, directionsService,markerArray, stepDisplay, map) 
    {
      // First, remove any existing markers from the map.
      for (var i = 0; i < markerArray.length; i++) {
          markerArray[i].setMap(null);
      }
      // Retrieve the start and end locations and create a DirectionsRequest using
      // WALKING directions.
      directionsService.route({
            origin: document.getElementById('inicioRuta').value,
            destination: document.getElementById('finRuta').value,
            travelMode: 'DRIVING'
          }, function(response, status) {
            // Route the directions and pass the response to a function to create
            // markers for each step.
            if (status === 'OK') {

              console.log(response.routes[0].warnings);
              // document.getElementById('warnings-panel').innerHTML =
              //     '<b>' + response.routes[0].warnings + '</b>';

              directionsDisplay.setDirections(response);
              hojaruta_api.showSteps(response, markerArray, stepDisplay, hojaruta_api.map);
            } else {
              // window.alert('Directions request failed due to ' + status);
            }
      });
    },


    showSteps: function(directionResult, markerArray, stepDisplay, map) 
    {
        // For each step, place a marker, and add the text to the marker's infowindow.
        // Also attach the marker to an array so we can keep track of it and remove it
        // when calculating new routes.
        var myRoute = directionResult.routes[0].legs[0];
        for (var i = 0; i < myRoute.steps.length; i++) {
          var marker = markerArray[i] = markerArray[i] || new google.maps.Marker;
          marker.setMap(hojaruta_api.map);
          marker.setPosition(myRoute.steps[i].start_location);
          hojaruta_api.attachInstructionText(
              stepDisplay, marker, myRoute.steps[i].instructions, hojaruta_api.map);
        }
    },


    attachInstructionText: function(stepDisplay, marker, text, map) {
        google.maps.event.addListener(marker, 'click', function() {
          // Open an info window when the marker is clicked on, containing the text
          // of the step.
          stepDisplay.setContent(text);
          stepDisplay.open(hojaruta_api.map, marker);
        });
    }


}