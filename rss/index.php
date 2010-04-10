<?php
header('Content-Type: application/xml; charset=utf-8');

// Exemplo de uso:
// http://ferrari.eti.br/correios/rss/?PB151832535BR

// Carrega o código carregado por query-string e gera o rss
$codigo = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '' ;
include_once '../correio.php';
$c = new Correio($codigo);
if (preg_match('@[A-Z0-9]{13}@', $codigo)){
?>
<?php echo '<?xml version="1.0" encoding="utf-8"?>' ?>
<rss xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:sy="http://purl.org/rss/1.0/modules/syndication/" version="2.0">
    <channel>
		<title>Rastreando encomenda: <?php echo $codigo ?></title>
		<link>http://ferrari.eti.br</link>
		<description>Rastreador de encomendas dos correios, gerador de RSS</description>
		<language>pt-BR</language>
		<?php if (!$c->erro): ?>
		<?php $c->track = array_reverse($c->track) ?>
		<?php foreach ($c->track as $l): ?>	
		<item>
			<?php if ($l->acao == 'encaminhado'): ?>
			<title><![CDATA[Encomenda: <?php echo $codigo ?>: <?php echo $l->detalhes ?>]]></title>
			<?php else: ?>
			<title><![CDATA[Encomenda: <?php echo $codigo ?>: <?php echo $l->acao ?>]]></title>
			<?php endif; ?>
			<link>http://ferrari.eti.br/correios/samples/class.php?code=<?php echo $codigo ?></link>
			<description><![CDATA[Status da encomenda: <?php echo $codigo ?> alterado para <?php echo $l->acao ?>. <?php echo $l->detalhes ?>]]></description>
		</item>
		<?php endforeach; ?>
		<?php endif; ?>
		
	</channel>
</rss>
<?php } ?>
