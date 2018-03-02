// ====================
// új üzenet létrehozása
// ===================
// $('form#create-message-form').submit((e) => {
//     e.preventDefault();

//     let partnerId = localStorage.getItem('message-partner-id');
//     let text = $('textarea#').val();

//     $.ajax({
//         type: 'POST',
//         url: SERVER_ROOT + 'parsers/main-parser.php',
//         data: {
//             'new-message': true,
//             'partner-id': partnerId,
//             'text': text
//         },
//         success: (resp, xhr, status) => {
//             if (resp == 'success') {
//                 alert('Az üzenetet sikeresen elküldtük!');
//             } else {
//                 alert('Valami hiba történt az üzenet küldése közben!');
//             }
//         },
//         error: (status, xhr, error) => {
//             alert(error);
//         }
//     });
// });

//üzenetbuborékot létrehozó függvény
function createMessageBubble(message, className) {
    let msgBubble = $('<div>', {
        class: className,
        title: message.date ? message.date : 'Nem rég küldve'
    });

    let p = $('<p>', {
        html: message.text
    });
    p.appendTo(msgBubble);
    msgBubble.appendTo('#conversation');
}

//üzenet előnézetet létrehozó függvény (popup ablakban)
function createMessagePreview(message) {
    //létező listelem eltávolítása
    $('li#partner-' + message.sender_id).remove();

    //keresztvén kinyerése
    let partnerName = message.sender_name.split(' ')[1];

    //html létrehozása, beszúrása a lista elejére
    let li = $('<li>', { class: 'message-item unread-message', 'data-message-id': message.id, 'data-partner-id': message.sender_id, id: 'partner-' + message.sender_id });
    let span = $('<span>', { css: { 'background-image': 'url(' + SERVER_ROOT + 'uploads/avatars/' + message.sender_avatar + ')' } });
    let h4 = $('<h4>', { html: message.sender_name });
    let p = $('<p>', { html: partnerName +': ' + message.text });
    let time = $('<time>', { html: message.date });
    span.appendTo(li);
    h4.appendTo(li);
    time.appendTo(li);
    p.appendTo(li);
    li.prependTo('.messages-popup section');
}

//üzenetek aljára görgetés
function scrollToBottom() {
    $('#read-message #conversation').animate({
        scrollTop: $('#read-message #conversation')[0].scrollHeight
    }, 200);
}

// ====================
// üzenet küldése
// ===================
function sendMessage() {

    let partnerId = localStorage.getItem('message-partner-id');
    let text = $('textarea#message').val();

    if (text == '') return false;

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: {
            'new-message': true,
            'partner-id': partnerId,
            'text': text
        },
        success: (resp, xhr, status) => {
            if (resp == 'success') {
                $('textarea#message').val('');
                createMessageBubble({ text: text, date: false }, 'msg-bubble clear msg-own');
                scrollToBottom();
            } else {
                alert('Valami hiba történt az üzenet küldése közben!');
            }
        },
        error: (status, xhr, error) => {
            alert(error);
        }
    });
}
$('button#btn-send-message').click(() => { sendMessage(); });
$('textarea#message').keyup((e) => {
    if (e.keyCode == 13) sendMessage();
});

// ====================
// új üzenetek figyelése
// ===================
function getNewMessages() {
    let beep = new Audio(PUBLIC_ROOT + 'resources/sounds/beep.mp3');

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'has-new-message': true },
        success: (resp, xhr, status) => {
            let messages = JSON.parse(resp);

            if (messages.length < 1) { return false; }

            let unreadCounter = parseInt($('span#message-counter').html()) + messages.length;
            $('span#message-counter').html(unreadCounter);

            beep.play();

            for (message of messages) {

                if (localStorage.getItem('message-partner-id') == message.sender_id && $('#read-message').is(':visible')) {
                    createMessageBubble(message, 'msg-bubble clear');
                }

                createMessagePreview(message);
            }

            scrollToBottom();
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });
}

window.setInterval(getNewMessages, 5000);


// ====================
// párbeszéd megnyitása
// ===================
$('body').on('click', 'li.message-item', (e) => {

    $('.page-overlay').fadeIn(300);
    $('.page-overlay #read-message').show();

    let clickedLi = $(e.currentTarget);

    let message = $(e.currentTarget);
    let modal = $('.page-overlay #read-message');
    let partnerId = message.attr('data-partner-id');

    localStorage.setItem('message-partner-id', partnerId);


    let data = {};

    //ha olvasatlan üzenetre kattintottunk
    if ( clickedLi.hasClass('unread-message') ) {
        clickedLi.removeClass('unread-message');
        data['set-to-seen'] = true;
    }

    data['partner-id'] = partnerId;
    data['get-conversation'] = true;


    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: data,
        success: (resp, xhr, status) => {
            //console.log(resp);
            
            let r = JSON.parse(resp);
            let messages = JSON.parse(r.messages);
            let affectedMessages = r.affected_messages;

            if (messages.length < 1) { return; }

            $('#conversation').empty();

            for (message of messages) {

                let msgClass;

                if (message.is_own == 1)
                    msgClass = 'msg-bubble clear msg-own';
                else
                    msgClass = 'msg-bubble clear';    

                createMessageBubble(message, msgClass);
            }

            let unreadCounter = parseInt($('span#message-counter').html()) - parseInt(affectedMessages);
            $('span#message-counter').html(unreadCounter);

            scrollToBottom();

        }
    });
});

