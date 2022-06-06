<?php

namespace App\Models;

use MF\Model\Model;

class Perfil extends Model
{
    private $id;
    private $nome;
    private $email;
    private $datanascimento;
    private $telefone;
    private $foto;
    private $senhaAtual;
    private $senhaNew;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function modificarFoto()
    {
        $query = "UPDATE usuario SET foto = :foto WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':foto', $this->__get('foto'));
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function modificarSenha()
    {
        if (!empty($this->__get('senhaAtual')) and !empty($this->__get('senhaNew'))) {
            $query = "SELECT * FROM usuario WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $this->__get('id'));
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (password_verify($this->__get('senhaAtual'), $body[0]['senha'])) {
                $senhaNovaHash = password_hash($this->__get('senhaNew'), PASSWORD_DEFAULT);
                $query = "UPDATE usuario SET senha = :senhaNew WHERE id = :id";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':senhaNew', $senhaNovaHash);
                $stmt->bindValue(':id', $this->__get('id'));
                if ($stmt->execute()) {
                    return true;
                }
            }
        }
        return false;
    }
    public function modificarDados()
    {

        $query = "UPDATE usuario SET nome = :nome, datanascimento = :datanascimento, telefone = :telefone WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':nome', $this->__get('nome'));
        //$stmt->bindValue(':email', $this->__get('email'));
        $stmt->bindValue(':datanascimento', $this->__get('datanascimento'));
        $stmt->bindValue(':telefone', $this->__get('telefone'));
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            return true;
        }


        return false;
    }
}
