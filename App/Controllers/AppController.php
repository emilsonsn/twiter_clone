<?php

namespace App\Controllers;
use MF\Controller\Action;
use MF\Model\Container;

class AppController extends Action{

    public function validarAutenticacao(){
        session_start();
        if(isset($_SESSION['autenticado']) && $_SESSION['autenticado']){
            return true;
        }
        header('location: /?login=erro');
    }

    public function timeline(){
        $this->validarAutenticacao();

        $usuarios = Container::getModel('usuario');
        $usuarios->__set('id',$_SESSION['id']);
        $this->view->usuarios = $usuarios;

        $tweet = Container::getModel('tweet');
        $tweet->__set('id_usuario',$_SESSION['id']);
        $tweet = $tweet->getAll();
        $this->view->tweets = $tweet;
        $this->render('timeline'); 
    }

    public function tweet(){
        session_start();
        $this->validarAutenticacao();
        $tweet = Container::getModel('tweet');
        $tweet->id_usuario = $_SESSION['id'];
        $tweet->tweet = $_POST['tweet'];
        $tweet->salvar();
        header('location: /timeline');
    }

    public function quemSeguir(){
        $this->validarAutenticacao();
        $find = isset($_GET['find']) ? $_GET['find'] : '';
        $usuarios = Container::getModel('usuario');
        $usuarios->__set('id',$_SESSION['id']);
        $this->view->usuario = $usuarios;
        $this->view->find = $find;
        if($find != ''){
            $usuarios->__set('nome',$_GET['find']);
            $usuarios = $usuarios->getAll();
            $this->view->usuarios = $usuarios;
        }   
            $this->render('quem_seguir');
    }

    public function acao(){
        $this->validarAutenticacao();
        if(isset($_GET['action']) && $_GET['action'] != '' && isset($_GET['id']) && $_GET['id'] != ''){
            $usuario = Container::getModel('usuario');
            $usuario->__set('id',$_SESSION['id']);
            $usuario->get = $_GET['find'];
            if($_GET['action'] == 'follow'){
                $usuario->seguirUsuario();
            }else if($_GET['action'] == 'unfollow'){
                $usuario->deixarDeSeguirUsuario();
            }
        }
    }

    public function deletarTweet(){
        $get = $_GET;
        $tweet = Container::getModel('tweet');
        $tweet->__set('id',$_GET['id']);
        $tweet->deletarTweet();
        header('location: /timeline');
    }
    
}
