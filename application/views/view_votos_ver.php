<?php include('view_overall_header.php'); ?>
<a href="<?php echo site_url('votos/lista'); ?>">Volver al listado de votos</a>
<h1 class="voto_title"><?php if($accion == 'emitir') echo "Confirmar - "; echo "{$voto['voto_id']} : " . htmlentities($voto['voto_titulo']); ?></h1>
<div style="margin-bottom: 30px; text-align: justify;">
<?php if($accion == 'confirmar') echo $voto['voto_texto'];
else if($accion == 'emitir') { ?>
<table class="tabla" style="width: 240px; margin: 30px auto 30px;">
<tr>
    	<td style="width: 120px; font-weight: bold; padding: 10px;">Tipo de voto</td>
        <td style="width: 120px; padding: 10px;"><?php echo $voto['tipo']; ?></td>
</tr>
</table>
<?php } ?>
</div>
<?php $this->load->helper('form'); ?>
<?php echo form_open("/votos/{$accion}", '', $hidden); ?>

<?php $counter = 0; ?>
<?php foreach($voto['preguntas'] as $key => $row) { ?>
<h3><?php echo ++$counter . ". " . htmlentities(utf8_decode($row['texto'])); ?></h3>
<div style="text-align: left; font-size: 12px;">
<?php 
echo "Puede seleccionar hasta {$row['limite']} ";
echo ($row['limite'] > 1) ? "opciones." : "opcion.";
?>
</div>
<div class="opciones">
<?php foreach($voto['preguntas'][$key]['opciones'] as $key2 => $row2) { ?>
	<div class="opcion">
<?php if($accion == 'emitir') { ?>
		<input name="opciones[<?php echo $key2 ?>]" type="hidden" value="<?php echo $row2['valor'] ?>" />
		<input id="opcion_<?php echo $key2 ?>" type="checkbox" <?php if($row2['marcado']) echo "checked=\"checked\""?> disabled="disabled" />
<?php } else { ?>
		<input id="opcion_<?php echo $key2 ?>" name="opciones[<?php echo $key2 ?>]" type="checkbox" value="<?php echo $row2['valor'] ?>" />
<?php } ?>
		<label for="opcion_<?php echo $key2 ?>"><?php echo htmlentities(utf8_decode($row2['texto'])); ?></label>
	</div>
<?php } ?>
</div>
<?php } ?>
<div style="margin-top: 10px;"><input type="submit" name="emitir" value="Votar" /></div>
</form>
<?php include('view_overall_footer.php'); ?>