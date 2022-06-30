<?php

namespace App\Models;

use MF\Model\Model;
use Spatie\Dropbox\Client as DropboxClient;

class Mensagem extends Model
{
    private $id;
    private $id_turma;
    private $id_user_recebeu;
    private $id_user_enviou;
    private $texto;

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }

    public function getMensagens()
    {
        //TOKEN DE ACESSO
        $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
        $env = parse_ini_file($envPath);
        $token = $env['tokendropbox'];
        //INSTANCIA DO CLIENTE DROPBOX
        $obDropboxClient = new DropboxClient($token);
        $query = "SELECT * FROM mensagem WHERE id_turma = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id_turma'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $content = [];
            foreach ($resultado as $value) {
                $link = '/assets/IMG/PERFIL/perfil.webp';

                //$list = $obDropboxClient->listFolder('/Gandula');
                if ($this->getInformacoesUser($value['id_user_enviou'])['foto'] == 'perfil.webp') {
                    $link = '/assets/IMG/PERFIL/perfil.webp';
                } else {
                    $link = $obDropboxClient->getTemporaryLink('/Gandula/' . $this->getInformacoesUser($value['id_user_enviou'])['foto'] . '');
                }
                $content[] = [
                    "id" => $value['id'],
                    "id_turma" => $value['id_turma'],
                    "id_user_enviou" => $value['id_user_enviou'],
                    "id_user_recebeu" => $value['id_user_recebeu'],
                    "texto" => $value['texto'],
                    "foto" => $link,
                    "nome" => $this->getInformacoesUser($value['id_user_enviou'])['nome'],
                ];
            }
            return $content;
        }
        return [];
    }
    public function getListaChatRecente_docente()
    {
        $query = "SELECT * FROM turma WHERE idUserCriador = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $content = [];
            foreach ($resultado as $value) {
                $getUserNomeEnviou = isset($this->getMessageRecente($value['id'])['id_user_enviou']) ? $this->getMessageRecente($value['id'])['id_user_enviou'] : null;
                //$nomeUsuario = isset($this->getInformacoesUser($this->getMessageRecente($value['id'])['id_user_enviou'])['nome']) ? $this->getInformacoesUser($this->getMessageRecente($value['id'])['id_user_enviou'])['nome'] : 'Servidor';
                $nomeUsuario = isset($this->getInformacoesUser($getUserNomeEnviou)['nome']) ? $this->getInformacoesUser($getUserNomeEnviou)['nome'] : 'Servidor';
                $mensagem = isset($this->getMessageRecente($value['id'])['texto']) ? $this->getMessageRecente($value['id'])['texto'] : 'Nenhuma mensagem enviada';
                $content[] = [
                    "id" => $value['id'],
                    "idTurma" => $value['id'],
                    "idMembro" => $value['idUserCriador'],
                    "nome" => $value['nome'],
                    "nomeUsuario" => $nomeUsuario,
                    "mensagem" => $mensagem
                ];
            }
            return $content;
        }
        return [];
    }
    public function getListaChatRecente_discente()
    {
        $query = "SELECT 
        turmamembros.idTurma, 
        turmamembros.idUserMembro, 
        turma.id, 
        turma.nome
        FROM turmamembros
        INNER JOIN turma ON turmamembros.idTurma=turma.id 
        WHERE turmamembros.idUserMembro = :id ORDER BY turmamembros.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $content = [];
            foreach ($resultado as $value) {

                $getUserNomeEnviou = isset($this->getMessageRecente($value['id'])['id_user_enviou']) ? $this->getMessageRecente($value['id'])['id_user_enviou'] : null;
                //$nomeUsuario = isset($this->getInformacoesUser($this->getMessageRecente($value['id'])['id_user_enviou'])['nome']) ? $this->getInformacoesUser($this->getMessageRecente($value['id'])['id_user_enviou'])['nome'] : 'Servidor';
                $nomeUsuario = isset($this->getInformacoesUser($getUserNomeEnviou)['nome']) ? $this->getInformacoesUser($getUserNomeEnviou)['nome'] : 'Servidor';
                $mensagem = isset($this->getMessageRecente($value['id'])['texto']) ? $this->getMessageRecente($value['id'])['texto'] : 'Nenhuma mensagem enviada';

                $content[] = [
                    "id" => $value['id'],
                    "idTurma" => $value['idTurma'],
                    "idMembro" => $value['idUserMembro'],
                    "nome" => $value['nome'],
                    "nomeUsuario" => $nomeUsuario,
                    "mensagem" => $mensagem
                ];
            }
            return $content;
        }
        return [];
    }

    public function getMessageRecente($id_turma)
    {
        if ($this->verificarSeMessageExiste($id_turma)) {
            $query = "SELECT * FROM mensagem WHERE id_turma = :id_turma ORDER BY id DESC LIMIT 1";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id_turma', $id_turma);
            if ($stmt->execute()) {
                $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return $resultado[0];
            }
        }

        return [];
    }
    public function verificarSeUsuarioTemMensagem()
    {
        $query = "SELECT * FROM mensagem WHERE id_user_enviou = :id OR id_user_recebeu = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $this->__get('id'));
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($resultado[0]['texto'])) {
                return true;
            }
        }
        return false;
    }
    public function verificarSeMessageExiste($id_turma)
    {
        $query = "SELECT * FROM mensagem WHERE id_turma = :id_turma";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_turma', $id_turma);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (!empty($resultado[0]['texto'])) {
                return true;
            }
        }

        return false;
    }
    public function getInformacoesUser($id = null)
    {
        if (isset($id)) {
            $query = "SELECT * FROM usuario WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':id', $id);
            if ($stmt->execute()) {
                $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                return $resultado[0];
            }
        }
        return [];
    }
    public function salvarMensagem()
    {
        $id_recebeu = $this->verificarUserQueEnviouMensagem($this->__get('id_turma'), $this->__get('id_user_enviou'));
        $query = "INSERT INTO mensagem (id_turma, id_user_enviou, id_user_recebeu, texto) VALUES (:id_turma, :id_user_enviou, :id_user_recebeu, :texto)";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_turma', $this->__get('id_turma'));
        $stmt->bindValue(':id_user_enviou', $this->__get('id_user_enviou'));
        $stmt->bindValue(':id_user_recebeu', $id_recebeu);
        $stmt->bindValue(':texto', $this->__get('texto'));
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    public function verificarUserQueEnviouMensagem($id, $id_user)
    {

        $query = "SELECT * FROM turma WHERE id = :id";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id', $id);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if ($id_user == $resultado[0]['idUserCriador']) {
                return $resultado[0]['idUserLider'];
            } else if ($id_user == $resultado[0]['idUserLider']) {
                return $resultado[0]['idUserCriador'];
            }
        }
        return false;
    }
}
