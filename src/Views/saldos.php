<?= $this->include('julio101290\boilerplate\Views\load\select2') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>
<?= $this->include('julio101290\boilerplate\Views\load\nestable') ?>
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>
<?= $this->section('content') ?>
<?= $this->include('julio101290\boilerplateinventory\Views\modulesSaldos/modalCaptureSaldos') ?>
<?= $this->include('julio101290\boilerplateinventory\Views\modulesSaldos/extraFields') ?>
<?= $this->include('julio101290\boilerplatemaintenance\Views/modulesProductsEmployes/modalEmployesProducts') ?>

<div class="card card-default">
    <div class="card-header">
        <div class="float-right">


            <div class="btn-group">
                <button class="btn btn-primary btnPrintCodes" data-toggle="modal">
                    <i class="fa fa-barcode"></i> Imprimir todos los códigos de barras
                </button>

            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table id="tableSaldos" class="table table-striped table-hover va-middle tableSaldos">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?= lang('saldos.fields.idEmpresa') ?></th>
                                <th><?= lang('saldos.fields.idAlmacen') ?></th>
                                <th><?= lang('saldos.fields.lote') ?></th>
                                <th><?= lang('saldos.fields.idProducto') ?></th>
                                <th><?= lang('saldos.fields.codigoProducto') ?></th>
                                <th><?= lang('saldos.fields.descripcion') ?></th>
                                <th><?= lang('saldos.fields.cantidad') ?></th>
                                <th><?= lang('saldos.fields.created_at') ?></th>
                                <th><?= lang('saldos.fields.updated_at') ?></th>
                                <th><?= lang('saldos.fields.deleted_at') ?></th>

                                <th><?= lang('saldos.fields.actions') ?></th>
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
<script>
    var tableSaldos = $('#tableSaldos').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        order: [[1, 'asc']],
        ajax: {
            url: '<?= base_url('admin/saldos') ?>',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [11],
                searchable: false,
                targets: [11]
            }],
        columns: [{'data': 'id'},
            {'data': 'nombreEmpresa'},
            {'data': 'nombreAlmacen'},
            {'data': 'lote'},
            {'data': 'idProducto'},
            {'data': 'codigoProducto'},
            {'data': 'descripcion'},
            {'data': 'cantidad'},
            {'data': 'created_at'},
            {'data': 'updated_at'},
            {'data': 'deleted_at'},

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                             <button class="btn btn-success btn-barcode" data-id="${data.id}"><i class="fas fa-barcode"></i></button>
                             <button class="btn btn-primary btnEditExtra" data-toggle="modal" idSaldos="${data.id}" data-target="#modalAddExtraFields">  <i class=" fa fa-plus"></i></button>
                             <button class="btn btn-info btnAddEmploye" data-toggle="modal" idProducts="${data.id}" data-target="#modalProductoEmploye">  <i class=" fa fa-user"></i></button>
                            </div>
                         </td>`
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


    $(".tableSaldos").on("click", ".btn-barcode", function () {

        var idProduct = $(this).attr("data-id");

        window.open("<?= base_url('admin/saldos/barcode/') ?>" + "/" + idProduct, "_blank");


    });


    $(".btnPrintCodes").on("click", function () {

        window.open("<?= base_url('admin/saldos/barcode/') ?>" + "/0", "_blank");

    });


    $(".tableSaldos").on("click", ".btnEditExtra", function () {

        var idBalance = $(this).attr("idsaldos");

        console.log("idBalance:", idBalance);

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