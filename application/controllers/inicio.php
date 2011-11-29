<?php 
/**
 * CLUCH - Sistema de votaciones online
 * @package votaciones
 * @copyright Copyright (c) 2011, Cultura Libre Universidad de Chile
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License Version 3
 */

if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script.');

/**
 * Clase Controlador: Inicio
 * @package votaciones
 * @subpackage controladores
 */
class Inicio extends CI_Controller
{
	public function index()
	{
		if($this->session->userdata('logged_in'))
		{
			$this->load->helper('url');
			redirect('/votos/lista', 'location');
		}
		else
		{
			$output = array(
				'titulo'	=> "Votaciones online :: Inicio"
			);
			
			$this->load->view('view_inicio', $output);
		}
	}
}

/* End of file inicio.php */
/* Location: controllers/inicio.php */
