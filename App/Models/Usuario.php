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
        $message = null;
        foreach($this->recuperaEmail() as $indice => $value) {
            if(is_int($indice) && $value[0] == $this->__get('email')){
                $valido = false;
                $message = '<h1 class="erro">Esse email já está sendo utilizado por outro usuário.</h1>';
            } 
        }
        if (strlen($this->__get('nome')) < 3 || strlen($this->__get('email')) < 3 || strlen($this->__get('senha')) < 3){
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
        $query = '
        select
            id,email,nome
        from
            tb_usuarios
        where
            email= :email and senha= :senha;
        ';
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

    public function getAll(){
        $query = "
        select
            u.id,u.nome,u.email, ( select count(*) from tb_usuarios_seguidores as us where us.id_usuario = :id and us.id_usuario_seguindo = u.id) as seguindo
        from
            tb_usuarios as u
        where
            u.nome like :nome and u.id != :id;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', '%'.$this->__get('nome').'%');
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function seguirUsuario(){
        $query = "
            insert
            into tb_usuarios_seguidores
                (id_usuario,id_usuario_seguindo)
            values
                (:id_usuario,:id_usuario_seguindo);
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario",$this->__get('id'));
        $stmt->bindValue(":id_usuario_seguindo",$_GET['id']);
        $stmt->execute();
        header("location: /quem_seguir?find=".$this->__get('get'));
    }

    public function deixarDeSeguirUsuario(){
        $query = "
            delete from
                tb_usuarios_seguidores
            where
                id_usuario = :id_usuario and id_usuario_seguindo = :id_usuario_seguindo;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario",$this->__get('id'));
        $stmt->bindValue(":id_usuario_seguindo",$_GET['id']);
        $stmt->execute();
        header("location: /quem_seguir?find=".$this->__get('get'));
        return true;
    }

    public function getUserInfor(){
        $query = "
            select nome from tb_usuarios where id = :id_usuario;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue((':id_usuario'),$_SESSION['id']);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalTweets(){
        $query = "
            select count(*) as total_tweets from tweets where id_usuario = :id_usuario;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue((':id_usuario'),$_SESSION['id']);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalFollowing(){
        $query = "
            select count(*) as total_following from tb_usuarios_seguidores where id_usuario = :id_usuario;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue((':id_usuario'),$_SESSION['id']);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getTotalFollowers(){
        $query = "
            select count(*) as total_followers from tb_usuarios_seguidores where id_usuario_seguindo = :id_usuario;
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue((':id_usuario'),$_SESSION['id']);
        $stmt->execute();
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}


