<?php

namespace App\Models;

use App\Services\DatabaseService;
use CodeIgniter\Model;

class TodoModel extends Model
{

    protected $table = 'task'; // Your database table name
    protected $primaryKey = 'Id'; // Primary key of the table
    protected $allowedFields =  ['title', 'description', 'date', 'status'];
    protected $databaseService;
    public function __construct()
    {
        parent::__construct();
        $this->databaseService = new DatabaseService();
    }
    public function getAllTodos()
    {
        return $this->databaseService->getAllToDos();
    }
    public function addTodos($data)
    {
        // return $this->insert($data);
        return $this->databaseService->insert($data);
    }
    public function deleteToDos($id)
    {
        $result=$this->databaseService->delete($id);
        return $result;
    }
    public function getToDoData($id)
    {
        return $this->find($id);
    }
    public function updateTodos($id,array $data)
    {
        return $this->databaseService->update($id,$data);
    }
}
