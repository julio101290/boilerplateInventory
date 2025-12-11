<?= $this->include('julio101290\boilerplate\Views\load/daterangapicker') ?>
<?= $this->include('julio101290\boilerplate\Views\load/toggle') ?>
<?= $this->include('julio101290\boilerplate\Views\load\datatables') ?>

<?= $this->extend('julio101290\boilerplate\Views\layout\index') ?>

<?= $this->section('content') ?>

<div class="card card-default">
    <div class="card-header"></div>

    <div class="card-body">
        <div class="row">

            <div class="col-md-6">

                <label for="codigo">Código escaneado</label>
                <input type="text" id="codigo" class="form-control" placeholder="Esperando escaneo…">

                <button id="btnStart" class="btn btn-primary mt-3 mb-3">
                    Iniciar lector
                </button>

                <button id="btnStop" class="btn btn-danger mt-3 mb-3" style="display:none;">
                    Detener lector
                </button>

                <video id="preview"
                    style="width:100%; max-width:450px; border:1px solid #ccc; display:none;"
                    autoplay muted></video>

            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('js') ?>

<!-- ZXing estable vía CDN -->
<script src="https://unpkg.com/@zxing/browser@0.1.5/umd/zxing-browser.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {

    console.log("ZXing cargado:", window['ZXingBrowser']);

    const ZX = window['ZXingBrowser']; // la UMD expone este objeto
    const BrowserMultiFormatReader = ZX.BrowserMultiFormatReader;

    const preview = document.getElementById('preview');
    const inputCodigo = document.getElementById('codigo');
    const btnStart = document.getElementById('btnStart');
    const btnStop = document.getElementById('btnStop');

    let reader = null;

    btnStart.addEventListener('click', async () => {

        btnStart.style.display = 'none';
        btnStop.style.display = 'inline-block';
        preview.style.display = 'block';

        reader = new BrowserMultiFormatReader();

        try {

            // Obtener cámaras
            const devices = await BrowserMultiFormatReader.listVideoInputDevices();

            if (!devices || devices.length === 0) {
                alert("No se detectan cámaras.");
                detener();
                return;
            }

            // Preferir cámara trasera
            let deviceId = devices[0].deviceId;
            if (devices.length > 1) {
                deviceId = devices[devices.length - 1].deviceId;
            }

            reader.decodeFromVideoDevice(deviceId, 'preview', (result, err) => {

                if (result) {
                    console.log("Detectado:", result.text);

                    // Filtrar caracteres válidos de Code 39
                    let clean = result.text.replace(/[^A-Za-z0-9\-\.\ \$\/\+\%]/g, "");
                    inputCodigo.value = clean;

                    detener(); // Detener tras primer lectura
                }

            });

        } catch (err) {
            console.error(err);
            alert('No se pudo iniciar la cámara.');
            detener();
        }
    });

    btnStop.addEventListener('click', detener);

    function detener() {
        try {
            if (reader) reader.reset();
        } catch {}

        preview.style.display = 'none';
        btnStart.style.display = 'inline-block';
        btnStop.style.display = 'none';
    }
});
</script>

<?= $this->endSection() ?>
