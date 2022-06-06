

const btnEnquete = document.querySelectorAll('.btn__enquete');
const opcaoResposta = document.getElementById('opcao_resposta')

btnEnquete.forEach(element => {
    element.addEventListener('click', (e)=>{
        btnEnquete.forEach(elementBtn => {
            elementBtn.classList.remove('active');
        })
        document.querySelector(`.btn__enquete__${e.target.dataset.id}`).classList.add('active')
        opcaoResposta.value = e.target.dataset.id;
    })
});