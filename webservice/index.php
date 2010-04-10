<?php

// WebService Rest de uso da classe com cache
// use: 
//  http://ferrari.eti.br/correios/webservice/?q=PB151832535BR
//  http://ferrari.eti.br/correios/webservice/?q=PB151832535BR&f=dump
//  http://ferrari.eti.br/correios/webservice/?q=PB151832535BR&f=serial
//  http://ferrari.eti.br/correios/webservice/?q=PB151832535BR&f=xml
//  e finalmente, para amantes de Ajax:
//  http://ferrari.eti.br/correios/webservice/?q=PB151832535BR&callback=minhaFuncJs

// Carrega o código carregado por query-string e outros parametros
$codigo = isset($_REQUEST['q']) ? $_REQUEST['q'] : '';
$formato = isset($_REQUEST['f']) ? $_REQUEST['f'] : '';
$jsonp = isset($_REQUEST['callback']) ? $_REQUEST['callback'] : '';

// valida o formato
if (!preg_match('@json|serial|dump|xml@', $formato)) $formato = 'json';

// variavel q armazena o obj
$obj = null;

// Valida o código
if (preg_match('@[A-Z0-9]{13}@', $codigo)){
	// incluimos a classe
	include_once '../correio.php';

	// Cria nome de arquivo de cache
	$cache_file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $codigo ;

	// Se o cache existir e tiver menos de 5 minutos de vida
	if (file_exists($cache_file) && date('U') - filemtime($cache_file) < 300){
		// Retorna o cache
		$obj = unserialize(file_get_contents($cache_file));
		$obj->cached = true;
	}else{
		// Senão, consulta...
		$obj = new Correio($codigo);
		// .. e renova o cache
		file_put_contents($cache_file, serialize($obj));

		$obj->cached = false;
	}
}else{
	// Retorna erro de código inválido
	$obj = json_decode('{"hash":null,"track":null,"status":null,"erro":true,"formato":"json","erro_msg":"C\u00f3digo de encomenda Inv\u00e1lido!"}');
}

// Muda cabeçalho padrão de content para texto simples
header("Content-Type: text/plain");

// Retorna no formato solicitado
switch ($formato){
	case 'serial':
		exit (serialize($obj));
	case 'dump':
		print_r($obj);
		exit ();
	case 'xml':
		header("Content-Type: text/xml");
		include_once 'x2xml.php';
		exit(x2xml($obj));
	case 'json':
	default:
		if ($jsonp) exit ($jsonp . '(' . json_encode($obj) . ')');
		exit (json_encode($obj));
}
