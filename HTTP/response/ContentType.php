<?php
namespace HTTP\response;

require_once('HttpHeader.php');

use HTTP\response\HTTPHeader;

class ContentType extends HTTPHeader {

	const ADOBE_SHOCKWAVE = 'application/x-director';
	const ADOBE_FLASH = 'application/x-shockwave-flash';
	const AVI = 'video/x-msvideo';
	const BINARY_DATA = 'application/octet-stream';
	const BMP = 'image/bmp';
	const BZIP = 'application/x-bzip';
	const BZIP_2 = 'application/x-bzip2';
	const C_FILE = 'text/x-c';
	const CSS = 'text/css';
	const FLASH_VIDEO = 'video/x-flv';
	const FLASH_VIDEO_4 = 'video/x-f4v';
	const GIF = 'image/gif';
	const HTML = 'text/html';
	const ICON_IMAGE = 'image/x-icon';
	const JAVA_FILE ='text/x-java-source,java';
	const JSON = 'application/json';
	const JPEG_IMAGE = 'image/jpeg';
	const JPEG_VIDEO = 'video/jpeg';
	const MPEG_4 = 'application/mp4';
	const MPEG_AUDIO = 'audio/mpeg';
	const MPEG_VIDEO = 'video/mpeg';
	const MPEG_AUDIO_4 = 'audio/mp4';
	const MPEG_VIDEO_4 = 'video/mp4';
	const OOG = 'application/ogg';
	const OOG_AUDIO = 'audio/ogg';
	const OOG_VIDEO = 'video/ogg';
	const PHOTOSHOP_IMAGE = 'image/vnd.adobe.photoshop';
	const PNG = 'image/png';
	const QUICKTIME_VIDEO = 'video/quicktime';
	const RAR = 'application/x-rar-compressed';
	const SGI_MOVIE = 'video/x-sgi-movie';
	const TAR = 'application/x-tar';
	const TEXT = 'text/plain';
	const VCARD = 'text/x-vcard';
	const WAV = 'audio/x-wav';
	const WMV = 'video/x-ms-wmv';
	const XHTML = 'application/xhtml+xml';
	const XML = 'application/xml';
	const ZIP = 'application/zip';
	const ZIP_7 = 'application/x-7z-compressed';

	public $content;

	public function set($content, $send = false) {
		$this->content = $content;
		
		$this->headerString = "Content-Type: {$this->content}";

		if($send) {
			$this->sendHeader();
		}
	}
}
