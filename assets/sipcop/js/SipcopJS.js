(function($) {    
  if ($.fn.style) {
    return;
  }

  // Escape regex chars with \
  var escape = function(text) {
    return text.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&");
  };

  // For those who need them (< IE 9), add support for CSS functions
  var isStyleFuncSupported = !!CSSStyleDeclaration.prototype.getPropertyValue;
  if (!isStyleFuncSupported) {
    CSSStyleDeclaration.prototype.getPropertyValue = function(a) {
      return this.getAttribute(a);
    };
    CSSStyleDeclaration.prototype.setProperty = function(styleName, value, priority) {
      this.setAttribute(styleName, value);
      var priority = typeof priority != 'undefined' ? priority : '';
      if (priority != '') {
        // Add priority manually
        var rule = new RegExp(escape(styleName) + '\\s*:\\s*' + escape(value) +
            '(\\s*;)?', 'gmi');
        this.cssText =
            this.cssText.replace(rule, styleName + ': ' + value + ' !' + priority + ';');
      }
    };
    CSSStyleDeclaration.prototype.removeProperty = function(a) {
      return this.removeAttribute(a);
    };
    CSSStyleDeclaration.prototype.getPropertyPriority = function(styleName) {
      var rule = new RegExp(escape(styleName) + '\\s*:\\s*[^\\s]*\\s*!important(\\s*;)?',
          'gmi');
      return rule.test(this.cssText) ? 'important' : '';
    }
  }

  // The style function
  $.fn.style = function(styleName, value, priority) {
    // DOM node
    var node = this.get(0);
    // Ensure we have a DOM node
    if (typeof node == 'undefined') {
      return this;
    }
    // CSSStyleDeclaration
    var style = this.get(0).style;
    // Getter/Setter
    if (typeof styleName != 'undefined') {
      if (typeof value != 'undefined') {
        // Set style property
        priority = typeof priority != 'undefined' ? priority : '';
        style.setProperty(styleName, value, priority);
        return this;
      } else {
        // Get style property
        return style.getPropertyValue(styleName);
      }
    } else {
      // Get CSSStyleDeclaration
      return style;
    }
  };
})(jQuery);

