const SERVER_ROOT = 'http://localhost/EduNET/server/';
const PUBLIC_ROOT = 'http://localhost/EduNET/public/';

$.ajaxSetup({
    headers: {
        'Security-Token': $('input#security-token').val()
    }
});

// ====================
// üzenet küldése
// ===================
$('form#create-message-form').submit((e) => {
    e.preventDefault();

    let receiverId = $('select#message-receiver').val();
    let text = $('textarea#message-text').val();

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: {
            'new-message':	true,
            'receiver-id':	receiverId,
            'text': 		text
        },
        success: (resp, xhr, status) => {
            if( resp == 'success' ){
				alert('Az üzenetet sikeresen elküldtük!');
			}else{
				alert('Valami hiba történt az üzenet küldése közben!');
			}
        },
        error: (status, xhr, error) => {
            alert(error);
        }
    });
});

// ====================
// új üzenetek figyelése
// ===================
function getNewMessages() {
    let beep = new Audio(PUBLIC_ROOT+'resources/sounds/beep.mp3');
	
    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: { 'has-new-message': true },
        success: (resp, xhr, status) => {
            let messages = JSON.parse(resp);
			
            if (messages.length < 1) { return false; }
            
            let unreadCounter = parseInt($('span#message-counter').html()) + messages.length;
            $('span#message-counter').html(unreadCounter);
			
            beep.play();

            for ( message of messages ){
                let li = $('<li>', { class: 'message-item unread-message', 'data-message-id': message.id });
                let span = $('<span>', { css: { 'background-image': 'url('+SERVER_ROOT+'uploads/avatars/'+message.sender_avatar+')' } });
                let h4 = $('<h4>', { html: message.sender_name });
                let p = $('<p>', { html: message.text });
                let time = $('<time>', { html: message.date });
                span.appendTo(li);
                h4.appendTo(li);
                time.appendTo(li);
                p.appendTo(li);
                li.prependTo('.messages-popup section');
            }
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });
}

window.setInterval(getNewMessages, 5000);


// ====================
// üzenet megnyitása
// ===================
$('body').on('click', 'li.message-item', (e) => {

    $('.page-overlay').fadeIn(300);
    $('.page-overlay #read-message').show();

    let message = $(e.currentTarget);
    let modal = $('.page-overlay #read-message');
    let messageId = message.attr('data-message-id');

    $(e.currentTarget).removeClass('unread-message');

    modal.find('h4').html(message.find('h4').html());
    modal.find('small').html(message.find('small').html());
    modal.find('p').html(message.find('p').html());

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: { 'message-seen': true, 'message-id': messageId },
        success: (resp, xhr, status) => {
			
            if ( resp == 'success' ) {
                let unreadCounter = parseInt($('span#message-counter').html()) - 1;
                $('span#message-counter').html(unreadCounter);   
            }
        }
    });
});


// ====================
// feladatlap létrehozása
// ====================
$('form#create-test-form').submit((e) => {
    e.preventDefault();

    let formData = new FormData();
    formData.append('create-new-test', true);
    formData.append('title', $('#test-title').val());
    formData.append('description', $('#test-description').val());
    formData.append('text', $('#test-text').val());
    formData.append('group-id', $('#test-group').val());
    formData.append('subject-id', $('#test-subject').val());
    formData.append('task-count', $('#test-taskcount').val());

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, status, xhr) => {
            if( resp == 'success' ){
                alert('Feladatlap sikeresen létrehozva!');
                window.location.assign('add_task');
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });
});

