<?php
require 'vendor/autoload.php';

use HTTP\request\AcceptType;
use HTTP\request\AcceptLanguage;

var_dump(AcceptType::getContent(), AcceptLanguage::getContent());
