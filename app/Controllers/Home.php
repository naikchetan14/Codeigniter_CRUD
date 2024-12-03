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


    public function upload()
    {
        // Get the uploaded file
        $file = $this->request->getFile("file");

        // Check if the file is valid
        if (!$file->isValid()) {
            return redirect()->back()->withInput()->with('error', $file->getErrorString());
        }

        // Upload the file
        $res = $this->todoModel->uploadFile($file);
        log_message("info", "CSV Todo Upload Attempt: " . $file->getName() . " - Result: " . json_encode($res));

        // Check for response status
        if (isset($res['status']) && $res['status'] === 'success') {
            return redirect()->back()->withInput()->with('success', $res['message']);
        } else {
            return redirect()->back()->withInput()->with('error', $res['message'] ?? 'File upload failed.');
        }
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
            session()->setFlashdata('success', 'ToDo Deleted Successfully!');
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
        $data = $this->request->getJSON(true); // true returns an associative array

        // Log the received data for debugging
        log_message('info', 'Received filter parameters: ' . json_encode($data));

        // Check if data is empty
        if (empty($data)) {
            log_message('error', 'No filter parameters provided. Returning empty result.');
            return $this->response->setJSON([]);
        }
        log_message('info', 'Rmy new log messsage running here: ' . json_encode($data));

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
