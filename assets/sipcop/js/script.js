$(document).ready(function () {
	if ($('.table').length > 0) {
		let url = window.location.origin, sub = "", data = [];
		if ($('.table').hasClass('table-policia')) {
			sub = '/admin/policia/list';
			data = [{"data": "nombre"}, {"data": "apellido"}, {"data": "idgrado"}, {"data": "codigo"}, {"data": "idvehiculo"}, {"data": "accion"}];
		} else {
			sub = "/admin/vehiculo/list";
			data = [{"data": "placa"}, {"data": "modelo"}, {"data": "marca"}, {"data": "accion"}];
		}
		$('.table').DataTable({
			"processing": true, "ajax": {
				"url": url + sub, "type": "POST"
			}, "columns": data, "aLengthMenu": [[5, 15, 45, -1], [5, 15, 45, 'All']], "language": {
				"aria": {
					"sortAscending": ": Actilet para ordenar la columna de manera ascendente",
					"sortDescending": ": Actilet para ordenar la columna de manera descendente"
				},
				"infoFiltered": "(filtrado  de un total de _MAX_ registros)",
				"lengthMenu": "<span class='seperator'></span>Mostrar _MENU_ registros",
				"sProcessing": "Procesando...",
				"info": "<span class='seperator'></span>Mostrando registros del _START_ al _END_",
				"infoEmpty": "Mostrando registros del 0 al 0",
				"emptyTable": "Ningún dato disponible en esta tabla",
				"search": '<i class="fa fa-search"></i>',
				"paginate": {
					"previous": '<i class="fa fa-angle-left"></i>', "next": '<i class="fa fa-angle-right"></i>'
				},
				"zeroRecords": "No se encontraron resultados"
			}
		});
		$('.table tbody').on('click', 'tr', function () {
			$(this).toggleClass('selected');
		});
	}
	$('.btn-modal').on('click', function () {
		event.preventDefault();
		let url = $(this).attr('data-url');
		$.get(url, null, null, 'HTML')
			.done(function (data) {
				$('.modal-content').html(data);
				$('#modals').modal('show');
				$('.modal-content').find('.chosen-select').select2({
					theme: "classic"
				});
				if ($('.modal-content').find('.chosen-select-ajax').length > 0) {
					let url = window.location.origin;
					$('.modal-content').find('.chosen-select-ajax').select2({
						allowClear: true, minimumResultsForSearch: 20, ajax: {
							url: url + "/admin/vehiculo/list2", dataType: 'json', delay: 250, data: function (params) {
								return {
									q: params.term, page: params.page
								};
							}, processResults: function (data, params) {
								var resData = [];
								data.forEach(function (value) {
									if (value.placa.toUpperCase().indexOf(params.term.toUpperCase()) != -1) resData.push(value)
								})
								return {
									results: $.map(resData, function (item) {
										return {
											text: item.placa, id: item.idvehiculo
										}
									})
								};
							}, cache: true
						}, minimumInputLength: 1,
					});
				}
			});
	});
	$('.container-full').on('click', '.destroy', function () {
		event.preventDefault();
		let url = $(this).attr('data-url'), mensaje = 'eliminar';
		if ($(this).attr('data-original-title') != 'Eliminar') {
			mensaje = 'restaurar';
		}
		swal({
			title: "¿Esta seguro de " + mensaje + " el registro?",
			text: mensaje + " registro",
			type: "info",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true,
		}, function () {
			$.get(url, null, null, 'json')
				.done(function (data) {
					if (data.id != false) {
						window.location.href = data.url;
					} else {
						swal("Error", "Errores encontrados", "error");
					}
				});
		});
	});
	$('.container-full').on('click', '.btn-edit', function () {
		event.preventDefault();
		let url = $(this).attr('data-url');
		$.get(url, null, null, 'HTML')
			.done(function (data) {
				$('.modal-content').html(data);
				$('#modals').modal('show');
				$('.modal-content').find('.chosen-select').select2({
					theme: "classic"
				});
			});
	});
	$('.modal-content').on('submit', '.form-modal', function () {
		event.preventDefault();
		let url = $(this).attr('action'), data = $(this).serialize();
		swal({
			title: "Desea enviar el formulario",
			text: "Enviar formulario",
			type: "info",
			showCancelButton: true,
			closeOnConfirm: false,
			showLoaderOnConfirm: true,
		}, function () {
			$.post(url, data, null, 'json')
				.done(function (data) {
					if (data.id != false) {
						window.location.href = data.url;
					} else {
						swal("Error", "Errores encontrados", "error");
					}
				});
		});
	});
});