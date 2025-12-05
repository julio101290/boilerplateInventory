<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class DataExtraFieldsProductsBalance extends Migration
{
    public function up()
    {
        // Tabla: data_extra_fields_products
        $this->forge->addField([
            'id' => [
                'type'           => 'int',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'idProduct' => [
                'type'       => 'int',
                'constraint' => 11,
                'null'       => false,
            ],
            'idField' => [
                'type'       => 'int',
                'constraint' => 11,
                'null'       => false,
            ],
            'value' => [
                'type'       => 'varchar',
                'constraint' => 1024,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'datetime',
                'null' => true,
            ],
        ]);

        // 🔑 Llave primaria
        $this->forge->addKey('id', true);

        // 🔹 Llaves foráneas opcionales (por si quieres activar relaciones)
        // $this->forge->addForeignKey('idProduct', 'products', 'id', 'CASCADE', 'CASCADE');
        // $this->forge->addForeignKey('idField', 'fieldsExtraProductos', 'id', 'CASCADE', 'CASCADE');

        // Crear tabla
        $this->forge->createTable('data_extra_fields_balance', true);
    }

    public function down()
    {
        $this->forge->dropTable('data_extra_fields_balance', true);
    }
}
