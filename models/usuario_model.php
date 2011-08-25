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
class Usuario_model extends CI_Model
{
	function __construct() {
		parent::__construct();
	}

	/**
	 * Registra los datos de usuario en la sesion de CodeIgniter (CI)
	 * @access public
	 * @param array $datos Arreglo con los datos de usuario
	 * @return bool Boolean indicando si el registro se efectuo correctamente
	 */
	function registrar($datos)
	{
		if( ! $datos)
		{
			// En caso de que $datos no contenga informacion
			return FALSE;
		}
		else
		{
			// Destruir la sesion CI actual y crear una nueva
			$this->session->sess_destroy();
			$this->session->sess_create();
	
			/*
			 * Buscar el ultimo codigo de carrera FCFM disponible
			 * De no existir, asignar 000: Sin datos FCFM
			 */
			$carrera_id = end(array_keys($datos['carreras']));
			$carrera_nombre = ($carrera_id) ? htmlentities($datos['carreras'][$carrera_id]) : "Sin datos FCFM";
			$carrera_id = ($carrera_id) ? $carrera_id : "000";

			// Guardar los datos en la sesion CI
			$this->session->set_userdata(array(
				'usuario_hash'				=> sha1($datos['nombre_completo'] . $datos['rut']),
				'usuario_nombre_completo'	=> htmlentities($datos['nombre_completo']),

				'sufragio_mesa_id'			=> $carrera_id,
				'sufragio_mesa_nombre'		=> $carrera_nombre,
				
				'logged_in_time'			=> time(),
				'logged_in'					=> TRUE
			));

			return TRUE;
		}
	}

	/**
	 * Valida al usuario al momento de ingresar al formulario de votacion, registrando en la session CodeIgniter (CI) el hash de sufragio junto con la hora.
	 * @access public
	 */
	function validar()
	{
		if( ! $this->session->userdata('logged_in'))
		{
			// Si no ha ingresado, devolver al index
			$this->session->set_flashdata('errors', array("Sesion expirada. Ingresar nuevamente via U-Pasaporte."));
			$this->load->helper('url');
			redirect('/', 'location');
		}
		else
		{
			// Cargar libreria de seguridad
			$this->load->library('seguridad');

			// Obtener el ID de la sesion CI actual y generar un hash al azar
			$sid = $this->session->userdata('session_id');
			$random_hash = $this->seguridad->random_hash();

			// Registrar en la sesion CI
			$this->session->set_userdata(array(
				'sufragio_hash_time'	=> time(),
				'sufragio_hash'			=> sha1($random_hash . sha1($sid) . sha1(time()))
			));

			/*
			 * Redireccionar en caso de que exista una URL registrada en la variable flash 'redireccionar'
			 */

			if( $this->session->flashdata('redireccionar') )
			{
				$this->load->helper('url');
				redirect($this->session->flashdata('redireccionar'), 'location');
			}
		}
	}
}

/* End of file usuario_modelo.php */
/* Location: models/usuario_modelo.php */