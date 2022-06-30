<?php

namespace App\Controllers;

use MF\Controller\Action;
use MF\Model\Container;
use App\Controllers\AuthController;
use App\Models\Usuario;

class IndexController extends Action
{
    public function index()
    {
        if ($this->validationSessao()) {
            header("Location: /timeline");
        }
        $this->render('index', 'layout');
    }
    public function teste()
    {
        $this->render('chat', 'layoutChat');
    }
    public function timeline()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $this->view->recentes = $this->getMessage();
        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();
        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $filtrarSala = isset($_GET['filtrar']) ? $_GET['filtrar'] : null;
        $turma->__set("filtragem", $filtrarSala);
        $this->view->salas = $turma->turmaListagem();
        /*echo "<pre>";
        print_r($this->view->salas);
        echo "</pre>";*/
        // echo count($this->view->salas);
        $this->render('home', 'layoutTimeline');
    }
    public function sala()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $this->view->recentes = $this->getMessage();

        $idTurmaGetUrl = isset($_GET['id']) ? $_GET['id'] : null;
        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();
        $postagem = Container::getModel('Postagem');
        $postagem->__set("id", $_SESSION['idUserOnline']);
        $postagem->__set("idTurma", $idTurmaGetUrl);
        $this->view->postagens = $postagem->mostrar();

        /*if ($_SESSION['idTurmaPostagem'] == $idTurmaGetUrl) {
        *}*/

        $this->view->viewEnquete = $this->getEnquete();

        $this->render('sala', 'layoutTimeline');
    }

    public function membro()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $this->view->recentes = $this->getMessage();

        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $idTurmaGetUrl = isset($_GET['id']) ? $_GET['id'] : null;
        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("idTurma", $idTurmaGetUrl);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();
        $this->view->membros = $turma->membroListar();

        $this->view->viewEnquete = $this->getEnquete();
        $this->render('membro', 'layoutTimeline');
    }
    public function membroLider()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $idUser = isset($_GET['id_user']) ? $_GET['id_user'] : null;
        $idTurma = isset($_GET['id_sala']) ? $_GET['id_sala'] : null;
        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("idTurma", $idTurma);
        $turma->__set("idLider", $idUser);
        if ($turma->membroTornarLider()) {
            header("Location: /membro?id=" . $idTurma . "&acao=sucesso");
        } else {
            header("Location: /membro?id=" . $idTurma . "&acao=erro");
        }
    }
    public function mensagem()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();

        $mensagem = Container::getModel('Mensagem');
        $id_turma = isset($_GET['id']) ? $_GET['id'] : null;
        $mensagem->__set("id_turma", $id_turma);
        $this->view->mensagens = $mensagem->getMensagens();
        $mensagem->__set("id", $_SESSION['idUserOnline']);
        if ($this->view->ocupacao['ocupacao'] == 'docente') {
            $this->view->recentes = $mensagem->getListaChatRecente_docente();
        } else {
            $this->view->recentes = $mensagem->getListaChatRecente_discente();
        }
        /*
        echo '<pre>';
        print_r($this->view->mensagens);
        echo '</pre>';
        */
        $this->render('mensagem', 'layoutTimeline');
    }
    public function getMessage()
    {
        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $mensagem = Container::getModel('Mensagem');
        $id_turma = isset($_GET['id']) ? $_GET['id'] : null;
        $mensagem->__set("id_turma", $id_turma);
        $mensagem->__set("id", $_SESSION['idUserOnline']);
        if ($mensagem->verificarSeUsuarioTemMensagem()) {
            if ($this->view->ocupacao['ocupacao'] == 'docente') {
                return $this->view->recentes = $mensagem->getListaChatRecente_docente();
            } else {
                return $this->view->recentes = $mensagem->getListaChatRecente_discente();
            }
        } else {
            return $this->view->recentes = [];
        }
    }
    public function mensagemSalvar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $id_turma = filter_input(INPUT_POST, 'id_turma', FILTER_VALIDATE_INT);
        $texto = filter_input(INPUT_POST, 'texto', FILTER_UNSAFE_RAW);

        $mensagem = Container::getModel('Mensagem');
        $mensagem->__set("id_turma", $id_turma);
        $mensagem->__set("id_user_enviou", $_SESSION['idUserOnline']);
        $mensagem->__set("texto", $texto);
        if ($mensagem->salvarMensagem()) {
            exit(json_encode(array("status" => "true")));
        } else {
            exit(json_encode(array("status" => "false")));
        }
    }
    public function enquete()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $this->view->recentes = $this->getMessage();


        $timeZone = new \DateTimeZone('America/Sao_Paulo');
        $objDateTo = new \DateTime();
        $this->view->data = $objDateTo->setTimezone($timeZone);

        $idTurmaGetUrl = isset($_GET['id']) ? $_GET['id'] : null;
        $idEnqueteGetUrl = isset($_GET['id_enquete']) ? $_GET['id_enquete'] : null;
        $_SESSION['idSalaCodigo'] = $idTurmaGetUrl;
        $_SESSION['idEnqueteCodigo'] = $idEnqueteGetUrl;
        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();
        $postagem = Container::getModel('Postagem');
        $postagem->__set("id", $_SESSION['idUserOnline']);
        $postagem->__set("idTurma", $idTurmaGetUrl);
        $this->view->postagens = $postagem->mostrar();

        $enquete = Container::getModel('Enquete');
        $enquete->__set("idTurma", $idTurmaGetUrl);
        $enquete->__set("idEnquete", $idEnqueteGetUrl);

        if ($this->view->ocupacao['ocupacao'] == 'discente') {
            if ($enquete->mostrar()) {
                $this->view->enquente = $enquete->mostrar();
                $this->view->enquentePerguntas = $enquete->mostrarPerguntas($this->view->enquente[0]['id']);
            } else {
                exit('Formulário encerrado');
            }
        }
        $this->view->enquenteGrafico = $enquete->mostrarInformacoesGrafico();
        $this->view->enquenteGraficoPerguntas = $enquete->mostrarPerguntasInformacoesGrafico();
        $this->view->enquenteGraficoRespostas = $enquete->mostrarRespostaInformacoesGrafico();
        /*if ($_SESSION['idTurmaPostagem'] == $idTurmaGetUrl) {
        *}*/
        $this->view->viewEnquete = $this->getEnquete();
        $this->view->viewEnqueteGet = $this->getEe('ff');


        $this->render('enquete', 'layoutTimeline');
    }

    public function enqueteCriar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $titulo = filter_input(INPUT_POST, 'titulo', FILTER_UNSAFE_RAW);
        $dataFinal = filter_input(INPUT_POST, 'data_final', FILTER_UNSAFE_RAW);
        $descricao = filter_input(INPUT_POST, 'descricao', FILTER_UNSAFE_RAW);
        $opcao = filter_input(INPUT_POST, 'opcao', FILTER_UNSAFE_RAW);
        $dataFinal = str_replace("T", ' ', $dataFinal) . ':00';
        echo "<pre>";
        print_r($_POST);
        echo '</pre>';
        $timeZone = new \DateTimeZone('America/Sao_Paulo');
        $objDateTo = new \DateTime();
        $objDateTo->setTimezone($timeZone);
        $dataInicial = $objDateTo->format('Y/m/d H:i:s');
        $dataFinal = str_replace("-", '/', $dataFinal);

        $enquete = Container::getModel('Enquete');
        $enquete->__set("id", $_SESSION['idUserOnline']);
        $enquete->__set("idTurma", $_SESSION['idSalaCodigo']);
        $enquete->__set('dataInicial', $dataInicial);
        $enquete->__set("dataFinal", $dataFinal);
        $enquete->__set("titulo", $titulo);
        $enquete->__set("descricao", $descricao);
        $enquete->__set("opcao", $opcao);
        if ($enquete->criar()) {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'success';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Enquete criada com sucesso!';
            header('Location: /timeline?response=true');
        } else {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Não conseguimos criar sua enquete!';
            header('Location: /timeline?response=false');
        }
    }
    public function enqueteVotos()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $idReposta = filter_input(INPUT_POST, 'opcao_resposta', FILTER_UNSAFE_RAW);

        $enquete = Container::getModel('Enquete');
        $enquete->__set("id", $_SESSION['idUserOnline']);
        $enquete->__set("idEnquetePergunta", $idReposta);
        $enquete->__set("idEnquete", $_SESSION['idEnqueteCodigo']);
        if ($enquete->salvarVoto()) {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'success';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Você acabou de deixar seu voto em uma enquete!';
            header('Location: /timeline');
        } else {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Você já votou nessa enquente!';
            header('Location: /timeline');
        }
    }
    public function enqueteVotosUsuarios()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $idReposta = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        $enquete = Container::getModel('Enquete');
        $enquete->__set("idEnqueteResposta", $idReposta);
        if ($enquete->mostrarUsuarioVotador()) {
            exit(json_encode(array($enquete->mostrarUsuarioVotador())));
        }
    }
    public function perfil()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $this->view->recentes = $this->getMessage();

        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();

        $this->render('perfil', 'layoutTimeline');
    }
    public function perfilUploadFoto()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $imagem = $_FILES['image'];
        $perfil = Container::getModel("Perfil");
        $perfil->__set("id", $_SESSION['idUserOnline']);
        $perfil->__set("foto", $imagem);
        if ($perfil->modificarFoto()) {
            exit(json_encode(array('status' => "success", 'src' => $imagem)));
        } else {
            exit(json_encode(array('status' => "erro", 'src' => $imagem)));
        }
    }
    public function perfilModificarSenha()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $senhaAtual = filter_input(INPUT_POST, 'perfil__senha__atual', FILTER_UNSAFE_RAW);
        $senhaNew = filter_input(INPUT_POST, 'perfil__senha__new', FILTER_UNSAFE_RAW);
        $perfil = Container::getModel("Perfil");
        $perfil->__set("id", $_SESSION['idUserOnline']);
        $perfil->__set("senhaAtual", $senhaAtual);
        $perfil->__set("senhaNew", $senhaNew);
        if ($perfil->modificarSenha()) {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'success';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Senha modificada com sucesso!';
            header("Location: /perfil?acao=sucesso");
        } else {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Não conseguimos modificar sua senha';
            header("Location: /perfil?acao=erro");
        }
    }
    public function perfilModificarDados()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        echo "<pre>";
        print_r($_POST);
        echo "</pre>";
        $nome = filter_input(INPUT_POST, 'perfil__value-nome', FILTER_UNSAFE_RAW);
        $email = filter_input(INPUT_POST, 'perfil__value-email', FILTER_UNSAFE_RAW);
        $telefone = filter_input(INPUT_POST, 'perfil__value-telefone', FILTER_UNSAFE_RAW);
        $datanascimento = filter_input(INPUT_POST, 'perfil__value-data', FILTER_UNSAFE_RAW);
        $perfil = Container::getModel("Perfil");
        $perfil->__set("id", $_SESSION['idUserOnline']);
        $perfil->__set("nome", $nome);
        $perfil->__set("email", $email);
        $perfil->__set("telefone", $telefone);
        $perfil->__set("datanascimento", $datanascimento);
        if ($perfil->modificarDados()) {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'success';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Informações modificadas com sucesso!';
            header("Location: /perfil?acao=sucesso");
        } else {
            $_SESSION['ALERTA_TOAST_TYPE'] = 'error';
            $_SESSION['ALERTA_TOAST_MESSAGE'] = 'Não conseguimos modificar as suas informações';
            header("Location: /perfil?acao=erro");
        }
    }
    public function calendario()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $this->view->recentes = $this->getMessage();

        $usuario = Container::getModel('Usuario');
        $this->view->ocupacao = $usuario->informacoesUsuario();

        $turma = Container::getModel('Turma');
        $turma->__set("id", $_SESSION['idUserOnline']);
        $turma->__set("ocupacao", $this->view->ocupacao['ocupacao']);
        $this->view->salas = $turma->turmaListagem();

        $this->render('calendario', 'layoutTimeline');
    }
    public function calendarioMostrar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }

        $calendario = Container::getModel('Calendario');
        $calendario->__set("idTurma", $_SESSION['idTurmaPostagem']);
        $this->view->calendario = $calendario->mostrar();

        exit(json_encode($this->view->calendario));
    }
    public function calendarioCadastrar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $title = filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW);
        $color = filter_input(INPUT_POST, 'color', FILTER_UNSAFE_RAW);
        $start = filter_input(INPUT_POST, 'start', FILTER_UNSAFE_RAW);
        $end = filter_input(INPUT_POST, 'end', FILTER_UNSAFE_RAW);
        //Converter a data e hora do formato brasileiro para o formato do Banco de Dados
        $data_start = str_replace('/', '-', $start);
        $data_start_conv = date("Y-m-d H:i:s", strtotime($data_start));

        $data_end = str_replace('/', '-', $end);
        $data_end_conv = date("Y-m-d H:i:s", strtotime($data_end));

        $calendario = Container::getModel('Calendario');
        $calendario->__set("idTurma", $_SESSION['idTurmaPostagem']);
        $calendario->__set("title", $title);
        $calendario->__set("color", $color);
        $calendario->__set("start", $data_start_conv);
        $calendario->__set("end", $data_end_conv);

        $this->view->calendarioCadastro = $calendario->cadastro();
        if ($this->view->calendarioCadastro) {
            $retorna = ['CALENDARIO_CADASTRO_SUCCESS_KEY' => true, 'mensagem' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
            $_SESSION['CALENDARIO_CADASTRO_MESANGEM'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
            exit(json_encode($retorna));
        } else {
            $retorna = ['CALENDARIO_CADASTRO_SUCCESS_KEY' => false, 'mensagem' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi cadastrado com sucesso!</div>'];
            $_SESSION['CALENDARIO_CADASTRO_MESANGEM'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
            exit(json_encode($retorna));
        }
    }
    public function calendarioEditar()
    {
        if (!$this->validationSessao()) {
            header("Location: /404");
        }
        $id = filter_input(INPUT_POST, 'id', FILTER_UNSAFE_RAW);
        $title = filter_input(INPUT_POST, 'title', FILTER_UNSAFE_RAW);
        $color = filter_input(INPUT_POST, 'color', FILTER_UNSAFE_RAW);
        $start = filter_input(INPUT_POST, 'start', FILTER_UNSAFE_RAW);
        $end = filter_input(INPUT_POST, 'end', FILTER_UNSAFE_RAW);
        //Converter a data e hora do formato brasileiro para o formato do Banco de Dados
        $data_start = str_replace('/', '-', $start);
        $data_start_conv = date("Y-m-d H:i:s", strtotime($data_start));

        $data_end = str_replace('/', '-', $end);
        $data_end_conv = date("Y-m-d H:i:s", strtotime($data_end));

        $calendario = Container::getModel('Calendario');
        $calendario->__set("id", $id);
        $calendario->__set("title", $title);
        $calendario->__set("color", $color);
        $calendario->__set("start", $data_start_conv);
        $calendario->__set("end", $data_end_conv);

        $this->view->calendarioCadastro = $calendario->editar();
        if ($this->view->calendarioCadastro) {
            $retorna = ['CALENDARIO_CADASTRO_SUCCESS_KEY' => true, 'mensagem' => '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>'];
            $_SESSION['CALENDARIO_CADASTRO_MESANGEM'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
            exit(json_encode($retorna));
        } else {
            $retorna = ['CALENDARIO_CADASTRO_SUCCESS_KEY' => false, 'mensagem' => '<div class="alert alert-danger" role="alert">Erro: Evento não foi cadastrado com sucesso!</div>'];
            $_SESSION['CALENDARIO_CADASTRO_MESANGEM'] = '<div class="alert alert-success" role="alert">Evento cadastrado com sucesso!</div>';
            exit(json_encode($retorna));
        }
    }
    public function getEnquete()
    {
        $idTurmaGetUrl = isset($_GET['id']) ? $_GET['id'] : null;
        $viewEnquete = Container::getModel('Enquete');
        $viewEnquete->__set('idTurma', $idTurmaGetUrl);
        return $viewEnquete->mostrarEnquete();
    }
    public function getEe($idd)
    {
        $id = $idd;
        return $id;
    }
    public function validationSessao()
    {
        $validation = new AuthController();
        return $validation->sessao();
    }
}
