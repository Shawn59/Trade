<?php
include "../views/Cart.html";
/**
 * Created by PhpStorm.
 * User: Shawn59
 * Date: 18.06.2017
 * Time: 20:19
 */
class Cart
{
    public
        $id,
        $id_user,
        $id_order,
        $address,
        $date,
        $fio;


    public function __construct()
    {

    }
    public function getTextAllTovars() {
        $bd = new BD();
        $sql = "SELECT DISTINCT orders.show_order, model.name as model, orders.id, tovars.id_tovar, tovars.id_user as id_manager, orders.id_trader,users.fio, tovars.name as tovar, tovars.price as price, tovars.about_by_tovar as about,
                tovars.icon as icon FROM orders, status, tovars, users, model
                WHERE   orders.id_trader = users.id_user and orders.id_tovar = tovars.id_tovar and tovars.id_model = model.id";
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
    public function delOrder($id){
        $bd = new BD();
        if(isset($id) && is_numeric($id)) {
            $sql = "DELETE FROM orders WHERE id = $id";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                return true;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        } else {
            echo "<script>alert('Одно из полей должно быть числом')</script>";
        }
        return false;
    }

    public function issetMap($id, $map_number, $map_code) {
        $bd = new BD();
        $sql = "SELECT * FROM users WHERE map_number = '$map_number' and map_code = '$map_code'";
        $result = mysqli_query($bd->link,$sql);
        if($result->num_rows> 0) {
            return true;
        } else {
            echo "<script>alert('Такой карты нет в базе у пользователя')</script>";
        }
        return false;
    }
    public function issetTovarCart($id_trader) {
        $bd = new BD();
        $sql = "SELECT * FROM orders WHERE id_trader = $id_trader and show_order = 0";
        $result = mysqli_query($bd->link,$sql);
        if($result->num_rows> 0) {
            return true;
        } else {
            echo "<script>alert('Корзина пустая')</script>";
        }
        return false;
    }

    public function updateBalans(){
        $bd = new BD();
        foreach ($_SESSION['user'] as $key => $value) {
            if ($key == 'id_user') {
                $id = $value;
            } else  if ($key == 'balans') {
                $balans = $value;
            }
        }
        $balans = $balans - $_POST['summa'];
        if($balans >= 0) {
            $sql = "UPDATE users SET balans = $balans WHERE id_user = $id";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                $_SESSION['user']->balans = $_SESSION['user']->balans + $balans;
                return true;
            }
        } else {
            echo "<script>alert('У вас недостаточно денежных средств')</script>";
        }


    }

    public function updateConfirmOrder($id_trader, $address, $map_number, $map_code){
        $bd = new BD();
        if(isset($id_trader) && is_numeric($id_trader) && $this->issetMap($id_trader, $map_number, $map_code) && $this->updateBalans() && $this->issetTovarCart($id_trader)) {
            $sql = "UPDATE orders SET show_order = 1 WHERE id_trader = $id_trader";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                echo "<script>alert('Ваш запрос отправлен на обработку. Следите за статусами заказов в разделе Заказы')</script>";
                return true;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        } else {
            echo "<script>alert('Некорректные данные')</script>";
        }
        return false;
    }

    public function delAllOrders($id_trader){
        $bd = new BD();
        if(isset($id_trader) && is_numeric($id_trader)) {
            $sql = "DELETE FROM orders WHERE id_trader = $id_trader";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                echo "<script>alert('Корзина пуста, мы очень опечалены вашим решением. Не заходите к нам больше =)')</script>";
                return true;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        } else {
            echo "<script>alert('Одно из полей должно быть числом')</script>";
        }
        return false;
    }

}
?>
// КОНТЕНТ для таблицы
<form name="MyForm" method="POST" action="Cart.php">
    <div class='Content'>
        <table class='table table-striped'>
            <tr class='success'>
                <td>
                Название
                </td>
                <td>
                Модель
                </td>
                <td>
                Описание
                </td>
                <td>
                Цена
                </td>
                <td>
                Изображение
                </td>
                <td>
                Покупатель
                </td>
                <td>
                Поставщик
                </td>
                <td></td>
                <td></td>
            </tr>
            <?php
                $cart = new Cart();
                $carts = $cart->getTextAllTovars();
                if (isset($_SESSION['user'])) {
                    foreach ($_SESSION['user'] as $key => $value) {
                        if ($key == 'id_user') {
                            $user_avt_id = $value;
                        }
                    }
                }
                if ($carts) {
                    foreach ($carts as $key => $value) {
                        $id_order = $value['id'];
                        $show_order = $value['show_order'];
                        $id_tovar = $value['id_tovar'];
                        $id_manager = $value['id_manager'];
                        $id_trader = $value['id_trader'];
                        $name = $value['tovar'];
                        $model = $value['model'];
                        $about_by_tovar = $value['about'];
                        $price = $value['price'];
                        $icon = "<img src='$value[icon]'>";
                        $fio = $value['fio'];
                        $name_company = $value['name_company'];
                        if(isset($_SESSION['roles']) && $_SESSION['roles'] == 3 && $user_avt_id == $id_trader && $show_order == 0) {
                            if(!isset($_POST['summa'])) {
                                $_POST['summa'] = 0;
                            }
                            $_POST['summa'] = $_POST['summa'] + $price;
                            echo
                            "
              <tr>
               <td>
                 $name
               </td>
               <td>
                 $model
               </td>
                <td>
                 $about_by_tovar
               </td>
               <td>
                 $price
               </td>
               <td>
                $icon
               </td>
                 <td>
                 $fio
               </td>
                 <td>
                 $name_company
               </td>
               ";
                echo " <td><button class='btn btn-link btn-lg btn-block' type='submit' name='DelOrder' value= $id_order> Удалить </button></td> 
                       <td></td>
                </tr>";
                        }
                    }
                }

            ?>
</table>
</div>
</form>

<?php
function reloadPage() {
    echo "<script>document.location.replace('../models/Cart.php');</script>"; //редирект
}
//Удаление товара
if(isset($_POST['DelOrder'])) {
    $cart = new Cart();
    $cart->delOrder($_POST['DelOrder']);
    reloadPage();
}

if(isset($_POST['Clear'])) {
    $cart = new Cart();
    $cart->delAllOrders($_POST['Clear']);
    reloadPage();
}

if(isset($_POST['SaveClofirm'])) {
    $addres = $_POST['address'];
    $map_number = $_POST['map_number'];
    $map_code = $_POST['map_code'];
    if($addres != "" && $map_number !="" && $map_code != "") {
        foreach ($_SESSION['user'] as $key => $value) {
            if ($key == 'id_user') {
                $id = $value;
            }
        }
        $cart = new Cart();
        $cart->updateConfirmOrder($id, $addres, $map_number, $map_code);
    } else {
        echo "<script>alert('Поля не могут быть пустыми')</script>";
    }
    reloadPage();
}
?>