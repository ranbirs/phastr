<!DOCTYPE html>
<html lang="<?= $this->lang; ?>">
<head>

    <?= $this->assets->get('meta'); ?>

    <title><?= $full_title = $this->title . ' | ' . $this->app_title; ?></title>

    <?= $this->assets->get('style', null, 'assets'); ?>

</head>
<body>

	<div class="navbar navbar-inverse navbar-fixed-top">
		<div class="container">
			<button type="button" class="navbar-toggle" data-toggle="collapse"
				data-target=".nav-collapse">
				<span class="icon-bar"></span> <span class="icon-bar"></span> <span
					class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="/"><?= $this->app_title; ?></a>

			<div class="nav-collapse">
            <?= $this->top_nav; ?>
            <?= $this->user_nav; ?>
        </div>
		</div>
	</div>

	<div class="container">
		<div class="starter-template">
			<h1 class="title"><?= $this->title; ?></h1>

			<div class="body">
				<div class="page">
                <?= $this->page; ?>
            </div>
			</div>
		</div>
	</div>

<?= $this->assets->get('script', null, 'assets'); ?>

</body>
</html>