// ====================
// feladat létrehozása
// ====================
$('form#create-task-form').submit((e) => {
    e.preventDefault();

    let taskType = $('#task-type').val();
    let optionNumber = 0;
    let parentContainer = '';
    let optionTexts = [];
    let optionAnswers = [];
    let formData = new FormData();
    formData.append('create-new-task', true);
    formData.append('question', $('#task-question').val());
    formData.append('text', $('#task-text').val());
    formData.append('type', taskType);
    formData.append('image', $('#task-image').get(0).files[0]);
    formData.append('max_points', $('#task-points').val());

    //választott feladattípushoz tartozó opciók begyűjtése
    switch( taskType ){
        case '1':
            optionNumber = $('section.quiz-options').children().length;
            for (let i = 1; i <= optionNumber; i++) {
                optionTexts[i] = $('section.quiz-options input[name=option-text-' + i + ']').val();
                optionAnswers[i] = +$('section.quiz-options input[name=option-ans-' + i + ']').is(':checked');
            }
            break;
        case '3':
            optionNumber = $('section.pairing-options').children().length;
            for (let i = 1; i <= optionNumber; i++) {
                optionTexts[i] = $('section.pairing-options input[name=option-text-' + i + ']').val();
                optionAnswers[i] = $('section.pairing-options input[name=option-ans-' + i + ']').val();
            }
            break;
        case '4':
            optionNumber = $('section.truefalse-options').children().length;
            for (let i = 1; i <= optionNumber; i++) {
                optionTexts[i] = $('section.truefalse-options input[name=option-text-' + i + ']').val();
                optionAnswers[i] = $('section.truefalse-options input[name=option-ans-' + i + ']:checked').val();
            }
            break;
    }
    //formData.append('task-option-number', optionNumber);
    formData.append('option_texts', JSON.stringify(optionTexts));
    formData.append('option_answers', JSON.stringify(optionAnswers));
    
    // for (val of formData.entries()) {
    //     console.table(val[0] + " - " + val[1]);
    // }

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, status, xhr) => {
            if( resp == 'success' ){
                alert('A feladat sikeresen hozzá lett adva a feladatlaphoz!');
                $('form#create-task-form')[0].reset();
                window.location.reload();
            }else if( resp == 'end' ){
                alert('A feladatlapod sikeresen elkészült. Az OK gomb lenyomása után visszatérsz a főoldalra!');
                window.location.assign('tests');
            }else{
                alert(resp);
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });

    let count = $('input#mennyiseg').val();
});


// ====================
// értesítés létrehozása
// ===================
$('form#create-notification-form').submit((e) => {
    e.preventDefault();
    let formData = new FormData();
    formData.append('create-new-notification', true);
    formData.append('text', $('input#nt-text').val());
    formData.append('group_id', $('select#nt-group-id').val());
    formData.append('subject_id', $('select#nt-subject-id').val());
    formData.append('date', $('input#nt-date').val());
    formData.append('type', $('input#nt-type').val());

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, xhr, status) => {
            if (resp == 'success') {
                alert('Az értesítés sikeresen létrehozva!');
                window.location.reload();
            }else{
				alert(resp);
			}
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });
});

// ====================
// értesítés törlése
// ====================
$('i.btn-delete-notification').click((e) => {
    console.log('click');
    let conf = confirm('Biztosan törölni szeretnéd az értesítést?');
    if (!conf) return false; 
    
    let notificationId = $(e.currentTarget).attr('data-notification-id');

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'delete-notification': true, 'notification-id': notificationId },
        success: (resp, xhr, status) => {
            if ( resp == 'success' ) {
                $(e.currentTarget).parent('li').animate({
                    height: '0'
                }, 200, function () { $(this).remove(); });
            }
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });

});

// ====================
// csoport létrehozása
// ====================
$('form#create-group-form').submit((e) => {
    e.preventDefault();

    let formData = new FormData();
    formData.append('create-new-group', true);
    formData.append('name', $('#group-name').val());
    formData.append('description', $('#group-description').val());
    formData.append('avatar', $('#group-avatar').get(0).files[0]);

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, status, xhr) => {
            if (resp == 'success') {
                alert('A csoport sikeresen létrehozva');
                window.location.reload();
            }
            else {
                alert(resp);
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });
});

