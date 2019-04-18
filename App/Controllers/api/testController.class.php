<?php
    namespace App\Controllers\api;
    
    class testController extends \Core\Controller{
        public function test($username, $password){
            $result = new \stdClass();
            $result->header = new \stdClass();
            $errors = [];
            
            if(!is_string($username)){
                $errors['username'] = 'Tên người dùng không được để trống';
            }else{
                if(mb_strlen($username) === 0 || mb_strlen($username) > 32){
                    $errors['username'] = 'Tên người dùng không được vượt quá 32 ký tự';
                }
            }
            
            if(!is_string($password)){
                $errors['password'] = 'Mật khẩu không hợp lệ';
            }else{
                if(mb_strlen($password) < 6){
                    $errors['password'] = 'Mật khẩu không được ít hơn 6 ký tự';
                }
            }
            
            if(count($errors)){
                $result->header->code = 1;
                $result->header->errors = $errors;
            }else{
                $result->header->code = 0;
                $result->header->message = 'OK from Server: Valid input data';
            }
            
            return $this->View->RenderJson($result);
        }
        
        public function searchProvince($name){
            $result = new \stdClass();
            $result->header = new \stdClass();
            
            $database = new \Library\Database\Database;
            $rows = $database->selectall()->from(DB_TABLE_PROVINCE)->where('name like '. new \Library\Database\DBString('%' . $database->escape($name) . '%'))->execute();
            
            $result->header->code = 0;
            $result->body = new \stdClass();
                $result->body->data = [];
            foreach($rows as $row){
                $p = new \stdClass();
                $p->name = $row->name;
                $p->id = $row->id;
                $result->body->data[] = $p;
            }
            
            return $this->View->RenderJson($result);
        }
    }