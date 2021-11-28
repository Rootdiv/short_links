<?php
	require_once 'includes/functions.php';

	if (isset($_SESSION['user']['id']) && !empty($_SESSION['user']['id'])) {
    header('Location: ' . get_url('profile.php'));
    die;
  }

	if(isset($_POST['login']) && !empty($_POST['login'])){
		register_user($_POST);
	}

	$error = get_message();
	$success = get_message('success');

	require_once 'includes/header.php';
?>
	<main class="container">
		<?php
			show_message($success, 'success');
			show_message($error);
		?>
		<div class="row mt-5">
			<div class="col">
				<h2 class="text-center">Регистрация</h2>
				<p class="text-center">Если у вас уже есть логин и пароль, <a href="<?=get_url('login.php')?>">войдите на сайт</a></p>
			</div>
		</div>
		<div class="row mt-3">
			<div class="col-4 offset-4">
				<form action="" method="POST">
					<div class="mb-3">
						<label for="login-input" class="form-label">Логин</label>
						<input type="text" class="form-control" id="login-input" name="login" required />
						<!-- <div class="valid-feedback">Все ок</div> -->
					</div>
					<div class="mb-3">
						<label for="password-input" class="form-label">Пароль</label>
						<input type="password" class="form-control" id="password-input" name="pass" required />
						<!-- <div class="invalid-feedback">А тут не ок</div> -->
					</div>
					<div class="mb-3">
						<label for="password-input2" class="form-label">Пароль еще раз</label>
						<input type="password" class="form-control" id="password-input2" name="pass2" required />
						<!-- <div class="invalid-feedback">И тут тоже не ок</div> -->
					</div>
					<button type="submit" class="btn btn-primary">Зарегистрироваться</button>
				</form>
			</div>
		</div>
	</main>
<?php require_once 'includes/footer.php' ?>
