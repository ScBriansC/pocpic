<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sistema Informático de Planificación y Control del Patrullaje Policial">
    <meta name="author" content="OGTIC - MININTER">
    <meta name="keyword" content="SIPCOP">
    <base href="<?php echo base_url(); ?>">
    <link rel="shortcut icon" href="assets/img/favicon.png">

    <title>SIPCOP - Ministerio del Interior</title>

    <!-- Bootstrap core CSS -->
    <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css?1" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="assets/js/gritter/css/jquery.gritter.css" />

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
    <script src="assets/js/html5shiv.js"></script>
    <script src="assets/js/respond.min.js"></script>
    <![endif]-->
    <script src="assets/js/jquery.js"></script>
    <script type="text/javascript" src="assets/js/gritter/js/jquery.gritter.js"></script>
    <style>
    html,body{
        margin-top: 0px!important;
        height: 100%;
    }
    </style>
    
</head>

<body class="lock-screen">
    <div class="lock-wrapper" style="margin-top:0px;">

        <div id="login-logo">
            <img src="assets/images/logo-login.png" id="Logo" />

        </div>
        <div class="lock-box text-center">
            <form role="form" class="form-inline frm-login-acc" method="post" id="formLogin" autocomplete="off">
                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                <input id="txtAction" type="hidden" name="action" value="login">
                <div id="SIPCOP"><img class="imglock_1 active" src="assets/images/sipcop_1.jpg" /><img class="imglock_2" src="assets/images/sipcop_2.jpg" /></div>
                <div class="lock-usr">
                <div class="form-group lock-inp">
                    <input type="text" placeholder="Usuario" id="txtUsuario" name="usr_codigo" class="form-control lock-input">
                    </div>
                </div>
                
                <div class="lock-token">
                    Ingrese su código, vence en:<br><span class="tiempo"></span> segundos
                </div>
                <div class="lock-pwd">
                    <div class="form-group lock-inp">
                        <input type="password" placeholder="Contraseña" id="txtClave" name="usr_clave" class="form-control lock-input">
                        <input type="text" placeholder="Código SMS" id="txtCodigo" name="usr_tokensms" class="form-control lock-input">
                        <button id="btnSubmit" class="btn btn-lock" type="submit">
                            <i class="fa fa-arrow-right"></i>
                        </button>
                    </div>
                </div>
                <div class="clear"></div>
                <div class="login-sep-inf"></div>
                <div class="clear"></div>
            </form>

        </div>
    </div>
    

<script>
$(function(){

    $('#formLogin').submit(function(ev){
        ev.preventDefault();
        var frm_data = $(this).serialize();
        $.post('login/validar', frm_data, function(resp){
            if(resp.status == 'success'){
                $.gritter.add({
                    position: 'bottom-right',
                    title: 'Mensaje',
                    text: 'Datos correctos',
                    class_name: 'gritter-success'
                });
                document.location.href = 'admin/home';
            }else if(resp.status == 'confirm'){
                $('#txtAction').val('token');
                $('#formLogin').removeClass('frm-login-acc');
                $('#formLogin').addClass('frm-login-confirm');
                $('.dv-login').hide();
                $('.dv-token').show();
                tiempo_token = parseInt(resp.tiempo);
                $('.tiempo').html(tiempo_token);
                temporizador();
            }else{
                $.gritter.add({
                    position: 'bottom-right',
                    title: 'Error',
                    text: resp.msg,
                    class_name: 'gritter-error'
                });
            }
        }, 'json').fail(function(err){
            $.gritter.add({
                position: 'bottom-right',
                title: 'Error',
                text: 'Seleccione una hora válida',
                class_name: 'gritter-error'
            });
        });
    });    

    function cambiarAvatar(num){
        
        setTimeout(function(){
            var num_next = (num==1)?2:1;
            $('.imglock_'+num).fadeOut('slow', function(){
                $('.imglock_'+num).removeClass('active');
                $('.imglock_'+num_next).addClass('active');
                $('.imglock_'+num).show();
                cambiarAvatar(num_next);
            });
        },2000);
        
    }

    cambiarAvatar(1);

    $(window).resize(function(){
        if($(window).width() > 767){
            $('.lock-wrapper').css('margin-top',($(window).height() - $('.lock-wrapper').innerHeight())/2 - 50);
        }else{
            $('.lock-wrapper').css('margin-top',0);
        }
    });
    $(window).resize();
});

var tiempo_token = 0;
var t_interval = null;
function temporizador(){
    if(t_interval != null){
        clearInterval(t_interval);
        t_interval = null;
    }
    t_interval = setInterval(function(){
        
        tiempo_token--;
        if(tiempo_token<=0){
            tiempo_token = 0;
            $('.dv-login').show();
            $('.dv-token').hide();
            clearInterval(t_interval);
            t_interval = null;
            $('#txtCodigo, #txtUsuario, #txtClave').val('');      
            $('#txtAction').val('login');
            $('#formLogin').addClass('frm-login-acc');
            $('#formLogin').removeClass('frm-login-confirm');
        }
        $('.tiempo').html(tiempo_token);
    }, 1000);
}

</script>
</body>
</html>