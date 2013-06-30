<?
$this->access = array('deny', 'public');
?>
<section>
	<p>User dashboard page...</p>
	<hr>
	<dl class="dl-horizontal">
		<dt>Token</dt>
		<dd><?= \sys\Init::session()->token(); ?></dd>
	</dl>
</section>
