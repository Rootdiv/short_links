<?php
require_once 'functions.php';

check_auth();

if (isset($_POST['link_id']) && !empty($_POST['link_id']) && isset($_POST['link']) && !empty($_POST['link'])) {
  $link_id = $_POST['link_id'];
  if (is_owner_link($link_id)) {
    if (update_link($_POST['link_id'], $_POST['link'])) {
      $_SESSION['success'] = 'Ссылка успешно обновлена!';
    } else {
      $_SESSION['error'] = 'Во время обновлена ссылки что-то пошло не так';
    }
  }
}

header('Location: ' . get_url('profile.php'));
die;
