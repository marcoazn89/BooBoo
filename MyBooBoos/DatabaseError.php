<?php
namespace BooBoo\MyBooBoos;

use BooBoo\MyBooBoos\MyBooBoos;

class DatabaseError extends MyBooBoos {

	const NOT_AVAILABLE = 'Unable to connect to database';
	const BAD_QUERY = 'Query is not formatted properly';

	protected function getTEXT() {
		return $this->getContents(__DIR__.'../../templates/DatabaseErrors/text.php');
	}

	protected function getHTML() {
		return $this->getContents(__DIR__.'../../templates/DatabaseErrors/html.php');
	}

	protected function getXML() {
		return $this->getContents(__DIR__.'../../templates/DatabaseErrors/xml.php');
	}

	protected function getJSON() {
		return $this->getContents(__DIR__.'../../templates/DatabaseErrors/json.php');
	}

	public function getTag() {
		return "DatabaseError";
	}
}
