document.querySelector('.header-open-menu').addEventListener('click', () => {
    document.querySelector("#barralateral").classList.add('barralateral-show')
})
document.querySelector('.barralateral-close-menu').addEventListener('click', () => {
    document.querySelector("#barralateral").classList.remove('barralateral-show')
})
document.querySelector('#container').addEventListener('click', () => {
    document.querySelector("#barralateral").classList.remove('barralateral-show')
})

function copiarCodigo() {
    let textoCopiado = document.getElementById("codigo");
    textoCopiado.select();
    textoCopiado.setSelectionRange(0, 99999)
    document.execCommand("copy");
    document.querySelector('.copy-codigo').classList.add('copy-codigo-show');


    setTimeout(() => {
        document.querySelector('.copy-codigo').classList.remove('copy-codigo-show');
    }, 2000)
}

