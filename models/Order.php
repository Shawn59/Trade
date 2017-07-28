<?php
include "../views/Order.html";
/**
 * Created by PhpStorm.
 * User: Shawn59
 * Date: 23.06.2017
 * Time: 10:46
 */
class Order
{
    public
        $id,
        $id_status,
        $id_tovar;

    public function __construct()
    {

    }

    //Массив таблицы с пол. именами вторичных ключей
    public function getTextAllOrders() {
        $bd = new BD();
        $sql = "SELECT DISTINCT  status.name as status, orders.show_order, model.name as model, orders.id, tovars.id_tovar, tovars.id_user as id_manager, orders.id_trader,users.fio, tovars.name as tovar, tovars.price as price, tovars.about_by_tovar as about,
                tovars.icon as icon FROM orders, status, tovars, users, model
WHERE   orders.id_trader = users.id_user and orders.id_tovar = tovars.id_tovar and tovars.id_model = model.id and orders.id_status = status.id";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив объектов. Каждый объект - это строка таблицы товары
        if($result) {
            foreach ($result as $value) {
                $arr_tovars[$i] = $value;
                $i++;
            }
            if($arr_tovars) {
                $i = 0;
                foreach ($arr_tovars as $value) {
                    $id_tovar = $value['id_tovar'];
                    $sql1 = "SELECT DISTINCT tovars.id_tovar ,users.name_company FROM tovars, users WHERE tovars.id_tovar = $id_tovar and tovars.id_user = users.id_user";
                    $result1 = mysqli_query($bd->link,$sql1);
                    foreach ($result1 as $value1) {
                        $arr_tovars[$i] = $arr_tovars[$i] + $value1;
                        $i++;
                    }
                }
                return $arr_tovars;
            }
        } else {
            echo "<script>alert('Нет ни одной записи в базе')</script>";
        }

        return false;
    }
    public function eddStatus($id) {
        $bd = new BD();
        if(is_numeric($id)) {
            $sql = "UPDATE orders SET id_status = 2 WHERE id = $id";
            $result = mysqli_query($bd->link,$sql);
            if ($result) {
                echo "<script>alert('Товар успешно подтверждён')</script>";
                return true;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        }
        else {
            echo "<script>alert('Одно из полей должно быть числом')</script>";
        }
        return false;
    }
}
?>
    // КОНТЕНТ для таблицы
<form name="MyForm" method="POST" action="Order.php">
<div class='Content'>
<table class='table table-striped'>
<tr class='success'>
<td>
Номер заказа
</td>
<td>
Покупатель
</td>
<td>
Поставщик
</td>
<td>
Статус
</td>
<td>
Товар
</td>
<td>
Цена
</td>
    <td>
        Описание
    </td>
    <td>
       Изображение
    </td>
<td></td>
<td></td>
</tr>
<?php
$order = new Order();
$orders = $order->getTextAllOrders();
if (isset($_SESSION['user'])) {
    foreach ($_SESSION['user'] as $key => $value) {
        if ($key == 'id_user') {
            $user_avt_id = $value;
        }
    }
}
if ($orders) {
    foreach ($orders as $key => $value) {
        $id = $value['id'];
        $id_manager = $value['id_manager'];
        $id_trader = $value['id_trader'];
        $fio = $value['fio'];
        $manager = $value['name_company'];
        $status = $value['status'];
        $tovar = $value['tovar'];
        $price = $value['price'];
        $about = $value['about'];
        $show_order = $value['show_order'];
        $icon = "<img src='$value[icon]'>";
        if(isset($_SESSION['roles']) &&  $show_order == 1 && ((($_SESSION['roles'] == 2 && $user_avt_id == $id_manager or $_SESSION['roles'] == 3 && $user_avt_id == $id_trader)) or $_SESSION['roles'] == 1 )) {
        echo
        "
              <tr>
               <td>
                 $id
               </td>
               <td>
                 $fio
               </td>
                <td>
                 $manager
               </td>
                <td>
                 $status
               </td>
               <td>
                 $tovar
               </td>
               <td>
                 $price
               </td>
               <td>
                 $about
               </td>
               <td>
                $icon
               </td>";
        //Условие вывода кнопок для товаров, в зависимости от роли

         if(isset($_SESSION['roles']) &&($_SESSION['roles'] == 1 or ($_SESSION['roles'] == 2  && $user_avt_id == $id_manager)) && $status == 'Обработка заказа') {
            echo" <td><button class='btn btn-link btn-lg btn-block' type='submit' name='EddOrder'  value= '$id'> Подтвердить </button></td>
                          
                </tr>";
           //<td><button class='btn btn-link btn-lg btn-block' type='submit' name='DelOrder' value= '$id'> Удалить </button></td>
        }
        else if(!isset($_SESSION['roles'])) {
            echo" <td></td> 
                          <td></td>
                </tr>";
        }
        else {
            echo" <td></td> 
                          <td></td>
                </tr>";
        }
    }
}
}

?>
</table>
</div>
</form>

<?php

function reloadPage() {
    echo "<script>document.location.replace('../models/Order.php');</script>"; //редирект
}
if(isset($_POST['EddOrder'])){
    $order = new Order();
    $order->eddStatus($_POST['EddOrder']);
reloadPage();
}
?>