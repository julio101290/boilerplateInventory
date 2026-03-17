<!-- Modal Pacientes -->
<div class="modal fade" id="modalAddbtnAddArticle" tabindex="-1" role="dialog" aria-labelledby="modalAddbtnAddArticle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Seleccione Articulo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="table-products" class="table table-striped table-hover va-middle tableProducts">
                                <thead>
                                    <tr>
                                        <th>#</th>

                                        <th>Descripcion</th>
                                        <th>Almacen</th>
                                        <th>lote</th>
                                        <th>Stock</th>
                                        <th>Imagen</th>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-dismiss="modal">
                    <?= lang('boilerplate.global.close') ?>
                </button>

            </div>
        </div>
    </div>
</div>



<?= $this->section('js') ?>


<script>

    var tableProducts = $('#table-products').DataTable({
        processing: true,
        serverSide: true,
        autoWidth: false,
        order: [
            [1, 'asc']
        ],

        ajax: {
            url: '<?= base_url('admin/getAllProductsInventory') ?>/0/0/0',
            method: 'GET',
            dataType: "json"
        },
        columnDefs: [{
                orderable: false,
                targets: [3],
                searchable: false,
                targets: [3]

            }],
        columns: [{
                'data': 'id'
            },
            {
                'data': 'description'
            },

            {
                'data': 'almacen'
            },

            {
                'data': 'lote'
            },

            {
                'data': 'stock'
            },

            {
                "data": function (data) {

                    if (data.routeImage == "") {
                        data.routeImage = "anonymous.png";
                    }

                    return `<td class="text-right py-0 align-middle">
                         <div class="btn-group btn-group-sm">
                         <img src="<?= base_URL("images/products") ?>/${data.routeImage}" data-action="zoom" width="40px" class="" style="">
                         </div>
                         </td>`
                }
            },

            {
                "data": function (data) {
                    return `<td class="text-right py-0 align-middle">
                            <div class="btn-group btn-group-sm">
                            <button class="btn bg-blue btnAddProduct" lote="${data.lote}" calculatelot="${data.calculatelot}" almacen="${data.almacen}" data-id="${data.id}" unidad="${data.unidad}" claveProductoSAT="${data.claveProductoSAT}" unidadSAT="${data.unidadSAT}" porcentIVARetenido="${data.porcentIVARetenido}" porcentISRRetenido="${data.porcentISRRetenido}" porcentTax="${data.porcentTax}" code="${data.code}" price = "${data.salePrice}" description = "${data.description}"><i class="fas fa-plus"></i></button>
                            </div>
                            </td>`
                }
            }
        ]
    });


    /**
     * Evento al hacer click al boton btnAgregarDiagnostico
     */

    // ---------------------------------------------------
    // Contador en memoria por "loteBase" para evitar duplicados
    // ---------------------------------------------------
    // Variable global dentro de este scope para controlar consecutivos
    var lotesContador = {};

    // ---------------------------------------------------
    // Evento: click en botones .btnAddProduct dentro de #table-products
    // ---------------------------------------------------
    $("#table-products").on("click", ".btnAddProduct", function (e) {
        e.preventDefault();

        var $btn = $(this);
        // si quieres bloquear el botón para prevenir doble click rápido, descomenta:
        // $btn.prop("disabled", true);

        var idProduct = $btn.attr("data-id");
        var almacen = $(".idStorage").val();
        var calculatelot = $btn.attr("calculatelot");
        var lote = $btn.attr("lote");
        var description = $btn.attr("description");
        var codeProduct = $btn.attr("code");
        var salePrice = $btn.attr("price");
        var porcentTax = $btn.attr("porcentTax");
        var porcentIVARetenido = $btn.attr("porcentIVARetenido");
        var porcentISRRetenido = $btn.attr("porcentISRRetenido");
        var claveUnidadSAT = $btn.attr("unidadSAT");
        var unidad = $btn.attr("unidad");
        var claveProductoSAT = $btn.attr("claveProductoSAT");

        // Preparar datos para el AJAX
        var datos = new FormData();
        datos.append("idAlmacen", almacen);
        datos.append("idProducto", idProduct);

        $.ajax({
            url: "<?= base_url('admin/inventory/getLastLot') ?>",
            method: "POST",
            data: datos,
            cache: false,
            contentType: false,
            processData: false,
            dataType: "json",

            success: function (respuesta) {

                // --- 1) Lote base desde backend (fallback si no viene) ---
                var loteCalculado = (respuesta && respuesta.lot) ? respuesta.lot : (lote || "UNKNOWN000001");

                if (!loteCalculado) {
                    console.error("No se pudo determinar el lote calculado");
                    return;
                }

                // 🔥 VALIDACIÓN NUEVA
                if (respuesta.error) {

                    Toast.fire({
                        icon: 'error',
                        title: respuesta.message
                    });
                    return;
                }


                // Asumimos que los últimos 6 caracteres son el consecutivo numérico
                var loteBase = loteCalculado.slice(0, -6);  // ej: "LMPLMLAPTOP"
                var consecutivoBackend = parseInt(loteCalculado.slice(-6), 10);
                if (isNaN(consecutivoBackend))
                    consecutivoBackend = 0;

                // --- 2) Decidir loteFinal usando lotesContador para evitar race conditions ---
                var loteFinal;

                if (!lotesContador.hasOwnProperty(loteBase)) {
                    // Primera vez que vemos este loteBase: inicializamos con el consecutivo del backend
                    lotesContador[loteBase] = consecutivoBackend;
                    loteFinal = loteCalculado;
                } else {
                    // Ya había un contador local
                    if (consecutivoBackend > lotesContador[loteBase]) {
                        // Backend avanzó por fuera de esta sesión: sincronizamos al backend
                        lotesContador[loteBase] = consecutivoBackend;
                        loteFinal = loteCalculado;
                    } else {
                        // Usamos el siguiente consecutivo en memoria
                        lotesContador[loteBase] = lotesContador[loteBase] + 1;
                        var nuevo = String(lotesContador[loteBase]).padStart(6, "0");
                        loteFinal = loteBase + nuevo;
                    }
                }

                // --- 3) Llamamos a la función que agrega el renglón ---
                agregarRenglon(
                        idProduct,
                        codeProduct,
                        loteFinal,
                        description,
                        salePrice,
                        porcentTax,
                        porcentIVARetenido,
                        porcentISRRetenido,
                        claveUnidadSAT,
                        unidad,
                        claveProductoSAT
                        );
            },

            error: function (xhr, status, err) {
                console.error("Error en getLastLot:", status, err);
                alert("No se pudo obtener el lote desde el servidor. Intenta de nuevo.");
            },

            complete: function () {
                // si bloqueaste el botón, re-habilítalo aquí:
                // $btn.prop("disabled", false);
            }
        });

    }); // fin evento btnAddProduct

    // ---------------------------------------------------
    // Handler para quitar renglón
    // ---------------------------------------------------
    $(document).on("click", ".quitProduct", function () {
        $(this).closest(".nuevoProduct").remove();
        // NOTA: no decrementamos lotesContador para evitar reuso accidental de consecutivos
    });

    // ---------------------------------------------------
    // FUNCIÓN QUE CONSTRUYE EL RENGLÓN
    // ---------------------------------------------------
    function agregarRenglon(idProduct, codeProduct, lote, description, salePrice,
            porcentTax, porcentIVARetenido, porcentISRRetenido,
            claveUnidadSAT, unidad, claveProductoSAT) {

        // Normalizar valores numéricos
        salePrice = Number(salePrice) || 0;
        porcentTax = Number(porcentTax) || 0;
        porcentIVARetenido = Number(porcentIVARetenido) || 0;
        porcentISRRetenido = Number(porcentISRRetenido) || 0;

        var tax = (porcentTax > 0) ? ((porcentTax * 0.01) * salePrice) : 0;
        var IVARetenido = (porcentIVARetenido > 0) ? ((porcentIVARetenido * 0.01) * salePrice) : 0;
        var ISRRetenido = (porcentISRRetenido > 0) ? ((porcentISRRetenido * 0.01) * salePrice) : 0;

        var neto = (((porcentTax * 0.01) + 1) * salePrice) - (IVARetenido + ISRRetenido);

        var renglon = "<div class=\"form-group row nuevoProduct\">";

        renglon += "<div class=\"col-1\">";
        renglon += "<button type=\"button\" class=\"btn btn-danger quitProduct\"><span class=\"far fa-trash-alt\"></span></button>";
        renglon += " <button type=\"button\" data-toggle=\"modal\" data-target=\"#modelMoreInfoRow\" class=\"btn btn-primary btnInfo\"><span class=\"fa fa-fw fa-pencil-alt\"></span></button> ";
        renglon += "<input type=\"hidden\" class=\"idProductR\" name=\"idProductR\" value=\"" + (idProduct || "") + "\">";
        renglon += "</div>";

        renglon += "<div class=\"col-1\">";
        renglon += "<input type=\"hidden\" class=\"claveProductoSATR\" name=\"claveProductoSATR\" value=\"" + (claveProductoSAT || "") + "\">";
        renglon += "<input type=\"hidden\" class=\"claveUnidadSatR\" name=\"claveUnidadSatR\" value=\"" + (claveUnidadSAT || "") + "\">";
        renglon += "<input type=\"hidden\" class=\"unidad\" name=\"unidad\" value=\"" + (unidad || "") + "\">";
        renglon += "<input type=\"text\" class=\"form-control codeProduct\" name=\"codeProduct\" value=\"" + (codeProduct || "") + "\"> </div>";

        renglon += "<div class=\"col-1\"> <input type=\"text\" class=\"form-control lote\" name=\"lote\" value=\"" + (lote || "") + "\" required> </div>";

        renglon += "<div class=\"col-6\"> <input type=\"text\" class=\"form-control description\" name=\"description\" value=\"" + (description || "") + "\" required> </div>";

        renglon += "<div class=\"col-1\"> <input type=\"number\" class=\"form-control cant\" name=\"cant\" value=\"1\" required>";
        renglon += "<input type=\"hidden\" class=\"porcentIVARetenido\" name=\"porcentIVARetenido\" value=\"" + porcentIVARetenido + "\">";
        renglon += "<input type=\"hidden\" class=\"porcentISRRetenido\" name=\"porcentISRRetenido\" value=\"" + porcentISRRetenido + "\">";
        renglon += "<input type=\"hidden\" class=\"porcentTax\" name=\"porcentTax\" value=\"" + porcentTax + "\"></div>";

        renglon += "<div class=\"col-1\"> <input type=\"number\" class=\"form-control price\" name=\"price\" value=\"" + salePrice + "\" required>";
        renglon += "<input type=\"hidden\" class=\"IVARetenido\" name=\"IVARetenido\" value=\"" + IVARetenido + "\">";
        renglon += "<input type=\"hidden\" class=\"ISRRetenido\" name=\"ISRRetenido\" value=\"" + ISRRetenido + "\">";
        renglon += "<input type=\"hidden\" class=\"tax\" name=\"tax\" value=\"" + tax + "\"> </div>";

        renglon += "<div class=\"col-1\"> <input readonly type=\"number\" class=\"form-control total\" name=\"total\" value=\"" + salePrice + "\">";
        renglon += "<input type=\"hidden\" class=\"neto\" name=\"neto\" value=\"" + neto + "\"> </div>";

        renglon += "</div>";

        $(".rowProducts").append(renglon);

        // Si tienes listProducts() definida en tu proyecto la llamamos para recalcular totales
        if (typeof listProducts === "function") {
            listProducts();
        }
    }


    var nombreDiv = "";


    /**
     * Eliminar Renglon Diagnostico
     */

    $(".rowProducts").on("click", ".quitProduct", function () {

        $(this).parent().parent().remove();

        listProducts();

    });


    /**
     * Mas datos Producto
     */

    $(".rowProducts").on("click", ".btnInfo", function () {

        nombreDiv = $(this);

        var unidadSAT = $(this).parent().parent().find(".claveUnidadSatR").val();
        var claveProductoSAT = $(this).parent().parent().find(".claveProductoSATR").val();

        var newOption = new Option(unidadSAT, unidadSAT, true, true);
        $('#unidadSATRow').append(newOption).trigger('change');
        $("#unidadSATRow").val(unidadSAT);

        var newOptionClaveProducto = new Option(claveProductoSAT, claveProductoSAT, true, true);
        $('#claveProductoSATRow').append(newOptionClaveProducto).trigger('change');
        $("#claveProductoSATRow").val(claveProductoSAT);


    });




    /**
     * Eliminar Renglon Diagnostico
     */

    $(".rowProducts").on("click", ".quitProduct", function () {



        $(this).parent().parent().remove();

        listProducts();

    });



    /**
     * Cambia Cantidad
     */

    $(".rowProducts").on("change", ".cant", function () {


        var cant = Number($(this).val());



        precio = $(this).parent().parent().find(".price").val();

        total = Number(cant) * Number(precio);

        porcIva = Number($(this).parent().parent().find(".porcentTax").val()) * 0.01;

        porcIVARetenido = Number($(this).parent().parent().find(".porcentIVARetenido").val()) * 0.01;

        porcISRRetenido = Number($(this).parent().parent().find(".porcentISRRetenido").val()) * 0.01;

        impuesto = (porcIva) * Number(total);

        IVARetenido = (porcIVARetenido) * Number(total);

        ISRRetenido = (porcISRRetenido) * Number(total);

        neto = ((porcIva + 1) * Number(total)) - (IVARetenido + ISRRetenido);

        $(this).parent().parent().find(".total").val(total);

        $(this).parent().parent().find(".neto").val(neto);

        $(this).parent().parent().find(".tax").val(impuesto);

        $(this).parent().parent().find(".IVARetenido").val(IVARetenido);

        $(this).parent().parent().find(".ISRRetenido").val(ISRRetenido);

        listProducts();

    });


    /**
     * Cambia Cantidad
     */

    $(".rowProducts").on("change", ".price", function () {


        var precio = Number($(this).val());



        cant = $(this).parent().parent().find(".cant").val();

        total = Number(cant) * Number(precio);

        porcIva = Number($(this).parent().parent().find(".porcentTax").val()) * 0.01;

        porcIVARetenido = Number($(this).parent().parent().find(".porcentIVARetenido").val()) * 0.01;

        porcISRRetenido = Number($(this).parent().parent().find(".porcentISRRetenido").val()) * 0.01;


        IVARetenido = (porcIVARetenido) * Number(total);

        ISRRetenido = (porcISRRetenido) * Number(total);

        neto = ((porcIva + 1) * Number(total)) - (IVARetenido + ISRRetenido);

        impuesto = (porcIva) * Number(total);

        $(this).parent().parent().find(".total").val(total);

        $(this).parent().parent().find(".neto").val(neto);

        $(this).parent().parent().find(".tax").val(impuesto);

        $(this).parent().parent().find(".IVARetenido").val(IVARetenido);

        $(this).parent().parent().find(".ISRRetenido").val(ISRRetenido);

        listProducts();

    });
</script>


<?= $this->endSection() ?>