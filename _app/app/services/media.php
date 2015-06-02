<?php

namespace app\services;

class Media
{
	
	use \sys\Loader;

	public function render($result, $type, $subj)
	{
		$this->loader()->init('sys/modules/view');
		
		$this->view->type = $type . '/' . $subj;
		$this->view->body = base64_decode($result);
		$this->view->layout('app/views/layouts/media/' . $type);
	}

	public function application($result, $type)
	{
		switch ($type) {
			case 'json':
			case 'pdf':
			case 'zip':
			case 'rss+xml':
			case 'atom+xml':
			case 'soap+xml':
			case 'ogg':
				break;
			default:
		}
	}

	public function text($result, $type)
	{
		switch ($type) {
			case 'xml':
			case 'plain':
			case 'javascript':
			case 'css':
				break;
			default:
		}
	}

	public function image($result, $type)
	{
		switch ($type) {
			case 'jpeg':
			case 'gif':
			case 'png':
			case 'svg+xml':
				break;
			default:
		}
	}

	public function audio($result, $type)
	{
		switch ($type) {
			case 'ogg':
			case 'mpeg':
			case 'mp4':
				break;
			default:
		}
	}

	public function video($result, $type)
	{
		switch ($type) {
			case 'ogg':
			case 'mpeg':
			case 'mp4':
				break;
			default:
		}
	}

}
