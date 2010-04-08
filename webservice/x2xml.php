<?php
/** Return the XML code from an Array or Object
 * @author Carlos André Ferrari <carlos@ferrari.eti.br>
 * @param mixed $array
 * @param String $container
 * @param Boolean $beginning
 * @param Integer $ident
 * @return String
 */
function x2xml($array, $container='root', $beginning=true, $ident=0){
	if (is_object($array)) $array = (array)$array;
	$output = ($beginning) ? '<'.'?'.'xml version="1.0" encoding="UTF-8"'.'?'.'>' . PHP_EOL : '';
	$output .= str_repeat("\t", $ident) . '<' . $container . '>' . PHP_EOL;
	foreach ($array as $k => $v){
		if (!preg_match('@^[a-z0-9A-Z]@', $k)) continue;
		if (preg_match('@^[0-9]@', $k)) $k = 'item';
		if (is_array($v) || is_object($v))
			$output .= x2xml($v, $k, false, $ident+1);
		else{
			if (!preg_match('@^[0-9a-zA-Z\-_\ \.\:\,\=\/]*$@', $v)) $v = '<![CDATA[' . $v . ']]>';
			if (is_bool($v)) $v = $v ? 'true' : 'false';
			$output .= ($v==='' || $v===null) ?
				str_repeat("\t", $ident+1) . '<' . $k . '/>' . PHP_EOL :
				str_repeat("\t", $ident+1) . '<' . $k . '>' . $v . '</' . $k . '>' . PHP_EOL;
		}
	}
	$output .= str_repeat("\t", $ident) . '</' . $container . '>' . PHP_EOL;
	return $output;
}