var SipcopJS = {
	tk_k:null,
	tk_v:null,
	logEnabled : false,

	init:function(k,v){
		this.tk_k = k;
		this.tk_v = v;
	},


	get_ubigeo: function (elDep, elProv, elDist){
        var depa = $(elDep).val();
        var prov = $(elProv).val();
        var dist = $(elDist).val();

        

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


	cargarComisarias: function (select, ubigeo, selected , callback){
        SipcopJS.post('admin/ubigeo/json_comisaria',{ubigeo:ubigeo}, function(data){
    		$(select).html('<option value="0">-- Seleccione --</option>');
    		$.each(data.data, function(index, obj){
    			var seleccionado = "";
    			if(selected){
    				if(selected == obj.ComisariaID){
    					seleccionado = "selected";
    				}
    			}
    			$(select).append('<option value="'+obj.ComisariaID+'" '+seleccionado+'>'+obj.ComisariaNombre+'</option>');
    		});
    		if(callback){
    			callback();
    		}
    	});
    },

    get_dependencia: function (elMacReg, elRegPol, elDivTer, elComi){
        var macReg = $(elMacReg).val();
        var regPol = $(elRegPol).val();
        var divTer = $(elDivTer).val();
        var comi = $(elComi).val();

        

        var dependencia = {};


        if(typeof macReg!='undefined' && macReg!='0'){
        	if(typeof comi!='undefined' && comi!='0'){
	            dependencia = {tipo:4,id:comi};
	        }else if(typeof divTer!='undefined' && divTer!='0'){
	            dependencia = {tipo:3,id:divTer};
	        }else if(typeof regPol!='undefined' && regPol!='0'){
	            dependencia = {tipo:2,id:regPol};
	        }else if(typeof macReg!='undefined' && macReg!='0'){
	            dependencia = {tipo:1,id:macReg};
	        }
        }else{
        	dependencia = {tipo:0,id:macReg};
        }

        return dependencia;
    },


	cargarDependencia: function (select, tipo, padre, selected , callback){
        SipcopJS.post('admin/ubigeo/json_dependencia',{tipo:tipo,padre:padre}, function(data){
    		$(select).html('<option value="0">-- Seleccione --</option>');
    		$.each(data.data, function(index, obj){
    			var seleccionado = "";
    			if(selected){
    				if(selected == obj.DependenciaID){
    					seleccionado = "selected";
    				}
    			}
    			$(select).append('<option value="'+obj.DependenciaID+'" '+seleccionado+'>'+obj.DependenciaNombre+'</option>');
    		});
    		if(callback){
    			callback();
    		}
    	});
    },

	dataTable:{
		render:{
			limite20Car: function(data, type, row) {
				if(data){
					return SipcopJS.cortarCadenaxPalabra(data, 5);
				}
				return '';
			},
			timeHuman: function(data, type, row) {
				if(data){
					return SipcopJS.parseTimeHuman(data);
				}
				return '';
			}
		}
	},
	cortarCadenaxPalabra: function(text, lim){    
	    var wordsToCut = lim;
	    var wordsArray = text.split(" ");
	    if(wordsArray.length>wordsToCut){
	        var strShort = "";
	        for(i = 0; i < wordsToCut; i++){
	            strShort += wordsArray[i] + " ";
	        }   
	        return strShort+"...";
	    }else{
	        return text;
	    }
	 },

	 modalPDF: function(titulo, pdf){
	 	var iframe = '<object type="application/pdf" data="'+pdf+'" width="100%" height="500">No Support</object>'
        $.createModal({
            title:titulo,
            message: iframe,
            closeButton:true,
            scrollable:false
        });
	 },

	center: function (obj) {
	   obj.css("position","absolute");
	   obj.css("top", (( $(window).height() - obj.height() ) / 2 - 150)  + "px");
	   obj.css("left", ( $(window).width() - obj.width() ) / 2 + "px");
	   return obj;
	},

	 msj:{
	 	success: function(titulo, mensaje){
	 		$.gritter.add({
	            title: titulo,
	            text: mensaje,
	            class_name: 'gritter-success'
	        });
	 	},
	 	error: function(titulo, mensaje){
	 		$.gritter.add({
	            title: titulo,
	            text: mensaje,
	            class_name: 'gritter-error'
	        });
	 	},
	 	confirm: function(titulo, mensaje, kallback){
		 	bootbox.confirm({title:titulo, message:mensaje, callback: function(confirm){
		        if(typeof kallback != "undefined"){
		          kallback(confirm);
		        }
		      }});
	 	},
	 	prompt: function(titulo, mensaje, kallback){
		 	bootbox.prompt({title:titulo, message:mensaje, inputType:'text', callback: function(confirm){
		        if(typeof kallback != "undefined"){
		          kallback(confirm);
		        }
		      }});
	 	}
	 },

	validarError: function(err){
		this.msj.error('Error '+err.status, err.responseText);
	},

	log: function(obj){
		if(this.logEnabled){
			console.log(obj);
		}
	},

	post: function(ruta, parametros, callback, callBackError){
		SipcopJS.log(ruta);
		SipcopJS.log(parametros);

		parametros[this.tk_k] = this.tk_v;

		$.post(ruta, parametros, function(data){
	        SipcopJS.log(data);
	        if(callback){
	        	callback(data);
	        }
	      }, 'json').error(function(err){
	        SipcopJS.validarError(err);
	        if(callBackError){
	        	callBackError(err);
	        }
	      });
	},

	zeroPad: function(num, places) {
      var zero = places - num.toString().length + 1;
      return Array(+(zero > 0 && zero)).join("0") + num;
    },

    utilValidateForm: function(form){
    	var mss = '';
    	var rules = '';
    	var msj = '';
		$.each($(form).serializeArray(), function(index, obj){
			rules += "\t\t\t"+obj.name+": {required: true},\n";
			msj += "\t\t\t"+obj.name+": {required: 'Información requerida.'},\n";
		});

		mss = "rules: {\n"+rules+"\n},\nmessages: {\n"+msj+"\n}";
		console.log(mss);
    },

    utilPhpForm: function(form){
    	var mss = '';
		$.each($(form).serializeArray(), function(index, obj){
		mss += ('$'+obj.name+' = '+'isset($_REQUEST[\''+obj.name+'\'])?trim($_REQUEST[\''+obj.name+'\']):\'\';\n');
		});
		console.log(mss);
    },

    utilModalForm: function(form){
    	var mss = '';
		$.each($(form).serializeArray(), function(index, obj){
			mss +="$('#"+obj.name+"').val(data."+obj.name+");\n";
		});
		console.log(mss);
    },

    parseTimeHuman: function(time){ //H:i:s
    	return new Date(Date.parseExact(time, "HH:mm:ss")).toString("hh:mm tt");
    },

    parseTimeDB: function(time){ //H:i:s
    	return new Date(Date.parseExact(time, "hh:mm tt")).toString("HH:mm:ss");
    },

    cargarUbigeo: function(select, tipo, padre, selected, callback){
    	SipcopJS.post('admin/ubigeo/json_ubigeo',{tipo:tipo,ubigeo:padre}, function(data){
    		$(select).html('<option value="0">-- Seleccione --</option>');
    		$.each(data.data, function(index, obj){
    			var seleccionado = "";
    			if(selected){
    				if(selected == obj.UbigeoCodigo){
    					seleccionado = "selected";
    				}
    			}
    			$(select).append('<option value="'+obj.UbigeoCodigo+'" '+seleccionado+'>'+obj.UbigeoNombre+'</option>');
    		});
    		if(callback){
    			callback();
    		}
    	});
    },

    crearCookie: function (variable,valor,dias) {
	    try{

	    	
	    	if (dias) {
		        var date = new Date();
		        date.setTime(date.getTime()+(dias*24*60*60*1000));
		        var expires = "; expires="+date.toGMTString();
		    }
		    else var expires = "";
		    document.cookie = variable+"="+valor+expires+"; path=/";
	    }catch(err){}
	},

	leerCookie: function (variable) {
	    try{
	    	var variableEQ = variable + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0;i < ca.length;i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1,c.length);
		        if (c.indexOf(variableEQ) == 0) return c.substring(variableEQ.length,c.length);
		    }
		    return null;
	    }catch(err){}
	},

	borrarCookie: function (variable) {
	    crearCookie(variable,"",-1);
	},

	format:{
		decimal: function(num, decimal){
			return parseFloat(Math.round(num * 100) / 100).toFixed(decimal);
		},
		digitos: function(num){
			return parseFloat(Math.round(num * 100) / 100).toFixed(0);
		},
		minToTime: function(num_min){
			var sec_num = parseInt(num_min*60, 10); // don't forget the second param
			var hours   = Math.floor(sec_num / 3600);
			var minutes = Math.floor((sec_num - (hours * 3600)) / 60);
			var seconds = sec_num - (hours * 3600) - (minutes * 60);

			if (hours   < 10) {hours   = "0"+hours;}
			if (minutes < 10) {minutes = "0"+minutes;}
			if (seconds < 10) {seconds = "0"+seconds;}
			return hours+':'+minutes+':'+seconds;
		}
	},
	autoPost: function(url, param){
		var form = $('<form>');
	    form.attr('method', 'post');
	    form.attr('action', url);
	    $('body').append(form);
	    for(var key in param) {
	        var val = param[key];
	        if(typeof val != 'undefined'){
	          form.append('<input type="hidden" name="'+key+'" value="'+val+'" />');
	        }
	    }
	    form.submit();
	    form.remove();
	},
	autoPostBlank: function(url, param){
		var form = $('<form>');
	    form.attr('target', '_blank');
	    form.attr('enctype','multipart/form-data');
	    form.attr('method', 'post');
	    form.attr('action', url);
	    $('body').append(form);
	    param[this.tk_k] = this.tk_v;
	    for(var key in param) {
	        var val = param[key];
	        if(typeof val != 'undefined'){
	          var txtArea = document.createElement('textarea');
	          $(txtArea).prop('name', key);
	          txtArea.textContent = val;
	          form.append($(txtArea));
	        }
	    }
	    form.submit();
	    form.remove();
	},

	generarChartImg: function(svgHTML, callback){
		$.ajax({
		    type: 'post',
		    url: 'http://export.highcharts.com/',
		    data: {
		type: 'image/png',async: true,content:'svg', svg: svgHTML, width:700

		},
		    success: function (data) {
		        callback('http://export.highcharts.com/'+ data);
		    }
		});
	},
	generarChartOpt: function(opt, callback){
		opt.credits = {enabled:false};
		opt.title = {text: ''};

		$.ajax({
		    type: 'post',
		    url: 'http://export.highcharts.com/',
		    data: {
		type: 'image/png',async: true,content:'options', options: JSON.stringify(opt), width:700

		},
		    success: function (data) {
		        callback('http://export.highcharts.com/'+ data);
		    }
		});
	}

};

$(function(){
	jQuery.validator.addMethod("sipcopDate", function(value, element) { 
	    return Date.parseExact(value, "yyyy-MM-dd");
	});

	$('.modal').on('hidden.bs.modal', function (e) {
	    if($('.modal').hasClass('in')) {
	    $('body').addClass('modal-open');
	    }    
	});

	/*if($(window).width()<751){
        SipcopJS.crearCookie('sidebar', 'abierto', 1);
    }*/
});