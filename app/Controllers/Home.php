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
        $response = $this->todoModel->getAllTodos(); // Get the todos directly
        $allTodos = $response['data'];
        return view('home', ['allTodos' => $allTodos]);
    }
    
    public function register(): string{
       return view('register');
    }
    public function login(): string{
        if(session()->get('isLoggedIn')){
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
    public function edit(string $id)
    {
        $title = $this->request->getPost('title');
        $description = $this->request->getPost('description');
        $date = $this->request->getPost('date');
        $status = $this->request->getPost('status');

        // Validate the input (optional, but recommended)
        // Log the incoming data
        if ($title && $description && $date) {
            // Add the todo item to the database
            $this->todoModel->updateTodos($id, [
                'id' => $id,
                'title' => $title,
                'description' => $description,
                'date' => $date,
                'status' => $status
            ], $id);
            session()->setFlashdata('success', 'Todo updated successfully!');

            // Redirect to the index page after adding
            return redirect()->to('/')->with('message', 'ToDO Updated Successfully!'); // Adjust the redirect URL if needed
        } else {
            // Handle the case where validation fails (optional)
            return redirect()->back()->withInput()->with('errors', 'All fields are required.');
        }
        // $this->todoModel->updateTodos($id);
        return redirect()->to('/');
    }
}
