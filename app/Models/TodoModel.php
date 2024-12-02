<?php

namespace App\Models;

use App\Services\DatabaseService;
use CodeIgniter\Model;
use PhpParser\Node\Stmt\TryCatch;

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
    
        try {
            // Check if no filters are applied, return all records
            // if (empty(array_filter($filters))) {
            //     log_message('info', 'No filters applied, returning all records.');
            //     $query = $builder->get();
            //     return $query->getResultArray();
            // }
            if (!isset($filters['title']) && !isset($filters['description']) && !isset($filters['status']) && !isset($filters['id'])) {
                log_message('info', 'No filters applied, returning all records.');
                $query = $builder->get();
                return $query->getResultArray();
            }
    
            log_message('info', 'Reach step 2: ' . json_encode($filters));
    
            // Apply filters based on the provided parameters
            if (!empty($filters['title'])) {
                log_message('info', 'Applying title filter: ' . $filters['title']);
                $builder->like('title', $filters['title']);
            }
    
            if (!empty($filters['description'])) {
                $description = trim($filters['description']);
                if ($description !== '') { // Ensure the description is not just whitespace
                    log_message('info', 'Applying description filter: ' . $description);
                    $escapedDescription = $this->db->escapeString($description);
                    $builder->where("description LIKE '%$escapedDescription%'");
                }
            }
    
            // Status filtering
            $validStatuses = [0, 1];
            log_message('info', 'Reach step 3: ' . json_encode($filters));
    
            if (isset($filters['status']) && in_array($filters['status'], $validStatuses, true)) {
                log_message('info', 'Applying status filter: ' . $filters['status']);
                $builder->where('status', $filters['status']);
            } else {
                log_message('error', 'Invalid status value: ' . json_encode($filters['status']));
            }
    
            log_message('info', 'Reach step 4: ' . json_encode($filters));
    
            // ID filtering
            if (!empty($filters['id'])) {
                log_message('info', 'Applying ID filter: ' . $filters['id']);
                $builder->where('id', (int)$filters['id']);
            }
    
            // Example of adding an OR condition if needed
            if (!empty($filters['or_conditions'])) {
                foreach ($filters['or_conditions'] as $column => $value) {
                    log_message('info', 'Applying OR condition: ' . $column . ' = ' . $value);
                    $builder->orWhere($column, $value);
                }
            }
    
            // Execute the query
            log_message('info', 'Executing the query...');
            $query = $builder->get();
            log_message('info', 'Last Query: ' . $this->db->getLastQuery());
    
            // Check if the query executed successfully
            if ($query) {
                log_message('info', 'Query executed successfully.');
            } else {
                log_message('error', 'Query execution failed.');
            }
    
            // Return the results
            return $query->getResultArray();
    
        } catch (\Exception $e) {
            log_message('error', 'Exception caught: ' . $e->getMessage());
            return []; // Return an empty array or handle the error as needed
        }
    }
}
