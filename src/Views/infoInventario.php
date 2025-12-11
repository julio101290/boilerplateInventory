<?= $this->include('julio101290\boilerplate\Views\load/daterangapicker') ?>
<?= $this->include('julio101290\boilerplate\Views\load\toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>

<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>

<div class="card card-default">
    <div class="card-header"><h4>Lector de Códigos de Barras</h4></div>

    <div class="card-body">
        <div class="row">

            <!-- ESCÁNER ZXING -->
            <div class="col-md-6">

                <label for="codigo">Código escaneado</label>
                <input type="text" id="codigo" class="form-control" placeholder="Esperando escaneo…">

                <button id="btnStart" class="btn btn-primary mt-3 mb-3">
                    Iniciar lector
                </button>

                <video id="preview"
                       style="width:100%; max-width:450px; border:2px solid #000; display:none;"
                       autoplay></video>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<!-- ZXing versión estable que sí funciona -->
<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>

<script>
let reader = null;
let scanning = false;

document.getElementById('btnStart').addEventListener('click', async () => {

    const video = document.getElementById('preview');
    const input = document.getElementById('codigo');

    video.style.display = "block";

    try {
        reader = new ZXingBrowser.BrowserMultiFormatReader();

        // Activar TRY_HARDER para mejorar lectura en etiquetas brillosas
        const hints = new Map();
        hints.set(ZXingBrowser.DecodeHintType.TRY_HARDER, true);
        reader.setHints(hints);

        // Obtener cámaras disponibles
        const devices = await ZXingBrowser.BrowserCodeReader.listVideoInputDevices();

        if (!devices || devices.length === 0) {
            alert("No se detectan cámaras.");
            return;
        }

        // Elegir la cámara trasera si existe
        let selectedDeviceId = devices[0].deviceId;
        devices.forEach(d => {
            if (d.label.toLowerCase().includes("back")) {
                selectedDeviceId = d.deviceId;
            }
        });

        scanning = true;

        // Iniciar decodificación desde el video
        reader.decodeFromVideoDevice(selectedDeviceId, video, (result, err) => {
            if (result) {
                input.value = result.text;

                // detener después de detectar
                reader.reset();
                video.style.display = "none";
                scanning = false;
            }
        });

    } catch (e) {
        console.error(e);
        alert("Error al iniciar la cámara.");
    }
});
</script>

<?= $this->endSection() ?>
