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

    let taskCount = $('input#task-count').val();
    let formData = new FormData();
    let answers = [];

    for ( let i = 1; i <= taskCount; i++ ){
        let data = $('input#task-' + i + '-data').val();
        let taskData = JSON.parse(data);
        let answer = new Object();

        answer['task-id'] = taskData['task-id'];

        if (taskData['task-options'].length > 0) {
            let options = [];

            for (x of taskData['task-options']) {
                let opt;
                switch (taskData['task-type']) {
                    //kvíz
                    case '1':
                        opt = {
                            'option-id': x,
                            'value': +$('input[name=option-' + x + ']').is(':checked')
                        }    
                        break; 
                    //párosítás
                    case '3':
                        opt = {
                            'option-id': x,
                            'value': $('input[name=option-' + x + ']').val()
                        }    
                        break; 
                    //igaz-hamis
                    case '4':
                        opt = {
                            'option-id': x,
                            'value': $('input[name=option-' + x + ']:checked').val()
                        }      
                        break; 
                }
                options.push(opt);
            }
            answer['task-options'] = options;

        } else {
            switch (taskData['task-type']) {
                //szöveges válasz
                case '2':
                    answer['text-answer'] = $('textarea[name=textarea-' + taskData['task-id'] + ']').val();
                    break;
                //fájlfeltöltés
                case '5':  
                    answer['file-name'] = 'file-' + taskData['task-id'];   
                    formData.append(answer['file-name'], $('input#'+answer['file-name']).get(0).files[0]);
                    break;
            }
        }

        answers.push(answer);
    }

    formData.append('answers', JSON.stringify(answers));
    formData.append('test-submission', true);

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/test-evaluator.php',
        processData: false,
        contentType: false,
        data: formData,
        success: (resp, xhr, status) => {
            if ( resp == 'success' ) {
                alert('A feladatlapod megoldásait sikeresen mentettük, ezennel nincs más dolgod ezzel a dolgozattal!');
                window.location.assign('home');
            } else {
                alert(resp);
            }
        }
    });
});
