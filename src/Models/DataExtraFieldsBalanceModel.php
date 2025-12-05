<?php

/**
 * Adaptado por julio101290
 */

namespace julio101290\boilerplateinventory\Models;

use CodeIgniter\Model;

class DataExtraFieldsBalanceModel extends Model {

    protected $table = 'data_extra_fields_balance';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = true;
    protected $allowedFields = [
        'id',
        'idProduct',
        'idField',
        'value',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $validationRules = [];
    protected $validationMessages = [];
    protected $skipValidation = false;
}
