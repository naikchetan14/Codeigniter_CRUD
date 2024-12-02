<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel as ModelsUserModel;

class User extends BaseController
{
    protected $userModel;
    public function __construct()
    {
        $this->userModel = new ModelsUserModel();
    }

    public function getLoginUser()
    {
        $validation = \Config\Services::validation();
        $validation->setRules([
            'email' => 'required|valid_email',
            'password' => 'required',
        ]);
        $data = $this->request->getPost();


        if (!$this->validate($validation->getRules(), $data)) {
            return redirect()->to(base_url('/login'))->with('message', 'Validation failed!')->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $user = $this->userModel->where('email', $email)->first();
        // log_message('info','My first info table'.  $user);
        // Check if user exists and verify password
        if ($user && password_verify($password, $user['password'])) {
            // Set session data
            session()->set([
                'isLoggedIn' => true,
                'userID' => $user['userID'],
                'userName' => $user['name']
            ]);
            return redirect()->to(base_url('/'))->with('message', 'Login Successful!');
        } else {
            log_message('debug', "Invalid login credentials for email: $email");
            session()->setFlashdata('errors', 'Invalid Email Or Password!');
            return redirect()->to(base_url('/login'))->with('message', 'Invalid email or password');
        }
    }
    public function addNewUser()
    {
        $validation = \Config\Services::validation();
        // Set validation rules
        $validation->setRules([
            'name' => 'required|min_length[5]|max_length[20]',
            'email' => 'required|valid_email',
            'password' => 'required|min_length[6]',
            'cpassword' => 'required|matches[password]',
        ]);
        $data = $this->request->getPost();


        // Validate the data
        if (!$this->validate($validation->getRules(), $data)) {
            return redirect()->to(base_url('/register'))->withInput()->with('errors', $this->validator->getErrors());
        }

        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $confirmPassword = $this->request->getPost('cpassword');

        if ($name &&  $email && $password && $confirmPassword === $password) {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $user = $this->userModel->addUser([
                'name' => $name,
                'email' => $email,
                'password' => $hashedPassword,
            ]);
            // Log the user information
            session()->set([
                'isLoggedIn' => true,
                'userID' => $user['userID'],
                'userName' => $user['name']
            ]);
            // session()->setFlashdata('success', 'User Added Successfully!');
            // Redirect to the index page after adding
            return redirect()->to(base_url('/'))->with('message', 'User Added Successfully!');
        } else {
            session()->setFlashdata('errors', 'Failed To Add User');
            return redirect()->to(base_url('/'))->with('message', 'Failed to add');
        }
    }

    public function LogOut()
    {
        session()->remove('isLoggedIn');
        session()->destroy();
        return redirect()->to(base_url('/login'));
    }
}
