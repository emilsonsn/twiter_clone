<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action{

	public function index(){
		$this->render('index');
	}

	public function inscreverse(){
		$this->render('inscreverse');
	}

	public function registrar(){
		$usuario = Container::getModel('Usuario');
		$usuario->__set('nome', $_POST['nome']);
		$usuario->__set('email', $_POST['email']);
		$usuario->__set('senha', md5($_POST['senha']));
		$cadastroSucesso = $usuario->salvar();
		if($cadastroSucesso['sucess'] == 'true'){
			$this->render('cadastro');
			return true;
		}
		echo $cadastroSucesso['message'];
		$this->render('inscreverse');
	}
}
