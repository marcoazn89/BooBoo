<?php
namespace BooBoo\MyBooBoos;

require_once('MyBooBoos.php');

use BooBoo\MyBooBoos\MyBooBoos;

class DatabaseError extends MyBooBoos {
	protected function getTEXT() {
		return $this->getContents('/templates/DatabaseErrors/text.php');
	}

	protected function getHTML() {
		return $this->getContents('/templates/DatabaseErrors/html.php');
	}

	protected function getXML() {
		return $this->getContents('/templates/DatabaseErrors/xml.php');
	}

	protected function getJSON() {
		return $this->getContents('/templates/DatabaseErrors/json.php');
	}

	public function getDescription() {
		return "Internal Server Error";
	}
}
