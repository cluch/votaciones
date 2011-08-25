<?php include('view_overall_header.php'); ?>
<div style="display: inline-block; vertical-align: top;">
<h1 class="voto_title">Estado del sufragio</h1>
<?php include('view_mensajes.php'); ?>
<table class="tabla" style="width: 300px; margin: 0 auto;">
<tr>
	<td>TIPO_VOTO</td>
	<td><?php echo $estado['TIPO_VOTO']; ?></td>
</tr>
<?php foreach($estado['COMPROBACIONES'] as $key => $value) { ?>
<tr>
	<td><?php echo $key; ?></td>
<?php
$color = ($value) ? "color: #00CC00;" : "color: #EE0000;";
$texto = ($value) ? "OK" : "FAILED";
echo "\t<td style=\"$color\">$texto</td>\n";
?>
</tr>
<?php } ?>
</table>
</div>
<?php if($estado['DATOS_CRIPT']) { ?>
<div id="llave_privada">
<h1 class="voto_title">Llave privada</h1>
<div class="llave">
<?php echo nl2br($estado['DATOS_CRIPT']['LLAVE_PRIVADA']); ?>
</div>
<?php } ?>
</div>
<?php if($estado['DATOS_CRIPT']) { ?>
<div>
<h1 class="voto_title">Firma digital</h1>
<div class="llave">
<?php echo $estado['DATOS_CRIPT']['FIRMA_DIGITAL']; ?>
</div>
</div>
<?php } ?>
<div style="margin-top: 30px;">
	<a href="<?php echo site_url('votos/lista'); ?>">Volver al listado de votos</a>
</div>
<?php include('view_overall_footer.php'); ?>
