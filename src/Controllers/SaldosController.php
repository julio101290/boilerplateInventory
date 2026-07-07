<?php

namespace julio101290\boilerplateinventory\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateinventory\Models\SaldosModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;
use julio101290\boilerplateinventory\Models\DataExtraFieldsBalanceModel;
use julio101290\boilerplateproducts\Models\ProductsModel;
use julio101290\boilerplateproducts\Models\FieldsExtraProductosModel;
use julio101290\boilerplateproducts\Models\CategoriasModel;
use julio101290\boilerplatestorages\Models\StoragesModel;
use julio101290\boilerplatestorages\Models\UsuariosAlmacenModel;

class SaldosController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $saldos;
    protected $empresa;
    protected $fieldsExtra;
    protected $fieldsExtraValues;
    protected $products;
    protected $storages;
    protected $storagesPerUser;

    public function __construct() {
        $this->saldos = new SaldosModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        $this->fieldsExtraValues = new DataExtraFieldsBalanceModel();
        $this->fieldsExtra = new FieldsExtraProductosModel();
        $this->products = new ProductsModel();
        $this->storages = new StoragesModel();
        $this->storagesPerUser = new UsuariosAlmacenModel;
        helper(['menu', 'utilerias']);
    }

    /**
     * Index
     * @return type
     */
    public function index() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $storagesUser = $this->storagesPerUser
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->asArray()->findAll();

        $storagesUser = count($storagesUser) === 0 ? [0] : array_column($storagesUser, "idStorage");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            //$fields = $this->saldos->allowedFields;
            $fields = [
                'id' => 'a.id',
                'nombreAlmacen' => 'c.name',
                'lote' => 'a.lote',
                'codigoProducto' => 'a.codigoProducto',
                'descripcion' => 'a.descripcion',
                'fullname' => 'e.fullname'
            ];
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->saldos->mdlGetSaldos($empresasID, $storagesUser);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike($field, $searchValue);
                }
                $builder->groupEnd();
            }


            $filteredBuilder = clone $builder;
            $recordsFiltered = $filteredBuilder->countAllResults(false);

            $data = $builder->orderBy("a." . $orderField, $orderDir)
                    ->get($length, $start)
                    ->getResultArray();

            return $this->response->setJSON([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $data,
            ]);
        }

        $titulos["title"] = "Info Productos";
        $titulos["subtitle"] = "Extrae la información de los productos por el codigo de barras";
        return view('julio101290\boilerplateinventory\Views\saldos', $titulos);
    }

    public function getSaldosFilters($idEmpresa, $idAlmacen, $idProducto) {
        helper('auth');

        $idUser = user()->id;
        if ($idEmpresa == "") {
            $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
            $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");
        } else {
            $empresasID = $idEmpresa;
        }

        $storagesUser = $this->storagesPerUser
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->asArray()->findAll();

        $storagesUser = count($storagesUser) === 0 ? [0] : array_column($storagesUser, "idStorage");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            //$fields = $this->saldos->allowedFields;
            $fields = [
                'id' => 'a.id',
                'nombreAlmacen' => 'c.name',
                'lote' => 'a.lote',
                'codigoProducto' => 'a.codigoProducto',
                'descripcion' => 'a.descripcion',
                'fullname' => 'e.fullname'
            ];
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->saldos->mdlGetSaldosFilters($empresasID, $idAlmacen, $idProducto, $storagesUser);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike($field, $searchValue);
                }
                $builder->groupEnd();
            }


            $filteredBuilder = clone $builder;
            $recordsFiltered = $filteredBuilder->countAllResults(false);

            $data = $builder->orderBy("a." . $orderField, $orderDir)
                    ->get($length, $start)
                    ->getResultArray();

            return $this->response->setJSON([
                        'draw' => $draw,
                        'recordsTotal' => $recordsTotal,
                        'recordsFiltered' => $recordsFiltered,
                        'data' => $data,
            ]);
        }

        $titulos["title"] = "Info Productos";
        $titulos["subtitle"] = "Extrae la información de los productos por el codigo de barras";
        return view('julio101290\boilerplateinventory\Views\saldos', $titulos);
    }

    public function getSaldos() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $idSaldos = $this->request->getPost("idSaldos");
        $dato = $this->saldos->whereIn('idEmpresa', $empresasID)
                ->where('id', $idSaldos)
                ->first();

        return $this->response->setJSON($dato);
    }

    public function getGetInfoProducts() {

        helper('auth');
        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        $titulos["title"] = lang('saldos.title');
        $titulos["subtitle"] = lang('saldos.subtitle');
        return view('julio101290\boilerplateinventory\Views\infoInventario', $titulos);
    }

    public function getGetInfoProductsCode() {

        helper('auth');
        $idUser = user()->id;
        $empresa = "";

        $idBalance = $this->request->getPost("codigo");
        $postData['searchTerm'] = "";
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");
        $empresa = (int) $empresa;

//        $result = $this->saldos->where("lote", $idBalance)->asObject()->first();

        $result = $this->saldos->mdlGetProducto($idBalance);

        if (empty($result)) {
            return $this->response->setJSON([]);
        }

        return $this->response->setJSON($result[0]);
    }

    /**
     * Vista pública de información de producto.
     * No requiere sesión ni usuario autenticado.
     * Recibe el código del producto por GET (segmento de la URL).
     */
    public function verProductoPublico($codigo = null) {
        $data['codigo'] = $codigo;
        $data['encontrado'] = false;
        $data['producto'] = null;
        $data['mensaje'] = null;

        if (empty($codigo)) {
            $data['mensaje'] = null; // sin código, solo mostramos el buscador vacío
            return view('julio101290\boilerplateinventory\Views\producto_publico', $data);
        }

        $result = $this->saldos->mdlGetProducto($codigo);

        if (empty($result)) {
            $data['mensaje'] = 'No se encontró el artículo con el código proporcionado.';
            return view('julio101290\boilerplateinventory\Views\producto_publico', $data);
        }

        $producto = $result[0];

        // Igual que en el flujo actual: separar el historial de mantenimiento
        if (!empty($producto->maintenanceHistory)) {
            $producto->maintenanceHistory = preg_replace('/\s*\|\|\s*/', "\n\n", $producto->maintenanceHistory);
        }

        $data['producto'] = $producto;
        $data['encontrado'] = true;

        return view('julio101290\boilerplateinventory\Views\producto_publico', $data);
    }

    public function save() {
        helper('auth');

        $userName = user()->username;
        $datos = $this->request->getPost();
        $idKey = $datos["idSaldos"] ?? 0;

        if ($idKey == 0) {
            try {
                if (!$this->saldos->save($datos)) {
                    $errores = implode(" ", $this->saldos->errors());
                    return $this->respond(['status' => 400, 'message' => $errores], 400);
                }
                $this->log->save([
                    "description" => lang("saldos.logDescription") . json_encode($datos),
                    "user" => $userName
                ]);
                return $this->respond(['status' => 201, 'message' => 'Guardado correctamente'], 201);
            } catch (\Throwable $ex) {
                return $this->respond(['status' => 500, 'message' => 'Error al guardar: ' . $ex->getMessage()], 500);
            }
        } else {
            if (!$this->saldos->update($idKey, $datos)) {
                $errores = implode(" ", $this->saldos->errors());
                return $this->respond(['status' => 400, 'message' => $errores], 400);
            }
            $this->log->save([
                "description" => lang("saldos.logUpdated") . json_encode($datos),
                "user" => $userName
            ]);
            return $this->respond(['status' => 200, 'message' => 'Actualizado correctamente'], 200);
        }
    }

    public function delete($id) {
        helper('auth');

        $userName = user()->username;
        $registro = $this->saldos->find($id);

        if (!$this->saldos->delete($id)) {
            return $this->respond(['status' => 404, 'message' => lang("saldos.msg.msg_get_fail")], 404);
        }

        $this->saldos->purgeDeleted();
        $this->log->save([
            "description" => lang("saldos.logDeleted") . json_encode($registro),
            "user" => $userName
        ]);

        return $this->respondDeleted($registro, lang("saldos.msg_delete"));
    }

    public function getBarcodePDF($idProducto, $idEmpresa, $idAlmacen, $idProducto2, $isMail = 0) {
        // TCPDF
        $pdf = new \TCPDF('L', 'mm', array(101, 50), true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Estilo Código de Barras
        $style1D = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false,
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 10,
            'stretchtext' => 4
        );

        // Estilo QR
        $styleQR = array(
            'border' => true,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false
        );

        helper('auth');
        $idUser = user()->id;

        if (!empty($idEmpresa)) {
            $empresasID = [$idEmpresa];
        } else {
            $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
            $empresasID = count($empresas) == 0 ? [0] : array_column($empresas, 'id');
        }

        $storagesUser = $this->storagesPerUser
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->asArray()->findAll();

        $storagesUser = count($storagesUser) === 0 ? [0] : array_column($storagesUser, "idStorage");

        // TODOS LOS PRODUCTOS
        if ($idProducto == 0) {

            $query = $this->saldos
                    ->select("id, lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->whereIn('idAlmacen', $storagesUser);

            if ($idAlmacen != 0) {
                $query->where("idAlmacen", $idAlmacen);
            }
            if ($idProducto2 != 0) {
                $query->where("idProducto", $idProducto2);
            }

            $productos = $query->findAll();
            foreach ($productos as $value) {

                if (strlen($value['lote']) <= 3) {
                    continue;
                }

                $pdf->AddPage();

                // Código de barras
                $pdf->write1DBarcode(
                        $value['lote'],
                        'C39',
                        5, // X
                        5, // Y
                        70, // Ancho
                        18, // Alto
                        0.4,
                        $style1D,
                        'N'
                );

                // QR
                $pdf->write2DBarcode(
                        $value['lote'],
                        'QRCODE,M',
                        78, // X
                        5, // Y
                        18, // Ancho
                        18, // Alto
                        $styleQR,
                        'N'
                );
                $pdf->SetAutoPageBreak(false, 0);
                $pdf->SetFont('helvetica', 'B', 15);
                $pdf->SetXY(5, 28);
                $pdf->MultiCell(90, 5, $value['descripcion'], 0, 'C');
            }
        } else {

            // SOLO UN PRODUCTO
            $producto = $this->saldos
                    ->select("lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->where("id", $idProducto)
                    ->first();

            $pdf->AddPage();

            // Código de barras
            $pdf->write1DBarcode(
                    $producto['lote'],
                    'C39',
                    5,
                    5,
                    70,
                    18,
                    0.4,
                    $style1D,
                    'N'
            );

            // QR
            $pdf->write2DBarcode(
                    $producto['lote'],
                    'QRCODE,M',
                    78,
                    5,
                    18,
                    18,
                    $styleQR,
                    'N'
            );
            $pdf->SetAutoPageBreak(false, 0);
            // Descripción debajo
            $pdf->SetFont('helvetica', 'B', 15);
            $pdf->SetXY(5, 28);
            $pdf->MultiCell(90, 5, $producto['descripcion'], 0, 'C');
        }

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta.pdf', 'I');
    }

    public function getQR96PDF($idProducto, $idEmpresa, $idAlmacen, $idProducto2, $isMail = 0) {

        // Medidas reales del ticket: 9.6cm x 9.6cm = 96mm x 96mm
        $labelSize = 96; // mm (ancho = alto, es cuadrado)

        $pdf = new \TCPDF('P', 'mm', array($labelSize, $labelSize), true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        // Estilo QR
        $styleQR = array(
            'border' => true,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false
        );

        helper('auth');
        $idUser = user()->id;

        if (!empty($idEmpresa)) {
            $empresasID = [$idEmpresa];
        } else {
            $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
            $empresasID = count($empresas) == 0 ? [0] : array_column($empresas, 'id');
        }

        $storagesUser = $this->storagesPerUser
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->asArray()->findAll();

        $storagesUser = count($storagesUser) === 0 ? [0] : array_column($storagesUser, "idStorage");

        // Dibuja el ticket completo (QR arriba, código y descripción abajo)
        $drawLabel = function ($pdf, $lote, $descripcion) use ($styleQR, $labelSize) {
            // QR grande, centrado, arriba
            $qrSize = 60;
            $qrX = ($labelSize - $qrSize) / 2;
            $qrY = 6;
            
            $loteQR = base_url("admin/inventario/producto/".$lote);

            $pdf->write2DBarcode(
                    $loteQR,
                    'QRCODE,M',
                    $qrX,
                    $qrY,
                    $qrSize,
                    $qrSize,
                    $styleQR,
                    'N'
            );

            // Código (lote) debajo del QR
            $pdf->SetFont('helvetica', '', 11);
            $pdf->SetXY(4, $qrY + $qrSize + 3);
            $pdf->MultiCell($labelSize - 8, 5, $lote, 0, 'C');

            // Descripción debajo del código
            $pdf->SetFont('helvetica', 'B', 12);
            $pdf->SetXY(4, $qrY + $qrSize + 11);
            $pdf->MultiCell($labelSize - 8, 5, $descripcion, 0, 'C');
        };

        if ($idProducto == 0) {
            $query = $this->saldos
                    ->select("id, lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->whereIn('idAlmacen', $storagesUser);

            if ($idAlmacen != 0) {
                $query->where("idAlmacen", $idAlmacen);
            }
            if ($idProducto2 != 0) {
                $query->where("idProducto", $idProducto2);
            }

            $productos = $query->findAll();

            foreach ($productos as $value) {
                if (strlen($value['lote']) <= 3) {
                    continue;
                }

                $pdf->AddPage();
                $drawLabel($pdf, $value['lote'], $value['descripcion']);
            }
        } else {
            $producto = $this->saldos
                    ->select("lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->where("id", $idProducto)
                    ->first();

            $pdf->AddPage();
            $drawLabel($pdf, $producto['lote'], $producto['descripcion']);
        }

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta_qr_96.pdf', 'I');
    }

    public function getQRPDFV2($idProducto, $idEmpresa, $idAlmacen, $idProducto2, $isMail = 0) {

        // Medidas reales del ticket individual
        $labelWidth = 51; // mm
        $labelHeight = 22; // mm
        $gap = 3; // separación entre los 2 tickets
        // Página = 2 tickets + separación
        $pageWidth = ($labelWidth * 2) + $gap; // 51+3+51 = 105 mm
        $pageHeight = $labelHeight;             // 22 mm

        $pdf = new \TCPDF('L', 'mm', array($pageWidth, $pageHeight), true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false, 0);

        // Estilo QR
        $styleQR = array(
            'border' => true,
            'vpadding' => 'auto',
            'hpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false
        );

        helper('auth');
        $idUser = user()->id;

        if (!empty($idEmpresa)) {
            $empresasID = [$idEmpresa];
        } else {
            $empresas = $this->empresa->mdlEmpresasPorUsuario($idUser);
            $empresasID = count($empresas) == 0 ? [0] : array_column($empresas, 'id');
        }

        $storagesUser = $this->storagesPerUser
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->asArray()->findAll();

        $storagesUser = count($storagesUser) === 0 ? [0] : array_column($storagesUser, "idStorage");

        // X inicial de cada ticket: izquierdo=0, derecho=51+3=54
        $offsets = [0, $labelWidth + $gap];

        // Dibuja UN ticket (QR + código + descripción) en la posición X que se le indique
        $drawLabel = function ($pdf, $offsetX, $lote, $descripcion) use ($styleQR, $labelWidth, $labelHeight) {
            // QR a la izquierda del ticket, centrado verticalmente
            $qrSize = 18;
            $qrX = $offsetX + 1.5;
            $qrY = ($labelHeight - $qrSize) / 2;
            
            $loteQR = base_url("admin/inventario/producto/".$lote);

            $pdf->write2DBarcode(
                    $loteQR,
                    'QRCODE,M',
                    $qrX,
                    $qrY,
                    $qrSize,
                    $qrSize,
                    $styleQR,
                    'N'
            );

            // Área de texto a la derecha del QR
            $textX = $qrX + $qrSize + 1.5;
            $textWidth = $labelWidth - ($textX - $offsetX) - 1;

            // Código (lote) arriba
            $pdf->SetFont('helvetica', '', 7);
            $pdf->SetXY($textX, 2);
            $pdf->MultiCell($textWidth, 3, $lote, 0, 'C');

            // Descripción debajo
            $pdf->SetFont('helvetica', 'B', 7.5);
            $pdf->SetXY($textX, 9);
            $pdf->MultiCell($textWidth, 3, $descripcion, 0, 'C');
        };

        if ($idProducto == 0) {
            $query = $this->saldos
                    ->select("id, lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->whereIn('idAlmacen', $storagesUser);

            if ($idAlmacen != 0) {
                $query->where("idAlmacen", $idAlmacen);
            }
            if ($idProducto2 != 0) {
                $query->where("idProducto", $idProducto2);
            }

            $productos = $query->findAll();

            foreach ($productos as $value) {
                if (strlen($value['lote']) <= 3) {
                    continue;
                }

                $pdf->AddPage();

                foreach ($offsets as $offsetX) {
                    $drawLabel($pdf, $offsetX, $value['lote'], $value['descripcion']);
                }
            }
        } else {
            $producto = $this->saldos
                    ->select("lote, descripcion")
                    ->whereIn("idEmpresa", $empresasID)
                    ->where("id", $idProducto)
                    ->first();

            $pdf->AddPage();

            foreach ($offsets as $offsetX) {
                $drawLabel($pdf, $offsetX, $producto['lote'], $producto['descripcion']);
            }
        }

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta_qr.pdf', 'I');
    }

    /**
     * Read Products
     */
    public function getProductsFieldsExtra() {


        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }
        $idBalance = $this->request->getPost("idBalance");

        $datosBalance = $this->saldos->where("id", $idBalance)->asObject()->first();

        $dataProduct = $this->products->where("id", $datosBalance->idProducto)->asObject()->first();

        //GET FIELD EXTRA
        $fieldExtra = $this->fieldsExtra->select("*")
                ->where("idCategory", $dataProduct->idCategory)
                ->where("idSubCategory", $dataProduct->idSubCategoria)
                ->findAll();

        $html = '';

// 🔹 Siempre agregar este campo oculto con el valor de $idProducts
        $html = '<input type="hidden" id="idProductExtraFields" name="idProductExtraFields" value="' . $idBalance . '">';

// 🔹 Si hay campos configurados
        if (!empty($fieldExtra)) {

            // 🔹 Obtener valores existentes para este producto (si ya hay guardados)
            $savedValues = $this->fieldsExtraValues
                    ->select('idField, value')
                    ->where('idProduct', $idBalance)
                    ->findAll();

            // Convertir a arreglo [idField => value] para acceso rápido
            $savedMap = [];
            foreach ($savedValues as $sv) {
                $savedMap[$sv['idField']] = $sv['value'];
            }

            foreach ($fieldExtra as $field) {
                $fieldId = (int) $field['id']; // ID único del campo
                $name = "extraField_{$fieldId}";
                $id = "extraField_{$fieldId}";
                $label = ucwords(str_replace('_', ' ', $field['description']));

                // 🔹 Si ya hay valor guardado, úsalo
                $value = old($name) ?? ($savedMap[$fieldId] ?? '');
                $errorClass = "<?= session('error.{$name}') ? 'is-invalid' : '' ?>";

                if ($field['type'] == 1) {
                    // 🔹 Campo tipo TEXT
                    $html .= <<<EOF
        <div class="form-group row">
            <label for="{$id}" class="col-sm-2 col-form-label">{$label}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                    </div>
                    <input type="text" name="{$name}" id="{$id}" 
                        class="form-control {$errorClass}" 
                        value="{$value}" placeholder="{$label}" autocomplete="on">
                </div>
            </div>
        </div>
        EOF;
                } elseif ($field['type'] == 2) {
                    // 🔹 Campo tipo SELECT
                    $optionsHtml = '';
                    $options = explode(',', $field['options']);
                    foreach ($options as $opt) {
                        $opt = trim($opt);
                        $selected = ($opt == $value) ? 'selected' : '';
                        $optionsHtml .= "<option value=\"{$opt}\" {$selected}>{$opt}</option>";
                    }

                    $html .= <<<EOF
        <div class="form-group row">
            <label for="{$id}" class="col-sm-2 col-form-label">{$label}</label>
            <div class="col-sm-10">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text"><i class="fas fa-pencil-alt"></i></span>
                    </div>
                    <select class="form-control" name="{$name}" id="{$id}" style="width:80%;">
                        {$optionsHtml}
                    </select>
                </div>
            </div>
        </div>
        EOF;
                }
            }
        }

        echo $html;
    }

    /**
     * Save or update Products
     */
    public function saveExtraFields() {
        helper('auth');
        $userName = user()->username ?? 'system';
        $idUser = user()->id ?? 0;

        // Recoger datos enviados
        $datos = $this->request->getPost();

        // Validación: debe venir el idProduct
        if (empty($datos['idProduct']) || $datos['idProduct'] == 0) {
            return $this->response->setStatusCode(400)
                            ->setJSON(['status' => 'error', 'message' => 'Falta el ID del producto']);
        }

        $idProduct = (int) $datos['idProduct'];

        // Cargar modelo de data extra
        $dataExtraModel = new \julio101290\boilerplateinventory\Models\DataExtraFieldsBalanceModel();

        try {
            // Eliminar registros previos del producto (para evitar duplicados)
            $dataExtraModel->where('idProduct', $idProduct)->delete();

            // Recorrer los campos y guardar uno por uno
            foreach ($datos as $key => $value) {

                // Saltar campos no relevantes
                if ($key === 'idProductExtraFields' || $key === 'csrf_test_name') {
                    continue;
                }

                // 🔹 Extraer idField desde el nombre del campo, ej: "extraField_5" → 5
                if (preg_match('/^extraField_(\d+)$/', $key, $matches)) {
                    $idField = (int) $matches[1];

                    // Guardar en la base de datos
                    $dataExtraModel->insert([
                        'idProduct' => $idProduct,
                        'idField' => $idField,
                        'value' => trim($value),
                    ]);
                }
            }

            // Registrar log
            $dateLog = [
                "description" => "Campos extra guardados para producto #{$idProduct}: " . json_encode($datos),
                "user" => $userName,
            ];
            $this->log->save($dateLog);

            return $this->response->setJSON([
                        'status' => 'ok',
                        'message' => 'Campos extra guardados correctamente',
            ]);
        } catch (\Exception $ex) {
            return $this->response->setStatusCode(500)->setJSON([
                        'status' => 'error',
                        'message' => 'Error al guardar campos extra: ' . $ex->getMessage(),
            ]);
        }
    }

    public function getAllProducts() {


        helper('auth');
        $empresa = "";
        $userName = user()->username ?? 'system';
        $idUser = user()->id ?? 0;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        $request = service('request');
        $postData = $request->getPost();

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }

        $idEmpresaq = $postData['idEmpresa'];
        if ($this->request->isAJAX()) {


            $request = $this->request;
            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            if (!isset($postData['searchTerm'])) {

                $postData['searchTerm'] = "";
            }
            $empresa = (int) $empresa;

            // Obtiene paginación manual desde el modelo
            $resultados = $this->saldos
                    ->mdlGetProductoEmpresa($empresasID, $empresa, $postData['searchTerm']);

            $data = array();

            $data[] = [
                'id' => '0',
                'text' => 'Seleccionar producto'
            ];
            foreach ($resultados as $value) {

                $data[] = array(
                    "id" => $value['id'],
                    "text" => $value['id'] . ' ' . $value['description'],
                );
            }

            $response['data'] = $data;

            return $this->response->setJSON($response);
        }
    }

    public function getStoragesAjax() {

        $request = service('request');
        $postData = $request->getPost();

        $response = array();

        // Read new token and assign in $response['token']
        $response['token'] = csrf_hash();

        helper('auth');
        $userName = user()->username;
        $idUser = user()->id;
        $idEmpresa = $postData["idEmpresa"];

        if (empty($idEmpresa)) {
            // Si no mandan empresa, puedes decidir:
            // 1) mandar un array vacío (fallará whereIn)
            // 2) o mandar todas (NO recomendado aquí)
            $empresasID = [0]; // valor imposible para evitar error SQL
        } else {
            $empresasID = is_array($idEmpresa) ? $idEmpresa : [$idEmpresa];
        }


//        $empresasID[0] = $postData["idEmpresa"];
//        $empresasID = array_column($empresasID[0], "id");
        $almacenesPorUsuario = $this->storagesPerUser->select("*")
                        ->where("idUsuario", $idUser)
                        ->where("status", "on")->findAll();

        $almacenesPorUsuario = array_column($almacenesPorUsuario, "idStorage");
        if (!isset($postData['searchTerm'])) {
            $listStorages = $this->storages
                            ->whereIn("idEmpresa", $empresasID)
                            ->whereIn("id", $almacenesPorUsuario)
                            ->get()->getResultArray();
        } else {
            $searchTerm = $postData["searchTerm"];
            $listStorages = $this->storages
                            ->whereIn("idEmpresa", $empresasID) // Filtro obligatorio
                            ->whereIn("id", $almacenesPorUsuario) // Filtro obligatorio
                            ->groupStart() // Inicia paréntesis: WHERE ... AND (
                            ->like('name', $searchTerm)
                            ->orLike('id', $searchTerm)
                            ->orLike('code', $searchTerm)
                            ->groupEnd() // Cierra paréntesis: )
                            ->get()->getResultArray();
        }



        $data = array();
        $data[] = [
            'id' => '0',
            'text' => 'Seleccionar Almacen'
        ];
        foreach ($listStorages as $storage) {
            $data[] = array(
                "id" => $storage['id'],
                "text" => $storage['code'] . ' ' . $storage['name'],
            );
        }

        $response['data'] = $data;

        return $this->response->setJSON($response);
    }
}
