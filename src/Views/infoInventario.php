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

/* Cuadro de escaneo */
.scan-area {
    position: absolute;
    border: 3px solid #00ff00;
    width: 60%;
    height: 40%;
    top: 30%;
    left: 20%;
    box-sizing: border-box;
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

                <label>Código escaneado</label>
                <input type="text" id="codigo" class="form-control" placeholder="Esperando código…">

                <button id="btnStart" class="btn btn-success mt-3">Iniciar lector</button>
                <button id="btnStop" class="btn btn-danger mt-3" style="display:none;">Detener</button>

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

<!-- ZXing estable sin módulos (0.1.5) -->
<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>

<script>
let reader = null;
let scanning = false;

document.getElementById('btnStart').addEventListener('click', async () => {

    const video = document.getElementById('preview');
    video.style.display = "block";

    document.getElementById('btnStart').style.display = "none";
    document.getElementById('btnStop').style.display = "inline-block";

    reader = new ZXingBrowser.BrowserMultiFormatReader();

    try {
        const devices = await ZXingBrowser.BrowserMultiFormatReader.listVideoInputDevices();

        if (!devices || devices.length === 0) {
            alert("No se detectan cámaras en el dispositivo.");
            return;
        }

        // Preferir cámara trasera
        const deviceId = devices.length > 1 ? devices[devices.length - 1].deviceId : devices[0].deviceId;

        scanning = true;

        reader.decodeFromVideoDevice(deviceId, video, (result, err) => {

            if (result) {
                const codigo = result.text.trim();

                console.log("Código detectado:", codigo);

                document.getElementById('codigo').value = codigo;

                reader.reset();
                scanning = false;
                video.style.display = "none";

                document.getElementById('btnStart').style.display = "inline-block";
                document.getElementById('btnStop').style.display = "none";
            }
        });

        // Ajustes de video (autofocus y exposición)
        const track = (await navigator.mediaDevices.getUserMedia({
            video: {
                deviceId: { exact: deviceId },
                focusMode: "continuous",
                exposureMode: "continuous",
                torch: false,        // podemos activar después si quieres
                width: { ideal: 1280 },
                height: { ideal: 720 }
            }
        })).getVideoTracks()[0];

        // Intentar autofocus
        if (track && track.applyConstraints) {
            track.applyConstraints({
                advanced: [
                    { focusMode: "continuous" },
                    { exposureMode: "continuous" }
                ]
            }).catch(() => {});
        }

    } catch (err) {
        console.error("Error iniciando cámara:", err);
        alert("No se pudo iniciar la cámara. Usa Chrome en Android.");
    }
});

document.getElementById('btnStop').addEventListener('click', () => {
    if (reader) reader.reset();
    scanning = false;
    document.getElementById('preview').style.display = "none";
    document.getElementById('btnStart').style.display = "inline-block";
    document.getElementById('btnStop').style.display = "none";
});
</script>

<?= $this->endSection() ?>
