<?
$this->full_title = $this->title . " | " . $this->app_title;
?>
<!DOCTYPE html>
<html lang="<?= \sys\Init::session()->client('lang'); ?>">
<head>

	<meta charset="utf-8">
	<meta name="author" content="5rc.org">

	<?= $this->assets('meta'); ?>

	<title><?= $this->full_title; ?></title>

	<link href="/css/bootstrap.min.css?2.3.0" rel="stylesheet">

	<?= $this->assets('style'); ?>

	<style>
	<!--
	nav a {cursor: pointer;}
	h1 {font-size: 32px;}
	h1, h2, h3 {font-weight: normal;}
	#page {margin-top: 0;}
	#load {padding-top: 96px;}
	#footer {padding-top: 64px; padding-bottom: 30px;}
	fieldset > legend, section > h2, article > h2 {font-size: 21px; font-weight: normal; line-height: 40px; margin-bottom: 20px; color: #999; border-bottom: 1px solid #e5e5e5;}
	-->
	</style>

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
			<div id="node" data-sitetitle="<?= $this->full_title; ?>" data-sitename="<?= $this->app_title; ?>"<? if ($this->callback) { ?> data-callback="<?= $this->callback; ?>"<? } ?>>
				<section class="container" id="content">
					<h1 class="title"><?= $this->title; ?></h1>
					<div id="body">
						<section><?= $this->page; ?></section>
					</div>
				</section>
			</div>
		</div>
	</div>

	<footer class="container">
		<hr />
	</footer>

<script src="/js/jquery-1.9.1.min.js"></script>
<script src="/js/bootstrap.min.js?2.3.2"></script>

<?= $this->assets('script'); ?>

</body>
</html>
