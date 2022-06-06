function openAlertaSala() {
    document.querySelector('.criarsala').classList.add('criarsala-show')
}
document.querySelector('#criarsalaColor').addEventListener('change', (e)=>{
    document.querySelector('#criarsalaCodigo').value = e.target.value
})
document.querySelector('#criarsala-btn-cancelar').addEventListener('click', () => {
    document.querySelector('.criarsala').classList.remove('criarsala-show')
})

$('#trumbowyg-editor').trumbowyg({
    lang: 'pt_br',
    btns: [
        ['viewHTML'],
        ['undo', 'redo'], // Only supported in Blink browsers
        ['formatting'],
        ['strong', 'em', 'del'],
        ['superscript', 'subscript'],
        ['link'],
        ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
        ['fontfamily'],
        ['fontsize'],
        ['unorderedList', 'orderedList'],
        ['horizontalRule'],
        ['removeformat'],
        ['emoji'],
        ['giphy']
    ],
    plugins: {
        fontfamily: {
            fontList: [{
                    name: 'Arial',
                    family: 'Arial, Helvetica, sans-serif'
                },
                {
                    name: 'Open Sans',
                    family: '\'Open Sans\', sans-serif'
                }
            ]
        },
        fontsize: {
            sizeList: [
                '8px',
                '9px',
                '10px',
                '11px',
                '12px',
                '14px',
                '18px',
                '24px',
                '30px',
                '36px'
            ],
            allowCustomSize: false
        },
        giphy: {
            apiKey: 'MJgFeRbXKj6kV1FyEQNVG5Lg4dO4OiM4'
        }
    },
    autogrow: true
});