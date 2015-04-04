<?php

function priority($string) {
	//sample: //"en-US,en;q=0.8,es;q=0.6"
	$content = array();
	$priority = array();
	$segments = explode(',', $string);

	foreach($segments as $segment) {
		$pieces = explode(';', $segment);

		$rank = 0.0;

		if(count($pieces) > 1) {
			$rank = explode('=', $pieces[1]);
			$content[$pieces[0]] = (float)$rank[1];
		}
		else {
			$rank = 1.0;
			$content[$pieces[0]] = $rank;
		}

		arsort($content);
	}

	return array_keys($content);
}
