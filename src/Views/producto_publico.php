<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Información de Producto</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

    <style>
        body {
            background: #10131a;
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            padding: 2rem 1rem;
        }

        .info-container {
            max-width: 560px;
            margin: auto;
            width: 100%;
        }

        .brand-strip {
            height: 6px;
            width: 100%;
            background: #d0272a;
            border-radius: 14px 14px 0 0;
        }

        .card-dark {
            background: #1a1e27;
            border: 1px solid #262b36;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.35);
        }

        .card-dark .card-body {
            padding: 1.75rem;
        }

        .title-row {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            margin-bottom: 1.25rem;
        }

        .title-row h5 {
            color: #f2f4f8;
            margin: 0;
            font-weight: 600;
        }

        .badge-gusa {
            background: #d0272a;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.3rem 0.6rem;
            border-radius: 6px;
        }

        .field-label {
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            color: #7b8496;
            margin-bottom: 0.15rem;
            display: block;
        }

        .field-value {
            color: #e7eaf0;
            font-size: 0.92rem;
            font-weight: 500;
            padding-bottom: 0.6rem;
            border-bottom: 1px solid #262b36;
            margin-bottom: 0.9rem;
            min-height: 1.4em;
            word-break: break-word;
        }

        .mantenimiento-box {
            white-space: pre-wrap;
            color: #c3c9d6;
            font-size: 0.85rem;
            background: #12151c;
            border: 1px solid #262b36;
            border-radius: 8px;
            padding: 0.75rem;
            max-height: 220px;
            overflow-y: auto;
        }

        .not-found-icon {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .search-form input {
            background: #12151c;
            border: 1px solid #262b36;
            color: #e7eaf0;
        }

        .search-form input::placeholder {
            color: #5b6270;
        }

        .search-form input:focus {
            background: #12151c;
            color: #e7eaf0;
            border-color: #d0272a;
            box-shadow: 0 0 0 0.2rem rgba(208,39,42,0.25);
        }

        .btn-gusa {
            background: #d0272a;
            border-color: #d0272a;
            color: #fff;
        }

        .btn-gusa:hover {
            background: #b31f22;
            border-color: #b31f22;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="info-container">

    <div class="brand-strip"></div>
    <div class="card-dark">
        <div class="card-body">

            <div class="title-row">
                <span class="badge-gusa">GUSA</span>
                <h5>Información de Producto</h5>
            </div>

            <!-- Buscador simple, funciona por GET, sin JS ni login -->
            <form class="search-form d-flex gap-2 mb-4" method="get"
                  action="<?= base_url('inventario/producto') ?>">
                <input type="text" name="codigoBuscado" class="form-control"
                       placeholder="Escribe el código del artículo…"
                       value="<?= esc($codigo ?? '') ?>">
                <button type="submit" class="btn btn-gusa">Buscar</button>
            </form>

            <?php if (!empty($mensaje)): ?>
                <div class="text-center text-white-50 py-4">
                    <div class="not-found-icon">🔍</div>
                    <p class="mb-0"><?= esc($mensaje) ?></p>
                </div>
            <?php elseif ($encontrado && $producto): ?>

                <span class="field-label">Producto</span>
                <div class="field-value"><?= esc($producto["idProducto"] ?? '') ?></div>

                <span class="field-label">Descripción</span>
                <div class="field-value"><?= esc($producto["descripcion"] ?? '') ?></div>

                <div class="row">
                    <div class="col-6">
                        <span class="field-label">Lote</span>
                        <div class="field-value"><?= esc($producto["lote"] ?? '') ?></div>
                    </div>
                    <div class="col-6">
                        <span class="field-label">Almacén</span>
                        <div class="field-value"><?= esc($producto["name"] ?? '') ?></div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-6">
                        <span class="field-label">Código del producto</span>
                        <div class="field-value"><?= esc($producto["codigoProducto"] ?? '') ?></div>
                    </div>
                    <div class="col-6">
                        <span class="field-label">Usuario asignado</span>
                        <div class="field-value"><?= esc($producto["fullname"] ?? '') ?></div>
                    </div>
                </div>

                <span class="field-label">Mantenimientos</span>
                <div class="mantenimiento-box"><?= esc($producto["maintenanceHistory"] ?? 'Sin registros.') ?></div>

            <?php else: ?>
                <div class="text-center text-white-50 py-4">
                    <p class="mb-0">Escribe un código o accede con un enlace que ya incluya el código del artículo.</p>
                </div>
            <?php endif; ?>

        </div>
    </div>
</div>

</body>
</html>