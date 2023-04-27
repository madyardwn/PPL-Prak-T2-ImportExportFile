<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Mahasiswa extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'nim'          => [
                'type'           => 'VARCHAR',
                'constraint'     => '9',
            ],
            'nama'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'ets' => [
                'type' => 'FLOAT',
            ],
            'eas' => [
                'type' => 'FLOAT',
            ],
            'final' => [
                'type' => 'FLOAT',
            ],
        ]);

        $this->forge->addKey('nim', true);
        $this->forge->createTable('mahasiswa');
    }

    public function down()
    {
        $this->forge->dropTable('mahasiswa');
    }
}
