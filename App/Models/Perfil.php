<?php

namespace App\Models;

use MF\Model\Model;
use Spatie\Dropbox\Client as DropboxClient;

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

        //TOKEN DE ACESSO
        
        $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
        $env = parse_ini_file($envPath);
        $token = $env['tokendropbox'];
        //INSTANCIA DO CLIENTE DROPBOX
        $obDropboxClient = new DropboxClient($token);
        //CRIA UMA PASTA NO DROPBOX
        //$obDropboxClient->createFolder('/Gandula2');
        //$obDropboxClient->upload('/imagem.png', file_get_contents(__DIR__.'/perfil.png'), 'add');
        //$list = $obDropboxClient->listFolder('/Gandula');
        /*$link = $obDropboxClient->getTemporaryLink('/Gandula/imagem.png');
        return $link;*/


        $ARQUIVO['PASTA'] = dirname(__DIR__, 2) . '/public/assets/IMG/PERFIL/';
        $ARQUIVO['TMP_NAME'] = $_FILES['image']['tmp_name'];
        $ARQUIVO['NAME'] = $_FILES['image']['name'];
        $ARQUIVO['RENAME'] =  'Gandula__' . uniqid() . '__' . time() . '.' . pathinfo($ARQUIVO['NAME'], PATHINFO_EXTENSION);
        if (move_uploaded_file($ARQUIVO['TMP_NAME'], $ARQUIVO['PASTA'] . $ARQUIVO['RENAME'])) {
            $obDropboxClient->upload('/Gandula/'. $ARQUIVO['RENAME']. '', file_get_contents($ARQUIVO['PASTA']. $ARQUIVO['RENAME']), 'add');
            $query = "UPDATE usuario SET foto = :foto WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':foto', $ARQUIVO['RENAME']);
            $stmt->bindValue(':id', $this->__get('id'));
            if ($stmt->execute()) {
                unlink($ARQUIVO['PASTA'] . $ARQUIVO['RENAME']);
                return true;
            }
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
