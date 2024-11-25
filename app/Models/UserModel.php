<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = "user";
    protected $primaryKey = "userID";
    protected $allowedFields =  ['name', 'email', 'password'];


    public function addUser($data)
    {
        $this->insert($data,true);
        return $this->find($this->insertID());;
    }
    public function getLoginUser($id)
    {
        return $this->where('id', $id)->find();
    }
}
