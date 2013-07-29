<?php

namespace app\models;

class Node extends \sys\Model {

	public $title, $body;

	function __construct()
	{
		parent::__construct();
	}

	public function data($fields, $path)
	{
		$data = $this->database()->select('node', $fields,
			"WHERE path = :path", array('path' => $path)
		);
		if ($data) {
			$data = $data[0];
			return $data;
		}
	}

}
