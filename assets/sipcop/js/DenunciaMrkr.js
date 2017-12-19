function DenunciaMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    this.args.color = '#f99d27';
    this.setMap(map);
}
DenunciaMrkr.prototype = new google.maps.OverlayView();
DenunciaMrkr.prototype.draw = function() {
    var self = this;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }
    div = $('<div class="denuncia-marker">' + '<div class="bloque1" style="background: ' + this.args.color + ';">'+'<div class="denuncia-tipo" style="border: 1px solid '+this.args.color+'">' + (this.args.oDenuncia.TipifNombre) + '</div>' +'<div class="bloque2">' + '<img src="assets/sipcop/img/mapa_delito/' + this.args.oDenuncia.TipoIcono + '" width="30" height="30" />' + '</div></div>' + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' + '</div>')[0];


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
    //this.getPanes().overlayLayer.style['zIndex'] = 1000;
};
DenunciaMrkr.prototype.getDenuncia = function() {
    return this.args.oDenuncia;
};
DenunciaMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    try{
        //this.setMap(null);
    }catch(err){}
};
DenunciaMrkr.prototype.getPosition = function() {
    return this.latlng;
};