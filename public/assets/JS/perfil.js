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

const upload = new Upload({ apiKey: "public_W142hQ8EVRFxai6Pge3NsvvgkgSG" });
const url = window.location.protocol + "//" + window.location.host + "/app/perfil/foto";

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