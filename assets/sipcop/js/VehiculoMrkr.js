function VehiculoMrkr(latlng, map, args) {
    this.latlng = latlng;
    this.args = args;
    this.setMap(map);

    this.enJurisdicicon =false;

    var pertenece = false;
    var self = this;
    $.each(map_api.usu_jurisdiccion, function(i_juris, o_juris){
        if(parseInt(o_juris) == parseInt(self.args.oPatrullaje.ComisariaID)){
            pertenece = true;
        }
    });


    if(pertenece && jurisdiccion_api && jurisdiccion_api.capa && jurisdiccion_api.capa['Comisaria_'+this.args.oPatrullaje.ComisariaID] && google && google.maps && google.maps.geometry && google.maps.geometry.poly){
       this.enJurisdicicon = google.maps.geometry.poly.containsLocation(this.latlng,jurisdiccion_api.capa['Comisaria_'+this.args.oPatrullaje.ComisariaID].polygon);
    }
}
VehiculoMrkr.prototype = new google.maps.OverlayView();
VehiculoMrkr.prototype.draw = function() {
    var self = this;
    var div = this.args.div;

    if (div) {
        $(div).remove();
    }

    var placa_lbl = '';

    if(this.args.oPatrullaje.VehiculoPlaca && $.trim(this.args.oPatrullaje.VehiculoPlaca)!=''){
        placa_lbl += this.args.oPatrullaje.VehiculoPlaca;
    }

    if(this.args.oPatrullaje.DispoDescripcion && $.trim(this.args.oPatrullaje.DispoDescripcion)!=''){
        placa_lbl += (placa_lbl!=''?' /<br>':'') + this.args.oPatrullaje.DispoDescripcion;
    }

    div = $('<div class="patrullero-marker">' + 
                '<div class="patrullero-msj" style="border: 1px solid ' + this.args.color + ';"></div>'
               +'<div class="patrullero-placa" style="border: 1px solid ' + this.args.color + ';">' + placa_lbl + '</div>' 
               + '<div class="bloque1" style="background: ' + this.args.color + ';"><div class="bloque2">' + '</div></div>' 
               + '<div class="pie" style="border-color: ' + this.args.color + ' transparent transparent transparent;"></div>' 
          + '</div>')[0];

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

VehiculoMrkr.prototype.remove = function() {
    if (this.args.div) {
        $(this.args.div).remove()
        this.args.div = null;
    }
    this.setMap(null);
};

VehiculoMrkr.prototype.getPosition = function() {
    return this.latlng;
};

VehiculoMrkr.prototype.getIco = function() {
    return this.args.oPatrullaje.PatrullajeIcono;
};


VehiculoMrkr.prototype.getIcoImg = function() {
    return 'assets/sipcop/img/ico-'+this.getIco()+'-'+this.args.oPatrullaje.Indicador+'.png';
};

VehiculoMrkr.prototype.act = function(mute) {
    if (this.args.div) {
        $(this.args.div).find('.bloque2').html('<img src="'+this.getIcoImg()+'" width="30" height="30" />');
    }

    if(!mute){
        mute = false;
    }

    var self = this;

    var jurisd = false;
    if(jurisdiccion_api && jurisdiccion_api.capa && 
        jurisdiccion_api.capa['Comisaria_'+self.args.oPatrullaje.ComisariaID] && 
        parseInt(jurisdiccion_api.capa['Comisaria_'+self.args.oPatrullaje.ComisariaID].flg) == 1 &&
        google && google.maps && google.maps.geometry && google.maps.geometry.poly){
        jurisd = google.maps.geometry.poly.containsLocation(this.latlng,jurisdiccion_api.capa['Comisaria_'+self.args.oPatrullaje.ComisariaID].polygon);
        map_api.notificarJurisdiccionPatrullaje(self.args.oPatrullaje, true, this.enJurisdicicon, jurisd, mute);
    }else{
        $.each(map_api.usu_jurisdiccion, function(i_juris, o_juris){
            if(!jurisd && jurisdiccion_api && jurisdiccion_api.capa && 
                jurisdiccion_api.capa['Comisaria_'+o_juris] &&
                google && google.maps && google.maps.geometry && google.maps.geometry.poly){
                jurisd = google.maps.geometry.poly.containsLocation(self.latlng,jurisdiccion_api.capa['Comisaria_'+o_juris].polygon);
            }
        });
        map_api.notificarJurisdiccionPatrullaje(self.args.oPatrullaje, false, this.enJurisdicicon, jurisd, mute);
    }

    this.enJurisdicicon = jurisd;

};

VehiculoMrkr.prototype.setMsj = function(msj) {
    if (msj && msj!='') {
        $(this.args.div).find('.patrullero-msj').html(msj).show();
        //console.log($(this.args.div).find('.patrullero-msj').html());
    }else{
        $(this.args.div).find('.patrullero-msj').html('').hide();
    }

};