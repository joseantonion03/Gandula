<?php

namespace App\Models;

use MF\Model\Model;
use Spatie\Dropbox\Client as DropboxClient;

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
            $query = "INSERT INTO postagem (idUserCriador, idTurma, conteudo, data) VALUES (:idCriador, :idTurma, :conteudo, :data)";
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
        //TOKEN DE ACESSO
        $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
        $env = parse_ini_file($envPath);
        $token = $env['tokendropbox'];
        //INSTANCIA DO CLIENTE DROPBOX
        $obDropboxClient = new DropboxClient($token);

        $query = "SELECT 
        postagem.id, 
        postagem.idUserCriador, 
        postagem.idTurma, 
        postagem.conteudo, 
        postagem.data, 
        usuario.id, 
        usuario.nome, 
        usuario.foto
        FROM postagem
        INNER JOIN usuario ON Postagem.idUserCriador=usuario.id 
        WHERE postagem.idTurma = :idTurma ORDER BY postagem.id DESC";
        //WHERE Postagem.idUserCriador = :idCriador AND Postagem.idTurma = :idTurma ORDER BY Postagem.id DESC";
        //$query = "SELECT * FROM Postagem WHERE idUserCriador = :idCriador AND idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        // $stmt->bindValue(':idCriador', $this->__get('id'));
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $content = [];
            foreach ($resultado as $value) {
                /*$list = $obDropboxClient->listFolder('/Gandula');
                $link = $obDropboxClient->getTemporaryLink($list['entries'][0]['path_display']);*/
                if ($value['foto'] == 'perfil.webp') {
                    $link = 'perfil.webp';
                } else {
                    $link = $obDropboxClient->getTemporaryLink('/Gandula/' . $value['foto'] . '');
                }
                $content[] = [
                    "id" => $value['id'],
                    "idUserCriador" => $value['idUserCriador'],
                    "idTurma" => $value['idTurma'],
                    "conteudo" => $value['conteudo'],
                    "data" => $value['data'],
                    "nome" => $value['nome'],
                    "foto" => $value['foto'],
                    'fotodropbox' => $link
                ];
            }
            return $content;
        }

        return [];
    }
    public function dadosCriadorPostagem($criador)
    {

        $query = "SELECT * FROM usuario WHERE id = :idCriador";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idCriador', $criador);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            return $resultado;
        }

        return false;
    }
}
