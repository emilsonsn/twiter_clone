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
        if ($this->validaCadastro()){
            try {
                $query = 'insert into tb_usuarios(nome,email,senha) values(:nome,:email,:senha);';
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome', $this->__get('nome'));
                $stmt->bindValue(':email', $this->__get('email'));
                $stmt->bindValue(':senha', $this->__get('senha'));
                $stmt->execute();
                echo "Registro salvo";
                return $this;
            }
            catch (\PDOException $err) {
                echo 'Erro na criação de um novo usuário. ERROR: ' . $err;
            }
        }
        else{
            echo 'Erro ao tentar registrar';
        }
    }

    public function validaCadastro()
    {
        $valido = true;

        if (strlen($this->__get('nome')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('email')) < 3) {
            $valido = false;
        }
        if (strlen($this->__get('senha')) < 3) {
            $valido = false;
        }
        foreach($this->recuperaEmail() as $indice => $value) {
            if(is_int($indice) && $value[0] == $this->__get('email')){
                $valido = false;
            } 
        }
        return $valido;
    }

    public function recuperaEmail(){
        $query = 'select email from tb_usuarios;';
        return ($this->db->query($query)->fetchAll());
    }

    


}
