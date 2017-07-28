<?php
include "../views/Catalog.html";
include "../models/Users.php";
include "../models/Model.php";

class  Tovar {
    public
        $id_tovar,
        $id_model,
        $id_user,
        $name,
        $price,
        $about_by_tovar,
        $icon;

    public function __construct (){

    }
    public function getAllTovars() {
        $bd = new BD();
        $sql = "SELECT * FROM tovars";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив объектов. Каждый объект - это строка таблицы товары
        foreach ($result as $value) {
            $tovar = new Tovar();
            $tovar->id_tovar = $value['id_tovar'];
            $tovar->id_model = $value['id_model'];
            $tovar->id_user = $value['id_user'];
            $tovar->name = $value['name'];
            $tovar->price = $value['price'];
            $tovar->about_by_tovar = $value['about_by_tovar'];
            $tovar->icon = $value['icon'];
            $tovars[$i] = $tovar;
            $i++;
        }
        return $tovars;
    }
    //Массив таблицы с пол. именами вторичных ключей
    public function getTextAllTovars() {
        $bd = new BD();
        $sql = "SELECT tovars.id_user as id_user, tovars.id_tovar, model.name as model, users.name_company as user, tovars.name, tovars.price, tovars.about_by_tovar,
                tovars.icon FROM tovars, model, users WHERE tovars.id_model = model.id and tovars.id_user = users.id_user";
        $result = mysqli_query($bd->link,$sql);
        $i = 0;
        //возвращает массив объектов. Каждый объект - это строка таблицы товары
        foreach ($result as $value) {
            $arr_tovars[$i] = $value;
            $i++;
        }
        return $arr_tovars;
    }
    public function getMaxIdTovar() {
        $bd = new BD();
        $sql = "SELECT MAX(id_tovar) as id FROM tovars";
        $result = mysqli_query($bd->link,$sql);
        if($result) {
            foreach ($result as $value) {
                return $value['id'];
            }
        }
        return false;
    }

