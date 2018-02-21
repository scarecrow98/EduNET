$(document).ready(function() {

    $('button#btn-generate-pass').click((e) => {
        e.preventDefault();

        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let pass = '';

        for (let i = 0; i <= 7; i++)
            pass += chars.charAt(Math.floor(Math.random() * chars.length));

        $('input[name="user-password"]').val(pass);
    });

    $('input#search-user').keyup((e) => {
        let q = $(e.currentTarget).val().trim();

        if (q == '') {
            $('#users li').css('background', 'transparent')
            return false;
        }

        $('#users li').css('background', 'transparent')
        $('#users li:contains(' + q + ')').css('background', '#c7c7c7');
    });


});