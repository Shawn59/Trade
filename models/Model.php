<?php

/**
 * Created by PhpStorm.
 * User: Shawn59
 * Date: 22.06.2017
 * Time: 18:29
 */
class Model
{
    public
        $id,
        $name;
    public function __construct() {

    }
    public function getModel() {
        $bd = new BD();
        $sql = "SELECT * FROM model";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив объектов. Каждый объект - это строка таблицы товары
        if ($result) {
            foreach ($result as $value) {
                $models[$i] = $value;
                $i++;
            }
            return isset($models) ? $models : false;
        }
       return false;
    }
    public function addModel($name) {
        $bd = new BD();
        $sql = "INSERT INTO model (name) VALUES ('$name')";
        $result = mysqli_query($bd->link,$sql);
        if ($result) {
            echo "<script>alert('Модель успешно добавлена')</script>";
        } else {
            echo "<script>alert('Невалидный запрос')</script>";
        }
        return false;
    }
}