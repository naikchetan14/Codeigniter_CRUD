<?php

namespace App\Services;

use CodeIgniter\Database\Exceptions\DatabaseException;
use GuzzleHttp\Client as HttpClient;
use Exception;

class DatabaseService
{
    private $db;
    private $httpClient;

    protected $table = 'task'; // MySQL database table name
    protected $primaryKey = 'Id'; // Primary key for MySQL

    public function __construct()
    {
        // Initialize MySQL
        $this->db = \Config\Database::connect();

        // Initialize HTTP client for MongoDB API
        $this->httpClient = new HttpClient(['base_uri' => 'http://localhost:3000/api/']); // Replace with your API base URL
    }

    public function insert(array $data)
    {
        $this->db->transStart(); // Start MySQL transaction
        $mongoInsertedId = null;

        try {
            log_message('debug', 'Data to be inserted into MySQL: ' . json_encode($data));

            // Insert into MySQL
            $this->db->table('task')->insert($data);
            $mysqlInsertedId = $this->db->insertID();
            log_message('debug', 'Inserted into MySQL with ID: ' . $mysqlInsertedId);

            // Insert into MongoDB via API
            log_message('debug', 'About to send data to MongoDB: ' . json_encode($data));

            // Attempt to send the request and capture the response
            try {
                $mongoResponse = $this->httpClient->post('addtodo', ['json' => $data]);
            } catch (Exception $httpException) {
                throw new Exception('MongoDB API insertion failed due to HTTP error.');
            }

            if ($mongoResponse->getStatusCode() !== 201) {
                throw new Exception('MongoDB API insertion failed with status: ' . $mongoResponse->getStatusCode());
            }
            if ($mongoResponse->getStatusCode() === 201) {
                $mongoResponseBody = json_decode($mongoResponse->getBody(), true);
                $mongoId = $mongoResponseBody['newtodo']['id'];

                // Update MySQL record with the MongoDB ID
                $this->db->table($this->table)->update(['mongo_id' => $mongoId], [$this->primaryKey => $mysqlInsertedId]);
            } else {
                throw new Exception('MongoDB API insertion failed.');
            }



            // Commit MySQL transaction
            $this->db->transComplete();
            log_message('debug', 'Data inserted successfully into MySQL and MongoDB');
            return ['status' => 'success', 'message' => 'Data inserted successfully'];
        } catch (Exception $e) {
            // Rollback MySQL
            $this->db->transRollback();
            log_message('error', 'Error during insert: ' . $e->getMessage());

            // Rollback MongoDB if inserted
            if ($mongoInsertedId) {
                $this->httpClient->delete("deletetodo/{$mongoInsertedId}");
            }

            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete($id)
    {
        $this->db->transStart(); // Start MySQL transaction

        try {
            // Get the MongoDB ID from MySQL
            $record = $this->db->table($this->table)->where($this->primaryKey, $id)->get()->getRow();
            if (!$record) {
                throw new Exception('Record not found in MySQL.');
            }

            $mongoId = $record->mongo_id; // Assuming you have a mongo_id column

            // Delete from MySQL
            $this->db->table($this->table)->delete([$this->primaryKey => $id]);

            // Delete from MongoDB via API
            $mongoResponse = $this->httpClient->delete("deletetodo/{$mongoId}");

            if ($mongoResponse->getStatusCode() !== 200) {
                throw new Exception('MongoDB API deletion failed.');
            }

            // Commit MySQL transaction
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'Data deleted successfully'];
        } catch (Exception $e) {
            // Rollback MySQL
            $this->db->transRollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }


    public function update($id, array $data)
    {
        $this->db->transStart(); // Start MySQL transaction

        try {
            // Get the MongoDB ID from MySQL
            $record = $this->db->table($this->table)->where($this->primaryKey, $id)->get()->getRow();
            if (!$record) {
                throw new Exception('Record not found in MySQL.');
            }

            $mongoId = $record->mongo_id; // Assuming you have a mongo_id column

            // Update MySQL
            $this->db->table($this->table)->update($data, [$this->primaryKey => $id]);
            log_message('debug', 'Updated to MySQL: ' . json_encode($data));

            // Update MongoDB via API
            try {
                $mongoResponse = $this->httpClient->put("updatetodo/{$mongoId}", ['json' => $data]);
            } catch (Exception $httpException) {
                throw new Exception('MongoDB API insertion failed due to HTTP error.');
            }
            if ($mongoResponse->getStatusCode() !== 200) {
                throw new Exception('MongoDB API update failed.');
            }
            log_message('debug', 'Updated To MongoD: ' . json_encode($data));


            // Commit MySQL transaction
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'Data updated successfully'];
        } catch (Exception $e) {
            // Rollback MySQL
            $this->db->transRollback();
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function getAllToDos()
    {
        try {
            // Fetch todos from MySQL
            $mysqlToDos = $this->db->table($this->table)->get()->getResultArray();
            log_message('debug', 'MySQL Response: ' . json_encode($mysqlToDos)); // Use json_encode for logging

            // Fetch todos from MongoDB via API
            $mongoResponse = $this->httpClient->get('alltodos');
    
            // Check if the response status is OK
            if ($mongoResponse->getStatusCode() !== 200) {
                throw new Exception('Failed to fetch records from MongoDB. Status Code: ' . $mongoResponse->getStatusCode());
            }
    
            // Decode the MongoDB response
            $mongoTodos = json_decode($mongoResponse->getBody(), true);
    
            // Log the MongoDB response for debugging
            log_message('debug', 'MongoDB Response: ' . json_encode($mongoTodos));
    
            // Check if mongoTodos is an array and contains the expected key
            if (!is_array($mongoTodos) || !isset($mongoTodos['todos'])) {
                throw new Exception('MongoDB response is not valid or does not contain todos.');
            }
    
            // Prepare the combined todos array
            $allTodos = [
                'mysql' => $mysqlToDos,
                'mongodb' => $mongoTodos['todos'], // Access the 'todos' array from the response
            ];
    
            return ['status' => 'success', 'data' => $allTodos];
        } catch (Exception $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }
}
