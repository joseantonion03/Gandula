<?php

namespace App\Models;

use MF\Model\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class Usuario extends Model
{
    private $id = 1;
    private $nome;
    private $email;
    private $senha;
    private $datanascimento;
    private $ocupacao;
    private $foto = "perfil.webp";
    private $token;
    protected $key = 'jose';

    public function __get($atributo)
    {
        return $this->$atributo;
    }
    public function __set($atributo, $valor)
    {
        $this->$atributo = $valor;
    }
    public function sessaoUsuario()
    {
        if (!empty($_COOKIE['token'])) {
            $query = "SELECT * FROM Usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($body as $value) {
                if (password_verify($value['token'], $_COOKIE['token'])) {
                    $this->__set('token', trim($value['token']));
                    try {
                        //$decoded = JWT::decode($this->__get('token'), $this->key, array('HS256'));
                        $this->__set('email', $value['email']);
                        $tks = explode('.', $this->__get('token'));
                        list($headb64, $bodyb64, $cryptob64) = $tks;
                        $header = JWT::jsonDecode(\Firebase\JWT\JWT::urlsafeB64Decode($bodyb64));
                        return $header;
                    } catch (\Exception $e) {
                        return ("Exception catched: " . $e->getMessage());
                    }
                    break;
                }
            }
        }
        return false;
    }
    public function informacoesUsuario()
    {
        if (!empty($_COOKIE['token'])) {
            $query = "SELECT * FROM Usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($body as $value) {
                if (password_verify($value['token'], $_COOKIE['token'])) {
                    $usuario = [
                        "id" => $value['id'],
                        "nome" => $value['nome'],
                        "email" => $value['email'],
                        "ocupacao" => $value['tipo'],
                        "foto" => $value['foto'],
                        "datanascimento" => $value['datanascimento'],
                        "telefone" => $value['telefone'],
                    ];
                    $_SESSION['idUserOnline'] = $value['id']; 
                    return $usuario;
                    break;
                }
            }
        }
        return false;
    }
    public function loginUsuario()
    {
        if (!empty($this->__get('email')) and !empty($this->__get('senha'))) {
            $query = "SELECT * FROM Usuario WHERE email = :email";
            $stmt = $this->db->prepare($query);
            $stmt->bindValue(':email', strtolower($this->__get('email')));
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($body as $value) {
                if (password_verify($this->__get('senha'), $value['senha'])) {
                    $crip_senha = password_hash($this->__get('senha'), PASSWORD_DEFAULT);
                    $payload = [
                        'email' => "{$this->__get('email')}",
                        'senha' => "{$crip_senha}"
                    ];

                    $jwt = JWT::encode($payload, $this->key, 'HS256');

                    $query = "UPDATE Usuario SET token = :token WHERE email = :email";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindValue(':email', $this->__get('email'));
                    $stmt->bindValue(':token', $jwt);
                    if ($stmt->execute()) {
                        $crip_token = password_hash($jwt, PASSWORD_DEFAULT);
                        $tempo = time() + (86400 * 30);
                        setcookie('token', $crip_token, $tempo, "/");
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function cadastrarUsuario()
    {
        if (
            !empty($this->__get('nome')) and
            !empty($this->__get('email')) and
            !empty($this->__get('senha')) and
            !empty($this->__get('datanascimento')) and
            !empty($this->__get('ocupacao'))
        ) {
            if ($this->cadastrarUsuario_UserExistente()) {
                $senha = password_hash($this->__get('senha'), PASSWORD_DEFAULT);

                $query = "INSERT INTO Usuario (nome, email, senha, datanascimento, tipo, foto) VALUES (:nome, :email, :senha, :datanascimento, :ocupacao, :foto)";
                $stmt = $this->db->prepare($query);
                $stmt->bindValue(':nome', $this->__get('nome'));
                $stmt->bindValue(':email', strtolower($this->__get('email')));
                $stmt->bindValue(':senha', $senha);
                $stmt->bindValue(':datanascimento', $this->__get('datanascimento'));
                $stmt->bindValue(':ocupacao', $this->__get('ocupacao'));
                $stmt->bindValue(':foto', $this->__get('foto'));
                if ($stmt->execute()) {
                    if ($this->loginUsuario()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function cadastrarUsuario_UserExistente()
    {
        $query = "SELECT * FROM Usuario WHERE email = :email";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':email', strtolower($this->__get('email')));
        $stmt->execute();
        if ($stmt->rowCount() > 0) {
            return false;
        } else {
            return true;
        }
    }
}
