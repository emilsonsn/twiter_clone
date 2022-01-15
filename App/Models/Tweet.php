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
            id,id_usuario,tweet,DATE_FORMAT(data_criacao, '%d/%m/%y %H:%i') as data_criacao
        From
            tweets
        Where
            id_usuario= :id_usuario
        Order by id desc;";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(":id_usuario",$this->__get('id_usuario'));
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }



}