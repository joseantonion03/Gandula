<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Models\Usuario;

class AuthController extends Action
{
    public function sessao()
    {
        $usuario = Container::getModel('Usuario');
        $this->view->usuario = $usuario->sessaoUsuario();
        if (!empty($this->view->usuario)) {
            foreach ($this->view->usuario as $key => $value) {
                if ($key == 'email') {
                    if ($value == $usuario->__get('email')) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
    public function login()
    {
        $usuario = Container::getModel('Usuario');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_UNSAFE_RAW);
        $usuario->__set('email', $email);
        $usuario->__set('senha', $senha);
        $this->view->response = $usuario->loginUsuario() == true ? 'success' : 'erro';
        exit(json_encode(array("response" => $this->view->response)));
    }
    public function cadastro()
    {

        $usuario = Container::getModel('Usuario');
        $nome = filter_input(INPUT_POST, 'nome', FILTER_UNSAFE_RAW);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $senha = filter_input(INPUT_POST, 'senha', FILTER_UNSAFE_RAW);
        $datanascimento = filter_input(INPUT_POST, 'datanascimento', FILTER_UNSAFE_RAW);
        $ocupacao = filter_input(INPUT_POST, 'ocupacao', FILTER_UNSAFE_RAW);

        $usuario->__set('nome', $nome);
        $usuario->__set('email', $email);
        $usuario->__set('senha', $senha);
        $usuario->__set('datanascimento', $datanascimento);
        $usuario->__set('ocupacao', $ocupacao);
        if($usuario->cadastrarUsuario()){
            header('Location: /timeline');
        }else{
            header('Location: /?erro=cadastro');
        }

        
    }
    public function sair()
    {
        setcookie('token', '', -10, "/");
        header('Location: /');
    }
}
