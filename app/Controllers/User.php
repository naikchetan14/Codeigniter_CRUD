<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\UserModel as ModelsUserModel;

class User extends BaseController{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new ModelsUserModel();
    }
    public function addNewUser(){
       
        $validation = \Config\Services::validation();
        // log_message('info', 'Add New User method called with data: ' . $this->request->getPost());
        // Set validation rules
        $validation->setRules([
            'name' => 'required|min_length[5]|max_length[20]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'cpassword' => 'required|min_length[6]',
        ]);
        $data = $this->request->getPost();


         // Validate the data
         if (!$this->validate($validation->getRules(), $data)) {
            redirect()->to(base_url('/register'))->with('message', 'failed to add!');
            // Validation failed, return to the form with error messages
            return $this->response->setJSON([
                'status' => 'error',
                'errors' => $this->validator->getErrors(),
            ]);
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword=$this->request->getPost('cpassword');
        if ($name &&  $email && $password && $confirmPassword === $password) {
            $this->userModel->addUser([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
            // session()->setFlashdata('success', 'User Added Successfully!');


            // Redirect to the index page after adding
            return redirect()->to(base_url('/'))->with('message', 'User Added Successfully!');
        } else {
            session()->setFlashdata('errors', 'Failed To Add User');
            return redirect()->to(base_url('/'))->with('message', 'Failed to add');

        }
    }

    public function LogOut(){
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}