const SERVER_ROOT = 'http://localhost/EduNET/server/';
const PUBLIC_ROOT = 'http://localhost/EduNET/public/';

$.ajaxSetup({
    headers: {
        'Security-Token': $('input#security-token').val()
    }
});

//felhasználói adatok lekérése a szerverről, és eltárolása a sessionStorage-be
$(document).ready(() => {
    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'get-user-data': true },
        success: (resp, xhr, status) => {
            let user = JSON.parse(resp);

            sessionStorage.setItem('user-name', user.name);
            sessionStorage.setItem('user-avatar', user.avatar);
            sessionStorage.setItem('user-email', user.email);
        }
    });
});
