<?php

namespace App\Models;

use MF\Model\Model;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Spatie\Dropbox\Client as DropboxClient;

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
    protected $key = 'Y5RM5Z7FXFPSDT9PYMZTBMX&OSHS!S#TV';

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
            $query = "SELECT * FROM usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($body as $value) {
                if (password_verify($value['token'], $_COOKIE['token'])) {
                    $this->__set('token', trim($value['token']));
                    if (!empty($value['senha'])) {
                        try {
                            //$decoded = JWT::decode($this->__get('token'), $this->key, array('HS256'));
                            $this->__set('email', $value['email']);
                            $_SESSION['USER_ID'] = $value['id'];
                            $_SESSION['USER_NOME'] = $value['nome'];
                            $_SESSION['USER_OCUPACAO'] = $value['tipo'];
                            $tks = explode('.', $this->__get('token'));
                            list($headb64, $bodyb64, $cryptob64) = $tks;
                            $header = JWT::jsonDecode(\Firebase\JWT\JWT::urlsafeB64Decode($bodyb64));
                            return $header;
                        } catch (\Exception $e) {
                            return ("Exception catched: " . $e->getMessage());
                        }
                    }
                    break;
                }
            }
        }
        return [];
    }
    public function informacoesUsuario()
    {
        if (!empty($_COOKIE['token'])) {
            //TOKEN DE ACESSO
            $envPath = realpath(dirname(__FILE__) . '/../../env.ini');
            $env = parse_ini_file($envPath);
            $token = $env['tokendropbox'];
            //INSTANCIA DO CLIENTE DROPBOX
            $obDropboxClient = new DropboxClient($token);

            $query = "SELECT * FROM usuario";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $body = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($body as $value) {
                if (password_verify($value['token'], $_COOKIE['token'])) {

                    $link = 'perfil.webp';

                    //$list = $obDropboxClient->listFolder('/Gandula');
                    if ($value['foto'] == 'perfil.webp') {
                        $link = 'perfil.webp';
                    } else {
                        $link = $obDropboxClient->getTemporaryLink('/Gandula/' . $value['foto'] . '');
                    }

                    $usuario = [
                        "id" => $value['id'],
                        "nome" => $value['nome'],
                        "email" => $value['email'],
                        "ocupacao" => $value['tipo'],
                        "foto" => $value['foto'],
                        "datanascimento" => $value['datanascimento'],
                        "telefone" => $value['telefone'],
                        "fotodropbox" => $link,
                    ];
                    $_SESSION['idUserOnline'] = $value['id'];
                    $_SESSION['nomeUserOnline'] = $value['nome'];
                    $_SESSION['fotoUserOnline'] = $value['foto'];
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
            $query = "SELECT * FROM usuario WHERE email = :email";
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

                    $query = "UPDATE usuario SET token = :token WHERE email = :email";
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

                $query = "INSERT INTO usuario (nome, email, senha, datanascimento, tipo, foto) VALUES (:nome, :email, :senha, :datanascimento, :ocupacao, :foto)";
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
        $query = "SELECT * FROM usuario WHERE email = :email";
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
