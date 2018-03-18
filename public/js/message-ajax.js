//üzenetbuborékot létrehozó függvény
function createMessageBubble(message, className) {
	//div elem létrehozása
    let msgBubble = $('<div>', {
        class: className, //class név a paraméterből
        title: message.date ? message.date : 'Nem rég küldve' //üzenet dátuma title attribútumban
    });

	//p elem a szövegnek
    let p = $('<p>', { html: message.text });
	//p hozzáadása a divhez, div hozzáadása az üzenetablakhoz
    p.appendTo(msgBubble);
    msgBubble.appendTo('#conversation');
}

//üzenet előnézetet létrehozó függvény (popup ablakban lista elem)
function createMessagePreview(message, className) {
    //a már meglévő listelem eltávolítása
    $('li#partner-' + message.sender_id).remove();

    //html létrehozása, beszúrása a lista elejére
    let li = $('<li>', { class: className, 'data-partner-id': message.sender_id, id: 'partner-' + message.sender_id });
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
// új üzenet létrehozása
// ===================
$('form#create-message-form').submit((e) => {
    e.preventDefault();

	//partnerazonosító és a szöveg eltárolása
    let partnerId = $('select#message-receiver').val();
    let text = $('textarea#message-text').val();

	//ajax kérés
    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: {
            'create-message': true,
            'partner-id': partnerId,
            'text': text
        },
        success: (resp, xhr, status) => {
			//szerver visszaküldi a megírt üzenetet, a partner adatait + egy status-t
            let data = JSON.parse(resp);
			
			//ha minden okés volt 
            if ( data.status == 'success' ) {
				//üzenetelőnézet létrehozása a lenyíló ablakban
                createMessagePreview({
                    'sender_id': partnerId,
                    'sender_name': data.partner_name,
                    'sender_avatar': data.partner_avatar,
                    'id': data.message_id,
                    'date': 'most',
                    'text': data.message
                }, 'message-item'); 
			//egyébként hibaüzenet kiírása
            } else {
                alert(data.status);
            }
        },
        error: (status, xhr, error) => {
            alert(error);
        }
    });
});

// ====================
// üzenet küldése
// ===================
//a függvény elküldi a chatbe írt üzenetet
function sendMessage() {

	//partnerazonosító a session storage-ból
    let partnerId = sessionStorage.getItem('message-partner-id');
	//az üzenet szövege az input mezőből
    let text = $('textarea#message').val();
	//a felesleges fehérkarakterek levágjuk a szöveg elejéről
	//és végéről, ha vannak
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
			//a szerver visszaküldi az általunk írt, ellenőrzött üzenetet
			//és egy status értéket, ami tartalmazza a hibaüzenetet
			//vagy 'success', ha nem volt probléma
            let response = JSON.parse(resp);
            if (response.status == 'success') {
				//inputmező ürítése, üzenet megjelenítése buborékban
                $('textarea#message').val('');
                createMessageBubble({ text: response.message, date: false }, 'msg-bubble clear msg-own');
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
//üzenetküldés függvény meghívása
//gombra kattintás
$('button#btn-send-message').click(() => { sendMessage(); });
//enter lenyomása
$('textarea#message').keyup((e) => {
    if (e.keyCode == 13) sendMessage();
});

// ====================
// új üzenetek figyelése
// ===================
function getNewMessages() {
	//hanfájl betöltése
    let beep = new Audio(PUBLIC_ROOT + 'resources/sounds/beep.mp3');

	//ajax kérés
    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'has-new-message': true },
        success: (resp, xhr, status) => {
			//üzentek visszaalakítása JS tömbbé
            let messages = JSON.parse(resp);

            //ha nincsenek új üzenetek, nem csinálunk semmit
            if (messages.length < 1) { return false; }

            //hang lejátszása
            beep.play();

			//végigmegyünk az üzeneteken
            for (message of messages) {

                //ha éppen annak a partnernak van megnyitva az ablaka, akitől kaptunk üzenetet, akkor az ablakba hozunk létre egy buborékot
                if (sessionStorage.getItem('message-partner-id') == message.sender_id && $('#read-message').is(':visible')) {
                    //üzenetbuborékot hoz létre a DOM-ban
					//az üzenetobjektumot és a megadott class nevet használva
					createMessageBubble(message, 'msg-bubble clear');
					
					//új buborék keletkezésekor az ablak aljára görgetünk
					scrollToBottom();
                }
                
                //előnézet létrehozása a lenyíló ablakban
                createMessagePreview(message, 'message-item unread-message');
            }
			
			//az üzenetek gombnak adunk egy class-t, ami jelzi,
			//hogy van bejövő üzenete
            $('button#btn-messages').addClass('has-new-message');
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

	//chatablak megnyitása
    $('.page-overlay').fadeIn(300);
    $('.page-overlay #read-message').show();

	//klikkelt listaelemből kiszedjük a data-partner-id értékét
    let clickedLi = $(e.currentTarget);
    let partnerId = clickedLi.attr('data-partner-id');

	//eltároljuk az aktuális partner azonosítóját a session storage-ban
    sessionStorage.setItem('message-partner-id', partnerId);


    let data = {};

    //ha olyan beszélgetésre kattintunk, aminek vannak olvasatlan üzenetei:
    if ( clickedLi.hasClass('unread-message') ) {
		//levesszük róla a classnevet
        clickedLi.removeClass('unread-message');
        $('button#btn-messages').removeClass('has-new-message');
		//küldeni kívánt adatokhoz hozzáadunk még egy 'set-to-seen' mezőt
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
				//üzenet buborék létrehozása
                createMessageBubble(message, msgClass);
            }
            scrollToBottom();

        }
    });
});

