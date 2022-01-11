<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class IndexController extends Action {

	public function index() {

		$this->render('index');
	}

	public function inscreverse() {
		$this->render('inscreverse');
	}

	public function registrar() {
		if(isset($_POST['nome']) && $_POST['email'] && $_POST['senha']){
			$usuario = Container::getModel('Usuario');
			$usuario->__set('nome', $_POST['nome']);
			$usuario->__set('email', $_POST['email']);
			$usuario->__set('senha', $_POST['senha']);
			$usuario->salvar();
		}
		else{
			echo 'É necessário preencher todos os dados para realizar a ação';
		}
	}
}


?>