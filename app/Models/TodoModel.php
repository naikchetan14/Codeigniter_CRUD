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
        $result = $this->databaseService->delete($id);
        return $result;
    }
    public function getToDoData($id)
    {
        return $this->find($id);
    }
    public function updateTodos($id, array $data)
    {
        return $this->databaseService->update($id, $data);
    }
    

    public function todoFilter($filters)
    {
        log_message('info', 'Filter Values: ' . json_encode($filters));
        $builder = $this->db->table('task');

        // Check if no filters are applied, return all records
        if (empty(array_filter($filters))) {
            $query = $builder->get();
            return $query->getResultArray();
        }

        // Apply filters based on the provided parameters
        if (!empty($filters['title'])) {
            $builder->like('title', $filters['title']);
            // $escapedTitle = $this->db->escapeString($filters['title']);
            // $builder->where("description LIKE '%$escapedTitle%'");
        }
        if (!empty($filters['description'])) {
            $description = trim($filters['description']);
            if ($description !== '') { // Ensure the description is not just whitespace
                // Manually escape the description to prevent SQL injection
                $escapedDescription = $this->db->escapeString($description);
                // Use where to construct the LIKE query without ESCAPE
                $builder->where("description LIKE '%$escapedDescription%'");
            }
        }



        if (isset($filters['status']) && $filters['status'] !== '') {
            $status = (int)$filters['status'];
            log_message('info', 'Value of status' . $status);
            if ($status === 0) {
                $builder->where('status', 0);
            }
            if ($status === 1) {
                $builder->where('status', 1);
            }
        }

        // ID filtering
        if (!empty($filters['id'])) {
            $builder->where('id', (int)$filters['id']);
        }

        // Example of adding an OR condition if needed
        if (!empty($filters['or_conditions'])) {
            foreach ($filters['or_conditions'] as $column => $value) {
                $builder->orWhere($column, $value);
            }
        }

        $query = $builder->get();
        log_message('info', 'Last Query: ' . $this->db->getLastQuery());

        // Execute the query and return the results
        return $query->getResultArray();
    }
}
