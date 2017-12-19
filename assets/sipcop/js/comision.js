var comision_api = {


	init:function(){


		$("body").on('click',"#btn-comisionon",function(){
		    comision_api.salioComision();
		});

		$("body").on('click',"#btn-comisionoff",function(){
		   swal({			
		   		title: 'Oppss...!',
  				type: 'info',
				text: 'No puede activar está opción por que la radio no está emitiendo señal', 

			});
		});
	},

	salioComision: function(contenedor,callback){

		var dispogps = map_api.mrkPatrullajeSelected.args.oPatrullaje.DispoGPS;
		var param = {};
		param.dispogps = dispogps;
		param[map_api.k_it] = map_api.v_it;

		// param.data = '';
		// param['data'] = '';

		if(dispogps){
            $.post('admin/home/saliocomision',param, function(data){

            	console.log(data);
                // var cad = '<table width="100%" border="0">'+
                //             '<thead><tr><th>Turno</th>';
                //             if($('#ckPatrullero').is(':checked')){
                //                 cad+='<th style="text-align:center">Patrulleros</th>';
                //             }
                //             if($('#ckMotorizado').is(':checked')){
                //                 cad+='<th style="text-align:center">Motorizados</th>';
                //             }
                //             if($('#ckPatpie').is(':checked')){
                //                 cad+='<th style="text-align:center">Pat. Pie</th>';
                //             }

                //             cad+='</tr></thead>'+
                //             '<tbody>';
                //  $.each(data.resumen_turno, function(idx, obj){
                //         cad += '<tr><td>'+obj.TURNO+'</td>';
                //         if($('#ckPatrullero').is(':checked')){
                //             cad += '<td align="center">'+(parseInt(obj.TotalPatrullero)+parseInt(obj.TotalPatInt))+'/'+(parseInt(data.resumen_total.TotalPatrullero)+parseInt(data.resumen_total.TotalPatInt))+'</td>';
                //         }
                //         if($('#ckMotorizado').is(':checked')){
                //             cad += '<td align="center">'+obj.TotalMotorizado+'/'+data.resumen_total.TotalMotorizado+'</td>';
                //         }
                //         if($('#ckPatpie').is(':checked')){
                //             cad += '<td align="center">'+obj.TotalPatPie+'/'+data.resumen_total.TotalPatPie+'</td>';
                //         }
                //         cad += '</tr>';
                // });

                // cad += '</tbody></table>';
                // $(contenedor).html(cad);
                // if(callback){
                //     callback({resp:'ok', status:'1'});
                // }

            },'json').fail(function(err){
                if(callback){
                    callback({err:err, status:'0'});
                }
            });



        }else{
        	swal({			
			   		title: 'Oppss...!',
	  				type: 'info',
	  				text: 'No existe DispoGPS',
			});
            if(callback){
                callback({resp:'ok', status:'1'});
            }
        }


	},

	volvioComision: function(){
		
	}
};
