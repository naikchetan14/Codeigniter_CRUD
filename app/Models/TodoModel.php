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

    public function todoFilter($filters) {
           // Retrieve the JSON input
           log_message('info', 'Filter Values: ' . json_encode($filters));
           $builder = $this->db->table('task');

           // Apply filters based on the provided parameters
           if (!empty($filters['title'])) {
               $builder->like('title', $filters['title']);
           }
           if (!empty($filters['description'])) {
            $description = trim($filters['description']);
            $builder->like('description', $description);
        }
           if (!empty($filters['status'])) {
               $builder->where('status', $filters['status']);
           }
           if (!empty($filters['id'])) {
               $builder->where('id', $filters['id']);
           }
           $query = $builder->get();
           log_message('info', 'Last Query: ' . $this->db->getLastQuery());
       
           // Execute the query and return the results
           return $query->getResultArray();
    }
}