// ====================
// diákok keresése és listázása
// ====================
$('input#student-name').keyup((e) => {
    let studentName = $(e.currentTarget).val();

    //ha a mező értéke üres, akkor ne küldjünk AJAX kérést feleslegesen
    if (studentName.length < 1) {
        $('ul.student-results').empty();
        return false;
    }

    //ha a tanuló hozzá lett már adva a csoporthoz, ne lehessen mégegyszer megtenni
    if ($(e.currentTarget).html() == 'Hozzáadva') {
        return false;
    }

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/search-parser.php',
        data: { 'student-name': studentName, 'group-id': localStorage.getItem('group-id') },
        success: (resp, status, xhr) => {
            //szerver válaszának visszalakítása JS tömbbé
            let results = JSON.parse(resp);
            console.log(results);

            //ha a tömb üres, akkor nem volt találat
            if( results.length < 1 ){
                $('ul.student-results').html('<li>Nincs eredmény</li>');
            }
            //ha nem üres, akkor felsoroljuk a találatokat egy listába
            else{
                $('ul.student-results').empty();
                for (student of results) {
                    let li = $('<li>',  { });

                    $('<img>', { src: SERVER_ROOT+'uploads/avatars/' + student.avatar }).appendTo(li);	
					
                    $('<h4>', { html: student.name }).appendTo(li);
					
                    $('<button>', {
						'data-student-id': student.id,
						'data-student-name': student.name,
						html: '<i class="ion-checkmark-round"></i> Tanuló felvétele',
						class: 'btn-add-student btn-rect bg-1',
					}).appendTo(li);
					
                    li.appendTo('ul.student-results');
                }
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });
});

// ====================
// tanuló hozzáadása a csoporthoz
// ====================
$('body').on('click', 'button.btn-add-student', (e) => {
    e.preventDefault();

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: {
            'add-group-member': true,
            'group-id': localStorage.getItem('group-id'),
            'student-id': $(e.currentTarget).attr('data-student-id')
        },
        success: (resp, status, xhr) => {
            if (resp == 'success') {
                $(e.currentTarget).html('Hozzáadva').removeClass('bg-1').addClass('bg-3');
            } else {
                alert(resp);
            }
        },
        error: (xhr, status, error) => {
            //hiba
        }
    });

});

// ====================
// feladatlap megosztása
// ====================
$('form#share-test-form').submit((e) => {
    e.preventDefault();

    let newTestAuthor = $('select#new-test-author').val();
    let newTestGroup = $('select#new-test-group').val();
    let testId = localStorage.getItem('test-id');

    if (!testId) {
        alert('Valami hiba történt!');
        return false;
    }

    localStorage.removeItem('test-id');

    $.ajax({
        type: 'POST',
        url: 'parsers/main-parser.php',
        data: {
            'share-test': true,
            'new-test-author': newTestAuthor,
            'new-test-group': newTestGroup,
            'original-test': testId,
        },
        success: (resp, status, xhr) => {
            alert(resp);
        },
        error: (xhr, status, error) => {
            alert(error);
        }
    });
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
// csoporttag törlése
// ===================
$('body').on('click', 'button.btn-delete-member', (e) => {

    if (confirm('Biztosan törölni szeretnéd ezt a csoporttagok?') == false) { return false; }

    let userId = $(e.currentTarget).attr('data-user-id');
    let groupId = $(e.currentTarget).attr('data-group-id');

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'delete-group-member': true, 'user-id': userId, 'group-id': groupId },
        success: (resp, xhr, status) => {
            if (resp == 'success') {
                $(e.currentTarget).parent('li').animate({
                    height: '0'
                }, 200, function () { $(this).remove(); });
            }
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

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT+'parsers/main-parser.php',
        data: formData,
        processData: false,
        contentType: false,
        success: (resp, xhr, status) => {
            if (resp == 'success') {
                alert('Sikeresen mentettük a beállításaidat!');
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


