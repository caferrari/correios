<?php header('Content-Type: text/html; charset=utf-8'); ?>
<!DOCTYPE HTML>
<html>

	<head>
		<title>Rastreamento nos correios</title>
	</head>

	<body>
		<h1>Rastreamento</h1>
		<?php
		include 'correio.php';
		$c = new Correio('PB151832535BR');
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
		<?php endif; ?>
	</body>
</html>
