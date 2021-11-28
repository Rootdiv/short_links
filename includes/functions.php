<?php
require_once 'config.php';

function get_url($page = '') {
  return HOST . "/$page";
}

function db() {
  try {
    return new PDO('mysql:host=' . DB_HOST . '; dbname=' . DB_NAME . '; charset=utf8', DB_USER, DB_PASS, [
      PDO::ATTR_EMULATE_PREPARES => false,
      PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
  } catch (PDOException $e) {
    die($e->getMessage());
  }
}

function db_query($sql = '', $exec = false) {
  if (empty($sql)) {
    return false;
  }

  if ($exec) {
    return db()->exec($sql);
  }
  return db()->query($sql);
}

function get_users_count() {
  return db_query('SELECT COUNT(`id`) FROM `users`;')->fetchColumn();
}

function get_links_count() {
  return db_query('SELECT COUNT(`id`) FROM `links`;')->fetchColumn();
}

function get_views_count() {
  return db_query('SELECT SUM(`views`) FROM `links`;')->fetchColumn();
}

function get_link_info($url) {
  if (empty($url)) {
    return [];
  }

  return db_query("SELECT * FROM `links` WHERE `short_link` = '$url'")->fetch();
}

function update_views($url) {
  if (empty($url)) {
    return false;
  }

  return db_query("UPDATE `links` SET `views` = `views` + 1 WHERE `short_link` = '$url';", true);
}

function get_user_info($login) {
  if (empty($login)) {
    return [];
  }

  return db_query("SELECT * FROM `users` WHERE `login` = '$login'")->fetch();
}

function add_user($login, $pass) {
  $password = password_hash($pass, PASSWORD_DEFAULT);
  return db_query("INSERT INTO `users` (`id`, `login`, `pass`) VALUES (NULL, '$login', '$password')", true);
}

function register_user($auth_data) {
  if (empty($auth_data) || !isset($auth_data['login']) || empty($auth_data['login']) ||
    !isset($auth_data['pass']) || !isset($auth_data['pass2'])) {
    return false;
  }

  $user = get_user_info($auth_data['login']);
  if (!empty($user)) {
    $_SESSION['error'] = "Пользователь '" . $auth_data['login'] . "' уже существует.";
    header('Location: ' . get_url('register.php'));
    die;
  }

  if ($auth_data['pass'] !== $auth_data['pass2']) {
    $_SESSION['error'] = 'Пароли не совпадают!';
    header('Location: ' . get_url('register.php'));
    die;
  }

  if (add_user($auth_data['login'], $auth_data['pass'])) {
    $_SESSION['success'] = 'Регистрация прошла успешно!';
    header('Location: ' . get_url('login.php'));
    die;
  }
  return true;
}

function login($auth_data) {
  if (empty($auth_data) || !isset($auth_data['login']) || empty($auth_data['login']) ||
    !isset($auth_data['pass']) || empty($auth_data['pass'])) {
    $_SESSION['success'] = 'Логин или пароль не может быть пустым.';
    header('Location: ' . get_url('login.php'));
    die;
  }
  ;

  $user = get_user_info($auth_data['login']);
  if (empty($user)) {
    $_SESSION['error'] = 'Логин или пароль неверен!';
    header('Location: ' . get_url('login.php'));
    die;
  }

  if (password_verify($auth_data['pass'], $user['pass'])) {
    $_SESSION['user'] = $user;
    header('Location: ' . get_url('profile.php'));
    die;
  } else {
    $_SESSION['error'] = 'Пароль неверен!';
    header('Location: ' . get_url('login.php'));
    die;
  }
}

function check_auth() {
  if (!isset($_SESSION['user']['id']) && empty($_SESSION['user']['id'])) {
    header('Location: ' . get_url());
    die;
  }
}

function get_user_links($user_id) {
  if (empty($user_id)) {
    return [];
  }

  return db_query("SELECT * FROM `links` WHERE `user_id` = '$user_id'")->fetchAll();
}

function delete_link($id) {
  if (empty($id)) {
    return false;
  }

  return db_query("DELETE FROM `links` WHERE `id` = '$id'", true);
}

// function generate_string($size = 6){
//   $new_string = str_shuffle(URL_CHARS);
//   return substr($new_string, 0, $size);
// }

function generate_string2($size = 6) {
  $new_string = '';
  for ($i = 0; $i < $size; $i++) {
    $new_string .= substr(URL_CHARS, rand(0, strlen(URL_CHARS)), 1);
  }
  return $new_string;
}

function generate_string($size = 6) {
  $str_size = strlen(URL_CHARS) - 1;
  $new_string = null;
  while ($size--) {
    $new_string .= URL_CHARS[rand(0, $str_size)];
  }

  return $new_string;
}

function add_link($link) {
  if (empty($link)) {
    return false;
  }
  $user_id = $_SESSION['user']['id'];
  $short_link = generate_string();
  return db_query("INSERT INTO `links` (`id`, `user_id`, `long_link`, `short_link`, `views`)
    VALUES (NULL, '$user_id', '$link',  '$short_link', 0)", true);
}

function update_link($link_id, $new_link) {
  if (empty($link_id) || empty($new_link)) {
    return false;
  }
  return db_query("UPDATE `links` SET `long_link` = '$new_link' WHERE `id` = '$link_id';", true);
}

function get_error_message() {
  $error = '';
  if (isset($_SESSION['error']) && !empty($_SESSION['error'])) {
    $error = $_SESSION['error'];
    $_SESSION['error'] = '';
  }
  return $error;
}

function get_success_message() {
  $success = '';
  if (isset($_SESSION['success']) && !empty($_SESSION['success'])) {
    $success = $_SESSION['success'];
    $_SESSION['success'] = '';
  }
  return $success;
}

function get_message($type = 'error') {
  $message = '';
  if (isset($_SESSION[$type]) && !empty($_SESSION[$type])) {
    $message = $_SESSION[$type];
    $_SESSION[$type] = '';
  }
  return $message;
}

function show_message($message, $type = 'danger') {
  if (!empty($message)) {
    echo '<div class="alert alert-' . $type . ' alert-dismissible fade show mt-3" role="alert">' . $message;
    echo '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>';
  }
}

function is_owner_link($link_id) {
  if (empty($link_id) && ctype_digit($link_id)) {
    return false;
  }

  if (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])) {
    $user_id = db_query("SELECT `user_id` FROM `links` WHERE `id` = $link_id;")->fetchColumn();
    if ($user_id == $_SESSION['user']['id']) {
      return true;
    }
  }
  $_SESSION['error'] = 'Айяйяй! Не хорошо так делать!';
  return false;
}
