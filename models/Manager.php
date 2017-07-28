<?php
include "../views/Managers.html";
/**
 * Created by PhpStorm.
 * User: Shawn59
 * Date: 23.06.2017
 * Time: 8:49
 */
class Manager
{
    public
        $id_user,
        $name_company,
        $about_by_company,
        $login;

    public function __construct()
    {

    }

    public function getAllManagers()
    {
        $bd = new BD();
        $sql = "SELECT  id_user, name_company, about_by_company, login FROM users WHERE id_roles = 2";
        $result = mysqli_query($bd->link, $sql);
        $i = 0;
        //возвращает массив ролей
        if ($result) {
            foreach ($result as $value) {
                $manager[$i] = $value;
                $i++;
            }
            return isset($manager) ? $manager : false;
        }
        return false;
    }

    public function getManager($id)
    {
        $bd = new BD();
        $sql = "SELECT  id_user, name_company, about_by_company FROM users WHERE id_user = $id";
        $result = mysqli_query($bd->link, $sql);
        //возвращает массив ролей
        if ($result) {
            foreach ($result as $value) {
                $manager = new Manager();
                $manager->id_user = $value['id_user'];
                $manager->name_company = $value['name_company'];
                $manager->about_by_company = $value['about_by_company'];

            }
            return isset($manager) ? $manager : false;
        }
        return false;
    }

    public function delManager($id){
    $bd = new BD();
    if(isset($id) && is_numeric($id)) {
        $sql = "DELETE FROM users WHERE id_user = $id";
        $result = mysqli_query($bd->link,$sql);
        if($result) {
            echo "<script>alert('Поставщик успешно удалён')</script>";
            return true;
        } else {
            echo "<script>alert('Невалидный запрос')</script>";
        }
    } else {
        echo "<script>alert('Одно из полей должно быть числом')</script>";
    }
    return false;
    }
    public function eddManager($id, $name_company, $about_by_company) {
        $bd = new BD();
        if(is_numeric($id)) {
            $sql = "UPDATE users SET name_company = '$name_company', about_by_company = '$about_by_company' WHERE id_user = $id";
            $result = mysqli_query($bd->link,$sql);
            if ($result) {
                echo "<script>alert('Товар успешно изменён')</script>";
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
<form name="MyForm" method="POST" action="Manager.php">
<div class='Content'>
<table class='table table-striped'>
<tr class='success'>
    <td>
    Логин
    </td>
    <td>
    Поставщик
    </td>
    <td>
    Описание
    </td>
    <td></td>
</tr>
<?php
$manager = new Manager();
$managers = $manager->getAllManagers();
if (isset($_SESSION['user'])) {
    foreach ($_SESSION['user'] as $key => $value) {
        if ($key == 'id_user') {
            $user_avt_id = $value;
        }
    }
}
if ($managers) {
    foreach ($managers as $key => $value) {
        $id_user = $value['id_user'];
        $name_company = $value['name_company'];
        $about_by_company = $value['about_by_company'];
        $login = $value['login'];
        echo
        "
              <tr>
               <td>
                 $login
               </td>
               <td>
                 $name_company
               </td>
                <td>
                 $about_by_company
               </td>
         ";
        //Условие вывода кнопок для товаров, в зависимости от роли
        if(isset($_SESSION['roles']) && $_SESSION['roles'] == 1 ) {
            echo" <td><button type='submit' name='DelManager' value= '$id_user'> Удалить </button></td> 
                          
                </tr>";
        }
        else if(isset($_SESSION['roles']) && $_SESSION['roles'] == 2 && $user_avt_id == $id_user  ) {
            echo" <td><button class='btn btn-link btn-lg btn-block' type='submit' name='EddManager' value= '$id_user'> Изменить </button></td> 
                          
                </tr>";
        }
        else if(!isset($_SESSION['roles'])) {
            echo" 
                 <td></td>
                </tr>";
        }
        else {
            echo" 
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
if(isset($_POST['DelManager'])) {
    $manager = new Manager();
    $manager->delManager($_POST['DelManager']);
    reloadPage();
}
function reloadPage() {
    echo "<script>document.location.replace('../models/Manager.php');</script>"; //редирект
}
//ВЫВОД МОД ОКНА ДЛЯ РЕДАКТИРОВАНИЯ
if(isset($_POST['EddManager'])) {
    echo "<script>
  $(document).ready(function() {
    $(\"#modal-6\").modal('show');
  });
</script>";
}
if(isset($_POST['SaveEddManager'])) {
    $manager = new Manager();
    $manager = $manager->getManager(2);
    $name_company = $_POST['edd_name_company'];
    $about_by_company = $_POST['edd_about_company'];
    $id = isset($_POST['managerId']) ? $_POST['managerId'] : false ;
        if($id && $name_company && $about_by_company) {
            $manager->eddManager($id, $name_company, $about_by_company);
        } else {
            echo "<script>alert('Не удалось изменить Поставщика (некорректные данные)')</script>";
        }
        reloadPage();
    }
?>