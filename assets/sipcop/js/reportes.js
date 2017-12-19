var reporte_api = {
    opcion_ui: 0,
    marker_added: null,

    usu_comisaria:0,
    filtro: {
        fecha: '',
        horaini:'',
        horafin:'',
        tipo_filtro: 0,
        ubigeo:'',
        comisaria:0,
        reset: 0
    },

    taskCount: 0,
    k_it:'',
    v_it:'',

    taskError:[],

    init: function(k_it, v_it){
        this.k_it=k_it;
        this.v_it=v_it;
        this.filtro[k_it] = v_it;
        // this.add_TaskLoader();
        // this.cargar('.cont-resumen',function(resp_data){
        //     reporte_api.end_TaskLoader();
        // });
    },

    add_TaskLoader: function(){
        if(!this.taskLoader){
            this.taskLoader=0;
        }
        if(this.taskLoader == 0){
            $('.btn-filtrar').addClass('fil-searching').html('Buscando...'); 
            $('.app-bg-loader img').centerBG();           
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
            reporte_api.taskError = [];
        }

        reporte_api.taskError.put(msj);
    },

	cargar: function(contenedor,callback){
        var filtro = false;
        filtro = reporte_api.filtro;
        filtro.fecha = $('#txtFecha3').val();
        filtro.horaini = $('#txtHoraIni3').val();
        filtro.horafin = $('#txtHoraFin3').val();
        filtro.comisaria = $('#txtComisaria3').val();
        filtro.ubigeo =reporte_api.get_ubigeo();
        filtro[this.k_it]=this.v_it;
        reset = 1;

        $('.cont-resumen').html('');
        console.log(filtro);
        if(filtro){
            $.post('admin/reporte/json_distancia_recorrida',filtro, function(data){
                
                var cad ='<table id="example3" class="display" cellspacing="0" width="100%"><thead><tr><th>DEPARTAMENTO</th><th>PROVINCIA</th><th>DISTRITO</th><th>COMISARIA</th><th>IDRADIO</th><th>PLACA</th><th>FECHA</th><th>HORA INICIO</th><th>HORA FIN</th><th>DISTANCIA RECORRIDA APROX</th></tr>'+
                         '</thead><tbody>';
                 $.each(data.distancia, function(idx, obj){
                      cad += '<tr><td>'+obj.UbigeoDepartamento+'</td>'+
                             '<td >'+obj.UbigeoProvincia+'</td>'+
                             '<td >'+obj.UbigeoDistrito+'</td>'+
                             '<td >'+obj.ComisariaNombre+'</td>'+
                             '<td >'+obj.RadioEtiqueta+'</td>'+
                             '<td >'+obj.VehiculoPlaca+'</td>'+
                             '<td align="center">'+obj.TrackerFecha+'</td>'+
                             '<td align="center">'+obj.TrackerHoraIni+'</td>'+
                             '<td align="center">'+obj.TrackerHoraFin+'</td>'+
                             '<td align="center">'+obj.TrackerKm+'</td></tr>';
                });
             
                cad += '</tbody></table>';
                // cad += '$(document).ready(function() {$("#example3").DataTable();} );';
                $('.cont-resumen').html(cad);
                if(callback){
                    callback({resp:'ok', status:'1'});
                }
            },'json').fail(function(err){
                if(callback){
                    callback({err:err, status:'0'});
                }
            });
        }      
        else{
            var cad = '<table id="example3" class="display" cellspacing="0" width="100%"><thead><tr><th>DEPARTAMENTO</th><th>PROVINCIA</th><th>DISTRITO</th><th>COMISARIA</th><th>IDRADIO date</th><th>PLACA</th><th>FECHA</th><th>HORA INICIO</th><th>HORA FIN</th><th>DISTANCIA RECORRIDA APROX</th></tr>'+
                         '</thead><tbody>';
            cad += '<tr><td align="center">No tiene activa la capa de Motorizados o Patrulleros</td></tr>';
            cad += '</tbody></table>';
            $('.cont-resumen').html(cad);
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
    }

}