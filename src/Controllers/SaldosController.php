<?php

namespace julio101290\boilerplateinventory\Controllers;

use App\Controllers\BaseController;
use julio101290\boilerplateinventory\Models\SaldosModel;
use CodeIgniter\API\ResponseTrait;
use julio101290\boilerplatelog\Models\LogModel;
use julio101290\boilerplatecompanies\Models\EmpresasModel;

class SaldosController extends BaseController {

    use ResponseTrait;

    protected $log;
    protected $saldos;
    protected $empresa;

    public function __construct() {
        $this->saldos = new SaldosModel();
        $this->log = new LogModel();
        $this->empresa = new EmpresasModel();
        helper(['menu', 'utilerias']);
    }

    public function index() {
        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);
        $empresasID = count($titulos["empresas"]) === 0 ? [0] : array_column($titulos["empresas"], "id");

        if ($this->request->isAJAX()) {
            $request = service('request');

            $draw = (int) $request->getGet('draw');
            $start = (int) $request->getGet('start');
            $length = (int) $request->getGet('length');
            $searchValue = $request->getGet('search')['value'] ?? '';
            $orderColumnIndex = (int) $request->getGet('order')[0]['column'] ?? 0;
            $orderDir = $request->getGet('order')[0]['dir'] ?? 'asc';

            $fields = $this->saldos->allowedFields;
            $orderField = $fields[$orderColumnIndex] ?? 'id';

            $builder = $this->saldos->mdlGetSaldos($empresasID);

            $total = clone $builder;
            $recordsTotal = $total->countAllResults(false);

            if (!empty($searchValue)) {
                $builder->groupStart();
                foreach ($fields as $field) {
                    $builder->orLike("a." . $field, $searchValue);
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

        $titulos["title"] = lang('saldos.title');
        $titulos["subtitle"] = lang('saldos.subtitle');
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

    public function getBarcodePDF($idProducto, $isMail = 0) {


        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

// define barcode style
        $style = array(
            'position' => '',
            'align' => 'C',
            'stretch' => false,
            'fitwidth' => true,
            'cellfitalign' => '',
            'border' => true,
            'hpadding' => 'auto',
            'vpadding' => 'auto',
            'fgcolor' => array(0, 0, 0),
            'bgcolor' => false, //array(255,255,255),
            'text' => true,
            'font' => 'helvetica',
            'fontsize' => 12,
            'stretchtext' => 4
        );

        helper('auth');

        $idUser = user()->id;
        $titulos["empresas"] = $this->empresa->mdlEmpresasPorUsuario($idUser);

        if (count($titulos["empresas"]) == "0") {

            $empresasID[0] = "0";
        } else {

            $empresasID = array_column($titulos["empresas"], "id");
        }


        if ($idProducto == 0) {


            $productos = $this->saldos->select("id,lote")->whereIn("idEmpresa", $empresasID)->findAll();

            foreach ($productos as $key => $value) {



                //$pdf->Cell(0, 0, 'BAR CODE', 0, 1);
                if (strlen($value["lote"]) <= 3) {

                    continue;
                }
                $pdf->AddPage('L', array(101, 50));
                $pdf->write1DBarcode($value["lote"], 'C39', '', '', '', 18, 0.4, $style, 'N');
            }

            ob_end_clean();
            $this->response->setHeader("Content-Type", "application/pdf");
            $pdf->Output('etiqueta.pdf', 'I');

            return;
        }


        $productos = $this->saldos->select("lote")
                        ->whereIn("idEmpresa", $empresasID)
                        ->where("id", $idProducto)->findAll();

        $pdf->AddPage('L', array(101, 50));

        $pdf->write1DBarcode($productos[0]["lote"], 'C39', '', '', '', 18, 0.4, $style, 'N');

        ob_end_clean();
        $this->response->setHeader("Content-Type", "application/pdf");
        $pdf->Output('etiqueta.pdf', 'I');
    }
}
