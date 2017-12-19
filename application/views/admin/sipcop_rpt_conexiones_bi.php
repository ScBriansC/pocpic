<?php
$usu_departamento = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,2).'0000'):'');
$usu_provincia = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,4).'00'):'');
$usu_distrito = ((@$usu_ubigeo)?(substr(@$usu_ubigeo,0,6)):'');
$tipo_ubigeo = 0;
if(@$usu_ubigeo){
    if($usu_departamento == $usu_ubigeo){
        $tipo_ubigeo = 1;
    }elseif($usu_provincia == $usu_ubigeo){
        $tipo_ubigeo = 2;
    }else{
        $tipo_ubigeo = 3;
    }
}

if($usu_comisaria > 0){
    $tipo_ubigeo = 4;
}

?>

<style type="text/css">
 iframe, object, embed {
    max-width: 100%;
}
</style>
 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Reporte de Conexiones BI
            </header>
            <div class="panel-body">
            <div id="Reporte">
                <div class="col-sm-12">
                    <div class="adv-table">
                        <iframe width="1200" height="750" src="https://app.powerbi.com/view?r=eyJrIjoiMjU2ZDk4ZmMtN2M4NC00ODM1LWI0MDQtOTVhNWI2YzdhMDk3IiwidCI6IjZmNjQ4ZjQ4LWIzODEtNDllNy05Zjk3LWE4OWQ1NzJmZDNkMCIsImMiOjR9" frameborder="0" allowFullScreen="true"></iframe>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </div>
</div>


