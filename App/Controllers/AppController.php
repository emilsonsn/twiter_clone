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
        if($find != ''){
            $usuario = Container::getModel('usuario');
            $usuario->__set('nome',$_GET['find']);
            $pesquisa = $usuario->getAll();
            $this->view->pesquisa = $pesquisa;
        }   
        $this->render('quemSeguir');

    }
    
}