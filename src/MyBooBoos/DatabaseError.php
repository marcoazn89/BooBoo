<?php
namespace MyBooBoos;

class DatabaseError extends ErrorTemplate {

	const NOT_AVAILABLE = 'Unable to connect to database';
	const BAD_QUERY = 'Query is not formatted properly';

	protected function getTEXT() {
		return __DIR__.'../../templates/databaseErrors/text.php';
	}

	protected function getHTML() {
		return __DIR__.'../../templates/databaseErrors/html.php';
	}

	protected function getXML() {
		return __DIR__.'../../templates/databaseErrors/xml.php';
	}

	protected function getJSON() {
		return __DIR__.'../../templates/databaseErrors/json.php';
	}

	public function getTag() {
		return "DatabaseError";
	}
}
