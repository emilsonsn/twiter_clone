<?php

namespace App\Models;

use MF\Model\Model;
class Tweet extends Model{
    private $id;
    private $id_usuario;
    private $tweet;
    private $data_criacao;

    public function __get($att){
        return $this->$att;
    }

    public function __set($att,$value){
        $this->$att = $value;
    }

    public function salvar(){
        $query = "
            insert into tweets(id_usuario,tweet) values(:id_usuario, :tweet);
        ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario",$this->__get('id_usuario'));
        $stmt->bindValue(":tweet",$this->__get('tweet'));
        $stmt->execute();
        $tweet= $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getAll(){   
        $query = "
        Select
            t.id,t.id_usuario,u.nome,t.tweet,DATE_FORMAT(t.data_criacao, '%d/%m/%y %H:%i') as data_criacao
        From
            tweets as t
            left join tb_usuarios as u on (t.id_usuario = u.id)
        Where
            t.id_usuario= :id_usuario
            or t.id_usuario in (select id_usuario_seguindo from tb_usuarios_seguidores where id_usuario = :id_usuario)
        Order by data_criacao desc;";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario",$this->__get('id_usuario'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deletarTweet(){
        $query = "
            delete from tweets where id = :id;
        ";
        $stmt =$this->db->prepare($query);
        $stmt->bindValue(":id", $this->__get('id'));
        $stmt->execute();
    }



}