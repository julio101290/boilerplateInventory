<!-- Modal Subcategorias -->
<div class="modal fade" id="modalAddExtraFields" tabindex="-1" role="dialog" aria-labelledby="modalAddExtraFields" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Datos Extra Producto</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="form-extraFields" class="form-horizonta l extraFields">




                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal"><?= lang('boilerplate.global.close') ?></button>
                <button type="button" class="btn btn-primary btn-sm btnSaveDataExtraFields" id="btnSaveDataExtraFields"><?= lang('boilerplate.global.save') ?></button>
            </div>
        </div>
    </div>
</div>

<?= $this->section('js') ?>


<script>

    $(document).on('click', '.btnAddSubcategorias', function (e) {


        $(".form-control").val("");

        $("#idSubcategorias").val("0");

        $("#btnSaveSubcategorias").removeAttr("disabled");

        $("#idCategoria").empty();

        $("#idEmpresa").val("0").trigger("change");

    });

    /**
     * Data Extra
     */

    $(document).on('click', '.btnSaveDataExtraFields', function (e) {

        e.preventDefault();
        
        console.log("Prueba");

        var $btn = $("#btnSaveExtraFields");
        $btn.attr("disabled", true);

        var form = $("#form-extraFields");

        // Obtener idProduct desde el campo oculto
        var idProduct = $("#idProductExtraFields").val();

        if (!idProduct || idProduct === "0") {
            Toast.fire({icon: 'error', title: "Falta el ID del producto"});
            $btn.removeAttr("disabled");
            return;
        }

        // Crear el objeto FormData
        var datos = new FormData();
        datos.append("idProduct", idProduct);

        // Recorrer todos los inputs, selects y textareas dentro del formulario
        form.find("input, select, textarea").each(function () {
            var $el = $(this);
            var name = $el.attr("name");
            var value = $el.val();

            // Saltar el campo oculto principal del producto
            if (name === "idProductExtraFields")
                return;

            // Guardar solo si tiene nombre
            if (name) {
                datos.append(name, value);
            }
        });

        $.ajax({
            url: "<?= base_url('admin/saldos/saveExtraFields') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",
            success: function (respuesta) {
                if (respuesta && typeof respuesta === "object") {
                    if (respuesta.status === "ok") {
                        Toast.fire({icon: "success", title: respuesta.message || "Campos extra guardados correctamente"});
                        $("#modalExtraFields").modal("hide");
                    } else {
                        Toast.fire({icon: "error", title: respuesta.message || "Error al guardar los campos extra"});
                    }
                } else {
                    Toast.fire({icon: "error", title: "Respuesta inválida del servidor"});
                }
            },
            error: function (jqXHR, textStatus, errorThrown) {
                var msg = "Error en la petición: " + textStatus;
                try {
                    if (jqXHR && jqXHR.responseJSON && jqXHR.responseJSON.message) {
                        msg = jqXHR.responseJSON.message;
                    } else if (jqXHR && jqXHR.responseText) {
                        msg = jqXHR.responseText;
                    }
                } catch (ex) {
                }
                Toast.fire({icon: "error", title: msg});
            },
            complete: function () {
                $btn.removeAttr("disabled");
            }
        });

    });

    /* 
     * AL hacer click al editar
     */



    $(document).on('click', '.btnEditSubcategorias', function (e) {


        var idSubcategorias = $(this).attr("idSubcategorias");

        //LIMPIAMOS CONTROLES
        $(".form-control").val("");

        $("#idSubcategorias").val(idSubcategorias);
        $("#btnGuardarSubcategorias").removeAttr("disabled");

    });


    $("#idEmpresa").select2();


    /**
     * Categorias por empresa
     */

    $(".idCategoria").select2({
        ajax: {
            url: "<?= base_url('admin/categorias/getCategoriasAjax') ?>",
            type: "post",
            dataType: 'json',
            delay: 250,
            data: function (params) {
                // CSRF Hash
                var csrfName = $('.txt_csrfname').attr('name'); // CSRF Token name
                var csrfHash = $('.txt_csrfname').val(); // CSRF hash
                var idEmpresa = $('.idEmpresa').val(); // CSRF hash

                return {
                    searchTerm: params.term, // search term
                    [csrfName]: csrfHash, // CSRF Token
                    idEmpresa: idEmpresa // search term
                };
            },
            processResults: function (response) {

                // Update CSRF Token
                $('.txt_csrfname').val(response.token);

                return {
                    results: response.data
                };
            },
            cache: true
        }
    });


</script>


<?= $this->endSection() ?>
        