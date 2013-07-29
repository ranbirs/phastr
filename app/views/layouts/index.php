<?
$this->full_title = $this->title . " | " . $this->app_title;
?>
<!DOCTYPE html>
<html lang="<?= \sys\Init::session()->client('lang'); ?>">
<head>

	<meta charset="utf-8">
	<meta name="author" content="5rc.org">
	<?= $this->assets->get('meta'); ?>

	<title><?= $this->full_title; ?></title>

	<?= $this->assets->get('style'); ?>

	<!--[if lt IE 9]>
	<script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->

</head>
<body>
	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<header class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				<a class="brand" href="/"><?= $this->app_title; ?></a>
				<nav class="nav-collapse">
					<?= $this->top_nav; ?>
					<?= $this->user_nav; ?>
				</nav>
			</header>
		</div>
	</div>

	<div id="page">
		<div id="load">
			<div id="node">
				<section class="container" id="content">
					<h1 class="title"><?= $this->title; ?></h1>
					<div id="body">
						<div class="page">
							<?= $this->page; ?>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>

	<footer class="container">
		<hr />
	</footer>

<?= $this->assets->get('script'); ?>

</body>
</html>
