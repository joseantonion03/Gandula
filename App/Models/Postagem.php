<?php

namespace App\Models;

use MF\Model\Model;

class Postagem extends Model
{
    private $id;
    private $idTurma;
    private $conteudo;
    private $data;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function criar()
    {
        if (
            !empty($this->__get('id')) and
            !empty($this->__get('idTurma')) and
            !empty($this->__get('conteudo'))
        ) {
            $query = "INSERT INTO Postagem (idUserCriador, idTurma, conteudo, data) VALUES (:idCriador, :idTurma, :conteudo, :data)";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':idCriador', $this->__get('id'));
            $stmt->bindValue(':idTurma', $this->__get("idTurma"));
            $stmt->bindValue(':conteudo', $this->__get('conteudo'));
            $stmt->bindValue(':data', $this->__get('data'));
            if ($stmt->execute()) {
                return true;
            }
        }
        return false;
    }
    public function mostrar()
    {
        $query = "SELECT 
        Postagem.id, 
        Postagem.idUserCriador, 
        Postagem.idTurma, 
        Postagem.conteudo, 
        Postagem.data, 
        Usuario.id, 
        Usuario.nome, 
        Usuario.foto
        FROM Postagem
        INNER JOIN Usuario ON Postagem.idUserCriador=Usuario.id 
        WHERE Postagem.idTurma = :idTurma ORDER BY Postagem.id DESC";
        //WHERE Postagem.idUserCriador = :idCriador AND Postagem.idTurma = :idTurma ORDER BY Postagem.id DESC";
        //$query = "SELECT * FROM Postagem WHERE idUserCriador = :idCriador AND idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        // $stmt->bindValue(':idCriador', $this->__get('id'));
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }

        return [];
    }
    public function dadosCriadorPostagem($criador)
    {

        $query = "SELECT * FROM Usuario WHERE id = :idCriador";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idCriador', $criador);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }

        return false;
    }
}
