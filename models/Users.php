<?php

/**
 * Created by PhpStorm.
 * User: Shawn59
 * Date: 18.06.2017
 * Time: 0:16
 */
class Users
{
    public
        $id_user,
        $login,
        $password,
        $id_roles,
        $balans;

    public function __construct (){

    }
    public function getUser($id){
        $bd = new BD();
        $sql = "SELECT * FROM users where id_user = $id";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив объектов. Каждый объект - это строка таблицы
        foreach ($result as $value) {
            return $value;
        }
        return false;
    }
    public function Avtoriz($login,$password){
        $bd = new BD();
        $sql = "SELECT * FROM users WHERE login = '$login' and password = '$password'";
        $result = mysqli_query($bd->link,$sql);
        if ($result) {
            foreach ($result as $value) {
                $this->id_user = $value['id_user'];
                $this->login = $value['login'];
                $this->password = $value['password'];
                $this->id_roles = $value['id_roles'];
                $this->balans = $value['balans'];
            }
            return $this;
        }
        return false;
    }
    public function Registration($login, $password, $id_roles, $map_number, $map_code, $fio, $balans){
        $bd = new BD();
        $sql = "INSERT INTO users(login, password, id_roles, map_number, map_code, fio, balans) VALUES
                                 ('$login', '$password', $id_roles, '$map_number', '$map_code', '$fio', $balans)";
        $result = mysqli_query($bd->link,$sql);
        if ($result) {
            echo "<script>alert('Регистрация прошла успешно')</script>";
            return true;
        } else {
            echo "<script>alert('Невалидный запрос')</script>";
        }
        return false;
    }
    public function RegistrationManager($login, $password,$id_roles, $name_company, $about_company){
        $bd = new BD();
        $sql = "INSERT INTO users(login, password, id_roles, name_company, about_by_company) VALUES
                                 ('$login', '$password', $id_roles, '$name_company', '$about_company')";
        $result = mysqli_query($bd->link,$sql);
        if ($result) {
            echo "<script>alert('Регистрация поставщика прошла успешно')</script>";
            return true;
        } else {
            echo "<script>alert('Невалидный запрос')</script>";
        }
        return false;
    }

    public function getRolesUsers(){
        $bd = new BD();
        $sql = "SELECT DISTINCT roles.id_roles, roles.name FROM users, roles WHERE roles.id_roles = users.id_roles";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив ролей
        foreach ($result as $value) {
            $roles[$i] = $value;
            $i++;
        }
        return isset($roles) ? $roles : false;
    }
    public function getUsersManager() {
        $bd = new BD();
        $sql = "SELECT  id_user, name_company FROM users WHERE id_roles = 2";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив ролей
        foreach ($result as $value) {
            $manager[$i] = $value;
            $i++;
        }
        return isset($manager) ? $manager : false;
    }
}