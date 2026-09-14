<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplateinventory\Views\modulesSaldos/modalCaptureSaldos') ?>
<?= $this->include('julio101290\boilerplateinventory\Views\modulesSaldos/extraFields') ?>
<?= $this->include('julio101290\boilerplatemaintenance\Views/modulesProductsEmployes/modalEmployesProducts') ?>

<div class="card card-outline card-primary shadow-sm">
    <div class="card-header bg-white py-3">
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center">
            <!-- Filtros principales -->
            <div class="form-row align-items-end flex-grow-1 mr-md-3">
                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3 mb-xl-0">
                    <label for="idEmpresaList" class="font-weight-bold text-secondary small mb-1">
                        <i class="fas fa-building mr-1 text-primary"></i> Empresa
                    </label>
                    <select class="form-control form-control-sm idEmpresaList" name="idEmpresaList" id="idEmpresaList" style="width:100%;">
                        <option value="0">Seleccione empresa</option>
                        <?php foreach ($empresas as $value): ?>
                            <option value="<?= $value['id'] ?>">
                                <?= $value['id'] ?> - <?= $value['nombre'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-3 col-lg-3 col-md-4 col-sm-6 mb-3 mb-xl-0">
                    <label for="idAlmacen" class="font-weight-bold text-secondary small mb-1">
                        <i class="fas fa-warehouse mr-1 text-primary"></i> Almacén
                    </label>
                    <select name="idAlmacen" id="idAlmacen" style="width: 100%;" class="form-control form-control-sm idAlmacen form-controlProducts">
                        <option value="0">Seleccione Almacén</option>
                    </select>
                </div>

                <div class="col-xl-4 col-lg-4 col-md-4 col-sm-8 mb-3 mb-xl-0" hidden>
                    <label for="idProducto" class="font-weight-bold text-secondary small mb-1">
                        <i class="fas fa-box-open mr-1 text-primary"></i> Productos
                    </label>
                    <select name="idProducto" id="idProducto" style="width: 100%;" class="form-control form-control-sm idProducto form-controlProducts">
                        <option value="0" selected>Seleccione el producto</option>
                    </select>
                </div>

                <div class="col-xl-2 col-lg-2 col-md-12 col-sm-4 mb-3 mb-xl-0">
                    <button type="button" class="btn btn-primary btn-sm btn-block shadow-sm btnAceptar" id="btnAceptar" name="btnAceptar">
                        <i class="fa fa-filter mr-1"></i> Filtrar
                    </button>
                </div>
            </div>

            <!-- Botones de acciones globales -->
            <div class="mt-2 mt-md-0">
                <button class="btn btn-success btn-sm shadow-sm btnPrintCodes">
                    <i class="fa fa-barcode mr-1"></i> Imprimir Códigos
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableSaldos" class="table table-striped table-hover table-bordered va-middle tableSaldos text-nowrap w-100">
                        <thead class="thead-light">
                            <tr>
                                <th>#</th>
                                <th><?= lang('saldos.fields.idEmpresa') ?></th>
                                <th><?= lang('saldos.fields.idAlmacen') ?></th>
                                <th><?= lang('saldos.fields.lote') ?></th>
                                <th><?= lang('saldos.fields.fullname') ?></th>
                                <th><?= lang('saldos.fields.idProducto') ?></th>
                                <th><?= lang('saldos.fields.codigoProducto') ?></th>
                                <th><?= lang('saldos.fields.descripcion') ?></th>
                                <th><?= lang('saldos.fields.cantidad') ?></th>
                                <th><?= lang('saldos.fields.created_at') ?></th>
                                <th><?= lang('saldos.fields.updated_at') ?></th>
                                <th><?= lang('saldos.fields.deleted_at') ?></th>
                                <th class="text-center"><?= lang('saldos.fields.actions') ?></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('js') ?>
<!-- Carga opcional de scripts para exportación si tu layout no los incluye globalmente -->
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>

<script>
    $(".btnAceptar").on("click", function () {
        collapsedGroups = {};
        ttop = '';
        fncAceptar();
    });

    function fncAceptar() {
        var idEmpresa = $('#idEmpresaList').val();
        var idAlmacen = $('.idAlmacen').val();
        var idProducto = $('.idProducto').val();
        console.log("idProducto", idProducto);

        tableSaldos.ajax.url(`<?= base_url('admin/saldos') ?>/` + idEmpresa + '/' + idAlmacen + '/' + idProducto).load();
    }

    var tableSaldos = $('#tableSaldos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        pageLength: 50,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: true,
        dom: '<"row mb-3"<"col-md-6"B><"col-md-6"f>>rt<"row mt-3"<"col-md-5"i><"col-md-7"p>>',
        buttons: [
            {
                extend: 'copy',
                text: '<i class="fas fa-copy mr-1"></i> Copiar',
                className: 'btn btn-secondary btn-sm shadow-sm',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel mr-1"></i> Excel',
                className: 'btn btn-success btn-sm shadow-sm',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'csv',
                text: '<i class="fas fa-file-csv mr-1"></i> CSV',
                className: 'btn btn-info btn-sm shadow-sm',
                exportOptions: { columns: ':not(:last-child)' }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print mr-1"></i> Imprimir',
                className: 'btn btn-dark btn-sm shadow-sm',
                exportOptions: { columns: ':not(:last-child)' }
            }
        ],
        language: {
            url: "//cdn.datatables.net/plug-ins/1.13.6/i18n/es-ES.json"
        },
        ajax: {
            url: '<?= base_url('admin/saldos') ?>',
            method: 'GET',
            dataType: "json"
        },
        initComplete: function () {
            this.api().columns().every(function () {
                var column = this;
                var header = $(column.header());
                
                if (column.index() === 12) {
                    return;
                }

                var title = header.text();
                header.html(`
                    <div class="font-weight-bold mb-1">${title}</div>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control" placeholder="Buscar..." />
                        <div class="input-group-append">
                            <span class="input-group-text bg-white text-muted px-1"><i class="fas fa-search fa-xs"></i></span>
                        </div>
                    </div>
                `);

                $('input', column.header()).on('keyup change clear', function () {
                    if (column.search() !== this.value) {
                        column.search(this.value).draw();
                    }
                });

                $('input', column.header()).on('click', function (e) {
                    e.stopPropagation();
                });
            });
        },
        columnDefs: [{
            orderable: false,
            searchable: false,
            targets: [12]
        }],
        columns: [
            {'data': 'id'},
            {'data': 'nombreEmpresa'},
            {'data': 'nombreAlmacen'},
            {'data': 'lote'},
            {'data': 'fullname'},
            {'data': 'idProducto'},
            {'data': 'codigoProducto'},
            {'data': 'descripcion'},
            {
                'data': 'cantidad',
                'render': function (data) {
                    return `<span class="badge badge-info px-2 py-1">${data}</span>`;
                }
            },
            {'data': 'created_at'},
            {'data': 'updated_at'},
            {'data': 'deleted_at'},
            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                             <button class="btn btn-success btn-barcode shadow-sm mr-1" data-id="${data.id}" title="Código de barras 1"><i class="fas fa-barcode"></i></button>
                             <button class="btn btn-success btn-barcodeV2 shadow-sm mr-1" data-id="${data.id}" title="Código de barras 2"><i class="fas fa-barcode"></i></button>
                             <button class="btn btn-success btn-barcodeV3 shadow-sm mr-1" data-id="${data.id}" title="Código de barras 3"><i class="fas fa-barcode"></i></button>
                             <button class="btn btn-primary btnEditExtra shadow-sm mr-1" data-toggle="modal" idSaldos="${data.id}" data-target="#modalAddExtraFields" title="Campos Extra"><i class="fa fa-plus"></i></button>
                             <button class="btn btn-info btnAddEmploye shadow-sm" data-toggle="modal" idProducts="${data.id}" data-target="#modalProductoEmploye" title="Asignar Empleado"><i class="fa fa-user"></i></button>
                         </div>
                        </td>`;
                }
            }
        ]
    });

    $(document).on('click', '#btnSaveSaldos', function (e) {
        var idSaldos = $("#idSaldos").val();
        var idEmpresa = $("#idEmpresa").val();
        var idAlmacen = $("#idAlmacen").val();
        var lote = $("#lote").val();
        var idProducto = $("#idProducto").val();
        var codigoProducto = $("#codigoProducto").val();
        var descripcion = $("#descripcion").val();
        var cantidad = $("#cantidad").val();

        $("#btnSaveSaldos").attr("disabled", true);
        var datos = new FormData();
        datos.append("idSaldos", idSaldos);
        datos.append("idEmpresa", idEmpresa);
        datos.append("idAlmacen", idAlmacen);
        datos.append("lote", lote);
        datos.append("idProducto", idProducto);
        datos.append("codigoProducto", codigoProducto);
        datos.append("descripcion", descripcion);
        datos.append("cantidad", cantidad);

        $.ajax({
            url: "<?= base_url('admin/saldos/save') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                if (respuesta?.message?.includes("Guardado") || respuesta?.message?.includes("Actualizado")) {
                    Toast.fire({
                        icon: 'success',
                        title: respuesta.message
                    });
                    tableSaldos.ajax.reload();
                    $("#btnSaveSaldos").removeAttr("disabled");
                    $('#modalAddSaldos').modal('hide');
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message || "Error desconocido"
                    });
                    $("#btnSaveSaldos").removeAttr("disabled");
                }
            }
        }).fail(function (jqXHR, textStatus, errorThrown) {
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: jqXHR.responseText
            });
            $("#btnSaveSaldos").removeAttr("disabled");
        });
    });

    $(".tableSaldos").on("click", ".btnEditSaldos", function () {
        var idSaldos = $(this).attr("idSaldos");
        var datos = new FormData();
        datos.append("idSaldos", idSaldos);
        $.ajax({
            url: "<?= base_url('admin/saldos/getSaldos') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                $("#idSaldos").val(respuesta["id"]);
                $("#idEmpresa").val(respuesta["idEmpresa"]).trigger("change");
                $("#idAlmacen").val(respuesta["idAlmacen"]);
                $("#lote").val(respuesta["lote"]);
                $("#idProducto").val(respuesta["idProducto"]);
                $("#codigoProducto").val(respuesta["codigoProducto"]);
                $("#descripcion").val(respuesta["descripcion"]);
                $("#cantidad").val(respuesta["cantidad"]);
            }
        });
    });

    $(".idAlmacen").select2({
        theme: 'bootstrap4',
        ajax: {
            url: "<?= base_url('admin/saldos/getStoragesAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var csrfName = $('.txt_csrfname').attr('name'); 
                var csrfHash = $('.txt_csrfname').val(); 
                var idEmpresa = $('.idEmpresaList').val(); 

                return {
                    searchTerm: params.term, 
                    [csrfName]: csrfHash, 
                    idEmpresa: idEmpresa 
                };
            },
            processResults: function (response) {
                $('.txt_csrfname').val(response.token);
                return {
                    results: response.data
                };
            },
            cache: true
        }
    });

    $("#idEmpresaList").select2({
        theme: 'bootstrap4'
    });

    $("#idEmpresaList").change(function () {
        $('.idAlmacen').val("0").trigger('change');
    })

    $(".idProducto").select2({
        theme: 'bootstrap4',
        ajax: {
            url: "<?= base_url('admin/saldos/getProductsAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                var csrfName = $('.txt_csrfname').attr('name'); 
                var csrfHash = $('.txt_csrfname').val(); 
                var idEmpresa = $('.idEmpresaList').val(); 

                return {
                    searchTerm: params.term, 
                    [csrfName]: csrfHash, 
                    idEmpresa: idEmpresa 
                };
            },
            processResults: function (response) {
                $('.txt_csrfname').val(response.token);
                return {
                    results: response.data
                };
            },
            cache: true
        }
    });

    $(".tableSaldos").on("click", ".btn-barcode", function () {
        var idProduct = $(this).attr("data-id");
        window.open("<?= base_url('admin/saldos/barcode/') ?>" + "/" + idProduct, "_blank");
    });

    $(".tableSaldos").on("click", ".btn-barcodeV2", function () {
        var idProduct = $(this).attr("data-id");
        window.open("<?= base_url('admin/saldos/barcodeV2/') ?>" + "/" + idProduct, "_blank");
    });

    $(".tableSaldos").on("click", ".btn-barcodeV3", function () {
        var idProduct = $(this).attr("data-id");
        window.open("<?= base_url('admin/saldos/barcodeV3/') ?>" + "/" + idProduct, "_blank");
    });

    $(".btnPrintCodes").on("click", function () {
        var idEmpresa = $('#idEmpresaList').val();
        var idAlmacen = $('.idAlmacen').val();
        var idProducto2 = $('.idProducto').val();
        window.open("<?= base_url('admin/saldos/barcode/') ?>" + "/0" + "/" + idEmpresa + "/" + idAlmacen + "/" + idProducto2, "_blank");
    });
    
    $(".tableSaldos").on("click", ".btnEditExtra", function () {
        var idBalance = $(this).attr("idsaldos");
        var datos = new FormData();
        datos.append("idBalance", idBalance);
        $.ajax({
            url: "<?= base_url('admin/saldos/getProductsFieldsExtra') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            success: function (respuesta) {
                $(".extraFields").html(respuesta);
            }
        })
    });
    
    $(".tableSaldos").on("click", ".btn-delete", function () {
        var idSaldos = $(this).attr("data-id");
        Swal.fire({
            title: '<?= lang('boilerplate.global.sweet.title') ?>',
            text: "<?= lang('boilerplate.global.sweet.text') ?>",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang('boilerplate.global.sweet.confirm_delete') ?>'
        }).then((result) => {
            if (result.value) {
                $.ajax({
                    url: `<?= base_url('admin/saldos') ?>/` + idSaldos,
                    method: 'DELETE',
                }).done((data, textStatus, jqXHR) => {
                    Toast.fire({
                        icon: 'success',
                        title: jqXHR.statusText,
                    });
                    tableSaldos.ajax.reload();
                }).fail((error) => {
                    Toast.fire({
                        icon: 'error',
                        title: error.responseJSON.messages.error,
                    });
                });
            }
        });
    });
    
    $(function () {
        $("#modalAddSaldos").draggable();
    });
</script>
<?= $this->endSection() ?>