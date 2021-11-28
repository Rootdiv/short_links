<?php
require_once 'functions.php';

check_auth();

if(isset($_POST['link']) && !empty($_POST['link'])){
  if(add_link($_POST['link'])){
    $_SESSION['success'] = 'Ссылка успешно добавлена!';
  }else{
    $_SESSION['error'] = 'Во время добавления ссылки что-то пошло не так';
  }
}

header('Location: ' . get_url('profile.php'));
die;