    public function addTovar($name, $price, $about_by_tovar, $icon, $id_model, $id_user) {
        $bd = new BD();
        if(is_numeric($price) && is_numeric($id_user) && is_numeric($id_model)) {
            $sql = "INSERT INTO tovars(name, price, about_by_tovar, icon, id_model, id_user) VALUES ('$name', $price, '$about_by_tovar', '$icon', $id_model, $id_user)";
            $result = mysqli_query($bd->link,$sql);
            if ($result) {
                echo "<script>alert('Товар успешно добавлен')</script>";
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

    public function eddTovar($id, $name, $price, $about_by_tovar, $icon, $id_model, $id_user) {
        $bd = new BD();
        if(is_numeric($price) && is_numeric($id_user) && is_numeric($id_model)) {
            $sql = "UPDATE tovars SET name = '$name', price = $price, about_by_tovar = '$about_by_tovar', icon = '$icon', id_model = $id_model, id_user = $id_user WHERE id_tovar = $id";
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

    public function getTovar($id)
    {
        $bd = new BD();
        if(isset($id) && is_numeric($id)) {
            $sql = "SELECT * FROM tovars WHERE id_tovar = $id";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                foreach ($result as $value) {
                    $tovar = new Tovar();
                    $tovar->id_tovar = $value['id_tovar'];
                    $tovar->id_model = $value['id_model'];
                    $tovar->id_user = $value['id_user'];
                    $tovar->name = $value['name'];
                    $tovar->price = $value['price'];
                    $tovar->about_by_tovar = $value['about_by_tovar'];
                    $tovar->icon = $value['icon'];
                }
                return $tovar;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        } else {
            echo "<script>alert('Одно из полей должно быть числом')</script>";
        }
        return false;
    }

    public function delTovar($id){
        $bd = new BD();
        if(isset($id) && is_numeric($id)) {
            $sql = "DELETE FROM tovars WHERE id_tovar = $id";
            $result = mysqli_query($bd->link,$sql);
            if($result) {
                echo "<script>alert('Товар успешно удалён')</script>";
                return true;
            } else {
                echo "<script>alert('Невалидный запрос')</script>";
            }
        } else {
            echo "<script>alert('Одно из полей должно быть числом')</script>";
        }
        return false;
    }
    public function addCart($id_trader, $id_tovar){
        $bd = new BD();
        $sql = "INSERT INTO orders(id_trader,id_tovar, id_status, show_order) VALUES ($id_trader, $id_tovar, 1, 0)";
        $result = mysqli_query($bd->link,$sql);
        if ($result) {
            echo "<script>alert('Товар успешно добавлен в корзину')</script>";
            return true;
        } else {
            echo "<script>alert('Невалидный запрос')</script>";
        }
        return false;
    }
}
?>

// КОНТЕНТ для таблицы
<form name="MyForm" method="POST" action="Tovar.php">
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
                Поставщик
                </td>
                <td>
                Цена
                </td>
                <td>
                Изображение
                </td>
                <td></td>
                <td></td>
            </tr>
            <?php
            $tovar = new Tovar();
            $tovars = $tovar->getTextAllTovars();
            if (isset($_SESSION['user'])) {
                foreach ($_SESSION['user'] as $key => $value) {
                    if ($key == 'id_user') {
                        $user_avt_id = $value;
                    }
                }
            }
            if ($tovars) {
                foreach ($tovars as $key => $value) {
                    $id_tovar = $value['id_tovar'];
                    $id_user_tovar = $value['id_user'];
                    $name = $value['name'];
                    $model = $value['model'];
                    $about_by_tovar = $value['about_by_tovar'];
                    $user = $value['user'];
                    $price = $value['price'];
                    $icon = "<img src='$value[icon]'>";
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
                 $user
               </td>
               <td>
                 $price
               </td>
               <td>
                $icon
               </td>";
                    //Условие вывода кнопок для товаров, в зависимости от роли
                    if(isset($_SESSION['roles']) && $_SESSION['roles'] == 3 ) {
                        echo" <td><button class='btn btn-link btn-lg btn-block' type='submit' name='AddCart' value= '$id_tovar'> Добавить в корзину </button></td> 
                          <td></td>
                </tr>";
                    }
                    else if(isset($_SESSION['roles']) &&($_SESSION['roles'] == 1 or ($_SESSION['roles'] == 2  && $user_avt_id == $id_user_tovar))) {
                        echo" <td><button class='btn btn-link btn-lg btn-block' type='submit' name='EddTovar'  value= '$id_tovar'> Изменить </button></td>
                          <td><button class='btn btn-link btn-lg btn-block' type='submit' name='DelTovar' value= '$id_tovar'> Удалить </button></td>
                </tr>";
                        $d = 0;
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

            ?>
        </table>
    </div>
</form>

<?php
function reloadPage() {
    echo "<script>document.location.replace('../models/Tovar.php');</script>"; //редирект
}
// Для модального окна регестрации selectRoles()
/*selectRoles();
function selectRoles() {
    $user = new Users();
    $_SESSION['selectRoles'] = $user->getRolesUsers();
} */
// Для модального окна добавление товара selectManager()
selectManager();
function selectManager() {
    $user = new Users();
    $_SESSION['selectManager'] = $user->getUsersManager();
}

// Для модального окна добавление товара selectModel()
selectModel();
function selectModel() {
    $model = new Model();
    $_SESSION['selectModel'] = $model->getModel();
}

//ОПЕРАЦИИ---------------------------------------------------------------------------------
//Удаление товара
if(isset($_POST['DelTovar'])) {
$tovar = new Tovar();
$tovar->delTovar($_POST['DelTovar']);
reloadPage();
}
//Добавление товара
//Получаем имя файла
if(isset($_POST['SaveAddTovar'])) {
    $full_path = "../images";
    $name = $_POST['t_name'];
    $price = $_POST['t_price'];
    $about = $_POST['t_about'];
    $model = $_POST['selectedModels'];
    if ($_SESSION['roles'] == 2) {
        foreach ($_SESSION['user'] as $key => $value) {
            if ($key == 'id_user'){
                $manager = $value;
            }
        }
    } else {
        $manager = $_POST['SelectedManager'];
    }
    $tovar = new Tovar();
   // $tovar->getTovar($_POST['EddTovar']);
    $id = $tovar->getMaxIdTovar() + 1;
    if($id && $name && $price && $model && $manager && isset($_FILES)) {

        $fileName = "id" . $id . "" . $_FILES['uploadFile']['name']; //формируем название для файла.
        // указание директории и имени нового файла на сервере
        $new_file = '../images/'. $fileName;
        // копирование файла
        if ((move_uploaded_file($_FILES['uploadFile']['tmp_name'],$new_file))) {

        } else {
            echo "Ошибка при загрузке файла";
        }
        $tovar->addTovar($name, $price, $about, $new_file, $model, $manager);
    } else {
        echo "<script>alert('Не удалось добавить товар (некорректные данные)')</script>";
    }
    reloadPage();
}
//ВЫВОД МОД ОКНА ДЛЯ РЕДАКТИРОВАНИЯ
if(isset($_POST['EddTovar'])) {
    echo "<script>
  $(document).ready(function() {
    $(\"#modal-4\").modal('show');
  });
</script>";
}

if(isset($_POST['SaveEddTovar'])) {
    $tovar = new Tovar();
    $full_path = "../images";
    $name = $_POST['edd_name'];
    $price = $_POST['edd_price'];
    $about = $_POST['edd_about'];
    $model = $_POST['eddSelectedModels'];
    if ($_SESSION['roles'] == 2) {
        foreach ($_SESSION['user'] as $key => $value) {
            if ($key == 'id_user'){
                $manager = $value;
            }
        }
    } else {
        $manager = $_POST['eddSelectedManager'];
    }
    $tovar = new Tovar();
    $id = isset($_POST['tovarId']) ? $_POST['tovarId'] : false ;
    if($id && $name && $price && $model && $manager && isset($_FILES)) {
        $fileName = "id" . $id . "" . $_FILES['eddUploadFile']['name']; //формируем название для файла.
        // указание директории и имени нового файла на сервере
        $new_file = '../images/'. $fileName;
        // копирование файла
        if ((move_uploaded_file($_FILES['eddUploadFile']['tmp_name'],$new_file))) {
            echo "Файл загружен на сервер";
            $tovar->EddTovar($id , $name, $price, $about, $new_file, $model, $manager);
        } else {
            echo "Ошибка при загрузке файла";
        }
    } else {
        echo "<script>alert('Не удалось добавить товар (некорректные данные)')</script>";
    }
    reloadPage();
}

if(isset($_POST['saveReg'])) {
    $login = $_POST['login_reg'];
    $password = md5($_POST['password_reg']);
    $fio = $_POST['fio_reg'];
    $map_number = $_POST['map_number_reg'];
    $map_code = $_POST['map_code_reg'];
    $id_roles = 3;
    $balans = 3000000;
    if( $map_code != "" && $map_number != "" && $fio != "" && $login != "" && $password != "") {
        $user = new Users();
        if ($user->Registration($login, $password, $id_roles, $map_number, $map_code, $fio, $balans)) {
            echo "<script>alert('На вашу карту зачислен бонусный платёж в размере 3 000 000. Да вы везунчик!!!')</script>";
        }
    } else {
        echo "<script>alert('Поля не могут быть пустыми')</script>";
    }
    reloadPage();
}

if(isset($_POST['saveAut'])) {
  $login = $_POST['login'];
  $password = md5($_POST['password']);
  $user = new Users();
  $roles = $user->Avtoriz($login,$password)? $user->id_roles : false;
  if($roles) {
      $_SESSION['roles'] = $roles;
      $_SESSION['user'] = $user;
  } else {
      echo "<script>alert('Логин или пароль неверные')</script>";
  }
    reloadPage();
   // echo "Работает";
}
if (isset($_POST['AddCart'])){
    $id_tovar = $_POST['AddCart'];
    $tovar = new Tovar();
    foreach ($_SESSION['user'] as $key => $value) {
        if ($key == 'id_user') {
            $id_trader = $value;
        }
    }
    $tovar->addCart($id_trader, $id_tovar);
    reloadPage();
}

if(isset($_POST['destroyAvt'])) {
    session_destroy();
    reloadPage();
}

if(isset($_POST['saveRegManager'])) {
    $login = $_POST['LoginM'];
    $password = md5($_POST['PasswordM']);
    $name_company = $_POST['NameCompany'];
    $about_by_tovar = $_POST['AboutCompany'];
    $id_roles = 2;
    if( $name_company != "" && $about_by_tovar != "" && $login != "" && $password != "") {
        $user = new Users();
        if ($user->RegistrationManager($login, $password, $id_roles, $name_company, $about_by_tovar)) {
            echo "<script>alert('Ты добавил нового поставщика! Это говорит о том, что твой сайт развивается! Молодец, горжусь тобой!')</script>";
        }
    } else {
        echo "<script>alert('Поля не могут быть пустыми')</script>";
    }
    reloadPage();
}
if(isset($_POST['saveModel'])) {
    $model = new Model();
    $name = $_POST['nameModel'];
    if($name) {
        $model->addModel($name);
    } else {
        echo "<script>alert('Полz не могут быть пустыми')</script>";
    }
}
?>

