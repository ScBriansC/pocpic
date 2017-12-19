<script>
var opt;

    function filtrar(){

        $('#dgTabla').dataTable().fnClearTable();
        $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').show();
        SipcopJS.post('admin/home/json_sesusu',{}, 
            function(data){
                $('.dataTables_processing', $('#dgTabla').closest('.dataTables_wrapper')).css('visibility','visible').hide(); 
            cargarData(data);
        });
    }

    function cargarData(reporte){
        $('#dgTabla').dataTable().fnClearTable();
        $('#dgTabla').dataTable().fnAddData(reporte.data);
        $('#dgTabla').dataTable().fnDraw();

     }

    function forzarCierre(sesion){
        SipcopJS.msj.confirm('Confirmación', '¿Está seguro de forzar el cierre de esta sesión?', function(resp){
            if(resp){
                SipcopJS.post('admin/home/forzar_cierre', {sesion:sesion}, function(data){
                    if(data.status == 'success'){
                        SipcopJS.msj.success('Éxito', data.msj);
                        filtrar();
                    }else{
                        SipcopJS.msj.error('Error', data.msj);
                    }
                });
            }
        });
        
    }

    preCarga = function(){SipcopJS.logEnabled = false;
      $('#dgTabla').dataTable({
          "bSort": true,
          "bFilter": true,
          "bPaginate": true,
          "pageLength": 10,
          "bInfo": true,
          "aoColumns": [
            {sTitle : "Dependencia", mData: "ComisariaNombre", mRender:function(data, type, row){
                return (data)?data:'Sin dependencia';
            }},
            {sTitle : "Usuario", mRender:function(data, type, row){
                return row.UsuarioNombre + ' ' + row.UsuarioApellido;
            }},
            {sTitle : "Fecha y hora", mData: "SesionFecha"},
            {sTitle : "IP", mData: "SesionIP"},
            {sTitle : " ", mRender: function(data, type, row){
                return '<a href="javascript:;" onclick="forzarCierre(\''+row.SesionID+'\')" class="btn btn-primary btn-xs tooltips" data-toggle="button" data-placement="top" data-original-title="Forzar cierre"><i class="fa fa-trash-o"></i></a>';
            }}
          ], 
          "bServerSide" : false,
          "bProcessing" : true
        });

        filtrar();

    }

    
 </script>


 <div class="row">
    <div class="col-sm-12">
        <section class="panel">
            <header class="panel-heading">
                Sesiones de usuario activas
            </header>
            <div class="panel-body">
            <div id="Reporte">
                <div class="col-sm-12">
                    <div class="adv-table">
                        <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgTabla" width="100%">
                        </table>
                    </div>
                </div>
            </div>
            </div>
        </section>
    </div>
</div>


<div id="modalDetalle" tabindex="-10" role="dialog" aria-labelledby="basicModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false" class="modal fade">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" data-dismiss="modal" aria-label="Close" class="close"><span aria-hidden="true">×</span></button>
        <h4 id="basicModalLabel" class="modal-title">Detalle de inventario &nbsp;&nbsp;&nbsp;
        <a id="btnExportar" href="javascript:;" class="btn btn-success btn-xs" data-toggle="button" >
        <i class="fa fa-download"></i> Exportar</a></h4>
      </div>
      <div class="modal-body">
        <div class="adv-table">
          <table  class="display table table-bordered table-striped table-condensed cf dgTabla" id="dgDetalle" width="100%">                   
          </table>
        </div>
      </div>
      <div class="clear"></div>
    </div>
  </div>
</div>