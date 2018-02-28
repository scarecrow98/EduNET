const SERVER_ROOT = 'http://localhost/EduNET/server/';
const PUBLIC_ROOT = 'http://localhost/EduNET/public/';

$.ajaxSetup({
    headers: {
        'Security-Token': $('input#security-token').val()
    }
});


// ====================
// csoporttagok listázása
// ====================
$('button.btn-view-members').click((e) => {
    $('.page-overlay').fadeIn(300);
    $('.page-overlay #view-members').show();
    $('#group-members').empty();

    let groupId = $(e.currentTarget).attr('data-group-id');

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'list-group-members': true, 'group-id': groupId },
        success: (resp, status, xhr) => {
            console.log(JSON.parse(resp));
            let members = JSON.parse(resp);

            if (members.length < 1) { $('#group-members').html('<li>Még nem vettél fel tagot ebbe a csoportba!</li>') }

            for (member of members) {
                let li = $('<li>');
                $('<h4>', { html: member.name }).appendTo(li);
                $('<img>', { src: SERVER_ROOT + 'uploads/avatars/' + member.avatar, width: 100, height: 100 }).appendTo(li);
                
                if ( $('#view-members').hasClass('teacher-true') ) {
                    $('<button>', {
                        class: 'btn-delete-member btn-rounded bg-3',
                        html: '<i class="ion-checkmark-round"></i> Csoporttag eltávolítása',
                        'data-user-id': member.id,
                        'data-group-id': groupId,
                    }).appendTo(li);   
                }

                li.appendTo('#group-members');
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });

});

// ====================
// beállítások frissítése
// ====================
$('button#btn-save-settings').click((e) => {
    e.preventDefault();

    let formData = new FormData();

    formData.append('update-user-settings', true);

    if ( $('#new-avatar').get(0).files[0] ) {
        formData.append('new-avatar', $('#new-avatar').get(0).files[0]);        
    }

    if ( $('#new-password1').val() && $('#new-password2').val() ) {
        formData.append('new-password1', $('#new-password1').val());
        formData.append('new-password2', $('#new-password2').val());
    }

    if ( $('#new-email').val() ) {
        formData.append('new-email', $('#new-email').val());
    }

    if ( localStorage.getItem('subscription-changed') ) {
        formData.append('new-email-subscription', $('#new-email-subscription').is(':checked') ? 1 : 0);
        localStorage.removeItem('subscription-changed');
    }

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, xhr, status) => {
            if ( resp == 'success' ) {
                alert('A beállításaidat sikeresen mentettük!');
                window.location.reload();
            } else {
                alert(resp);
            }
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });
});


// ====================
// feladatlap beküldése
// ====================
$('input#btn-submit-test').click((e) => {
    e.preventDefault();

    if (!confirm('Biztosan be akarod küldeni a feladatlapot?')) return false;
    
    // let answers = [
    //     {
    //         "task-id": 1,
    //         "task-type": 2,
    //         "answer": "Lorem ipsum dolor sit amet",
    //     },
    //     {
    //         "task-id": 3,
    //         "task-type": 2,
    //         "answer": "",    
    //     }
    // ];

    for (let i = 1; i < 10; i++ ){
        
    }

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/test-evaluator.php',
        data: { 'test-submission': JSON.stringify(answers) },
        success: (resp, xhr, status) => {
            alert(resp);
        }
    });
});
