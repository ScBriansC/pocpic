 var v=true;
  // $("span.help-block").hide();

  function verificar(){
      var v1=0,v2=0,v3=0,v4=0,v5=0;
      v1=validacion('txtTitulo');
      v2=validacion('txtDetalle');
      v3=validacion('txtTipo');
      v4=validacion('txtEstado');
      v5=validacion('txt_mapa_direccion');
      if (v1===false || v2===false || v3===false || v4===false || v5===false) {
           swal("Advertencia", "Debe Completar todo los campos", "error");
      }else{
          guardar();
      }
  } 


  function validacion(campo){
      var a=0;
      
      if (campo==='txtTitulo')
      {
          titulo = document.getElementById(campo).value;
          if( titulo == null || titulo.length == 0 || /^\s+$/.test(titulo) ) {
              $("#glypcn"+campo).remove();
              $('#'+campo).parent().attr("class", "form-group has-error has-feedback");
              $('#'+campo).parent().children('span').text("Ingrese Titulo").show();
              $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
              return false;
             
          }
          else
          {
              if(!isNaN(titulo)) {
                  $("#glypcn"+campo).remove();
                  $('#'+campo).parent().attr("class", "form-group has-error has-feedback");
                  $('#'+campo).parent().children('span').text("No Acepta Numeros").show();
                  $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");      
                  return false;
              }
              else{

                  $("#glypcn"+campo).remove();
                  $('#'+campo).parent().attr("class", "form-group has-success has-feedback");
                  $('#'+campo).parent().children('span').hide();
                  $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
                  return true;
              }
              
              
          }     
      }
      if (campo==='txtDetalle'){

          detalle = document.getElementById(campo).value;
          if( detalle == null || detalle.length == 0 || /^\s+$/.test(detalle) ) {
              
              $("#glypcn"+campo).remove();
              $('#'+campo).parent().attr("class", "form-group has-error has-feedback");
              $('#'+campo).parent().children('span').text("Ingrese Detalle").show();
              $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
              return false;
              
          }
          else{
          
                  $("#glypcn"+campo).remove();
                  $('#'+campo).parent().attr("class", "form-group has-success has-feedback");
                  $('#'+campo).parent().children('span').hide();
                  $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
                  return true;
                         
          } 
      }

      if (campo==='txtTipo'){

          tipo = document.getElementById(campo).selectedIndex;
          if( tipo == null || tipo == 0 ) {
              $('#txtTipo').parent().attr("class", "form-group has-error");
              return false;
          }
          else{
              $('#txtTipo').parent().attr("class", "form-group has-success");
              return true;

          }
      }

      if (campo==='txtEstado'){

          estado = document.getElementById(campo).selectedIndex;
          if( estado == null || estado == 0 ) {
              $('#txtEstado').parent().attr("class", "form-group has-error");
              return false;
          }
          else{
              $('#txtEstado').parent().attr("class", "form-group has-success");
              return true;

          }
      }

      if (campo==='txt_mapa_direccion'){

          direccion = document.getElementById(campo).value;
          if( direccion == null || direccion.length == 0 || /^\s+$/.test(direccion) ) {
              
              $("#glypcn"+campo).remove();
              $('#'+campo).parent().attr("class", "form-group has-error has-feedback");
              $('#'+campo).parent().children('span').text("Ingrese Dirección").show();
              $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-remove form-control-feedback'></span>");
              return false;
              
          }
          else{
              $("#glypcn"+campo).remove();
              $('#'+campo).parent().attr("class", "form-group has-success has-feedback");
              $('#'+campo).parent().children('span').hide();
              $('#'+campo).parent().append("<span id='glypcn"+campo+"' class='glyphicon glyphicon-ok form-control-feedback'></span>");
              return true;                         
          } 
      }

  }
