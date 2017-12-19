function CapturadorMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    this.setMap(map);
    this.enJurisdicicon = false;

    if(jurisdiccion_api && jurisdiccion_api.capa!=null && google && google.maps && google.maps.geometry && google.maps.geometry.poly){
        
        if(jurisdiccion_api.capa['Comisaria_'+this.args.oDispoGPS.ComisarsiaID]){
           this.enJurisdicicon = google.maps.geometry.poly.containsLocation(this.latlng,jurisdiccion_api.capa['Comisaria_'+this.args.oDispoGPS.ComisariaID]);
        }
    }



}
CapturadorMrkr.prototype = new google.maps.OverlayView();

CapturadorMrkr.prototype.draw = function() {
    var self = this;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }
    div = $('<div class="radio-marker">' + '<div class="radio-msj" style="border: 1px solid ' + this.args.color + ';"></div><div class="radio-etiqueta" style="border: 1px solid ' + this.args.color + ';">' + ((this.args.oDispoGPS.VehiculoPlaca && this.args.oDispoGPS.VehiculoPlaca!='' && this.args.oDispoGPS.VehiculoPlaca!='null')?this.args.oDispoGPS.VehiculoPlaca:this.args.oDispoGPS.RadioEtiqueta) + '</div>' + '<div class="bloque1" style="background: ' + this.args.color + ';"><div class="bloque2">' + '<img src="assets/sipcop/img/ico-radio_3-'+this.args.oDispoGPS.Indicador+'.png" width="30" height="30" />' + '</div></div>' + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' + '</div>')[0];

    if (typeof self.args.callback !== 'undefined') {
        google.maps.event.clearListeners(div, "click");
        google.maps.event.addDomListener(div, "click", function(event) {
            self.args.callback(self);
            google.maps.event.trigger(self, "click");
        });
    }
    var panes = this.getPanes();
    if(panes){
        panes.overlayImage.appendChild($(div)[0]);
        var point = this.getProjection().fromLatLngToDivPixel(this.latlng);
        if (point) {
            div.style.left = (point.x - 19) + 'px';
            div.style.top = (point.y - 45) + 'px';
        }
    }

    this.args.div = div;

    self.act(true);

    //this.getPanes().overlayLayer.style['zIndex'] = 3000;
};

CapturadorMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    this.setMap(null);
};


CapturadorMrkr.prototype.getPosition = function() {
    return this.latlng;
};


CapturadorMrkr.prototype.act = function(mute) {

    if (this.args.div) {
       $(this.args.div).find('.bloque2').html('<img src="assets/sipcop/img/ico-radio_3-'+this.args.oDispoGPS.Indicador+'.png" width="30" height="30" />');
    }   
    var cond = jurisdiccion_api && jurisdiccion_api.capa!=null && google && google.maps && google.maps.geometry && google.maps.geometry.poly;

    if(cond){
        var capa_juris = jurisdiccion_api.capa['Comisaria_'+this.args.oDispoGPS.ComisariaID];     
        
        if(capa_juris){

           this.enJurisdicicon = google.maps.geometry.poly.containsLocation(this.latlng(),capa_juris);
           if(!this.enJurisdicicon){
                capturador_api.notificarSalidaJurisdiccion(this.args.oDispoGPS);
           }
        }else{
            //console.log('Comisaria_'+this.args.oDispoGPS.ComisariaID); Comisaría sin jurisdicción
        }
    }
};