<?php
namespace BooBoo\MyBooBoos;

use HTTP\HTTP;
use HTTP\response\ContentType;
use HTTP\response\Status;

abstract class MyBooBoos {
	
	public $statusCode;

	abstract protected function getTEXT();
	abstract protected function getHTML();
	abstract protected function getXML();
	abstract protected function getJSON();
	abstract public function getDescription();

	public function __construct($statusCode = 500) {
		HTTP::status($statusCode);
		$status = Status::getInstance();
		$this->statusCode = $status->code;
	}

	public function printErrorMessage($contentType) {
		switch($contentType) {
			case ContentType::TEXT:
				return $this->getTEXT();
				break;
			case ContentType::HTML:
				return $this->getHTML();
				break;
			case ContentType::XML:
				return $this->getXML();
				break;
			case ContentType::JSON:
				return $this->getJSON();
				break;
		}
	}	

	public function __toString() {
		return get_called_class();
	}

	protected function getContents($file, $data = null) {
		ob_start();
		include($file);
		$buffer = ob_get_contents();
		ob_end_clean();
		return $buffer;
	}
}
