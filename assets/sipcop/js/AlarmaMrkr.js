function AlarmaMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    // this.args.color = '#e66f17';
    this.args.color = '#ff0000';
    this.setMap(map);
}
AlarmaMrkr.prototype = new google.maps.OverlayView();
AlarmaMrkr.prototype.draw = function() {
    var self = this;
    self.alarma.AlarmaEncendido = (self.alarma.AlarmaEncendido==2)?0:self.alarma.AlarmaEncendido;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }

    div = $('<div class="alarma-marker">' + '<div class="bloque1" style="background: ' + this.args.color + ';">'+'<div class="alarma-etiqueta" style="border: 1px solid '+this.args.color+'">ALM-' + (this.alarma.AlarmaReferencia) + '</div>'+'<div class="bloque2">' + '<img src="assets/sipcop/img/alarma.png" width="30" height="30" />' + '</div></div>' + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' + '</div>')[0];


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

    self.encender(self.alarma.AlarmaEncendido);
    // this.getPanes().overlayLayer.style['zIndex'] = 1001;
};
AlarmaMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    try{
        if(this.getMap()){
            //this.setMap(null);
        }
    }catch(err){}
};
AlarmaMrkr.prototype.getPosition = function() {
    return this.latlng;
};
AlarmaMrkr.prototype.encender = function(val) {
    this.alarma.AlarmaEncendido = val = (val==2)?0:val;
    $(this.args.div).find('.bloque1').css('background',(val==0)?'#ff0000':'#fdd22d');
    $(this.args.div).find('.pie').css('border-color',(val==0)?'#ff0000 transparent transparent transparent':'#fdd22d transparent transparent transparent');
    $(this.args.div).find('img').prop('src', (val==0)?'assets/sipcop/img/alarma.png':'assets/sipcop/img/alarma-on.png');
    if(val == 1){
        map_api.playAlarma3();
    }
};