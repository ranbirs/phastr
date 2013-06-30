<?php

$method = $this->request_method;
$path = \sys\utils\Helper::getPath(array('view', 'request_example'), 'xhr') . "/";
$script = <<<script
	$(function () {
		$('#post_example_trigger').click(function () {
			$.{$method}('{$path}',
				{
					post_data: $('#post_example_input').val()
				},
				function (data) {
					alert(data);
				}
			);
		});
	});
script;
$this->assets('script', null, $script);
?>
<section>
	<div class="body">
		<p><?= $this->body; ?></p>
	</div>
</section>
<section>
	<div class="form">
		<?= $this->test_form; ?>
	</div>
	<div class="form">
		<?= $this->simple_form; ?>
	</div>
</section>
<section>
	<h3>Posting a [non-Form] request...</h3>
	<div class="form">
		<div class="row">
			<input id="post_example_input" type="text">
		</div>
		<div class="row">
			<button class="btn btn-primary" type="button" id="post_example_trigger">Submit</button>
		</div>
	</div>
</section>
