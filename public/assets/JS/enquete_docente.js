const valorOpcao = document.getElementById('valor__opcao');
const btnOpcao = document.getElementById('valor__opcao__btn');
const opcao = document.getElementById('opcao');
const contentOpcao = document.getElementById('content-body-opcao');
let valorArray = [];

btnOpcao.addEventListener('click', (e) => {
    console.log('FDGDF');
    console.log(valorOpcao.value.length);
    if (valorOpcao.value.length > 0) {
        valorArray.push({ opcao: `${valorOpcao.value}` });


    }
    opcao.value = JSON.stringify(valorArray);
    contentOpcao.innerHTML += `
        <button type="button" class="fx-st-ctr">
            <div class="fx-center">
                <input type="radio" name="enquete" value="ee">
                <span>${valorOpcao.value}</span>
        </div>
        </button>
        `
    valorOpcao.value = '';
})

const btnAcaoEnquete = document.querySelectorAll('.container-enquete-diretorio button');
const contentEnquete = document.querySelectorAll('.container-enquete');

btnAcaoEnquete.forEach(element => {
    element.addEventListener('click', (e)=>{
        btnAcaoEnquete.forEach((elementBtn, index) => {
            elementBtn.classList.remove('active')  
            contentEnquete[index].classList.remove('active')
        })
        e.target.classList.add('active')
        contentEnquete[e.target.dataset.id].classList.add('active')
    })
})

const btnEnqueteShow = document.querySelectorAll('.btn__enquete__show');
const usuariosEnqueteShow = document.querySelectorAll('.enquete-resultado-usuarios');

btnEnqueteShow.forEach(element => {
    element.addEventListener('click', (e)=>{
        usuariosEnqueteShow[e.target.dataset.id].classList.toggle('active');
    })
})