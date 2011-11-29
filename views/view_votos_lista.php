<?php include('view_overall_header.php') ?>
<h1 class="voto_title">Votos</h1>
<?php include('view_mensajes.php'); ?>
<table class="tabla_lista" style="width: 800px; margin: 0 auto;">
<tr style="background-color: #DFDFDF; border-bottom: 1px solid #EFEFEF;">
	<th style="width: 80px;">ID</th>
	<th>T&iacute;tulo</th>
	<th style="width: 150px;">Fecha de inicio</th>
	<th style="width: 150px;">Fecha de t&eacute;rmino</th>
	<th style="width: 260px;" colspan="2">Opciones</th>
</tr>
<?php foreach($votos as $key => $row) { ?>
<tr>
	<td><?php echo $row['voto_id']; ?></td>
	<td><?php echo $row['voto_titulo']; ?></td>
	<td><?php echo $row['voto_fecha_inicio']; ?></td>
	<td><?php echo $row['voto_fecha_termino']; ?></td>
	<td style="width: 70px;"><a href="<?php echo site_url('/votos/ver/' . $row['voto_id']); ?>">Ver</a></td>
	<td style="width: 90px;"><a href="<?php echo site_url('/votos/resultados/' . $row['voto_id']); ?>">Resultados</a></td>
</tr>
<?php } ?>
</table>
<?php include('view_overall_footer.php') ?>
