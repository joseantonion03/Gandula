<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Controllers\AuthController;
use App\Models\Turma;

class TurmaController extends Action
{
    public function turmaCriar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $timeZone = new \DateTimeZone('America/Sao_Paulo');
        $objDateTo = new \DateTime();
        $this->view->data = $objDateTo->setTimezone($timeZone);
        $data = $objDateTo->format('Y/m/d');

        $turma = Container::getModel('Turma');
        $nome = filter_input(INPUT_POST, 'criarsalaCodigoTurma', FILTER_UNSAFE_RAW);
        $cor = filter_input(INPUT_POST, 'criarsalaColor', FILTER_UNSAFE_RAW);

        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("nome", $nome);
        $turma->__set("cor", $cor);
        $turma->__set("data", $data);
        
        if ($turma->turmaCriar()) {
            header("Location: /timeline?msg=sucesso&dir=turma&acao=criar");
        } else {
            header("Location: /timeline?msg=erro&dir=turma&acao=criar");
        }
    }
    public function turmaEntrar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $turma = Container::getModel('Turma');
        $codigo = filter_input(INPUT_POST, 'adicionarsalaCodigoTurma', FILTER_UNSAFE_RAW);
        $cor = filter_input(INPUT_POST, 'adicionarsalaColor', FILTER_UNSAFE_RAW);

        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("codigo", $codigo);
        $turma->__set("cor", $cor);
        if ($turma->turmaEntrar()) {
            header("Location: /timeline?msg=sucesso&dir=turma&acao=entra");
        } else {
            header("Location: /timeline?msg=erro&dir=turma&acao=entra");
        }
    }
    public function postagemCriar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $postagem = Container::getModel('Postagem');
        $conteudo = filter_input(INPUT_POST, 'conteudo', FILTER_UNSAFE_RAW);
        $idTurma = filter_input(INPUT_POST, 'idturma', FILTER_VALIDATE_INT);
        $timeZone = new \DateTimeZone('America/Sao_Paulo');
        $objDateTo = new \DateTime();
        $objDateTo->setTimezone($timeZone);
        $postagem->__set("id", $_SESSION['idUserOnline']);
        $postagem->__set("idTurma", $_SESSION['idTurmaPostagem']);
        $postagem->__set("conteudo", $conteudo);
        $postagem->__set('data', $objDateTo->format('Y/m/d H:i:s'));

        if ($_SESSION['idTurmaPostagem'] == $idTurma) {
            if ($postagem->criar()) {
                header("Location: /sala?id=$idTurma&msg=sucesso&acao=criar");
            } else {
                header("Location: /sala?id=$idTurma&msg=erro&acao=criar");
            }
        }
    }

    public function validationSessao()
    {
        $validation = new AuthController();
        return $validation->sessao();
    }
}
