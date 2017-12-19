<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <base href="<?= base_url(); ?>">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <title>SIPCOP  - Ministerio del Interior</title>
    <!--Core CSS -->
    <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/js/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/js/jvector-map/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <link href="assets/css/clndr.css" rel="stylesheet">
    <!--clock css-->
    <link href="assets/js/css3clock/css/style.css" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/js/morris-chart/morris.css">
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css?562" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" href="assets/js/sweetalert/sweet-alert.css" />
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

</head>
<body>

<link href="assets/sipcop/css/home.css" rel="stylesheet">

<script type="text/javascript" src="https://maps.google.com/maps/api/js?key=AIzaSyAQGNCgXhTrE7TJROgFSOftaosTVUtqXY8&libraries=visualization"></script>


<script type="text/javascript" src="assets/sipcop/js/markerclusterer.js"></script>
<script type="text/javascript" src="assets/sipcop/js/MarkerWithLabel.js"></script>
<script type="text/javascript" src="assets/sipcop/js/app_capturador.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/CapturadorMrkr.js?<?php echo rand(1,1000); ?>"></script>
<script type="text/javascript" src="assets/sipcop/js/map_jurisdiccion_capturador.js"?<?php echo rand(1,1000); ?>"></script>


<div id="cnv_map" class="map-full" style="overflow:hidden;"></div>


<div class="app-bg-loader"><img src="assets/sipcop/img/loader.gif" width="320"></div>

<script>
var fecha_actual;


function actualizar_posiciones(){
    capturador_api.descargar_gps();

}

preCarga = function(){
    SipcopJS.logEnabled = true;


    fecha_actual = (new Date()).toString('dd/MM/yyyy');
    capturador_api.usu_comisaria = 0;

    function initJS(){
        capturador_api.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
        jurisdiccion_api.init('0',capturador_api.usu_comisaria,'<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
    }

    initJS();
    

    

    $(window).resize(function(){
        $('#cnv_map').css('width','100%');
        $('#cnv_map').height($(window).height()-$('header.header').height() - 6);
       // $('.select-fil form-control').select2();
    });

    $(window).resize();

    actualizar_posiciones();
    
    setInterval(function(){
        actualizar_posiciones();
    }, 30000);

    setInterval(function(){
        capturador_api.programarSaleJurisdiccion();
    }, 60000);


}
</script>


<script src="assets/js/date.js"></script>
<script src="assets/js/jquery.js"></script>
<script src="assets/js/jquery.mask.min.js"></script>
<script src="assets/js/jquery-ui/jquery-ui-1.10.1.custom.min.js"></script>
<script src="assets/bs3/js/bootstrap.min.js"></script>
<script src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
<script src="assets/js/jquery.scrollTo.min.js"></script>
<script src="assets/js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js"></script>
<script src="assets/js/jquery.nicescroll.js"></script>
<script src="assets/js/morris-chart/morris.js"></script>
<script src="assets/js/sweetalert/sweet-alert.js"></script>
<script src="assets/js/morris-chart/raphael-min.js"></script>
<script src="assets/js/jquery.customSelect.min.js" ></script>
<script type="text/javascript" src="assets/js/jquery.validate.min.js"></script>
<script type="text/javascript" src="assets/js/gritter/js/jquery.gritter.js"></script>
<script type="text/javascript" src="assets/js/jquery.numeric.js"></script>
<script type="text/javascript" src="assets/js/bootbox.js"></script>
<!--common script init for all pages-->
<script src="assets/sipcop/js/SipcopJS.js"></script>
<script src="assets/js/scripts.js"></script>
<script>
SipcopJS.init('<?php echo $this->security->get_csrf_token_name(); ?>','<?php echo $this->security->get_csrf_hash(); ?>');
</script>
<!--script for this page-->
<script>
$(function(){
    try{
        
        if(typeof preCarga != 'undefined'){
            preCarga();
        }
    }catch(err){SipcopJS.log(err);}
});
</script>


</body>
</html>