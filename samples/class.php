<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE html>
<html>

	<head>
		<title>Rastreamento nos correios</title>
		<link rel="stylesheet" type="text/css" href="style.css" />
	</head>

	<body>
		<?php
			$code = @$_REQUEST['code'];
		?>
		<h1>Rastreamento<?php echo $code ? ': ' . $code : ''?></h1>
		
		<form>
		<fieldset><legend>Pesquisar</legend>
			<p><label>Código para rastreamento:</label> <input type="text" size="14" maxlength="13" name="code" value="<?php echo $code ? $code : 'PB151832535BR'?>" />
			<button>Pesquisar!</button>
		</fieldset>
		</form>
		<?php
		if ($code):
		include_once '../correio.php';
		$c = new Correio($code);
		if (!$c->erro):
		?>

		<h2>Status: <?php echo $c->status ?></h2>
		
		<table>
			<tr>
				<td>Data</td>
				<td>Local</td>
				<td>Ação</td>
				<td>Detalhes</td>
			</tr>
			<?php foreach ($c->track as $l): ?>
				<tr>
					<td><?php echo $l->data ?></td>
					<td><?php echo $l->local ?></td>
					<td><?php echo $l->acao ?></td>
					<td><?php echo $l->detalhes ?></td>
				</tr>
			<?php endforeach; ?>
		</table>
		<?php else: ?>
		<?php echo $c->erro_msg ?>
		<?php endif; endif;?>
	</body>
</html>
