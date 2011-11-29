<?php
/**
 * CLUCH - Sistema de votaciones online
 * @package votaciones
 * @copyright Copyright (c) 2011, Cultura Libre Universidad de Chile
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public
 * License Version 3
 */

if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script.');

/**
 * Clase Controlador: Usuario
 *
 * Encargada del registro y validacion de los usuarios al momento de la
 * votacion.
 * @package votaciones
 * @subpackage controladores
 */
class Usuario extends CI_Controller
{
	/**
	 * Obtiene el ID de la sesion PHP para transformarla en una sesion
	 * CodeIgniter y posteriormente guardar los datos a traves del modelo
	 * Usuario.
	 * @access public
	 */
	function upasaporte()
	{
		// Obtener el ID de la sesion PHP
		$sid = $this->uri->segment(3);

		// Comprobar que el ID no sea vacio
		if(strlen($sid) == 0)
		{
			$this->load->helper('url');
			$this->session->set_flashdata('errores',
				array("No se ha encontrado la sesi&oacute;n U-Pasaporte.")
			);
			redirect('/', 'location');
		}
		else
		{
			session_id($sid);
			session_start();

			// Comprobar que existan los datos de la sesion PHP
			if( ! $_SESSION)
			{
				$this->load->helper('url');
				$this->session->set_flashdata('errores',
					array("No se han encontrado los datos de usuario.")
				);
				redirect('/', 'location');
			}
			else
			{
				// Cambiar de sesion PHP a sesion CodeIgniter
				session_regenerate_id(TRUE);
				$datos = $_SESSION;
				unset($_SESSION);
				session_destroy();
				unset($sid);

				$this->load->model('Usuario_Model', 'Usuario');
				$registrado = $this->Usuario->registrar($datos);

				$this->load->helper('url');

				if( ! $registrado)
				{
					$this->session->set_flashdata('errores',
						array("No se ha podido registrar el usuario. Ingrese nuevamente.")
					);
					redirect('/', 'location');	
				}
				else
				{
					redirect('/votos/lista', 'location');
				}
			}
		}
	}

	/**
	 * Destruye la sesion CodeIgniter y redirecciona al index.
	 * @access public
	 */
	function salir()
	{
		$this->session->sess_destroy();
		$this->load->helper('url');
		redirect('/', 'location');
	}
}

/* End of file usuario.php */
/* Location: controllers/usuario.php */