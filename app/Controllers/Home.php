<?php

namespace App\Controllers;

use App\Models\TodoModel as ModelsTodoModel;
use Config\Session;

class Home extends BaseController
{
    protected $todoModel;
    public function __construct()
    {
        $this->todoModel = new ModelsTodoModel();
    }
    public function index(): string
    {
        $nameList = [];
        $descriptionList = [];
        $todoIDS = [];
        $response = $this->todoModel->getAllTodos(); // Get the todos directly
        if (isset($response["data"]["mysql"])) {
            $allTodos = $response['data'];
            $mysqlArrList = $response['data']['mysql'];

            foreach ($mysqlArrList as $todos) {
                $nameList[] = $todos['title'];
                $descriptionList[] = $todos['description'];
                $todoIDS[] = $todos['Id'];
            }
        }
        return view('home', [
            'allTodos' => $allTodos,
            'nameList' => $nameList,
            'descriptionList' => $descriptionList,
            'todoIDS' => $todoIDS
        ]);
    }

    public function download()
    {
        $data = $this->todoModel->findAll();
        $filename = 'data_export_' . date('Ymd') . '.csv';
        header("Content-Description: File Transfer");
        header("Content-Disposition: attachment; filename=$filename");
        header("Content-Type: application/csv; ");
        $file = fopen('php://output', 'w');
        $header = array("Id", "title", "description", "date", "status");
        fputcsv($file, $header);
        foreach ($data as $row) {
            fputcsv($file, $row);
        }
        fclose($file);
        exit;
    }


