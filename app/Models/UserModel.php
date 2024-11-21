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
        return $this->insert($data,true);
    }
    public function getLoginUser($id)
    {
        return $this->where('id', $id)->find();
    }
}
