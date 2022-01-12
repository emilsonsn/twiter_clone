<?php

namespace App\Controllers;

//os recursos do miniframework
use MF\Controller\Action;
use MF\Model\Container;

class AuthController extends Action{
    public function autenticar(){
        $credenciais = $_POST;
        $usuario = Container::getModel('usuario');
        $usuario->__set('email',$_POST['email']);
        $usuario->__set('senha',$_POST['senha']);
        $usuario = $usuario->autenticar();

        if($usuario->__get('id') != '' && $usuario->__get('id') != ''){
            session_start();
            $_SESSION['autenticado'] = true;
        }
        else{ 
            echo '<h1 class="erro">Erro na autenticação</h1>';
            $this->render('home');
        }

    }
}