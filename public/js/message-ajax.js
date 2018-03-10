// ====================
// új üzenet létrehozása
// ===================
$('form#create-message-form').submit((e) => {
    e.preventDefault();

    let partnerId = $('select#message-receiver').val();
    let text = $('textarea#message-text').val();

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: {
            'create-message': true,
            'partner-id': partnerId,
            'text': text
        },
        success: (resp, xhr, status) => {
            let data = JSON.parse(resp);

            createMessagePreview({
                'sender_id': partnerId,
                'sender_name': data.partner_name,
                'sender_avatar': data.partner_avatar,
                'id': data.message_id,
                'date': 'most',
                'text': text
            }, 'message-item');
        },
        error: (status, xhr, error) => {
            alert(error);
        }
    });
});

//üzenetbuborékot létrehozó függvény
function createMessageBubble(message, className) {
    let msgBubble = $('<div>', {
        class: className,
        title: message.date ? message.date : 'Nem rég küldve'
    });

    let p = $('<p>', { html: message.text });
    p.appendTo(msgBubble);
    msgBubble.appendTo('#conversation');
}

//üzenet előnézetet létrehozó függvény (popup ablakban)
function createMessagePreview(message, className) {
    //létező listelem eltávolítása
    $('li#partner-' + message.sender_id).remove();
 
    //html létrehozása, beszúrása a lista elejére
    let li = $('<li>', { class: className, 'data-message-id': message.id, 'data-partner-id': message.sender_id, id: 'partner-' + message.sender_id });
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

    let partnerId = sessionStorage.getItem('message-partner-id');
    let text = $('textarea#message').val();
    text = text.trim();

    //ha üres az üzenetmező, return false
    if (text.length < 1 || text == '\n') return false;

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
            //hiba
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

            //ha nincsenek új üzenetek, nem csinálunk semmit
            if (messages.length < 1) { return false; }

            //hang lejátszása
            beep.play();

            for (message of messages) {

                //ha éppen annak a partnernak van megnyitva az ablaka, akitől kaptunk üzenetet, akkor az ablakba hozunk létre egy buborékot
                if (sessionStorage.getItem('message-partner-id') == message.sender_id && $('#read-message').is(':visible')) {
                    createMessageBubble(message, 'msg-bubble clear');
                }
                
                //előnézet létrehozása
                createMessagePreview(message, 'message-item unread-message');
            }
            $('button#btn-messages').addClass('has-new-message');
            scrollToBottom();
        },
        error: (status, xhr, error) => {
            //hiba
        }
    });
}
//5 másodpercenként lekérjük az új üzeneteket
window.setInterval(getNewMessages, 5000);


// ====================
// párbeszéd megnyitása
// ===================
$('body').on('click', 'li.message-item', (e) => {

    $('.page-overlay').fadeIn(300);
    $('.page-overlay #read-message').show();

    let clickedLi = $(e.currentTarget);
    let partnerId = clickedLi.attr('data-partner-id');

    sessionStorage.setItem('message-partner-id', partnerId);


    let data = {};

    //ha olvasatlan üzenetre kattintottunk
    if ( clickedLi.hasClass('unread-message') ) {
        clickedLi.removeClass('unread-message');
        $('button#btn-messages').removeClass('has-new-message');
        data['set-to-seen'] = true; //ezzel a POST elemmel tudatjuk a szerverrel, hogy megnéztük az üzentetet, szóvál állítsa olvasottá
    }

    data['partner-id'] = partnerId;
    data['get-conversation'] = true;


    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: data,
        success: (resp, xhr, status) => {
            //console.log(resp);
            let messages = JSON.parse(resp);

            if (messages.length < 1) { return; }

            //párbeszéd ablak ürítése, és feltöltése lekért üzenetekkels
            $('#conversation').empty();
            for (message of messages) {

                let msgClass;

                //class név meghatározása, az alapján, hogy mi írtuk vagy kaptuk az üzenetet
                if (message.is_own == 1)
                    msgClass = 'msg-bubble clear msg-own';
                else
                    msgClass = 'msg-bubble clear';    

                createMessageBubble(message, msgClass);
            }
            scrollToBottom();

        }
    });
});

