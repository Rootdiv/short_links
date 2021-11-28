<?php
  require_once 'includes/functions.php';

  check_auth();

  if (isset($_GET['link']) && !empty($_GET['link'])) {
    $short_link = $_GET['link'];
    $link = get_link_info($short_link);
    if (empty($link) || !is_owner_link($link['id'])) {
      get_url('profile.php');
    }
  }

  require_once 'includes/header_edit.php';
?>
	<main class="container">
  <div class="row mt-5">
			<div class="col">
				<h2 class="text-center">Редактирование ссылки</h2>
		</div>
    <div class="row mt-3">
			<div class="col-4 offset-4">
				<form action="<?=get_url('includes/edit.php');?>" method="POST">
					<div class="mb-3">
            <input type="hidden" name="link_id" value="<?=$link['id']?>" />
						<label for="link-input" class="form-label">Новая ссылка</label>
						<input type="text" class="form-control" id="link-input" value="<?=$link['long_link']?>" name="link" required />
					</div>
					<button type="submit" class="btn btn-warning">Редактировать</button>
				</form>
			</div>
		</div>
	</main>
<?php require_once 'includes/footer.php';?>
