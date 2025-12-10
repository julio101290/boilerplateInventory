<?= $this->include('julio101290\boilerplate\Views\load/daterangapicker') ?>
<?= $this->include('julio101290\boilerplate\Views\load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>

<!-- Extend from layout index -->
<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<!-- Section content -->
<?= $this->section('content') ?>


<!-- SELECT2 EXAMPLE -->
<div class="card card-default">
    <div class="card-header">

        <div class="float-left">
            <div class="btn-group"></div>
            <div class="btn-group"></div>
        </div>

        <div class="float-right">
            <div class="btn-group"></div>
        </div>
    </div>

    <div class="card-body">
        <div class="row">

            <!-- -------------------------------------------------------- -->
            <!--   ESCANER DE CÓDIGO DE BARRAS CODE 39 - QUAGGAJS        -->
            <!-- -------------------------------------------------------- -->
            
            <div class="col-md-6">
                <label for="codigo">Código escaneado</label>
                <input type="text" id="codigo" class="form-control" placeholder="Esperando escaneo...">

                <button id="btnStart" class="btn btn-primary mt-3 mb-3">
                    Iniciar lector
                </button>

                <div id="scanner"
                    style="width:100%; max-width:450px; height:300px; border:1px solid #ccc; display:none;">
                </div>
            </div>

        </div>
    </div>
</div>
<!-- /.card -->

<?= $this->endSection() ?>

<!-- Section JS -->
<?= $this->section('js') ?>

<!-- Librería QuaggaJS -->
<script src="https://unpkg.com/quagga@0.12.1/dist/quagga.min.js"></script>

<script>
document.getElementById('btnStart').addEventListener('click', function () {

    // Mostrar contenedor del lector
    document.getElementById('scanner').style.display = "block";

    Quagga.init({
        inputStream: {
            name: "Live",
            type: "LiveStream",
            target: document.querySelector('#scanner'),
            constraints: {
                facingMode: "environment",
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        },
        decoder: {
            readers: [{
                format: "code_39_reader",
                config: {
                    tryCode39: true,
                    code39: {
                        enable: true,
                        minConfidence: 0.5,
                        checkCode: true
                    }
                }
            }]
        },
        locate: true,
        numOfWorkers: 2,
        frequency: 10
    },
    function (err) {
        if (err) {
            console.error("Error iniciando Quagga:", err);
            alert("No se pudo acceder a la cámara");
            return;
        }

        Quagga.start();
        console.log("Lector iniciado...");
    });

    Quagga.onDetected(function (result) {

        // Filtro de confianza -> evita lecturas falsas (% $ * / etc.)
        if (result.codeResult.confidence < 0.70) {
            console.log("Lectura débil ignorada:", result.codeResult.code);
            return;
        }

        let code = result.codeResult.code;

        // Limpiar cualquier carácter inválido (solo letras y números)
        code = code.replace(/[^A-Za-z0-9]/g, "");

        console.log("DETECTADO:", code);

        // Poner en el input
        document.getElementById('codigo').value = code;

        // Detener escáner
        Quagga.stop();
        document.getElementById('scanner').style.display = "none";
    });
});
</script>

<?= $this->endSection() ?>
