<?php 
/**
 * CLUCH - Sistema de votaciones online
 * @package votaciones
 * @copyright Copyright (c) 2011, Cultura Libre Universidad de Chile
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License Version 3
 */

if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script.');

/**
 * Clase Biblioteca: Seguridad
 * @package votaciones
 * @subpackage bibliotecas
 */
class Seguridad
{
	var $separador = "$";
	
	/**
	 * Retorna un hash SHA1 de datos generados al azar.
	 * @access public
	 * @return string hash SHA1
	 */
	function random_hash()
	{
		/* 
		 * /dev/random solo tiene 4kb de datos disponibles, por lo que
		 * momentaneamente se utilizara /dev/urandom.
		 */
		$hash = hash('sha1', fread(fopen('/dev/urandom', 'r'), 16));
		return $hash;
	}

	/**
	 * Encripta datos utilizando encriptacion asimetrica RSA y genera firma de validacion.
	 * @access public
	 * @param string $datos informacion a encriptar
	 * @param string $clave clave para proteger la llave privada
	 * @return array arreglo con las llaves 'llave_privada', 'llave_publica', 'firma_digital', 'datos_encriptados'
	 */
	function encriptarDatos($datos, $clave)
	{
		// Generar llave privada
		$recurso = openssl_pkey_new();

		// Exportar la llave privada con clave
		openssl_pkey_export($recurso, $llave_privada, $clave);

		// Obtener llave publica
		$llave_publica = openssl_pkey_get_details($recurso);
		$llave_publica = trim($llave_publica["key"]);

		// Serializar datos
		$datos = serialize($datos);

		// Obtener firma y transformarla a base64
		openssl_sign($datos, $firma_digital, $recurso, OPENSSL_ALGO_SHA1);
		$firma_digital = trim(base64_encode($firma_digital));

		// Serializar los datos
		$datos = str_split($datos, 245);
		$datos_encriptados = array();

		foreach ($datos as $valor)
		{
			// Encriptar datos con llave privada
			openssl_private_encrypt($valor, $valor_encriptado, $recurso);
			$datos_encriptados[] = trim(base64_encode($valor_encriptado));
		}

		return array(
			'llave_privada'		=> $llave_privada,
			'llave_publica'		=> $llave_publica,
			'firma_digital'		=> $firma_digital,
			'datos_encriptados'	=> implode($this->separador, $datos_encriptados)
		);
	}

	/**
	 * Comprueba que la firma digital coincida con los datos encriptados.
	 * @access public
	 * @param string $datos informacion encriptada a comprobar, en base64
	 * @param string $firma_digital firma digital en base64
	 * @param string $llave_publica llave publica asociada a los datos encriptados
	 * @return array tupla (boolean, mensaje)
	 */
	public function comprobarFirma($datos, $firma_digital, $llave_publica) {
		// Transformar los datos a base binaria y desencriptarlos
		$datos = base64_decode($datos);
		openssl_public_decrypt($datos, $datos_desencriptados, $llave_publica);

		// Transformar la firma digital a base binaria
		$firma_digital = base64_decode($firma_digital);

		// Comprobar que la firma digital coincida con los datos desencriptados
		$comprobar = openssl_verify($datos_desencriptados, $firma_digital, $llave_publica, OPENSSL_ALGO_SHA1);

		if ($comprobar == 1) {
			return array(TRUE, "La firma digital es correcta");
		} elseif ($comprobar == 0) {
			return array(FALSE, "La firma digital no coincide con los datos entregados");
		} else {
			return array(FALSE, "Ocurrio un problema al comprobar la firmar digital");
		}
	}

	/**
	 * Desencripta los datos mediante el metodo encriptarDatos
	 * @access public
	 * @param string $datos_encriptados informacion encriptada
	 * @param string $llave_publica llave publica asociada a los datos encriptados
	 * @return mixed objeto desencriptado
	 */
	function desencriptarDatos($datos_encriptados, $llave_publica)
	{
		$datos = explode($this->separador, $datos_encriptados);
		$datos_desencriptados = array();

		foreach ($datos as $dato)
		{
			openssl_public_decrypt(base64_decode($dato), $datos_desencriptados[], $llave_publica);
		}

		return unserialize(implode('', $datos_desencriptados));
	}
}

/* End of file Seguridad.php */
/* Location: libraries/Seguridad.php */
