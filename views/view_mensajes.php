<?php if($this->session->flashdata('errores') || !empty($mostrar_errores)) { ?>
<ul id="mensajes_error">
<?php if( $this->session->flashdata('errores') ) { ?>
<?php foreach($this->session->flashdata('errores') as $error) { ?>
	<li><?php echo $error; ?></li>
<?php } ?>
<?php } ?>
<?php if( isset($mostrar_errores) ) { ?>
<?php foreach($mostrar_errores as $error) { ?>
        <li><?php echo $error; ?></li>
<?php } ?>
<?php } ?>
</ul>
<?php } ?>
