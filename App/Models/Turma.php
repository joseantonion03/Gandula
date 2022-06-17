<?php

namespace App\Models;

use MF\Model\Model;

class Turma extends Model
{
    private $id;
    private $nome;
    private $idTurma;
    private $idLider;
    private $cor;
    private $codigo;
    private $ocupacao;
    private $data;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function turmaCriar()
    {

        $stringCodigo = uniqid();
        $codigo = substr($stringCodigo, (strlen($stringCodigo) - 6), strlen($stringCodigo));
        $this->__set('codigo', strtoupper($codigo));

        $query = "INSERT INTO Turma (idUserCriador, idUserLider, nome, cor, codigo, data) 
        VALUES (:idCriador, :idLider, :nome, :cor, :codigo, :data)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idCriador', $this->__get('id'));
        $stmt->bindValue(':idLider', "");
        $stmt->bindValue(':nome', $this->__get('nome'));
        $stmt->bindValue(':cor', $this->__get('cor'));
        $stmt->bindValue(':codigo', $this->__get('codigo'));
        $stmt->bindValue(':data', $this->__get('data'));
        if ($stmt->execute()) {
            return true;
        }

        return false;
    }
    public function turmaEntrar()
    {

        $query = "SELECT * FROM Turma WHERE codigo = :codigo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':codigo', $this->__get('codigo'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $idTurma = $resultado[0]['id'];

            if(!$this->verificarSeMembroExiste($idTurma, $this->__get('id'))){
                $query = "INSERT INTO Turmamembros (idTurma, idUserMembro, cor) 
                VALUES (:idTurma, :idUserMembro, :cor)";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':idTurma', $idTurma);
                $stmt->bindValue(':idUserMembro', $this->__get('id'));
                $stmt->bindValue(':cor', $this->__get('cor'));
                if ($stmt->execute()) {
                    return true;
                }
            }
 
        }


        return false;
    }
    public function verificarSeMembroExiste($idTurma, $idMembro){
        $query = "SELECT * FROM Turmamembros WHERE idTurma = :idTurma AND idUserMembro = :idUserMembro";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $idTurma);
        $stmt->bindValue(':idUserMembro', $idMembro);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if(count($resultado) > 0){
                return true;
            }
        }
        return false;
    }
    public function turmaListagem()
    {
        // $query = "SELECT * FROM Turma WHERE idUserCriador = :idCriador ORDER BY id DESC";
        if ($this->__get('ocupacao') == "docente") {
            $query = "SELECT * FROM Turma WHERE idUserCriador = :idCriador ORDER BY id DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':idCriador', $this->__get('id'));
        } else {
            if (count($this->turmaListagemAluno()) > 0) {
                $query = "SELECT * FROM Turma WHERE id=0";
                foreach ($this->turmaListagemAluno() as $listAluno) :
                    $query .= " OR id={$listAluno['idTurma']}";
                endforeach;
                $query .= " ORDER BY id DESC";
                $stmt = $this->db->prepare($query);
            } else {
                return [];
            }
        }

        if ($stmt->execute()) {
            $resultado = [];

            if ($stmt->rowCount() > 0) {
                foreach ($stmt->fetchAll(\PDO::FETCH_ASSOC) as $key => $row) {
                    $resultado[] = array(
                        $key => [
                            'id' => $row['id'],
                            'idUserCriador' => $row['idUserCriador'],
                            'idUserLider' => $row['idUserLider'],
                            'nome' => $row['nome'],
                            'cor' => $row['cor'],
                            'codigo' => $row['codigo'],
                            'total' => $this->turmaTotalAluno($row['id']),
                            'data' => $row['data']
                        ]
                    );
                    //$resultado[] = $row;
                }
            } else {
                return [];
            }
            return $resultado;
        }


        //return $this->turmaListagemAluno();
        return [];
    }
    public function turmaListagemAluno()
    {
        $query = "SELECT * FROM Turmamembros WHERE idUserMembro = :idMembro ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idMembro', $this->__get('id'));
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultado;
    }
    public function turmaTotalAluno($params)
    {
        $query = "SELECT COUNT(*) as total FROM Turmamembros WHERE idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $params);
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultado[0]['total'];
    }
    public function membroListar()
    {
        $query = "SELECT 
        Turmamembros.id, 
        Turmamembros.idTurma, 
        Turmamembros.idUserMembro, 
        Turmamembros.cor, 
        Usuario.id, 
        Usuario.nome, 
        Usuario.foto
        FROM Turmamembros
        INNER JOIN Usuario ON Turmamembros.idUserMembro=Usuario.id 
        WHERE Turmamembros.idTurma = :idTurma ORDER BY Turmamembros.id DESC";

        //$query = "SELECT * FROM Turmamembros WHERE idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultado;
    }
    public function membroTornarLider()
    {
        $query = "UPDATE turma SET idUserLider = :idLider WHERE id = :idTurma AND idUserCriador = :idCriador";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idLider', $this->__get('idLider'));
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->bindValue(':idCriador', $this->__get('id'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
}
