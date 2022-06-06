function openAlertaSala() {
    document.querySelector('.adicionarsala').classList.add('adicionarsala-show')
}

document.querySelector('#adicionarsalaColor').addEventListener('change', (e)=>{
    document.querySelector('#adicionarsalaCodigo').value = e.target.value
})
document.querySelector('#adicionarsala-btn-cancelar').addEventListener('click', () => {
    document.querySelector('.adicionarsala').classList.remove('adicionarsala-show')
})

