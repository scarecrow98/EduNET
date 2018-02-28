// ====================
// üzenet küldése
// ===================
$('form#create-message-form').submit((e) => {
    e.preventDefault();

    let receiverId = $('select#message-receiver').val();
    let text = $('textarea#message-text').val();

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: {
            'new-message': true,
            'receiver-id': receiverId,
            'text': text
        },
        success: (resp, xhr, status) => {
            if (resp == 'success') {
                alert('Az üzenetet sikeresen elküldtük!');
            } else {
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
                let li = $('<li>', { class: 'message-item unread-message', 'data-message-id': message.id });
                let span = $('<span>', { css: { 'background-image': 'url(' + SERVER_ROOT + 'uploads/avatars/' + message.sender_avatar + ')' } });
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
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'message-seen': true, 'message-id': messageId },
        success: (resp, xhr, status) => {

            if (resp == 'success') {
                let unreadCounter = parseInt($('span#message-counter').html()) - 1;
                $('span#message-counter').html(unreadCounter);
            }
        }
    });
});

