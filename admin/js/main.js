$(document).ready(function() {

    //jelszó generátor
    $('button#btn-generate-pass').click((e) => {
        e.preventDefault();

        let chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let pass = '';

        for (let i = 0; i <= 7; i++)
            pass += chars.charAt(Math.floor(Math.random() * chars.length));

        $('input[name="user-password"]').val(pass);
    });

});