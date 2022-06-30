<?php

namespace App\Models;

use MF\Model\Model;
use Spatie\Dropbox\Client as DropboxClient;

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
    private $filtragem;

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

        if (!empty($this->__get('nome')) and !empty($this->__get('cor'))) {
            $query = "INSERT INTO turma (idUserCriador, idUserLider, nome, cor, codigo, data) 
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
        }

        return false;
    }
    public function turmaEntrar()
    {

        if (!empty($this->__get('codigo'))) {

            if ($this->verificarSeExisteSala($this->__get('codigo'))) {
                $query = "INSERT INTO turmamembros (idTurma, idUserMembro, cor) 
                VALUES (:idTurma, :idUserMembro, :cor)";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':idTurma', $this->verificarSeExisteSala($this->__get('codigo')));
                $stmt->bindValue(':idUserMembro', $this->__get('id'));
                $stmt->bindValue(':cor', $this->__get('cor'));
                if ($stmt->execute()) {
                    return true;
                }
            } else {
                $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
                $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Esta sala não existe!';
                return false;
            }
        } else {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'warning';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Você não preencheu os campos!';
            return false;
        }
        $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
        $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Não conseguimos entrar na turma!';
        return false;
    }
    public function verificarSeExisteSala($codigo)
    {
        $query = "SELECT * FROM turma WHERE codigo = :codigo";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':codigo', $codigo);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $idTurma = $resultado[0]['id'];
            if (!$this->verificarSeMembroExiste($idTurma, $this->__get('id'))) {
                return $idTurma;
            }
        }
        return false;
    }
    public function verificarSeMembroExiste($idTurma, $idMembro)
    {
        $query = "SELECT * FROM turmamembros WHERE idTurma = :idTurma AND idUserMembro = :idUserMembro";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $idTurma);
        $stmt->bindValue(':idUserMembro', $idMembro);
        if ($stmt->execute()) {
            $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (count($resultado) > 0) {
                return true;
            }
        }
        return false;
    }
    public function turmaListagem()
    {
        // $query = "SELECT * FROM Turma WHERE idUserCriador = :idCriador ORDER BY id DESC";
        if ($this->__get('ocupacao') == "docente") {
            $query = "SELECT * FROM turma WHERE idUserCriador = :idCriador ";
            if ($this->__get('filtragem') === 'crescente') {
                $query .= "ORDER BY id ASC";
            } else {
                $query .= "ORDER BY id DESC";
            }
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':idCriador', $this->__get('id'));
        } else {
            if (count($this->turmaListagemAluno()) > 0) {
                $query = "SELECT * FROM turma WHERE id=0";
                foreach ($this->turmaListagemAluno() as $listAluno) :
                    $query .= " OR id={$listAluno['idTurma']}";
                endforeach;
                //$query .= " ORDER BY id DESC";
                if ($this->__get('filtragem') === 'crescente') {
                    $query .= " ORDER BY id ASC";
                } else {
                    $query .= " ORDER BY id DESC";
                }
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
        $query = "SELECT * FROM turmamembros WHERE idUserMembro = :idMembro ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idMembro', $this->__get('id'));
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultado;
    }
    public function turmaTotalAluno($params)
    {
        $query = "SELECT COUNT(*) as total FROM turmamembros WHERE idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $params);
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $resultado[0]['total'];
    }
    public function membroListar()
    {
        //TOKEN DE ACESSO
        $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
        $env = parse_ini_file($envPath);
        $token = $env['tokendropbox'];
        //INSTANCIA DO CLIENTE DROPBOX
        $obDropboxClient = new DropboxClient($token);

        $query = "SELECT 
        turmamembros.id, 
        turmamembros.idTurma, 
        turmamembros.idUserMembro, 
        usuario.id, 
        usuario.nome, 
        usuario.foto
        FROM turmamembros
        INNER JOIN usuario ON turmamembros.idUserMembro=usuario.id 
        WHERE turmamembros.idTurma = :idTurma ORDER BY turmamembros.id DESC";

        //$query = "SELECT * FROM Turmamembros WHERE idTurma = :idTurma ORDER BY id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':idTurma', $this->__get('idTurma'));
        $stmt->execute();
        $resultado = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $content = [];
        foreach ($resultado as $value) {
            if ($value['foto'] == 'perfil.webp') {
                $link = 'perfil.webp';
            } else {
                $link = $obDropboxClient->getTemporaryLink('/Gandula/' . $value['foto'] . '');
            }

            $content[] = [
                "id" => $value['id'],
                "idTurma" => $value['idTurma'],
                "idUserMembro" => $value['idUserMembro'],
                "nome" => $value['nome'],
                "foto" => $value['foto'],
                'fotodropbox' => $link
            ];
        }
        return $content;
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
