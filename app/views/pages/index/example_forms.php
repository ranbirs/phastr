<?php

$path = \sys\Res::route() . '/xhr/post/post_example/';
$script = <<<script
	$(function () {
		$('#post_example_trigger').click(function () {
			$.post('{$path}',
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
<section><?= $this->body; ?></section>
<section><?= $this->test_form; ?></section>
<section><?= $this->simple_form; ?></section>
<section>
	<h3>Posting a [non-Form] request...</h3>
	<div>
		<input id="post_example_input" type="text">
	</div>
	<div>
		<button class="btn btn-primary" type="button" id="post_example_trigger">Submit</button>
	</div>
</section>
