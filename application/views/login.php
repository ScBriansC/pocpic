<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Patrullaje Bicentenario - Ministerio del Interior</title>
    <base href="<?php echo base_url(); ?>" />

    <!-- Icons -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="assets/css/style.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="card-group mb-0">
                    <div class="card p-4">
                        <div class="card-block">
                            <form id="frmLogin" method="post" autocomplete="off">
                                <p class="text-muted" align="center"><img src="assets/img/logos.png" width="100" /></p>
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <input id="txtAction" type="hidden" name="action" value="login">
                                <div class="dv-login  mb-3">
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-user"></i>
                                        </span>
                                        <input id="txtUsuario" name="usr_codigo" type="text" class="form-control" placeholder="Usuario" autocomplete="off">
                                    </div>
                                    <div class="input-group mb-3">
                                        <span class="input-group-addon"><i class="icon-lock"></i>
                                        </span>
                                        <input id="txtClave" name="usr_clave" type="password" class="form-control" placeholder="Clave" autocomplete="off">
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary px-4 col-12" name="submit" value="login">Ingresar</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="dv-token mb-3" style="display:none">
                                    <div class="row">
                                        <div class="col-12">
                                            Ingrese el código que fue enviado a su celular, vence en <span class="tiempo" style="color:#20a8d8; font-weight: bold;"></span> segundos.
                                            <br><br>
                                        </div>
                                    </div>
                                    <div class="input-group mb-4">
                                        <span class="input-group-addon"><i class="icon-screen-smartphone"></i>
                                        </span>
                                        <input id="txtCodigo" name="usr_tokensms" type="text" class="form-control" placeholder="Código SMS" autocomplete="off">
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <button type="button" class="btn btn-primary px-4 col-12" name="submit" value="token">Verificar</button>
                                        </div>
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-12">
                                        <div id="dvResp" class="card-inverse card-success text-center" style="display: none;"></div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and necessary plugins -->
    <script src="assets/components/jquery/jquery.min.js"></script>
    <script src="assets/components/tether/js/tether.min.js"></script>
    <script src="assets/components/bootstrap/js/bootstrap.min.js"></script>
    <script>
    $(function(){
        $('form#frmLogin').submit(function(ev){
            ev.preventDefault();
            $('#dvResp').html('').fadeOut('fast');
            $.post('login/validar', $(this).serialize(), function(resp){
                $('#dvResp').removeClass('card-success').removeClass('card-danger');
                if(resp.status == 'success'){
                    $('#dvResp').addClass('card-success').html(resp.msg).fadeIn('fast', function(){
                        document.location.href = 'admin/home';
                    });
                }else if(resp.status == 'confirm'){
                    $('.dv-login').hide();
                    $('.dv-token').show();
                    tiempo_token = parseInt(resp.tiempo);
                    $('.tiempo').html(tiempo_token);
                    temporizador();
                }else{
                    $('#dvResp').addClass('card-danger').html(resp.msg).fadeIn('fast');
                }
            }, 'json').fail(function(err){
                $('#dvResp').addClass('card-danger').html('Ocurrió un error').fadeIn('fast');
            });
        });
        $("form#frmLogin button").click(function(ev) {
            ev.preventDefault();
            $('#txtAction').val($(this).val());
            $('form#frmLogin').submit();
        });
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
                $('#txtCodigo, #txtUsuario, #txtClave, #txtAction').val('');
            }
            $('.tiempo').html(tiempo_token);
        }, 1000);
    }
    </script>


</body>

</html>