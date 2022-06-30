/* websocket functions */

var mySocket;
const socketMessageListener = (event) => {
    var data = JSON.parse(event.data);
    if (data.nick != "") {
        $(".nick").html(data.nick);
    }
    setTimeout(function () {
        fakeMessage(data.from, data.msg);
    }, 100);
};

const socketOpenListener = (event) => { // Open
    console.log('Connected');
};

const socketCloseListener = (event) => { // Closed
    if (mySocket) {
        console.error('Disconnected.');
    }
    mySocket = new WebSocket('ws://10.70.202.155:8080');
    mySocket.addEventListener('open', socketOpenListener);
    mySocket.addEventListener('message', socketMessageListener);
    mySocket.addEventListener('close', socketCloseListener);
}; socketCloseListener();

/* chat functions */

var $messages = $('.messages-content'),
    d, h, m,
    i = 0;

$(window).load(function () {
    $messages.mCustomScrollbar();
});

function updateScrollbar() {
    $messages.mCustomScrollbar("update").mCustomScrollbar('scrollTo', 'bottom', {
        scrollInertia: 10,
        timeout: 0
    });
}

function setDate() {
    d = new Date()
    if (m != d.getMinutes()) {
        m = d.getMinutes();
        $('<div class="timestamp">' + d.getHours() + ':' + m + '</div>').appendTo($('.message:last'));
    }
}

function insertMessage() {
    msg = $('.message-input').val();
    if ($.trim(msg) == '') {
        return false;
    } sendMessage(nome_user_online, msg);
    $('<div class="message message-personal">' + msg + '</div>').appendTo($('.mCSB_container')).addClass('new');
    setDate();
    $('.message-input').val(null);
    updateScrollbar();
    setTimeout(function () {
        fakeMessage();
    }, 1000 + (Math.random() * 20) * 100);
}

$('.message-submit').click(function () {
    insertMessage();
});

$(window).on('keydown', function (e) {
    if (e.which == 13) {
        insertMessage(); return false;
    }
})

var Fake = ['Bate papo Iniciado com sucesso!']

function fakeMessage(from = '', msg = '') {
    if (i == 0 || msg != '') {
        $(`<div class="message loading new"><figure class="avatar"><img src="/assets/IMG/PERFIL/${foto_user_online}" /></figure><span></span></div>`).appendTo($('.mCSB_container'));
        updateScrollbar();
        setTimeout(function () {
            $('.message.loading').remove();
            $('<div class="message new" title="' + from + '"><figure class="avatar"><img src="/assets/IMG/PERFIL/' + foto_user_online + '" /></figure>' + (from != "" ? from + ': ' : 'Servidor: ') + (msg != '' ? msg : Fake[i]) + '</div>').appendTo($('.mCSB_container')).addClass('new');
            setDate();
            updateScrollbar();
            i++;
        }, 1000 + (Math.random() * 20) * 100);
    }
}

function sendMessage(nome, msg) {
    const data = {
        nome: nome,
        mensagem: msg
    };
    mySocket.send(JSON.stringify(data));
    salvarMessageBd(msg);
}


document.querySelector('.sala-informacoes-toggle').addEventListener('click', () => {
    document.querySelector('.sala-informacoes').classList.toggle('active');
    document.querySelector('body').classList.toggle('overflow-hidden');
    document.querySelector('.modal-bg-active').classList.toggle('active');
})


document.querySelector('.modal-bg-active').addEventListener('click', () => {
    document.querySelector('.sala-informacoes').classList.remove('active');
    document.querySelector('body').classList.remove('overflow-hidden');
    document.querySelector('.modal-bg-active').classList.remove('active');
})




function salvarMessageBd(texto) {
    const urlParams = new URLSearchParams(window.location.search);
    const getIDTurmaUrl = urlParams.get('id');
    const url = window.location.protocol + "//" + window.location.host + "/app/mensagem/salvar";

    $.ajax({
        url: url,
        method: 'POST',
        dataType: 'json',
        data: {
            id_turma: getIDTurmaUrl,
            texto: texto
        },
        success: function (response) {
            console.log(response);
        }
    });
};

