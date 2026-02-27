<?php

namespace julio101290\boilerplateinventory\Models;

use CodeIgniter\Model;

class SaldosModel extends Model {

    protected $table = 'saldos';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = ['id'
        , 'idEmpresa'
        , 'idAlmacen'
        , 'idProducto'
        , 'codigoProducto'
        , 'descripcion'
        , 'cantidad'
        , 'lote'
        , 'created_at'
        , 'deleted_at'
        , 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [
        'idEmpresa' => 'required|is_natural_no_zero|max_length[20]',
        'idAlmacen' => 'required|is_natural_no_zero|max_length[20]',
        'lote' => 'string|max_length[128]',
        'idProducto' => 'required|is_natural_no_zero|max_length[20]',
        'codigoProducto' => 'string|max_length[64]',
        'descripcion' => 'required|string|max_length[1024]',
        'cantidad' => 'required|decimal',
    ];
    protected $validationMessages = [];
    protected $skipValidation = false;

    public function mdlGetSaldos($idEmpresas) {
        return $this->db->table('saldos a')
                        ->select("
            a.id
            ,a.idEmpresa
            ,a.idAlmacen
            ,a.idProducto
            ,a.codigoProducto
            ,a.lote
            ,a.descripcion
            ,a.cantidad
            ,a.created_at
            ,a.deleted_at
            ,a.updated_at
            ,b.nombre AS nombreEmpresa
            ,c.name AS nombreAlmacen
            ,COALESCE(e.fullname, 'Sin asignar') AS fullname
        ")

                        // Empresas
                        ->join('empresas b', 'a.idEmpresa = b.id')

                        // Almacenes
                        ->join('storages c', 'a.idAlmacen = c.id')

                        // 👇 LEFT JOIN para que NO reviente si no hay relación en productsEmployes
                        ->join('productsemployes pe', 'pe.idProduct = a.id', 'left')

                        // 👇 LEFT JOIN para que NO reviente si no hay empleado
                        ->join('employes e', 'e.id = pe.idEmploye', 'left')
                        ->whereIn('a.idEmpresa', $idEmpresas)
                        ->orderBy('a.id', 'DESC');
    }

    public function mdlGetSaldosFilters($idEmpresas, $idAlmacen = null, $idProducto = null) {
        if (is_string($idEmpresas)) {
            $idEmpresas = array_filter(explode(',', $idEmpresas));
        }

        if (!is_array($idEmpresas)) {
            $idEmpresas = [];
        }

        $builder = $this->db->table('saldos a')
                ->select("
            a.id,
            a.idEmpresa,
            a.idAlmacen,
            a.idProducto,
            a.codigoProducto,
            a.lote,
            a.descripcion,
            a.cantidad,
            a.created_at,
            a.deleted_at,
            a.updated_at,
            b.nombre AS nombreEmpresa,
            c.name AS nombreAlmacen,
            COALESCE(e.fullname, 'Sin asignar') AS fullname
        ")
                ->join('empresas b', 'a.idEmpresa = b.id')
                ->join('storages c', 'a.idAlmacen = c.id')
                ->join('productsemployes pe', 'pe.idProduct = a.id', 'left')
                ->join('employes e', 'e.id = pe.idEmploye', 'left')
                ->orderBy('a.id', 'DESC');

        if (count($idEmpresas) > 0) {
            // 👇 OJO: whereIn
            $builder->whereIn('a.idEmpresa', $idEmpresas);
        }

        if (!empty($idAlmacen) && (int) $idAlmacen !== 0) {
            $builder->where('a.idAlmacen', (int) $idAlmacen);
        }

        if (!empty($idProducto) && (int) $idProducto !== 0) {
            $builder->where('a.idProducto', (int) $idProducto);
        }

        return $builder; // 👈 CLAVE
    }

    public function mdlGetProductoEmpresa($empresas, $idProducto, $search) {

        $builder = $this->db->table('products a')
                ->select(
                        'a.id AS id,
            a.code AS code,
            a.idEmpresa AS idEmpresa,
            a.validateStock AS validateStock,
            a.inventarioRiguroso AS inventarioRiguroso,
            a.idCategory AS idCategory,
            a.idSubCategoria AS idSubCategoria,
            c.clave AS clave,
            c.descripcion AS descripcionCategoria,
            a.description AS description,
            a.stock AS stock,
            a.buyPrice AS buyPrice,
            a.salePrice AS salePrice,
            a.porcentSale AS porcentSale,
            a.porcentTax AS porcentTax,
            a.routeImage AS routeImage,
            a.created_at AS created_at,
            a.deleted_at AS deleted_at,
            a.updated_at AS updated_at,
            a.barcode AS barcode,
            a.unidad AS unidad,
            b.nombre AS nombreEmpresa,
            a.porcentIVARetenido AS porcentIVARetenido,
            a.porcentISRRetenido AS porcentISRRetenido,
            a.nombreUnidadSAT AS nombreUnidadSAT,
            a.nombreClaveProducto AS nombreClaveProducto,
            a.unidadSAT AS unidadSAT,
            a.inmuebleOcupado AS inmuebleOcupado,
            a.tasaExcenta AS tasaExcenta,
            a.predial AS predial,
            a.calculatelot AS calculatelot,
            a.claveProductoSAT AS claveProductoSAT'
                )
                ->join('empresas b', 'a.idEmpresa = b.id')
                ->join('categorias c', 'a.idCategory = c.id')
                ->where('a.deleted_at', null)
                ->whereIn('a.idEmpresa', $empresas);

        // 🔍 Búsqueda por texto
        if (!empty($search)) {
            $builder->groupStart()
                    ->like('a.description', $search)
                    ->orLike('a.code', $search)
                    ->orLike('a.barcode', $search)
                    ->groupEnd();
        }

        return $builder->get()->getResultArray();
    }
    public function mdlGetProducto($code) {

        $builder = $this->db->table('saldos a')
                ->select(
                        'a.idAlmacen,
            a.lote,
            a.idProducto,
            a.codigoProducto,
            a.descripcion,
            b.id,
            b.name,
            d.fullname,
            e.date,
            E.generalObservations'
                )
               ->join('storages b', 'a.idAlmacen = b.id')
               ->join('productsemployes c', 'a.id = c.idProduct')
               ->join('employes d', 'c.idEmploye = d.id')
               ->join('ordermaintenance e', 'a.id = e.idProduct')
               ->where('a.lote', $code);

        return $builder->get()->getResultArray();
    }
}
