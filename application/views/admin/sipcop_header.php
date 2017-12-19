
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <base href="<?= base_url(); ?>">
    <link rel="shortcut icon" href="assets/images/favicon.ico">
    <title>SIPCOP <?php echo (isset($pag_titulo)?(' :: ' . $pag_titulo):''); ?> - Ministerio del Interior</title>
    <!--Core CSS -->
    <link href="assets/bs3/css/bootstrap.min.css" rel="stylesheet">
    <link href="assets/js/jquery-ui/jquery-ui-1.10.1.custom.min.css" rel="stylesheet">
    <link href="assets/css/bootstrap-reset.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet">
    <link href="assets/js/jvector-map/jquery-jvectormap-1.2.2.css" rel="stylesheet">
    <link href="assets/css/clndr.css" rel="stylesheet">
    <!--clock css-->
    <link href="assets/js/css3clock/css/style.css?<?php echo rand(1,1000); ?>" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="stylesheet" href="assets/js/morris-chart/morris.css">
    <!-- Custom styles for this template -->
    <link href="assets/css/style.css?<?php echo rand(1,1000); ?>" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet"/>
    <link rel="stylesheet" type="text/css" href="assets/js/gritter/css/jquery.gritter.css" />
    <link rel="stylesheet" href="assets/js/sweetalert/sweet-alert.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datepicker/css/datepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-timepicker/css/timepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/bootstrap-datetimepicker/css/datetimepicker.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/select2/select2.css" />
    <link rel="stylesheet" type="text/css" href="assets/js/daterangepicker/css/daterangepicker.css">
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->

<?php
if(isset($csslib)){
    foreach ($csslib as $kcss=>$css) {
        echo '<link href="'.$css.'" rel="stylesheet"/>';
    }
}
?>
</head>
<body>
<section id="container">
<!--header start-->
<header class="header fixed-top clearfix">
<!--logo start-->
<div class="brand">

    <a href="admin/home" class="logo">
        <img src="assets/images/logo.png" alt="">
    </a>
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div>
</div>
<!--logo end-->

<div class="top-nav clearfix">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">
        <!--<li>
            <input type="text" class="form-control search" placeholder=" Search">
        </li>-->
        <li>
            <a href="javascript:;" class="tooltips" data-placement="bottom" data-toggle="tooltip" data-original-title="Duración de la sesión" style="padding: 6px 14px;">
                <i class="fa fa-clock-o"></i>
                <span class="username" id="txtSesTiempo">00:00:00</span>
            </a>
        </li>
        <li>
            <button type="button" onclick="modalAyuda()" class="btn btn-round btn-info" style="padding: 6px 14px;"><i class="fa fa-phone"></i> Soporte</button>
        </li>
        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="" src="<?= ($obj_usuario['FLGPNP']==0)?('assets/images/avatar1_small.jpg'):('assets/images/avatar2_small.jpg'); ?>">
                <span class="username"><?= $obj_usuario['NOMBRE']; ?></span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                <li><a href="javascript:;" onclick="openModalChangePass()" ><i class="fa fa-key"></i>Cambiar Contraseña</a></li>
                <li><a href="login/salir"><i class="fa fa-sign-out"></i>Salir</a></li>
            </ul>
        </li>
        <!-- user login dropdown end -->
    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
<!--sidebar start-->
<aside>
    <div id="sidebar" class="nav-collapse<?php echo ($sidebar == 'abierto')?'':' hide-left-bar' ?>">
        <!-- sidebar menu start-->
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
            <?php $this->load->view('admin/sipcop_menu', array('obj_usuario'=>$obj_usuario)); ?>            
                <li class="no-full"></li>
                <li class="no-full"></li>
                <li class="no-full"></li>
                <li class="no-full"></li>
                <li class="no-full"></li>
            </ul>            
        </div>
        <!-- sidebar menu end-->
    </div>
</aside>


<script src="assets/sipcop/js/Change_password.js"></script>

<!--sidebar end-->
<!--main content start-->
<section id="main-content" class="<?php echo ($sidebar == 'abierto')?'':' merge-left' ?>">
<section class="wrapper <?php echo @$wrapper_class; ?>">
