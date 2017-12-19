
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Tablet</title>
    <base href="https://patrullajebicentenario.mininter.gob.pe/ora_desa/" />

    <!-- Icons -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet">
    <link href="assets/css/simple-line-icons.css" rel="stylesheet">

    <!-- Main styles for this application -->
    <link href="assets/css/style_login.css" rel="stylesheet">

</head>

<body class="app flex-row align-items-center">
    <div class="container mt-3">
        <div class="row justify-content-center">
            <div class="col-lg-4">
                <div class="card-group mb-0">
                    <div class="card p-3">
                            <form id="frmPlacas" method="post" autocomplete="off">
                                <input type="hidden" name="<?php echo $this->security->get_csrf_token_name(); ?>" value="<?php echo $this->security->get_csrf_hash(); ?>">
                                <div class="row mb-3">
                                <div class="col-sm-12">
                                	<select id="cboTipo" name="action" class="form-control">
                                		<option value="0">-- Seleccione --</option>
                                		<option value="1">Información</option>
                                		<option value="2">Transmisión  TABLET</option>s
                                	</select>
                                </div>
                                <div class="col-sm-10 pr-0">
	                                    <div class="input-group mb-3">
	                                        <span class="input-group-addon"><i class="fa fa-fire"></i>
	                                        </span>
	                                        <input id="txtPlaca" name="placa" type="text" class="form-control" placeholder="Placa" autocomplete="off">
	                                    </div>
                                    </div>
                                    <div class="col-sm-2 pl-0">
                                            <button type="button" class="btn btn-primary form-control" name="submit" value="buscar"><i class="fa fa-search"></i></button>
                                        </div>
                                </div>
                                <div class="row">
                                    <div class="col-12" id="dvPlacas">
                                        
                                    </div>
                                </div>
                            </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap and necessary plugins -->
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/tether/js/tether.min.js"></script>
    <script src="assets/bs3/js/bootstrap.min.js"></script>
    <script>
    $(function(){
        $('form#frmPlacas').submit(function(ev){
            ev.preventDefault();
            $('#dvPlacas').html('Buscando...');
            $.post('tablet/buscar', $(this).serialize(), function(resp){
                $('#dvPlacas').html('');
                $.each(resp.data, function(idx, objx){
                	var placa = '';
                	if($('#cboTipo').val() == '1'){
                		placa = '<div style="border-bottom: 1px dashed #333;padding-bottom: 3px;margin-bottom: 3px;">'+
	                	'<strong>Placa: </strong> '+objx.LABEL+'<br>'+
	                	'<strong>Comisaría: </strong> '+objx.COMISARIA+'<br>'+
	                	'<strong>Proveedor seleccionado: </strong> '+objx.PROVEEDOR+'<br>'+
	                	'<strong>IP: </strong> '+objx.ADDRESS+'<br>'+
	                	'<strong>Departamento: </strong> '+objx.DEPARTAMENTO+'<br>'+
	                	'<strong>Provincia: </strong> '+objx.PROVINCIA+'<br>'+
	                	'<strong>Distrito: </strong> '+objx.DISTRITO+'<br>'+
	                	'</div>';
                	}else if($('#cboTipo').val() == '2'){
                		placa = '<div style="border-bottom: 1px dashed #333;padding-bottom: 3px;margin-bottom: 3px;">'+
	                	'<strong>Placa: </strong> '+objx.LABEL+'<br>'+
	                	'<strong>Comisaría: </strong> '+objx.COMISARIA+'<br>'+
	                	'<strong>Proveedor seleccionado: </strong> '+objx.PROVEEDOR+'<br>'+
	                	'<strong>IP: </strong> '+objx.ADDRESS+'<br>'+
	                	'<strong>Fecha ini: </strong> '+objx.FECHAMIN+'<br>'+
	                	'<strong>Fecha fin: </strong> '+objx.FECHAMAX+'<br>'+
	                	'<strong>Departamento: </strong> '+objx.DEPARTAMENTO+'<br>'+
	                	'<strong>Provincia: </strong> '+objx.PROVINCIA+'<br>'+
	                	'<strong>Distrito: </strong> '+objx.DISTRITO+'<br>'+
	                	'</div>';
                	}
					$('#dvPlacas').append(placa);
                });
            }, 'json').fail(function(err){
                $('#dvPlacas').html('Error al buscar');
            });
        });
        $("form#frmPlacas button").click(function(ev) {
            ev.preventDefault();
            $('#txtAction').val($(this).val());
            $('form#frmPlacas').submit();
        });
    });

    </script>


</body>

</html>