    public function upload() {
        // Get the uploaded file
        $file = $this->request->getFile("file");
        log_message("info", "Uploaded file: " . $file->getName());
    
        // Check if the file is valid
        if (!$file->isValid()) {
            return redirect()->back()->withInput()->with("error", "Invalid File Upload");
        }
    
        // Define valid MIME types for CSV files
        $validMimeTypes = ['text/csv', 'application/csv', 'text/plain', 'application/octet-stream'];
        if (!in_array($file->getMimeType(), $validMimeTypes)) {
            return redirect()->back()->withInput()->with("error", "Only CSV Files are allowed.");
        }
    
        // Check the file extension
        if ($file->getExtension() != 'csv') {
            return redirect()->back()->withInput()->with("error", "Only CSV Files are allowed.");
        }
    
        // Move the uploaded file to the uploads directory
        if (!$file->move(WRITEPATH . 'uploads')) {
            log_message("error", "Failed to move uploaded file: " . $file->getErrorString());
            return redirect()->back()->withInput()->with("error", "File upload failed: " . $file->getErrorString());
        }
    
        // Get the actual name of the moved file
        $newFileName = $file->getName();
        $filePath = WRITEPATH . 'uploads/' . $newFileName; // Construct the file path
        log_message("info", "File moved to: " . $filePath);
    
        // Check if the file exists after moving
        if (!file_exists($filePath)) {
            log_message("error", "File does not exist: " . $filePath);
            return redirect()->back()->withInput()->with("error", "File does not exist.");
        }
    
        // Attempt to open the file for reading
        if (($handle = fopen($filePath, 'r')) === false) {
            $error = error_get_last();
            log_message("error", "Failed to open file: " . $error['message'] . " | File Path: " . $filePath);
            return redirect()->back()->withInput()->with("error", "Failed to open file for reading: " . $error['message']);
        }
    
        // Skip the header row
        fgetcsv($handle); // Read and ignore the first row (header)
    
        // Process the CSV file
        while (($data = fgetcsv($handle, 1000, ',')) !== false) {
            // Ensure that the data has the expected number of columns
            if (count($data) >= 4) {
                $this->todoModel->insert([
                    'Id' => $data[0],
                    'title' => $data[1],
                    'description' => $data[2],
                    'date' => $data[3],
                    'status' => $data[4]
                ]);
            } else {
                log_message("warning", "CSV row does not have enough columns: " . implode(',', $data));
            }
        }
        fclose($handle);
    
        // Redirect back with success message
        return redirect()->back()->withInput()->with('success', 'File Uploaded and Imported Successfully');
    }
    public function register()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }

        return view('register');
    }
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/');
        }
        return view('login');
    }
    public function add()
    {
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $date = $this->request->getPost('date');

        if ($title &&  $description && $date) {
            $this->todoModel->addTodos([
                'title' => $title,
                'description' => $description,
                'date' => $date,
            ]);
            session()->setFlashdata('success', 'Todo Added successfully!');


            // Redirect to the index page after adding
            return redirect()->to(base_url('/'))->with('message', 'ToDO Added Succeessfully!');
        } else {
            session()->setFlashdata('errors', 'Failed To Add Todo');
        }
        // return view('home');
    }

    public function delete($id)
    {
        if ($this->todoModel->deleteTodos($id)) {
            session()->setFlashdata('success', 'Deleted successfully!');
            return redirect()->to(base_url('/'))->with('message', 'ToDO Deleted Succeessfully!');
        } else {
            session()->setFlashdata('errors', 'Failed To Delete the Todo');
        }
    }
    public function getToDoData($id)
    {
        $todo = $this->todoModel->getToDoData($id);
        return view('home', ['todo' => $todo]);
    }
    public function edit($id)
    {
        log_message('debug', 'Before the starting code .');
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $date = $this->request->getPost('date');
        $status = $this->request->getPost('status');

        if ($status === '0' || $status === '1') {

            // Add the todo item to the database
            $updateResult = $this->todoModel->updateTodos($id, [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'date' => $date,
                'status' => $status
            ]);

            if ($updateResult['status'] === 'success') {
                session()->setFlashdata('success', 'Todo Updated successfully!');
                return redirect()->to('/')->with('message', 'ToDO Updated Successfully!');
            } else {
                session()->setFlashdata('errors', $updateResult['message']);
                return redirect()->to('/')->withInput()->with('errors', $updateResult['message']);
            }
        } else {
            return redirect()->to('/')->withInput()->with('errors', 'Status code must be 0 and 1');
        }
        return redirect()->to(base_url('/'))->with('message', 'failed to add the todo');
        // $this->todoModel->updateTodos($id);
    }
    public function filter()
    {

        // Log the received data for debugging
        // log_message('info', 'Received filter parameters: ' . json_encode($data));
        //     log_message('info','Running filter method');
        //     $title=$this->request->getPost("title");
        //     $description=$this->request->getPost("description");
        //     $status=$this->request->getPost("status");

        //     $idVal=$this->request->getPost("idVal");
        //     log_message('info', "Received filter parameters: Title: $title, Description: $description,ID: $idVal");
        //     $todoFilterResult=$this->todoModel->todoFilter([
        //         'title' => $title,
        //         'description' => $description,
        //         'status' => $status,
        //         'id'=> $idVal
        //     ]);
        //     return $this->response->setJSON($todoFilterResult);
        $data = $this->request->getJSON(true); // true returns an associative array

        // Log the received data for debugging
        log_message('info', 'Received filter parameters: ' . json_encode($data));

        // Check if data is empty
        if (empty($data)) {
            log_message('error', 'No filter parameters provided. Returning empty result.');
            return $this->response->setJSON([]);
        }

        // Extract parameters from the data array
        $title = $data['title'] ?? null;
        $description = $data['description'] ?? null;
        $status = isset($data['status']) ? (int)$data['status'] : null;
        $idVal = $data['id'] ?? null;

        log_message('info', "Running filter method with parameters: Title: $title, Description: $description, ID: $idVal");

        // Call the model's filtering method
        $todoFilterResult = $this->todoModel->todoFilter([
            'title' => $title,
            'description' => $description,
            'status' => $status,
            'id' => $idVal
        ]);

        // Return the result as JSON
        return $this->response->setJSON($todoFilterResult);
    }
}
