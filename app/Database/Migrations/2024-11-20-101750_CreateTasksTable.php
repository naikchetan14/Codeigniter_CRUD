<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTasksTable extends Migration
{
    public function up()
    {
        // Create the 'task' table
        $this->forge->addField([
            'Id' => [
                'type' => 'INT',
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'title' => [
                'type' => 'VARCHAR(30)',
                'constraint' => '255',
                'null' => false, // Not null
            ],
            'description' => [
                'type' => 'VARCHAR(50)',
                'null' => false, // Optional field
            ],
            'date' => [
                'type' => 'DATETIME',
                'null' => false, // Not null
            ],
            'status'=>[
                'type'=> 'BOOLEAN',
                'null' => true, // Optional field
            ]
        ]);
        
        // Add the primary key
        $this->forge->addKey('Id', true); // The first parameter is the column name, the second parameter indicates it's a primary key

        // Create the table
        $this->forge->createTable('task');
    }

    public function down()
    {
         // Drop the 'task' table if it exists
         $this->forge->dropTable('task');
    }
}
