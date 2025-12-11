<?= $this->include('julio101290\boilerplate\Views\load/daterangapicker') ?>
<?= $this->include('julio101290\boilerplate\Views\load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>

<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>

<style>
#preview {
    width: 100%;
    max-width: 450px;
    border: 2px solid #1e88e5;
    border-radius: 10px;
    display: none;
    background: #000;
}

.scan-area {
    position: absolute;
    border: 3px solid #00ff00;
    width: 60%;
    height: 40%;
    top: 30%;
    left: 20%;
    border-radius: 8px;
    pointer-events: none;
}

.scanner-wrapper {
    position: relative;
    width: 100%;
    max-width: 450px;
}
</style>

<div class="card card-default">
    <div class="card-body">
        <div class="row">

            <div class="col-md-6">

                <!-- SWITCH MODO MANUAL - CAMARA -->
                <label class="mt-2">Modo de captura</label><br>
                <label>
                    <input type="checkbox" id="switchManual">
                    <span>Modo manual</span>
                </label>

                <br><br>

                <label>Código</label>
                <input type="text" id="codigo" class="form-control" placeholder="Esperando código…">

                <button id="btnStart" class="btn btn-success mt-3">Iniciar cámara</button>
                <button id="btnStop" class="btn btn-danger mt-3" style="display:none;">Detener</button>

                <button id="btnFlash" class="btn btn-warning mt-3" style="display:none;">Linterna ON/OFF</button>

                <div class="scanner-wrapper mt-3">
                    <video id="preview" autoplay></video>
                    <div class="scan-area"></div>
                </div>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('js') ?>

<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>

<script>

let reader = null;
let track = null; 
let torchEnabled = false;
let scanning = false;

// =============================================================
//                   MODO MANUAL / CAMARA
// =============================================================
document.getElementById('switchManual').addEventListener('change', function () {
    const manual = this.checked;

    if (manual) {
        // Ocultar cámara
        document.getElementById('btnStart').style.display = "none";
        document.getElementById('btnStop').style.display = "none";
        document.getElementById('btnFlash').style.display = "none";
        document.getElementById('preview').style.display = "none";

        if (reader) reader.reset();
    } else {
        // Mostrar botón de cámara
        document.getElementById('btnStart').style.display = "inline-block";
    }
});

// =============================================================
//                 INICIAR CAMARA (ZXING)
// =============================================================
document.getElementById('btnStart').addEventListener('click', async () => {

    const video = document.getElementById('preview');
    video.style.display = "block";

    document.getElementById('btnStart').style.display = "none";
    document.getElementById('btnStop').style.display = "inline-block";
    document.getElementById('btnFlash').style.display = "inline-block";

    reader = new ZXingBrowser.BrowserMultiFormatReader();

    try {
        const devices = await ZXingBrowser.BrowserMultiFormatReader.listVideoInputDevices();
        if (!devices.length) {
            alert("No hay cámaras disponibles");
            return;
        }

        const deviceId = devices.length > 1 ? devices[devices.length - 1].deviceId : devices[0].deviceId;

        scanning = true;

        reader.decodeFromVideoDevice(deviceId, video, (result, err) => {
            if (result) {
                const code = result.text.trim();
                document.getElementById('codigo').value = code;

                enviarCodigo(code);

                detenerScanner();
            }
        });

        // Obtener TRACK de cámara (para usar linterna)
        const stream = await navigator.mediaDevices.getUserMedia({
            video: { deviceId: { exact: deviceId } }
        });

        track = stream.getVideoTracks()[0];

    } catch (err) {
        alert("Error iniciando cámara");
        console.error(err);
    }
});

// =============================================================
//                 DETENER CAMARA
// =============================================================
function detenerScanner() {
    if (reader) reader.reset();
    scanning = false;

    document.getElementById('preview').style.display = "none";
    document.getElementById('btnStart').style.display = "inline-block";
    document.getElementById('btnStop').style.display = "none";
    document.getElementById('btnFlash').style.display = "none";

    if (track) {
        track.stop();
        track = null;
    }
}

document.getElementById('btnStop').addEventListener('click', detenerScanner);

// =============================================================
//                 LINTERNAAA (TORCH)
// =============================================================
document.getElementById('btnFlash').addEventListener('click', async () => {
    if (!track) {
        alert("Cámara no lista");
        return;
    }

    const capabilities = track.getCapabilities();
    if (!capabilities.torch) {
        alert("El dispositivo no soporta linterna");
        return;
    }

    torchEnabled = !torchEnabled;

    try {
        await track.applyConstraints({
            advanced: [{ torch: torchEnabled }]
        });
    } catch (e) {
        console.error(e);
    }
});

// =============================================================
//        ENVÍO AJAX POST AL DETECTAR O AL DAR ENTER
// =============================================================
function enviarCodigo(code) {
    console.log("ENVIANDO AJAX:", code);

    $.post("<?= base_url('ruta/generica') ?>", 
    { codigo: code },
    function (resp) {
        console.log("Respuesta AJAX:", resp);
    });
}

// ENTER en modo manual
document.getElementById('codigo').addEventListener('keypress', function (e) {
    if (e.key === "Enter") {
        const code = this.value.trim();
        if (code !== "") {
            enviarCodigo(code);
        }
    }
});

</script>

<?= $this->endSection() ?>
