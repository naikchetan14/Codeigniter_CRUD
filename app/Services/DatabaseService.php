<?php

namespace App\Services;

use CodeIgniter\Database\Exceptions\DatabaseException;
use DateTime;
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
        log_message('debug', 'Before logging MySQL update message.');

        try {
            // Get the MongoDB ID from MySQL
            log_message('debug', 'Inserted into MySQL with ID: ' . 'Runnging nxnbcb');
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
 

    //sample code for upload
 // public function upload($uploadedFile)
    // {
    //     $this->db->transStart();
    //     $insertedIDs = [];
    //     try {
    //         // Check if the file is valid
    //         if (!$uploadedFile->isValid()) {
    //             throw new Exception('Invalid File Upload');
    //         }

    //         // Define valid MIME types for CSV files
    //         $validMimeTypes = ['text/csv', 'application/csv', 'text/plain', 'application/octet-stream'];
    //         if (!in_array($uploadedFile->getMimeType(), $validMimeTypes)) {
    //             throw new Exception('only CSV Files are allowed');
    //         }

    //         // Check the file extension
    //         if ($uploadedFile->getExtension() != 'csv') {
    //             throw new Exception('Only CSV Files are allowed');
    //         }

    //         // Move the uploaded file to the uploads directory
    //         if (!$uploadedFile->move(WRITEPATH . 'uploads')) {
    //             log_message("error", "Failed to move uploaded file: " . $uploadedFile->getErrorString());
    //             throw new Exception("File Upload failed");
    //         }

    //         // Get the actual name of the moved file
    //         $newFileName = $uploadedFile->getName();
    //         $filePath = WRITEPATH . 'uploads/' . $newFileName; // Construct the file path
    //         log_message("info", "File moved to: " . $filePath);

    //         // Check if the file exists after moving
    //         if (!file_exists($filePath)) {
    //             log_message("error", "File does not exist: " . $filePath);
    //             throw new Exception("File does not exist");
    //         }



    //         // Attempt to open the file for reading
    //         if (($handle = fopen($filePath, 'r')) === false) {
    //             $error = error_get_last();
    //             log_message("error", "Failed to open file: " . $error['message'] . " | File Path: " . $filePath);
    //             throw new Exception('failed to open the file');
    //         }

    //         // Skip the header row
    //         fgetcsv($handle); // Read and ignore the first row (header)

    //         while (($data = fgetcsv($handle, 1000, ',')) !== false) {
    //             // Ensure that the data has the expected number of columns
    //             log_message('info','inside while Loop');
    //             $csvData = [
    //                 'Id' => $data[0],
    //                 'title' => $data[1],
    //                 'description' => $data[2],
    //                 'date' => $data[3],
    //                 'status' => $data[4]
    //             ];

    //             log_message('info','message'.$csvData);


    //             if (empty($csvData['Id']) || empty($csvData['title']) || empty($csvData['description']) || empty($csvData['date']) || empty($csvData['status'])) {
    //                 throw new Exception('Invalid Data format in the CSV Row.');
    //             }

    //             log_message('info','Reach here');
    //                 // Insert into MySql
    //                 $this->db->table('task')->insert($csvData);
    //                 $mySqlInsertedIDs = $this->db->insertID;
    //                 $insertedIDs['mysql'][] = $mySqlInsertedIDs;

    //                 //Insert into mongodb using API
    //                 $mongoResponse = $this->httpClient->post('addtodo', ['json' => $csvData]);
    //                 if ($mongoResponse->getStatusCode() !== 200) {
    //                     throw new Exception('failed to insert data into mongodb via API.');
    //                 }
    //                 log_message('info','Reach here mongoCross');

    //                 $mongoInsertedId = json_decode($mongoResponse->getBody(), true);
    //                 $insertedIDs['mongodb'][] = $mongoInsertedId;
    //         }

    //         fclose($handle);
    //         $this->db->transComplete();
    //         return ['status' => 'success', 'message' => 'File Uploaded Successfully'];

    //     } catch (Exception $e) {
    //         $this->db->transRollback();

    //         if (!empty($insertedIDs['mongodb'])) {
    //             foreach ($insertedIDs['mongodb'] as $mongoID) {
    //                 $this->httpClient->delete("deletetodo/{$mongoID}");
    //             }
    //         }
    //         return ['status' => 'error', 'message' => $e->getMessage()];
    //     }
    // }


    public function upload($uploadedFile)
    {
        $this->db->transStart();
        $insertedIDs = [];
        try {
            // Check if the file is valid
            if (!$uploadedFile->isValid()) {
                throw new Exception('Invalid File Upload');
            }

            // Define valid MIME types for CSV files
            $validMimeTypes = ['text/csv', 'application/csv', 'text/plain', 'application/octet-stream'];
            if (!in_array($uploadedFile->getMimeType(), $validMimeTypes)) {
                throw new Exception('Only CSV Files are allowed');
            }

            // Move the uploaded file to the uploads directory
            if (!$uploadedFile->move(WRITEPATH . 'uploads')) {
                log_message("error", "Failed to move uploaded file: " . $uploadedFile->getErrorString());
                throw new Exception("File Upload failed");
            }

            // Get the actual name of the moved file
            $newFileName = $uploadedFile->getName();
            $filePath = WRITEPATH . 'uploads/' . $newFileName; // Construct the file path
            log_message("info", "File moved to: " . $filePath);

            // Attempt to open the file for reading
            if (($handle = fopen($filePath, 'r')) === false) {
                throw new Exception('Failed to open the file');
            }

            // Skip the header row
            fgetcsv($handle); // Read and ignore the first row (header)

            while (($data = fgetcsv($handle, 1000, ',')) !== false) {

                // Ensure that the data has the expected number of columns
                if (count($data) < 5) {
                    continue; // Skip this row and continue with the next
                }

                // Validate and format the date
                $date = DateTime::createFromFormat('d-m-y', $data[3]);
                if (!$date) {
                    continue; // Skip this row
                }
                $formattedDate = $date->format('Y-m-d');
                // Generate a new unique ID for the record
                $newId = uniqid('', true); // You can replace this with your preferred method of ID generation
                $csvData = [
                    'Id' => $data[0],
                    'title' => $data[1],
                    'description' => $data[2],
                    'date' => $formattedDate, // Use the formatted date
                    'status' => $data[4]
                ];

                // Validate the content of csvData
                if (empty($csvData['title']) || empty($csvData['description']) || empty($csvData['date'])) {
                    continue; // Skip this row and continue with the next
                }

                // Check for duplicate entry in MySQL
                $existingRecord = $this->db->table('task')->where('Id', $csvData['Id'])->get()->getRow();
                if ($existingRecord) {
                    continue; // Skip this row
                }

                // Insert into MySQL
                $this->db->table('task')->insert($csvData);
                if ($this->db->affectedRows() === 0) {
                    throw new Exception('Failed to insert into MySQL');
                }
                $mySqlInsertedIDs = $this->db->insertID();
                $insertedIDs['mysql'][] = $mySqlInsertedIDs;

                // Insert into MongoDB using API
                $mongoResponse = $this->httpClient->post('addtodo', ['json' => $csvData]);
                if ($mongoResponse->getStatusCode() !== 201) {

                    // Rollback MySQL transaction if MongoDB insertion fails
                    $this->db->transRollback();

                    // Clean up MySQL entries if they were created
                    foreach ($insertedIDs['mysql'] as $insertedId) {
                        $this->db->table('task')->delete(['Id' => $insertedId]);
                    }

                    throw new Exception('Failed to insert data into MongoDB via API.');
                }


                // Decode the MongoDB response
              // Decode the MongoDB response
            $mongoInsertedId = json_decode($mongoResponse->getBody(), true);
            $mongoId = $mongoInsertedId['newtodo']['id']; // Get the MongoDB ID from the response

            // Update the MySQL record with the MongoDB ID
            $this->db->table('task')->update(['mongo_id' => $mongoId], ['Id' => $data[0]]);

            // Store the MongoDB ID
            $insertedIDs['mongodb'][] = $mongoId;
            }

            fclose($handle);
            $this->db->transComplete();
            return ['status' => 'success', 'message' => 'File Uploaded Successfully'];
        } catch (Exception $e) {
            $this->db->transRollback();

            // Clean up MongoDB entries if they were created
            if (!empty($insertedIDs['mongodb'])) {
                foreach ($insertedIDs['mongodb'] as $mongoID) {
                    $this->httpClient->delete("deletetodo/{$mongoID}");
                }
            }
            log_message('error', 'Upload error: ' . $e->getMessage());
        }
    }
}
