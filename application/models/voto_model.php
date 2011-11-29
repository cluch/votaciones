<?php
/**
 * CLUCH - Sistema de votaciones online
 * @package votaciones
 * @copyright Copyright (c) 2011, Cultura Libre Universidad de Chile
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License Version 3
 */

if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script.');

/**
 * Clase Modelo: Usuario
 * @package votaciones
 * @subpackage modelos
 */
class Voto_model extends CI_Model
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Obtiene lista con votos disponibles.
	 * @access public
	 * @return array Listado de votos
	 */
	function lista()
	{
		$query = $this->db->query("SELECT voto_id, voto_titulo, voto_fecha_inicio, voto_fecha_termino FROM modelo_votos ORDER BY voto_id DESC");
		return $query->result_array();
	}

	/**
	 * Obtiene resultados del voto.
	 * @access public
	 * @param int $voto_id ID del voto
	 * @return array Arreglo con los resultados por pregunta y totales del voto.
	 */
	function resultados($voto_id)
	{
		if( ! is_numeric($voto_id)) return array();

		// Tipo de votos definidos
		$tipos = array(	"1" => array( 'tipo' => 'validos', 'cantidad' => 0 ), 
						"2" => array( 'tipo' => 'nulos',   'cantidad' => 0 ), 
						"0" => array( 'tipo' => 'blancos', 'cantidad' => 0 )
		);
		
		$query = $this->db->query("SELECT voto_id, voto_titulo FROM modelo_votos WHERE voto_id = {$voto_id}");
		$voto = $query->row_array();

		if(!$voto)
		{
			// Si no existe el voto
			return array();
		}
		else
		{
			$resultados = array();
			$resultados['voto_id'] = $voto['voto_id'];
			$resultados['voto_titulo'] = $voto['voto_titulo'];

			// Obtener los datos de las preguntas y las opciones
			$consulta = "SELECT p.pregunta_id, p.pregunta_texto, o.opcion_id, o.opcion_texto
					FROM modelo_opciones AS o
						INNER JOIN modelo_preguntas AS p ON (p.pregunta_id = o.opcion_pregunta_id)
					WHERE p.pregunta_voto_id = {$voto_id}";

			$query = $this->db->query($consulta);

			foreach ($query->result_array() as $row)
			{
				$pregunta = &$resultados['votos_preguntas'][$row['pregunta_id']];
				$pregunta['texto'] = $row['pregunta_texto'];
				$pregunta['total'] = 0;

				$opcion = &$resultados['votos_preguntas'][$row['pregunta_id']]['opciones'][$row['opcion_id']];
				$opcion['texto'] = $row['opcion_texto'];
				$opcion['cantidad'] = 0;
			}

			// Obtener detalle de los votos emitidos y sumar para obtener el total
			$consulta = "SELECT * FROM sufragios_votos WHERE voto_id = {$voto_id}";
			$query = $this->db->query($consulta);

			$resultados['voto_total'] = 0;

			$this->load->library('seguridad');

			foreach ($query->result_array() as $row)
			{
				$opciones = $this->seguridad->desencriptarDatos($row['voto_datos_encriptados'], $row['voto_llave_publica']);
				$valido = $this->validar($voto_id, $opciones, $row['sufragio_hash']);

				$tipos[$valido[1]]['cantidad']++;

				if($valido[1] == 1)
				{
					foreach ($opciones as $opcion_hash => $valor_hash)
					{
						$datos = $this->buscarOpcion($row['sufragio_hash'], $opcion_hash, $valor_hash);

						$resultados['votos_preguntas'][$datos['pregunta_id']]['total']++;
						$resultados['votos_preguntas'][$datos['pregunta_id']]['opciones'][$datos['opcion_id']]['cantidad']++;
					}
				}

				$resultados['voto_total']++;
			}

			$resultados['total_por_tipo'] = $tipos;

			return $resultados;
		}
	}

	/**
	 * Obtiene datos del voto
	 * @access public
	 * @param int $id ID del voto
	 * @return array Arreglo con los datos del voto
	 */
	function obtener($id)
	{
		if(!is_numeric($id)) return array();
		$hash_sufragio = $this->session->userdata('sufragio_hash');
		$query = $this->db->query("SELECT voto_id, voto_titulo, voto_texto FROM modelo_votos WHERE voto_id = {$id}");

		if($query->num_rows() == 0)
		{
			return array();
		}
		else
		{
			$voto = $query->row_array();

			$consulta = "SELECT SHA1( CONCAT( '$hash_sufragio', v.voto_hash_privado ) ) AS voto_hash_publico,
				p.pregunta_texto, p.pregunta_limite, o.opcion_texto,
				SHA1( CONCAT( '$hash_sufragio', p.pregunta_hash_privado ) ) AS pregunta_hash_publico,
				SHA1( CONCAT( '$hash_sufragio', v.voto_hash_privado, p.pregunta_hash_privado, o.opcion_hash_privado ) ) AS opcion_hash_publico,
				SHA1( CONCAT( '$hash_sufragio', v.voto_hash_privado, p.pregunta_hash_privado, o.opcion_hash_privado, SHA1( o.opcion_texto ) ) ) AS opcion_valor
				FROM modelo_opciones as o
				INNER JOIN modelo_preguntas as p ON (p.pregunta_id = o.opcion_pregunta_id)
				INNER JOIN modelo_votos as v ON (v.voto_id = p.pregunta_voto_id)
				WHERE v.voto_id = {$voto['voto_id']}";

			$query = $this->db->query($consulta);

			if($query->num_rows() > 0)
			{
				foreach ($query->result_array() as $row)
				{
					$pregunta = &$voto['preguntas'][$row['pregunta_hash_publico']];
					$pregunta['limite'] = $row['pregunta_limite'];
					$pregunta['texto']  = $row['pregunta_texto'];

					$opcion = &$voto['preguntas'][$row['pregunta_hash_publico']]['opciones'][$row['opcion_hash_publico']];
					$opcion['texto'] = $row['opcion_texto'];
					$opcion['valor'] = $row['opcion_valor'];
				}
			}

			return $voto;
		}
	}

	/**
	 * Busca en la base de datos el ID de pregunta y opcion correspondientes a los datos hasheados.
	 * @access private
	 * @param string $hash_opcion ID de la opcion hasheado
	 * @param string $hash_valor Valor de la opcion hasheado
	 * @return array Arreglo con las llaves 'opcion_id', 'pregunta_id' y 'pregunta_limite'
	 */
	private function buscarOpcion($sufragio_hash, $opcion_hash, $valor_hash)
	{
		if( strlen($sufragio_hash) != 40 || strlen($opcion_hash) != 40 || strlen($valor_hash) != 40 )
		{
			return array();
		}
		else
		{
			$consulta = "SELECT o.opcion_id, p.pregunta_id, p.pregunta_limite
					FROM modelo_opciones as o
						INNER JOIN modelo_preguntas as p ON (p.pregunta_id = o.opcion_pregunta_id)
						INNER JOIN modelo_votos as v ON (v.voto_id = p.pregunta_voto_id)
					WHERE SHA1( CONCAT( '{$sufragio_hash}', v.voto_hash_privado, p.pregunta_hash_privado, o.opcion_hash_privado ) ) = '%s'
						AND SHA1( CONCAT( '{$sufragio_hash}', v.voto_hash_privado, p.pregunta_hash_privado, o.opcion_hash_privado, SHA1( o.opcion_texto ) ) ) = '%s'";

			$query = $this->db->query(sprintf($consulta, $opcion_hash, $valor_hash));
			return $query->row_array();
		}
	}

	/**
	 * Valida el voto y las opciones contra la base de datos segun el ID de pregunta y opcion correspondientes a los datos hasheados.
	 * @access public
	 * @param int $id ID del voto
	 * @param array $opciones Opciones del voto a comprobar
	 * @return array Arreglo con la tupla (voto valido, tipo de voto)
	 */
	function validar($id, $opciones, $sufragio_hash)
	{
		if( ! is_numeric($id)) return array(FALSE, 0);
		$query = $this->db->query("SELECT voto_id FROM modelo_votos WHERE voto_id = {$id}");
		$voto = $query->row_array();

		if($voto)
		{
			$voto_valido = TRUE;
			if($opciones)
			{
				$preguntas = array();

				foreach ($opciones as $key => $value)
				{
					$datos = $this->buscarOpcion($sufragio_hash, $key, $value);

					if($datos)
					{
						$voto_valido = $voto_valido && TRUE;
						$preguntas[$datos['pregunta_id']]['suma'] = (isset($preguntas[$datos['pregunta_id']])) ? $preguntas[$datos['pregunta_id']]['suma'] + 1 : 1;
						$preguntas[$datos['pregunta_id']]['limite'] = $datos['pregunta_limite'];
					}
					else
					{
						$voto_valido = $voto_valido && FALSE;
					}
				}

				// Comprobar el limite de las preguntas
				$limites_validos = TRUE;
				foreach ($preguntas as $key => $value)
				{
					$limites_validos = $limites_validos && $preguntas[$key]['suma'] <= $preguntas[$key]['limite']; 
				}

				return array($voto_valido, ($limites_validos) ? 1 : 2);
			}
			else return array(TRUE, 0);
		}
		else return array(FALSE, 0);
	}

	/**
	 * Guarda los datos en la base de datos, previa verificacion utilizando el metodo validar de la clase.
	 * @access public
	 * @param int $voto_id ID del voto
	 * @param array $opciones Opciones correspondientes al voto
	 * @return array Arreglo con datos referentes al voto y las validaciones            
	 */
	public function emitir($voto_id, $opciones)
	{
		$sufragio_hash = $this->session->userdata('sufragio_hash');
		$estado = array();
		$estado['DATOS_CRIPT'] = array();
		$texto_voto_tipo = array("BLANCO", "VALIDO", "NULO");

		// Validar las opciones y obtener el tipo de voto: 0 = blanco, 1 = valido, 2 = nulo
		list($voto_valido, $voto_tipo) = $this->validar($voto_id, $opciones, $sufragio_hash);

		$estado['TIPO_VOTO'] = $texto_voto_tipo[$voto_tipo];

		if( ! $voto_valido)
		{
			$estado['COMPROBACIONES']['VALIDAR_VOTO'] = FALSE;
			return $estado;
		}
		else
		{
			$estado['COMPROBACIONES']['VALIDAR_VOTO'] = TRUE;
		}

		$sufragio_mesa_id	= $this->session->userdata('sufragio_mesa_id');
		$sufragio_hash		= $this->session->userdata('sufragio_hash');
		$usuario_hash		= $this->session->userdata('usuario_hash');
		$usuario_ip			= $_SERVER['REMOTE_ADDR'];

		$consulta = "SELECT registro_id FROM sufragios_registros WHERE registro_user_hash = '{$usuario_hash}' AND registro_voto_id = '{$voto_id}'";
		$query = $this->db->query($consulta);

		// Si el usuario ya voto
		if($query->num_rows() > 0)
		{
			$estado['COMPROBACIONES']['VOTO_UNICO'] = FALSE;
			return $estado;
		}
		else
		{
			$estado['COMPROBACIONES']['VOTO_UNICO'] = TRUE;
		}

		/*
		 * Guardar en BD utilizando transacciones (SQL Atomicity).
		 * Para lo cual se requiere que el servidor MySQL soporte InnoDB.
		 */
		$this->db->trans_begin();

		$consulta = "INSERT INTO sufragios_registros (registro_fecha, registro_ip, registro_user_hash, registro_voto_id, registro_mesa_id)
			VALUES (NOW(), '{$usuario_ip}', '{$usuario_hash}', '{$voto_id}', '{$sufragio_mesa_id}')";
		$this->db->query($consulta);

		// Guardar el voto y las opciones, encriptadas.
		$this->load->library('seguridad');
		$info = $this->seguridad->encriptarDatos($opciones, "test");

		// Guardar voto en tabla sufragios
		$consulta = "INSERT INTO sufragios_votos (sufragio_hash, voto_id, voto_mesa_id, voto_llave_publica, voto_firma_digital, voto_datos_encriptados)
				VALUES ('{$sufragio_hash}', '{$voto_id}', '{$sufragio_mesa_id}', '{$info['llave_publica']}', '{$info['firma_digital']}', '{$info['datos_encriptados']}')";

		$this->db->query($consulta);

		// Si la transaccion fue incorrecta, revertir los cambios, de lo contrario confirmar la escritura.
		if( $this->db->trans_status() === FALSE )
		{
			$this->db->trans_rollback();
			$estado['COMPROBACIONES']['REGISTRAR_EN_BD'] = FALSE;
		}
		else
		{
			$this->db->trans_commit();
			$estado['DATOS_CRIPT']['LLAVE_PRIVADA'] = $info['llave_privada'];
			$estado['DATOS_CRIPT']['FIRMA_DIGITAL'] = $info['firma_digital'];
			$estado['COMPROBACIONES']['REGISTRAR_EN_BD'] = TRUE;
		}

		return $estado;
	}
}

/* End of file voto_modelo.php */
/* Location: models/voto_modelo.php */
