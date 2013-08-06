<!DOCTYPE html>
<html>

<head>
	<meta name="robots" content="noindex, nofollow">
	<title>Not Found</title>
</head>

<body>
	<div id="node">
		<h1 class="title">Not Found</h3>
		<div id="body">
			<h3>The requested page could not be found</h3>
			<? if ($this->error): ?>
			<p><?= $this->error; ?></p>
			<? endif; ?>
			<section><a href="/">Home</a></section>
		</div>
	</div>
</body>

</html>
