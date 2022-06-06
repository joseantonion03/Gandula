$(document).ready(function () {
    $("#addevent").on("submit", function (event) {
        event.preventDefault();
        console.log(event);
        $.ajax({
            method: "POST",
            url: "/app/calendario/cadastrar",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (retorna) {
                location.reload();
            }
        })
    });

    $('.btn-canc-vis').on("click", function () {
        $('.visevent').slideToggle();
        $('.formedit').slideToggle();
    });

    $('.btn-canc-edit').on("click", function () {
        $('.formedit').slideToggle();
        $('.visevent').slideToggle();
    });

    $("#editevent").on("submit", function (event) {
        event.preventDefault();
        $.ajax({
            method: "POST",
            url: "/app/calendario/editar",
            data: new FormData(this),
            contentType: false,
            processData: false,
            success: function (retorna) {
                location.reload();
            }
        })
    });
});
