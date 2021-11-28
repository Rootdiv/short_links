<?php
require_once 'functions.php';

check_auth();

if (isset($_GET['id']) && !empty($_GET['id'])) {
  if (is_owner_link($_GET['id'])) {
    if (delete_link($_GET['id'])) {
      $_SESSION['success'] = 'Ссылка успешно удалена!';
    }
  }
}

header('Location: ' . get_url('profile.php'));
die;
