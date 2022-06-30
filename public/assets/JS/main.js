document.querySelector('.formulario-icone-password').addEventListener('click', (e) => {
  // console.log();
  e.target.classList.toggle('fa-eye-slash')
  const inputPassword = document.getElementById('floatingPassword');
  inputPassword.type = inputPassword.type == 'password' ? 'text' : 'password';
})
document.querySelector('#cadastrarUsuario').addEventListener('click', () => {
  document.querySelector('#cadastro').classList.add('cadastro-show')
})
document.querySelector('.btn-close-cadastro').addEventListener('click', () => {
  document.querySelector('#cadastro').classList.remove('cadastro-show')
})
document.querySelectorAll('.submit-cadastro-ocupacao').forEach((e) => {
  e.addEventListener('click', (element) => {
    document.getElementById('docente-box').classList.remove('input-radio-checked')
    document.getElementById('discente-box').classList.remove('input-radio-checked')
    document.querySelector(`#${e.dataset.name}-box`).classList.add('input-radio-checked');
  })
})
/*
$(function () {
  $("#form-cadastro-container").owlCarousel({
    items: 1,
    look: true,
    nav: false,
    responsiveClass: true,
    autoPlayHoverPause: false
  })
})*/
/*
document.querySelector('.btn-next').addEventListener('click', () => {
  console.log('ee');
})*/
/*
var owl = $('#form-cadastro-container');
owl.owlCarousel();*/

//input-radio-checked
/*
var googleUser = {};
var startApp = function () {
  gapi.load('auth2', function () {
    // Retrieve the singleton for the GoogleAuth library and set up the client.
    auth2 = gapi.auth2.init({
      client_id: 'YOUR_CLIENT_ID.apps.googleusercontent.com',
      cookiepolicy: 'single_host_origin',
      // Request scopes in addition to 'profile' and 'email'
      //scope: 'additional_scope'
    });
    attachSignin(document.getElementById('customBtn'));
  });
};

function attachSignin(element) {
  console.log(element.id);
  auth2.attachClickHandler(element, {},
    function (googleUser) {
      document.getElementById('name').innerText = "Signed in: " +
        googleUser.getBasicProfile().getName();
    }, function (error) {
      alert(JSON.stringify(error, undefined, 2));
    });
}
startApp();
*/

const btnEnviar = document.querySelector('#btn-login');
btnEnviar.addEventListener('click', login);
const Toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 5000,
  timerProgressBar: true,
  didOpen: (toast) => {
    toast.addEventListener('mouseenter', Swal.stopTimer)
    toast.addEventListener('mouseleave', Swal.resumeTimer)
  }
})

async function login(e) {
  e.preventDefault()

  document.querySelector('#btn-login').setAttribute('disabled', 'true');
  btnEnviar.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Carregando...`;

  let email = document.querySelector('#floatingInput').value
  let senha = document.querySelector('#floatingPassword').value

  const url = window.location.protocol + "//" + window.location.host + "/app/auth/login";

  if (email.length > 0 && senha.length > 0) {
    await $.ajax({
      url: url,
      method: 'POST',
      dataType: 'json',
      data: {
        email: email,
        senha: senha
      },
      success: function (response) {
        if (response.response == 'success') {
          Toast.fire({
            icon: 'success',
            title: 'Conectado com sucesso'
          })
          location.href = "/timeline";
        } else {

          Toast.fire({
            icon: 'error',
            title: 'Sua e-mail ou sua senha está incorreta'
          })
          document.querySelector('.form-erro').classList.add('form-erro-show')
          document.querySelector('.form-erro span').innerHTML = "Sua e-mail ou sua senha está incorreta"
          document.querySelector('#btn-login').removeAttribute('disabled');
          btnEnviar.innerHTML = `<i class="fas fa-sign-in-alt"></i> Acessar`;
        }
      }
    });
  } else {
    Toast.fire({
      icon: 'warning',
      title: 'É necessário preencher os campos abaixo'
    })
    document.querySelector('.form-erro').classList.add('form-erro-show')
    document.querySelector('.form-erro span').innerHTML = "É necessário preencher os campos abaixo"
    document.querySelector('#btn-login').removeAttribute('disabled');
    btnEnviar.innerHTML = `<i class="fas fa-sign-in-alt"></i> Acessar`;
  }

}

let timeline = document.querySelectorAll('.caminho-form-container-icon');
var currentTab = 0; // Current tab is set to be the first tab (0)
showTab(currentTab); // Display the current tab
function showTab(n) {
  // This function will display the specified tab of the form...
  var x = document.getElementsByClassName("tab");
  x[n].style.display = "flex";
  //... and fix the Previous/Next buttons:
  if (n == 0) {
    document.getElementById("prevBtn").style.display = "none";
  } else {
    document.getElementById("prevBtn").style.display = "inline";
  }
  if (n == (x.length - 1)) {
    document.getElementById("nextBtn").innerHTML = "Finalizar";
  } else {
    document.getElementById("nextBtn").innerHTML = "Próxima";
  }
  //... and run a function that will display the correct step indicator:
  fixStepIndicator(n)
}

function nextPrev(n) {
  // This function will figure out which tab to display
  var x = document.getElementsByClassName("tab");
  // Exit the function if any field in the current tab is invalid:
  if (n == 1 && !validateForm()) return false;
  // Hide the current tab:
  x[currentTab].style.display = "none";
  var currentTabAnterior = currentTab;
  // Increase or decrease the current tab by 1:
  currentTab = currentTab + n;
  if (currentTab >= 0) {
    if (currentTab < x.length) {
      timeline[currentTab].classList.add('caminho-icon-analise')
    }
    if (currentTab > currentTabAnterior) {
      timeline[currentTabAnterior].classList.remove('caminho-icon-analise')
      timeline[currentTabAnterior].classList.add('caminho-icon-active')
    }
    if (currentTab < currentTabAnterior) {
      timeline[currentTab].classList.remove('caminho-icon-active')
      timeline[currentTabAnterior].classList.remove('caminho-icon-analise')
    }
  }
  // if you have reached the end of the form...
  if (currentTab >= x.length) {
    // ... the form gets submitted:
    document.getElementById("regForm").submit();
    return false;
  }
  // Otherwise, display the correct tab:
  showTab(currentTab);
}

function validateForm() {
  // This function deals with validation of the form fields
  var x, y, i, valid = true;
  x = document.getElementsByClassName("tab");
  y = x[currentTab].getElementsByTagName("input");
  // A loop that checks every input field in the current tab:
  for (i = 0; i < y.length; i++) {
    // If a field is empty...
    if (currentTab < 2) {
      if (y[i].value == "") {
        // add an "invalid" class to the field:
        y[i].className += " invalid";
        // and set the current valid status to false
        valid = false;
      }
    }
  }
  // If the valid status is true, mark the step as finished and valid:
  if (valid) {
    document.getElementsByClassName("step")[currentTab].className += " finish";
  }
  return valid; // return the valid status
}

function fixStepIndicator(n) {
  // This function removes the "active" class of all steps...
  var i, x = document.getElementsByClassName("step");
  for (i = 0; i < x.length; i++) {
    x[i].className = x[i].className.replace(" active", "");
  }
  //... and adds the "active" class on the current step:
  x[n].className += " active";
}
