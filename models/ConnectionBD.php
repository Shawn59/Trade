<?php

class BD {
public $link = '';

    public function __construct() {
        $host = 'localhost';
        $admin = 'root';
        $admin_pasword = '';
        $this->link=mysqli_connect($host,$admin,$admin_pasword);
        if(!$this->link)
        {
            die("can not connection localhost");
        }
        $con_bd=mysqli_select_db($this->link,"Traders");
        if(!$con_bd)
        {
            die("can not connection к БД");
        }
        mysqli_query($this->link,"SET NAMES utf8"); //преобразование кодировки
    }
}
$f = new BD();
