   function dibujarGeocerca() {
        // var drawingManager;
        if(!map_api.drawingManager){
            map_api.drawingManager = new google.maps.drawing.DrawingManager({
                      drawingMode: google.maps.drawing.OverlayType.POLYGON,
                      drawingControl: true,
                      drawingControlOptions: {
                        position: google.maps.ControlPosition.TOP_CENTER,
                        drawingModes: ['polygon']
                        // drawingModes: ['marker', 'circle', 'polygon', 'polyline', 'rectangle']
                      },
                      markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
                      circleOptions: {
                        fillColor: '#ffff00',
                        fillOpacity: 1,
                        strokeWeight: 5,
                        clickable: false,
                        editable: true,
                        zIndex: 1
                      }
                    });

                    google.maps.event.addListener(map_api.drawingManager, 'polygoncomplete', function (polygon) {
                        if(polygon){
                            var str = '';
                            var data = [];


                            $.each(map_api.oMrkPatrullero, function(idx, obj){

                            if(google.maps.geometry.poly.containsLocation(obj.latlng, polygon)){
                            str += ('Placa: '+obj.args.oPatrullaje.VehiculoPlaca+', Comisaría: '+obj.args.oPatrullaje.ComisariaNombre+', RadioID: '+obj.args.oPatrullaje.RadioID) +'\n';
                                data.push({ComisariaNombre:obj.args.oPatrullaje.ComisariaNombre,Placa:obj.args.oPatrullaje.VehiculoPlaca,RadioID:obj.args.oPatrullaje.RadioID,VehiculoModelo:'Patrullero'});
                            }
                            });
                            $.each(map_api.oMrkMotorizado, function(idx, obj){

                            if(google.maps.geometry.poly.containsLocation(obj.latlng, polygon)){
                                str += ('Placa: '+obj.args.oPatrullaje.VehiculoPlaca+', Comisaría: '+obj.args.oPatrullaje.ComisariaNombre+', RadioID: '+obj.args.oPatrullaje.RadioID) +'\n';
                                data.push({ComisariaNombre:obj.args.oPatrullaje.ComisariaNombre,Placa:obj.args.oPatrullaje.VehiculoPlaca,RadioID:obj.args.oPatrullaje.RadioID,VehiculoModelo:'Motorizado'});
                            }
                            });


                            $.each(map_api.oMrkPatPie, function(idx, obj){

                            if(google.maps.geometry.poly.containsLocation(obj.latlng, polygon)){
                                str += ('Placa: '+obj.args.oPatrullaje.VehiculoPlaca+', Comisaría: '+obj.args.oPatrullaje.ComisariaNombre+', RadioID: '+obj.args.oPatrullaje.RadioID) +'\n';
                                data.push({ComisariaNombre:obj.args.oPatrullaje.ComisariaNombre,Placa:obj.args.oPatrullaje.VehiculoPlaca,RadioID:obj.args.oPatrullaje.RadioID,VehiculoModelo:'Patrullaje a Pie'});
                            }
                            });

                            $('#dgGeocerca').dataTable().fnClearTable();                           
                            $('#dgGeocerca').dataTable().fnAddData(data);
                            $('#dgGeocerca').dataTable().fnDraw();
       
                            $('#modalGeocerca').modal('show');

                            // console.log(data);

                            polygon.setMap(null);
                            delete polygon;
                            polygon = null;
                        }
                    });
                    map_api.drawingManager.setMap(map_api.map);
                    $('#btnGeoCerca').html('Desactivar GeoCerca');
                    $('#btnGeoCerca').removeClass();
                    $('#btnGeoCerca').addClass('btn btn-danger tooltips');
        }else{
            map_api.drawingManager.setMap(null);
            delete map_api.drawingManager;
            map_api.drawingManager = null;
                    $('#btnGeoCerca').html('Dibujar GeoCerca');
                    $('#btnGeoCerca').removeClass();
                    $('#btnGeoCerca').addClass('btn btn-primary tooltips');

        }  
    };


function fijarMapa(){
    if(!map_api.fijar_coord){
        map_api.fijar_coord = {};
        map_api.fijar_coord.latlng = new google.maps.LatLng(map_api.map.getCenter().lat(),map_api.map.getCenter().lng());
        map_api.fijar_coord.zoom = map_api.map.getZoom();
        $('#btnFijarMapa').html('Desactivar Fijar Mapa');
        $('#btnFijarMapa').addClass('btn-danger');
        $('#btnFijarMapa').removeClass('btn-primary');
        map_api.fijar_coord.time = setInterval(function(){
            map_api.map.setZoom(map_api.fijar_coord.zoom);
            map_api.map.setCenter(map_api.fijar_coord.latlng);
        },200);
    }else{
        if(map_api.fijar_coord){
            clearInterval(map_api.fijar_coord.time);
        }
        map_api.fijar_coord = null;
        $('#btnFijarMapa').html('Fijar Mapa');
        $('#btnFijarMapa').removeClass('btn-danger');
        $('#btnFijarMapa').addClass('btn-primary');
    }
}