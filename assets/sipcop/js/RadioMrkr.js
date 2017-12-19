function RadioMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    this.setMap(map);

    this.enJurisdicicon =false;
    if(jurisdiccion_api && jurisdiccion_api.jurisdiccion && google && google.maps && google.maps.geometry && google.maps.geometry.poly){
       this.enJurisdicicon = google.maps.geometry.poly.containsLocation(this.latlng,jurisdiccion_api.jurisdiccion);
    }
}
RadioMrkr.prototype = new google.maps.OverlayView();
RadioMrkr.prototype.draw = function() {
    var self = this;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }
    div = $('<div class="radio-marker">' + '<div class="radio-msj" style="border: 1px solid ' + this.args.color + ';"></div><div class="radio-etiqueta" style="border: 1px solid ' + this.args.color + ';">' + ((this.args.oRadio.VehiculoPlaca && this.args.oRadio.VehiculoPlaca!='' && this.args.oRadio.VehiculoPlaca!='null')?this.args.oRadio.VehiculoPlaca:this.args.oRadio.RadioEtiqueta) + '</div>' + '<div class="bloque1" style="background: ' + this.args.color + ';"><div class="bloque2">' + '<img src="assets/sipcop/img/ico-radio_3-'+this.args.oRadio.Indicador+'.png" width="30" height="30" />' + '</div></div>' + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' + '</div>')[0];

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
RadioMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    this.setMap(null);
};

RadioMrkr.prototype.getPosition = function() {
    return this.latlng;
};

RadioMrkr.prototype.act = function(mute) {
    if (this.args.div) {
       $(this.args.div).find('.bloque2').html('<img src="assets/sipcop/img/ico-radio_3-'+this.args.oRadio.Indicador+'.png" width="30" height="30" />');
    }

    if(!mute){
        mute = false;
    }

    if(jurisdiccion_api && jurisdiccion_api.jurisdiccion && google && google.maps && google.maps.geometry && google.maps.geometry.poly){
       var jurisd = google.maps.geometry.poly.containsLocation(this.latlng,jurisdiccion_api.jurisdiccion);
       if(this.args.oRadio.ComisariaID == map_api.usu_comisaria){
            map_api.notificarJurisdiccionMotorizado(this.args.oRadio, true, this.enJurisdicicon, jurisd, mute);
       }else{
            map_api.notificarJurisdiccionMotorizado(this.args.oRadio, false, this.enJurisdicicon, jurisd, mute);
       }
       this.enJurisdicicon = jurisd;
    }
};