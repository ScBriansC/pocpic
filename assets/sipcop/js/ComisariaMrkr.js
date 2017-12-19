function ComisariaMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    this.setMap(map);
}
ComisariaMrkr.prototype = new google.maps.OverlayView();
ComisariaMrkr.prototype.draw = function() {
    var self = this;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }
    div = $('<div class="comisaria-marker">' + '<div class="comisaria-nombre" style="border: 1px solid ' + this.args.color + ';">' + this.args.oComisaria.ComisariaNombre + '</div>' + '<div class="bloque1" style="background: ' + this.args.color + ';"><div class="bloque2">' + '<img src="assets/sipcop/img/ico-comisaria-white.png" width="30" height="30" />' + '</div></div>' + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' + '</div>')[0];



    
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
};
ComisariaMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    this.setMap(null);
};
ComisariaMrkr.prototype.getPosition = function() {
    return this.latlng;
};