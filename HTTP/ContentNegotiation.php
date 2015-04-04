<?php
namespace HTTP;

class ContentNegotiation {
	public static function negotiate($requestedContent, $supportedContent) {
		$negotiation = array_intersect($requestedContent, $supportedContent);

		return $negotiation;
	}
}
