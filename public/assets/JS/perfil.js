const inputConfiguracao = document.querySelectorAll('#nav-profile input');
const buttonConfiguracao = document.querySelector('#nav-profile button');

buttonConfiguracao.addEventListener('click', (e) => {

    inputConfiguracao.forEach((element) => {
        element.removeAttribute('readonly');
        buttonConfiguracao.innerHTML = "Salvar";
        setTimeout(() => {
            buttonConfiguracao.setAttribute('type', 'submit');
        }, 1000);
    });
});

const url = window.location.protocol + "//" + window.location.host + "/app/perfil/foto";
const readerImg = new FileReader();

document.getElementById('perfil__upload-file-input').addEventListener('change', function () {
    var imagemUser = document.getElementById('perfil__upload-file-input').files[0];
    readerImg.onloadend = function () {
        document.getElementById('perfil__upload-imagem').src = readerImg.result
        document.getElementById('perfil__upload-imagem-main').src = readerImg.result
        $.ajax({
            name: 'image',
            url: `/app/perfil/foto`,
            method: 'POST',
            dataType: 'json',
            type: "POST",
            processData: false,
            contentType: false,
            data: new FormData($('#myForm')[0]),
            cache: false,

            success: function (response) {
                if (response.status == "success") {
                    Toast.fire({
                        icon: 'success',
                        title: 'Arquivo carregado com sucesso'
                    })
                } else {
                    Toast.fire({
                        icon: 'error',
                        title: 'Não conseguimos carregar sua imagem'
                    })
                }
            }
        });
    }
    if (imagemUser) {
        readerImg.readAsDataURL(imagemUser);
    } else {
        preview.src = "";
    }

})



/*
$(() => {
    $("#perfil__upload-file-input").on("change",
        upload.createFileInputHandler({
            onBegin: () => {
                $("#perfil__upload-file-input").hide()
                $("#perfil__upload-titulo").show();
            },
            onProgress: ({ progress }) => {
                $("#perfil__upload-titulo .progress-bar").html(`${progress}%`)
                $("#perfil__upload-titulo .progress-bar").css("width", `${progress}%`);
            },
            onError: (error) => {
                $("#perfil__upload-titulo .progress-bar").html(`Error: ${error.message}`)
            },
            onUploaded: ({ fileUrl, fileId }) => {

                $.ajax({
                    url: url,
                    method: 'POST',
                    dataType: 'json',
                    data: {
                        image: fileUrl,
                    },
                    success: function (response) {
                        console.log(response);
                        if (response.status == "success") {
                            $("#perfil__upload-titulo .progress-bar").html(`Arquivo carregado com sucesso!`);
                            $("#perfil__upload-imagem").attr("src", fileUrl);
                            $("#perfil__upload-imagem-main").attr("src", fileUrl);
                        }
                    }
                });
            }
        })
    )
})
*/