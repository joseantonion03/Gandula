<?php

namespace App;

use \MF\Init\Bootstrap;

class Route extends Bootstrap
{

    protected function initRoutes()
    {

        $routes['home'] = array(
            'route' => '/',
            'controller' => 'IndexController',
            'action' => 'index'
        );
        $routes['timeline'] = array(
            'route' => '/timeline',
            'controller' => 'IndexController',
            'action' => 'timeline'
        );
        $routes['teste'] = array(
            'route' => '/teste',
            'controller' => 'IndexController',
            'action' => 'teste'
        );
        $routes['sala'] = array(
            'route' => '/sala',
            'controller' => 'IndexController',
            'action' => 'sala'
        );
        $routes['membro'] = array(
            'route' => '/membro',
            'controller' => 'IndexController',
            'action' => 'membro'
        );
        $routes['membroLiber'] = array(
            'route' => '/app/sala/lider',
            'controller' => 'IndexController',
            'action' => 'membroLider'
        );
        $routes['perfil'] = array(
            'route' => '/perfil',
            'controller' => 'IndexController',
            'action' => 'perfil'
        );
        $routes['perfilFoto'] = array(
            'route' => '/app/perfil/foto',
            'controller' => 'IndexController',
            'action' => 'perfilUploadFoto'
        );
        $routes['perfilModificarSenha'] = array(
            'route' => '/app/perfil/senha',
            'controller' => 'IndexController',
            'action' => 'perfilModificarSenha'
        );
        $routes['perfilModificarDados'] = array(
            'route' => '/app/perfil/dados',
            'controller' => 'IndexController',
            'action' => 'perfilModificarDados'
        );
        $routes['calendario'] = array(
            'route' => '/calendario',
            'controller' => 'IndexController',
            'action' => 'calendario'
        );
        $routes['calendarioMostrar'] = array(
            'route' => '/app/calendario/mostrar',
            'controller' => 'IndexController',
            'action' => 'calendarioMostrar'
        );  
        $routes['calendarioCadastrar'] = array(
            'route' => '/app/calendario/cadastrar',
            'controller' => 'IndexController',
            'action' => 'calendarioCadastrar'
        ); 
        $routes['calendarioEditar'] = array(
            'route' => '/app/calendario/editar',
            'controller' => 'IndexController',
            'action' => 'calendarioEditar'
        );  
        $routes['mensagem'] = array(
            'route' => '/mensagem',
            'controller' => 'IndexController',
            'action' => 'mensagem'
        );
        $routes['enquete'] = array(
            'route' => '/enquete',
            'controller' => 'IndexController',
            'action' => 'enquete'
        );
        $routes['enqueteCriar'] = array(
            'route' => '/app/enquete/criar',
            'controller' => 'IndexController',
            'action' => 'enqueteCriar'
        );
        $routes['enqueteVotos'] = array(
            'route' => '/app/enquete/votos',
            'controller' => 'IndexController',
            'action' => 'enqueteVotos'
        );
        $routes['enqueteVotosUsuarios'] = array(
            'route' => '/app/enquete/usuarios',
            'controller' => 'IndexController',
            'action' => 'enqueteVotosUsuarios'
        );
        $routes['sessao'] = array(
            'route' => '/app/auth/sessao',
            'controller' => 'AuthController',
            'action' => 'sessao'
        );
        $routes['login'] = array(
            'route' => '/app/auth/login',
            'controller' => 'AuthController',
            'action' => 'login'
        );
        $routes['cadastro'] = array(
            'route' => '/app/auth/cadastro',
            'controller' => 'AuthController',
            'action' => 'cadastro'
        );
        $routes['turma'] = array(
            'route' => '/app/turma/criar',
            'controller' => 'TurmaController',
            'action' => 'turmaCriar'
        );
        $routes['turmaEntrar'] = array(
            'route' => '/app/turma/entrar',
            'controller' => 'TurmaController',
            'action' => 'turmaEntrar'
        );
        $routes['postagem'] = array(
            'route' => '/app/postagem/criar',
            'controller' => 'TurmaController',
            'action' => 'postagemCriar'
        );
        $routes['sair'] = array(
            'route' => '/app/auth/sair',
            'controller' => 'AuthController',
            'action' => 'sair'
        );
        $this->setRoutes($routes);
    }


}
