<?php
namespace MyBooBoos;

class DatabaseError extends Error {

	const NOT_AVAILABLE = 'Unable to connect to database';
	const BAD_QUERY = 'Query is not formatted properly';

	protected function getTEXT() {
		return __DIR__.'../../templates/DatabaseErrors/text.php';
	}

	protected function getHTML() {
		return __DIR__.'../../templates/DatabaseErrors/html.php';
	}

	protected function getXML() {
		return __DIR__.'../../templates/DatabaseErrors/xml.php';
	}

	protected function getJSON() {
		return __DIR__.'../../templates/DatabaseErrors/json.php';
	}

	public function getTag() {
		return "DatabaseError";
	}
}
