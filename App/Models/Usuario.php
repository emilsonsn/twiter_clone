<?php

namespace App\Models;

use JetBrains\PhpStorm\Internal\ReturnTypeContract;
use MF\Model\Model;

class Usuario extends Model
{
    private $id;
    private $nome;
    private $email;
    private $senha;

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $value)
    {
        $this->$name = $value;
    }

    public function salvar(){
        $validacao = $this->validaCadastro();
        if ($validacao[0]){
            try {
                $query = 'insert into tb_usuarios(nome,email,senha) values(:nome,:email,:senha);';
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome', $this->__get('nome'));
                $stmt->bindValue(':email', $this->__get('email'));
                $stmt->bindValue(':senha', $this->__get('senha'));
                $stmt->execute();
                return ['sucess' => 'true'];
            }
            catch (\PDOException $err) {
                return ['sucess' => 'false','message' => ('<h1 class="erro">Erro na criação de um novo usuário. ERROR: '. $err . '</h1>')];
            }
        }
        else{
            return  ['sucess' => 'false','message' => $validacao[1]];
        }
    }

    public function validaCadastro(){
        $valido = true;
        $message = '<h1 class="sucesso">Cadastro realizado com sucesso!</h1>';

        foreach($this->recuperaEmail() as $indice => $value) {
            if(is_int($indice) && $value[0] == $this->__get('email')){
                $valido = false;
                $message = '<h1 class="erro">Esse email já está sendo utilizado por outro usuário.</h1>';
            } 
        }
        if (strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha'))){
            $valido = false;
            $message = '<h1 class="erro">Preenchar todos os campos para realizar a ação.</h1>';
        }
        
        return [$valido,$message];
    }
    public function recuperaEmail(){
        $query = 'select email from tb_usuarios;';
        return ($this->db->query($query)->fetchAll());
    }

    public function autenticar(){
        $query = 'select id,email,nome from tb_usuarios where email= :email and senha= :senha;';
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email',$this->__get('email'));
        $stmt->bindValue(':senha',$this->__get('senha'));
        $stmt->execute();
        $usuario = $stmt->fetch(\PDO::FETCH_ASSOC);
        if(isset($usuario['id']) && isset($usuario['nome'])){
            $this->__set('id',$usuario['id']);
            $this->__set('nome',$usuario['nome']);
        }
        return $this;
    }
}
