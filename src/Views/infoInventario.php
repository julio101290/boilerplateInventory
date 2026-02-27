<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>

<style>
    .qr-container {
        max-width: 520px;
        margin: auto;
    }

    #reader {
        width: 100%;
        border-radius: 14px;
        overflow: hidden;
    }

    .qr-frame {
        border: 3px solid #00e676;
        border-radius: 12px;
    }
    .imputClass{
          display: block;
        width: 100%;
        padding: 0.375rem 0;
        margin-bottom: 0;
        line-height: 1.5;
        color: #567594;
        background-color: transparent;
        border: solid transparent;
        border-width: 1px 0;
        font-size: 0.80rem;
        font-family: 'Poppins', sans-serif;
    }

</style>

<div class="card shadow qr-container">
    <div class="card-body text-center">

        <h5 class="mb-3">📷 Lector de Código QR</h5>

        <div class="form-check form-switch mb-3 text-start">
            <input class="form-check-input" type="checkbox" id="switchManual">
            <label class="form-check-label">Modo manual</label>
        </div>

        <input type="text" id="codigo" class="form-control mb-3 text-center"
               placeholder="Esperando QR o código…">

        <div class="d-grid gap-2 mb-3">
            <button id="btnStart" class="btn btn-success">Encender cámara</button>
            <button id="btnStop" class="btn btn-danger d-none">Detener</button>
        </div>

        <div id="reader" class="qr-frame d-none"></div>

    </div>
 <div class="card-body  d-none" id="detalleProducto">
    <div class="row g-2">
        <div class="col-12 col-md-4">
            <label class="small text-muted mb-0">PRODUCTO</label>
            <input class="imputClass" id="idProducto" readonly value="">
        </div>
        <div class="col-12 col-md-8">
            <label class="small text-muted mb-0">DESCRIPCION</label>
            <input class="imputClass" id="description" readonly value="">
        </div>
        <div class="col-12 col-md-4">
            <label class="small text-muted mb-0">LOTE</label>
            <input class="imputClass" id="lote" readonly value="">
        </div>
        <div class="col-12 col-md-8">
            <label class="small text-muted mb-0">ALMACEN</label>
            <input class="imputClass" id="idAlmacen" readonly value="">
        </div>
         <div class="col-12 col-md-4">
            <label class="small text-muted mb-0">CODIGO DEL PRODUCTO</label>
            <input class="imputClass" id="codigoProducto" readonly value="">
        </div>
        <div class="col-12 col-md-8">
            <label class="small text-muted mb-0">USUARIO ASIGNADO</label>
            <input class="imputClass" id="usuario" readonly value="">
        </div>
        <br>
         <div class="col-12 col-md-12">
            <label class="small text-muted mb-0">MANTENIMIENTOS</label>
            <input class="imputClass" id="mantenimiento" readonly value="">
        </div>
    </div>

</div>


<?= $this->endSection() ?>

<?= $this->section('js') ?>

<script src="https://unpkg.com/html5-qrcode"></script>

<script>
    let qrScanner = null;

// =============================================================
// MODO MANUAL
// =============================================================
    $('#switchManual').on('change', function () {
        if (this.checked) {
            detenerScanner();
            $('#btnStart').hide();
        } else {
            $('#btnStart').show();
        }
    });

// =============================================================
// INICIAR CAMARA (ESTABLE)
// =============================================================
    $('#btnStart').on('click', async function () {

        $('#btnStart').addClass('d-none');
        $('#btnStop').removeClass('d-none');
        $('#reader').removeClass('d-none');

        qrScanner = new Html5Qrcode("reader");

        try {
            await qrScanner.start(
                    {facingMode: "environment"}, // webcam o trasera
                    {
                        fps: 10,
                        qrbox: {width: 250, height: 250}
                    },
                    (decodedText) => {
                $('#codigo').val(decodedText);
                enviarCodigo(decodedText);
                detenerScanner();
            }
            );
        } catch (err) {
            console.error(err);
            alert('No se pudo acceder a la cámara');
            detenerScanner();
        }
    });

// =============================================================
// DETENER
// =============================================================
    $('#btnStop').on('click', detenerScanner);

    function detenerScanner() {
        if (qrScanner) {
            qrScanner.stop().then(() => {
                qrScanner.clear();
                qrScanner = null;
            });
        }

        $('#reader').addClass('d-none');
        $('#btnStop').addClass('d-none');
        $('#btnStart').removeClass('d-none');
    }

// =============================================================
// AJAX CI4
// =============================================================
    function enviarCodigo(code) {
        $.post(
                "<?= base_url('admin/generica') ?>",
                {codigo: code},
                function(res) {
                    if (!res || Object.keys(res).length === 0) {
                    $('#detalleProducto').addClass('d-none');
                    alert('Producto no encontrado');
                    return; // ⛔ corta la ejecución
                    }
                    
                    console.log(res);

                    // Asignar al input
                    $('#idProducto').val(res.idProducto);
                    $('#description').val(res.descripcion);
                    $('#lote').val(res.lote);
                    $('#codigoProducto').val(res.codigoProducto);
                    $('#idAlmacen').val(res.name);
                    $('#usuario').val(res.fullname);
                    $('#mantenimiento').val(res.date + ' - ' + res.generalObservations);
                    $('#detalleProducto').removeClass('d-none').hide().fadeIn(200);
                },
                'json'

        );
    }

// =============================================================
// ENTER MANUAL
// =============================================================
    $('#codigo').on('keypress', function (e) {
        if (e.key === 'Enter' && this.value.trim()) {
            console.log("probando");
            enviarCodigo(this.value.trim());
        }
    });
</script>

<?= $this->endSection() ?>
