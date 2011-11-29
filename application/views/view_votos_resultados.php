<?php include('view_overall_header.php'); ?>
<h1 class="voto_title"><?php echo $resultados['voto_id'] . ": " . htmlentities(utf8_decode($resultados['voto_titulo'])); ?></h1>
<div style="width: 100%; margin: 0 auto;">
<?php foreach($resultados['votos_preguntas'] as $key => $row) { ?>
<?php $divisor = ($row['total'] > 0) ? $row['total'] : 1; ?>
<table class="tabla" style="display: inline-block; margin: 0 25px 25px 25px; width: 300px; vertical-align: middle; *display: inline;">
<tr><th colspan="3"><?php echo htmlentities(utf8_decode($row['texto'])); ?></th></tr>
<tr>
	<th style="width: 100px;">Opci&oacute;n</th>
	<th style="width: 100px;">Cantidad</th>
	<th style="width: 100px;">%</th>
</tr>
<?php foreach($resultados['votos_preguntas'][$key]['opciones'] as $key2 => $row2) { ?>
<tr>
	<td><?php echo htmlentities(utf8_decode($row2['texto'])); ?></td>
	<td><?php echo htmlentities(utf8_decode($row2['cantidad'])); ?></td>
	<td><?php echo sprintf("%.2f", $row2['cantidad'] / $divisor * 100); ?></td>
</tr>
<?php } ?>
<tr style="font-weight: bold;">
        <td>Total</td>
        <td><?php echo htmlentities(utf8_decode($row['total'])); ?></td>
        <td><?php echo sprintf("%.2f", $row['total'] / $divisor * 100); ?></td>
</tr>
</table>
<?php } ?>
</div>
<table class="tabla" style="width: 300px; margin: 30px auto;">
<?php $divisor = ($resultados['voto_total'] > 0) ? $resultados['voto_total'] : 1; ?>
<tr><th colspan="3">Detalles de la votaci&oacute;n</th></tr>
<tr>
	<th style="width: 100px;">Votos</th>
	<th style="width: 100px;">Cantidad</th>
	<th style="width: 100px;">%</th>
</tr>
<?php foreach($resultados['total_por_tipo'] as $key => $row) { ?>
<tr>
	<td><?php echo ucwords($row['tipo']); ?></td>
	<td><?php echo $row['cantidad']; ?></td>
	<td><?php echo sprintf("%.2f", $row['cantidad'] / $divisor * 100); ?></td>
</tr>
<?php } ?>
<tr style="font-weight: bold;">
	<td>Total</td>
	<td><?php echo $resultados['voto_total']; ?></td>
	<td><?php echo sprintf("%.2f", $resultados['voto_total'] / $divisor * 100); ?></td>
</tr>
</table>
<a href="<?php echo site_url('votos/lista'); ?>">Volver al listado de votos</a>
<?php include('view_overall_footer.php') ?>
