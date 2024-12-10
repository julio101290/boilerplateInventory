<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class InventoryDetails extends MigrationCarta {

    public function up() {
        // QuotesDetails
        $this->forge->addField([
            'id' => ['type' => 'int', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'idInventory' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'idProduct' => ['type' => 'int', 'constraint' => 11, 'null' => true],
            'lote' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'description' => ['type' => 'varchar', 'constraint' => 512, 'null' => true],
            'unidad' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'claveProductoSAT' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'claveUnidadSAT' => ['type' => 'varchar', 'constraint' => 64, 'null' => true],
            'codeProduct' => ['type' => 'varchar', 'constraint' => 32, 'null' => true],
            'cant' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'price' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'porcentTax' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'porcentIVARetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'IVARetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'porcentISRRetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'ISRRetenido' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'tax' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'neto' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'total' => ['type' => 'decimal', 'constraint' => '18,4', 'null' => true],
            'created_at' => ['type' => 'datetime', 'null' => true],
            'updated_at' => ['type' => 'datetime', 'null' => true],
            'deleted_at' => ['type' => 'datetime', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('inventorydetails', true);
    }

    public function down() {
        $this->forge->dropTable('inventorydetails', true);
    }

}
