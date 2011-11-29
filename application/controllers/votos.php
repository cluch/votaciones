<?php
/** 
 * CLUCH - Sistema de votaciones online
 * @package votaciones
 * @copyright Copyright (c) 2011, Cultura Libre Universidad de Chile
 * @license http://www.gnu.org/licenses/lgpl.txt GNU Lesser General Public License Version 3
 */

if ( ! defined('BASEPATH')) exit('No se permite el acceso directo al script.');

/**
 * Clase Controlador: Votos
 *
 * Esta clase se encarga de recibir las consultas y obtener datos a traves de
 * los modelos para finalmente desplegarlos mediante las vistas.
 * @package votaciones
 * @subpackage controladores
 */
class Votos extends CI_Controller
{
	/**
	 * Redirecciona a la lista de votos si el usuario ha ingresado
	 * correctamente.
	 * @access public
	 */
	function index()
	{
		if($this->session->userdata('sufragio_hash'))
		{
			$this->session->unset_userdata('sufragio_hash');
		}
		$this->load->helper('url');
		redirect('/votos/lista', 'location');
	}

	/**
	 * Obtiene los datos de los votos disponibles mediante el modelo Voto y
	 * muestra la lista de ellos a traves de la vista votos_lista.
	 * @access public
	 */
 	function lista()
	{
		if( ! $this->session->userdata('logged_in'))
		{
			$this->load->helper('url');
			$this->session->set_flashdata('errores',
				array("Acceso denegado. Debe ingresar v&iacute;a U-Pasaporte.")
			);
			redirect('/', 'location');
		}
		else
		{
			if($this->session->userdata('sufragio_hash'))
			{
				$this->session->unset_userdata('sufragio_hash');
			}
			
			$this->load->model('Voto_Model', 'Voto');
			$votos = $this->Voto->lista();

			$output = array(
				'titulo'	=> "Listado de votos",
				'votos'		=> $votos
			);
			
			$this->load->view('view_votos_lista', $output);
		}
	}

	/**
	 * Recibe el HTTP POST a traves de CodeIgniter y guarda los datos con el
	 * modelo Voto. Posteriormente despliega el resultado en la vista
	 * votos_emitir.
	 * @access public
	 */
	function emitir()
	{

		$voto_id = $this->input->post('voto_id');
		$this->load->helper('url');

		if( ! $this->session->userdata('logged_in'))
		{
			$this->session->set_flashdata('errores',
				array("Acceso denegado. Debe Ingresar v&iacute;a U-Pasaporte.")
			);
			redirect('/', 'location');
		}
		else if( ! $voto_id)
		{
			$this->session->set_flashdata('errores',
				array("Debe indicar el n&uacute;mero de voto")
			);
			redirect('/votos/lista', 'location');
		}
		else if( ! $this->session->userdata('sufragio_hash')
			|| time() - $this->session->userdata('sufragio_hash_time') > 600)
		{
			$this->session->set_flashdata('errores',
				array("Acceso denegado. Debe Ingresar v&iacute;a U-Pasaporte.")
			);
			redirect('/votos/ver/' . $voto_id, 'location');
		}

		$this->load->model('Voto_Model', 'Voto');
		$voto = $this->Voto->obtener($voto_id);

		$opciones = $this->input->post('opciones');
		$estado = $this->Voto->emitir($voto_id, $opciones);

		$this->session->unset_userdata('sufragio_hash');

		$output = array(
			'titulo'		=> "Estado del sufragio",
			'estado'		=> $estado
		);

		$this->load->view('view_votos_emitir', $output);
	}
	
	/**
	 * El usuario debe confirmar el voto a emitir.
	 * @access public
	 */
	function confirmar()
	{
		$this->ver();
	}

	/**
	 * Recopila la informacion para un voto en particular utilizando el modelo Voto y muestra el formulario a traves de la vista votos_ver.
	 * @access public
	 */
	function ver()
	{
		$accion = $this->uri->segment(2);

		if($accion == 'ver')
		{
			$voto_id = $this->uri->segment(3);
			$accion = 'confirmar';
		}
		else if($accion == 'confirmar')
		{
			$voto_id = $this->input->post('voto_id');
			$accion = 'emitir';
		}
		
		if( ! $this->session->userdata('logged_in'))
		{
			$this->load->helper('url');
			$this->session->set_flashdata('errores', array("Acceso denegado. Debe Ingresar v&iacute;a U-Pasaporte."));
			redirect('/', 'location');
		}

		if ( ( $accion == 'confirmar' && ! $this->session->userdata('sufragio_hash') ) || time() - $this->session->userdata('sufragio_hash_time') > 600)
		{
			$this->load->model('Usuario_Model', 'Usuario');
			$this->Usuario->validar();
		}
		
		//Obtener los datos del voto a traves del modelo correspondiente
		$this->load->model('Voto_Model', 'Voto');
		$voto = $this->Voto->obtener($voto_id);

		if( ! $voto)
		{
			$this->load->helper('url');
			$this->session->set_flashdata('errores', array("El voto solicitado no existe."));
			redirect('/votos/lista', 'location');
		}
		else
		{
			if($accion == 'emitir')
			{
				$opciones = $this->input->post('opciones');
				$sufragio_hash = $this->session->userdata('sufragio_hash');
				list($voto['valido'], $voto['tipo']) = $this->Voto->validar($voto_id, $opciones, $sufragio_hash);

				$tipos = array('Blanco', 'Válido', 'Nulo');
				$voto['tipo'] = $tipos[$voto['tipo']];

				foreach ($voto['preguntas'] as $key => $value)
				{
					foreach ($voto['preguntas'][$key]['opciones'] as $key2 => $value2)
					{
						$opcion = &$voto['preguntas'][$key]['opciones'][$key2];

						if(isset($opciones[$key2]))
						{
							$opcion['marcado'] = $opcion['valor'] == $opciones[$key2];
						}
						else $opcion['marcado'] = FALSE;
					}
				}
			}
			
			$output = array(
				'accion'	=> $accion,
				'titulo'	=> "Voto {$voto['voto_id']}: {$voto['voto_titulo']}",
				'voto'		=>	$voto,
				'hidden'	=>	array(
									'voto_id'	=> $voto['voto_id']
								)
			);
						
			$this->load->view('view_votos_ver', $output);
		}
	}

	/**
	 * Despliega los resultados para un voto, obteniendo los datos a traves del modelo Voto y posteriormente utilizando la vista votos_resultados.
	 * @access public
	 */
	function resultados()
	{
		if( ! $this->session->userdata('logged_in'))
		{
			$this->load->helper('url');
			$this->session->set_flashdata('errores', array("Acceso denegado. Debe Ingresar v&iacute;a U-Pasaporte."));
			redirect('/', 'location');
		}
		else
		{
			if($this->session->userdata('sufragio_hash')) $this->session->unset_userdata('sufragio_hash');
			$id = $this->uri->segment(3);

			// Obtener arreglo a traves del modelo
			$this->load->model('Voto_Model', 'Voto');
			$resultados = $this->Voto->resultados($id);

			if( ! $resultados)
			{
				$this->load->helper('url');
				$this->session->set_flashdata('errores', array("El voto indicado no existe."));
				redirect('/votos/lista', 'location');
			}
			else
			{
				$output = array(
					'titulo'		=>	"Resultados del voto {$id}",
					'resultados'	=>	$resultados
				);

				$this->load->view('view_votos_resultados', $output);
			}
		}
	}
}

/* End of file votos.php */
/* Location: controllers/votos.php */
