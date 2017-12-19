<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Sys_Controller  {

	var $usuario_login;

	function __construct()
  {
  	parent::__construct(FALSE);
		
  }


	public function index()
	{
		if(isset($this->usuario_login) && $this->usuario_login !== FALSE){
			redirect('/admin/home', 'refresh');
		}else{
			$this->load->view('sipcop_login_ant');
		}
		
	}

	public function validar(){
		$codigo = $this->input->post('usr_codigo');
		$clave = $this->input->post('usr_clave');
		$submit = $this->input->post('action'); //login o token
		$usr_tokensms = $this->input->post('usr_tokensms');

		$result = array();
		if(isset($codigo) && trim($codigo)!='' && isset($clave) && trim($clave)!=''){
			$login = $this->m_usuario->get_byLogin($codigo, $clave, 'IDUSUARIO, FLGACTIVO, IDROL, CELULAR, CKSMS');
			if($login){
				if($login['FLGACTIVO'] == '1'){
					if($login['CKSMS'] != '1'){
						$result['status'] = 'success';
						$result['msg'] = 'Datos de accesos correctos...';						
						$idsesion = $this->_registrarSesion($login['IDUSUARIO']);
						if($idsesion > 0){
							$this->session->set_userdata('usuario_login', $login['IDUSUARIO']);
							$this->session->set_userdata('usuario_sesion', $idsesion);
						}else{
							$result['status'] = 'error';
							$result['msg'] = 'Ocurrió un error al registrar sesión';
						}
					}else{
						$sesion = $this->m_sesion->get_SesionActiva($login['IDUSUARIO']);
						if(!$sesion){
							if($submit == 'login'){
								$tokensms = $this->m_tokensms->get_TokenActivo($login['IDUSUARIO']);
								if($tokensms){
									$result['status'] = 'confirm';
									$result['tiempo'] = (int)@$tokensms['TIEMPO'];
								}else{
									if(isset($login['CELULAR']) && strlen(trim($login['CELULAR'])) == 9){
										if($this->_registrarTokenSMS($login['IDUSUARIO'],$login['CELULAR']) > 0){
											$result['status'] = 'confirm';
											$result['tiempo'] = $this->tokensms_duration;
										}else{
											$result['status'] = 'error';
											$result['msg'] = 'Ocurrió un error al generar código';
										}
									}else{
										$result['status'] = 'error';
										$result['msg'] = 'No tiene número de celular configurado';
									}
								}
							}elseif($submit == 'token'){
								if(strlen(trim($usr_tokensms)) == 6){
									$tokensms = $this->m_tokensms->get_TokenActivo($login['IDUSUARIO'], $usr_tokensms);
									if($tokensms){
										$result['status'] = 'success';
										$result['msg'] = 'Datos de accesos correctos...';
										$this->session->set_userdata('usuario_login', $login['IDUSUARIO']);
										$idtokensms = $this->m_tokensms->edit_TokenSMS($tokensms['IDTOKENSMS']);
										$idsesion = $this->_registrarSesion($login['IDUSUARIO'], $tokensms['IDTOKENSMS']);
										if($idsesion > 0){
											$this->session->set_userdata('usuario_login', $login['IDUSUARIO']);
											$this->session->set_userdata('usuario_sesion', $idsesion);
										}else{
											$result['status'] = 'error';
											$result['msg'] = 'Ocurrió un error al registrar sesión';
										}
									}else{
										$result['status'] = 'error';
										$result['msg'] = 'Código inválido';
									}
								}else{
									$result['status'] = 'error';
									$result['msg'] = 'Código inválido';
								}
							}
						}else{
							$result['status'] = 'error';
							$result['msg'] = 'Debe cerrar sesión para volver a ingresar desde otro dispositivo';
						}
					}
				}else{
					$result['status'] = 'error';
					$result['msg'] = 'Usuario no permitido';
				}
			}else{
				$result['status'] = 'error';
				$result['msg'] = 'Datos de acceso incorrecto';
			}
		}else{
			$result['status'] = 'error';
			$result['msg'] = 'Ingrese usuario y/o clave';
		}

		echo json_encode($result);
	}

	public function salir(){
		$idsesion = $this->usuario_sesion;
		$this->m_sesion->finalizar($idsesion,2,$this->_getIP(),$this->_esDispositivoMovil(), $this->_getDispositivo());
		$this->session->set_userdata('usuario_login', FALSE);
		$this->session->set_userdata('usuario_sesion', FALSE);
		redirect('/login', 'refresh');
	}
}
