
![Logo](https://gandula.000webhostapp.com/Arquivos/Frame%201058.png)

# Sistema de agendamento e reposição de aula

O projeto está sendo desenvolvido como requisito fundamental para finalização do curso técnico em informática para internet, o PCC (Projeto de Conclusão do Curso) no Instituto Federal Baiano Campus Guanambi.

## Criadores

- [@joseantonion03](https://www.github.com/joseantonion03)
- [@CauaneC](https://www.github.com/CauaneC)
- [@lauradias1](https://www.github.com/lauradias1)

## Pilha de tecnologia

As seguintes ferramentas foram usadas na construção do projeto:

**Linguagens:** HTML, CSS, JavaScript, PHP

**Compilador CSS:** Sass

**Framework:** Booststrap

**Bibliotecas:** Jquery, Font Awesome, Trumbowyg, Chart.js, FullCalendar, dropbox-api, Ratchet PHP

**Banco de Dados:** MySQL

## Executar o projeto

### Pré-requisitos

Antes de começar, você vai precisar ter instalado em sua máquina o PHP e o MySQL, recomendamos o que instale o [XAMPP](https://www.apachefriends.org/index.html), um pacote que já tudo pronto e configurado. Além disto é bom ter um editor para trabalhar com o código como [VSCode](https://code.visualstudio.com/).

### Windows/Mac OS/Linux

Antes de tudo, caso esteja utilizando o pacote XAMPP/LAMPP, é necessário colocar os arquivos do projeto na pasta HTDOCS, logo após, procure o arquivo httpd.conf e faça as alterações abaixo:

Antes
```bash
  DocumentRoot "C:/xampp/htdocs"
  <Directory "C:/xampp/htdocs">
```
Depois
```bash
  DocumentRoot "C:/xampp/htdocs/Gandula/public"
  <Directory "C:/xampp/htdocs/Gandula/public">
```


Após feito as alterações do arquivo httpd.conf, basta iniciar o projeto pelo pacote XAMPP, na opção START.

![Start XAMPP](https://gandula.000webhostapp.com/Arquivos/Captura%20de%20tela%202022-06-05%20231016.png)

## Como gerar o token do dropbox-api?

Instale os arquivos necessário do dropbox-api via composer

```bash
  composer require spatie/dropbox-api

```
- [Documentação  do dropbox-api](https://github.com/spatie/dropbox-api)

Logo após fazer o download do projeto, no arquivo env.ini, existe uma variável chamada "tokendropbox", nela é onde iremos colocar o token de acesso para podermos armazenar nossas imagens no sistema de armazenamento do dropbox. Para obter o token de acesso basta acessar o link abaixo:

- [Dropbox-api](https://www.dropbox.com/developers/apps)

Se houve dúvidas de como gerar o token, segue o tutorial abaixo:

- [Youtube: Upload de arquivos para o Dropbox com PHP - WDEV](https://www.youtube.com/watch?v=mLj3g5VbCcQ)

Agora que já temos o token de acesso, não esqueça de criar uma pasta no dropbox chamada "Gandula"

## Como executar o serviço de WebSocket (Ratchet)?

Fácil! Dentro da pasta PUBLIC, temos um arquivo chamado app.php, basta executarmos via terminal usando uma instalação PHP. OBS.: A instalação PHP precisa está configurada nas variáveis de ambiente, o PATH.

```bash
  php app.php

```

## Screenshots

![App Timeline](https://gandula.000webhostapp.com/Arquivos/Captura%20de%20tela%202022-06-05%20232357.png)


## Suporte

Para suporte, e-mail: ja2915588@gmail.com.
