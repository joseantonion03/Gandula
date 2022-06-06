<?php

namespace App\Models;

use MF\Model\Model;

class Calendario extends Model
{
    private $id;
    private $idTurma;
    private $conteudo;
    private $title;
    private $color;
    private $start;
    private $end;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function mostrar()
    {

        $query = "SELECT * FROM horario WHERE idTurma = :idTurma";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        if($stmt->execute()){
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }

        return [];
    }
    public function cadastro()
    {
        $query = "INSERT INTO horario (idTurma, title, color, start, end) VALUES (:idTurma, :title, :color, :start, :end)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->bindValue(':title', $this->__get('title'));
        $stmt->bindValue(':color', $this->__get('color'));
        $stmt->bindValue(':start', $this->__get('start'));
        $stmt->bindValue(':end', $this->__get('end'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function editar()
    {
        $query = "UPDATE horario SET title = :title, color = :color, start = :start, end = :end WHERE id = :id ";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        $stmt->bindValue(':title', $this->__get('title'));
        $stmt->bindValue(':color', $this->__get('color'));
        $stmt->bindValue(':start', $this->__get('start'));
        $stmt->bindValue(':end', $this->__get('end'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}

