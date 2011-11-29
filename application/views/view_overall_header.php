<?php $this->load->helper('url'); ?>
<?php header('Content-type: text/html; charset=utf-8'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?php echo $titulo; ?></title>
	<link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/style/style.css"); ?>" />
	<link rel="stylesheet" type="text/css" href="<?php echo site_url("assets/style/style_print.css"); ?>" media="print" />
</head>
<body>
<?php if($this->session->userdata('logged_in')) { ?>
<div id="menu-superior">
	<ul id="menu-superior-lista">
<?php if( $this->session->userdata('sufragio_hash') ) echo "\t\t<li>" . $this->session->userdata('sufragio_hash') . "</li>\n"; ?>
		<li><?php echo $_SERVER['REMOTE_ADDR']; ?></li>
		<li>Mesa <?php echo $this->session->userdata('sufragio_mesa_id') . ": " . $this->session->userdata('sufragio_mesa_nombre'); ?></li>	
		<li><?php echo $this->session->userdata('usuario_nombre_completo'); ?></li>
		<li><a href="<?php echo site_url("usuario/salir"); ?>">Salir</a></li>
	</ul>
</div>
<?php } ?>
<div id="contenedor">
