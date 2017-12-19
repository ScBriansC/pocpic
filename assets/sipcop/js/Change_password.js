function openModalChangePass(){
    $('#changePassword').modal('show');
    $('#changePassword').find('input').val('');
    $("#dbcontra").show();
    $("#dbvalido").hide();
}



function changePassword(){

    var passActual = $.trim($('#passActual').val());
    var passNuevo = $.trim($('#passNuevo').val());
    var passNuevoAgain = $.trim($('#passNuevoAgain').val());

    var blank = ' ';


    if(!passActual==' ' || passActual.search(blank) > 0){
        $("#errorPass").css("display","none");
        if(passActual.length >= 3)
        {
            $("#errorPass").css("display","none");
            if(!passNuevo==' ' || passNuevo.search(blank) > 0){
                $("#errorPassN").css("display","none");
                if(passNuevo.length >= 8)
                {
                    $("#errorPassN").css("display","none");
                    if(!passNuevoAgain==' ' || passNuevoAgain.search(blank) > 0){
                        $("#errorPassNN").css("display","none");
                        if(passNuevoAgain.length >= 8)
                        {
                             $("#errorPassN").css("display","none");
                             if(passNuevoAgain===passNuevo){
                                $("#errorPassN").css("display","none");
                                SipcopJS.post('admin/home/json_checkpassword',{
                                    claveAnt: passActual,
                                    claveNew: passNuevoAgain,
                                }, 
                                function(data){
                                    if(data.status == 'confirm'){
                                        $("#dbcontra").css("display","none");
                                        $("#dbvalido").css("display","");
                                    }else{
                                       $("#errorPass").css("display","");
                                       $("#errorPass" ).html( "La contraseña no es correcta" );
                                    }                                    
                                });
                             }
                             else{
                                 $("#errorPassNN").css("display","");
                                 $( "#errorPassNN" ).html( "Tiene que ser igual al campo anterior" );
                             }
                        }
                        else{
                            $("#errorPassNN").css("display","");
                            $( "#errorPassNN" ).html( "Minimo 8 Caracterés" );
                        }
                    }else{
                        $("#errorPassNN").css("display","");
                        $("#errorPassNN" ).html( "No se aceptan campos en blanco" );
                    }
                }else{
                     $("#errorPassN").css("display","");
                     $("#errorPassN" ).html( "Minimo 8 Caracterés" );
                }
            }else{
                $("#errorPassN").css("display","");
                $("#errorPassN" ).html( "No se aceptan campos en blanco" );
            }
        }
        else{
            $("#errorPass").css("display","");
            $( "#errorPass" ).html( "Minimo 3 Caracterés" );
        }
    }else{
        $("#errorPass").css("display","");
        $( "#errorPass" ).html( "No se aceptan campos en blanco" );
    }
}

function validartoken(){

    var passActual = $.trim($('#passActual').val());
    var passNuevo = $.trim($('#passNuevo').val());
    var passNuevoAgain = $.trim($('#passNuevoAgain').val());
    var token = $.trim($('#lbtoken').val());
    var blank = ' ';


    if(!passActual==' ' || passActual.search(blank) > 0){
        $("#errorPass").css("display","none");
        if(passActual.length >= 3)
        {
            $("#errorPass").css("display","none");
            if(!passNuevo==' ' || passNuevo.search(blank) > 0){
                $("#errorPassN").css("display","none");
                if(passNuevo.length >= 8)
                {
                    $("#errorPassN").css("display","none");
                    if(!passNuevoAgain==' ' || passNuevoAgain.search(blank) > 0){
                        $("#errorPassNN").css("display","none");
                        if(passNuevoAgain.length >= 8)
                        {
                             $("#errorPassN").css("display","none");
                             if(passNuevoAgain===passNuevo){
                                $("#errorPassN").css("display","none");
                                //
                                if(!token==' ' || token.search(blank) > 0){
                                    $("#errortoken").css("display","none");
                                    SipcopJS.post('admin/home/json_updatePassword',{
                                        claveAnt:passActual,
                                        claveNew:passNuevoAgain,
                                        token:token
                                    },
                                    function(data){
                                            if(data.data >0){
                                                $('#changePassword').modal('hide');
                                                SipcopJS.msj.success('Mensaje', 'La contraseña ha sido actualizada');
                                            }
                                    });
                                }else{
                                    $("#errortoken").css("display","");
                                    $("#errortoken" ).html( "Token Incorrecto" );
                                }  
                                //                           
                             }
                             else{
                                 $("#errorPassNN").css("display","");
                                 $( "#errorPassNN" ).html( "Tiene que ser igual al campo anterior" );
                             }
                        }
                        else{
                            $("#errorPassNN").css("display","");
                            $( "#errorPassNN" ).html( "Minimo 8 Caracterés" );
                        }
                    }else{
                        $("#errorPassNN").css("display","");
                        $("#errorPassNN" ).html( "No se aceptan campos en blanco" );
                    }
                }else{
                     $("#errorPassN").css("display","");
                     $("#errorPassN" ).html( "Minimo 8 Caracterés" );
                }
            }else{
                $("#errorPassN").css("display","");
                $("#errorPassN" ).html( "No se aceptan campos en blanco" );
            }
        }
        else{
            $("#errorPass").css("display","");
            $( "#errorPass" ).html( "Minimo 3 Caracterés" );
        }
    }else{
        $("#errorPass").css("display","");
        $( "#errorPass" ).html( "No se aceptan campos en blanco" );
    }

}