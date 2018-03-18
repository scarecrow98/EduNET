// ====================
// csoporttagok listázása
// ====================
$('button.btn-view-members').click((e) => {
	//modális ablak megnyitása
    $('.page-overlay').fadeIn(300);
    $('.page-overlay #view-members').show();
    $('#group-members').empty();

	//kattintott gomb data-group-id attribútuma tárolja a csoport azonosítóját
    let groupId = $(e.currentTarget).attr('data-group-id');

    $.ajax({
        type: 'POST',
        url: SERVER_ROOT + 'parsers/main-parser.php',
        data: { 'list-group-members': true, 'group-id': groupId },
        success: (resp, status, xhr) => {
            let members = JSON.parse(resp);
	
			//ha még nincsenek csoporttagok
            if (members.length < 1) { $('#group-members').html('<li>Még nem vettél fel tagot ebbe a csoportba!</li>'); return; }

			//végigiterálunk a felhasználókon
            for (member of members) {
				//listaelem készítése + kép + név hozzáadása strong tagben
                let li = $('<li>', { class: 'clear' });
                $('<img>', { src: SERVER_ROOT + 'uploads/avatars/' + member.avatar }).appendTo(li);
                $('<strong>', { html: member.name }).appendTo(li);
                
                //ha a modalnak van teacher-true nevű class neve,
                //csak akkor jelenítjük meg a tag törlése gombot
                if ( $('#view-members').hasClass('teacher-true') ) {
                    $('<button>', {
                        class: 'btn-delete-member btn-rect bg-2',
                        html: '<i class="ion-close-round"></i>Eltávolítás',
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

	//ha van kép megadva, akkor hozzáadjuka formdata-hoz
    if ( $('#new-avatar').get(0).files[0] ) {
        formData.append('new-avatar', $('#new-avatar').get(0).files[0]);        
    }

	//ha ki van töltve mindkettő jelszó mező
    if ( $('#new-password1').val() && $('#new-password2').val() ) {
        formData.append('new-password1', $('#new-password1').val());
        formData.append('new-password2', $('#new-password2').val());
    }

	//ha ki van töltve az email mező
    if ( $('#new-email').val() ) {
        formData.append('new-email', $('#new-email').val());
    }

	//ha meg lett változtatva a feliratkozós checkbox értéke
    if ( sessionStorage.getItem('subscription-changed') ) {
        formData.append('new-email-subscription', $('#new-email-subscription').is(':checked') ? 1 : 0);
        sessionStorage.removeItem('subscription-changed');
    }

	//ajax kérés a main-parser.php-nak
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

    let taskCount = $('input#task-count').val(); //feladatok száma
    let formData = new FormData(); //formData objektum a POST adatok küldéséhez
    let answers = []; //ebben lesznek eltárolva a diák válaszai

	//végigmegyünk az egyes feladatok adatain
    for ( let i = 1; i <= taskCount; i++ ){
		//minden task-[feladat száma]-data nevű rejtett inpuból kivesszük a JSON stringet
		//visszalakítjuk a JS által is értelmezhező formába
		//és így már tudjuk a feladat azonosítóját, a típusát és a hozzátartozó opciókat
        let data = $('input#task-' + i + '-data').val();
        let taskData = JSON.parse(data);
		
		//inicializálunk egy átmeneti objektumot, amiben egy feladat adatati és az arra adott válaszok lesznek tárolva
		//minden ciklus végén hozzáadjuk az answers tömbhöz
        let answer = new Object();

		
		//feladat azonosító hozzáadása az objektumhoz
        answer['task-id'] = taskData['task-id'];

		//ha vannak feladatopciók
        if (taskData['task-options'].length > 0) {
			//inicializálunk egy tömböt, ami tárolja az opciók adatait és a rájuk adott válaszokat
            let options = [];

			//végigmegyünk az opciókon
            for (x of taskData['task-options']) {
                //egy opció azonsítóját és a rá érkező válasz tárolja a ciklusban,
				//a ciklus végén hozzáadjuk az options tömbhöz
				let opt;
				
				//feladattípustól függően kiemeljül a DOM-ból a diák válaszát
                switch (taskData['task-type']) {
                    //kvíz feladat
                    case '1':
                        opt = {
                            'option-id': x,
                            'value': +$('input[name=option-' + x + ']').is(':checked')
                        }    
                        break; 
                    //párosítás feladat
                    case '3':
                        opt = {
                            'option-id': x,
                            'value': $('input[name=option-' + x + ']').val()
                        }    
                        break; 
                    //igaz-hamis feladat
                    case '4':
                        opt = {
                            'option-id': x,
                            'value': $('input[name=option-' + x + ']:checked').val()
                        }      
                        break; 
                }
				//hozzáadjuk az options tömbbhöz az opt elemet
                options.push(opt);
            }
			
			//a feladat adatait tároló objektumhoz hozzáadjuk az opcióazonosítókat és a válaszokat tároló tömböt
            answer['task-options'] = options;

		//egyébként, ha nincsenek opciók a feladathoz
        } else {
			//feladattíputól függően kiszedjük a DOM-ból a diák válaszát
            switch (taskData['task-type']) {
                //szöveges válasz
                case '2':
                    answer['text-answer'] = $('textarea[name=textarea-' + taskData['task-id'] + ']').val();
                    break;
                //fájlfeltöltés
                case '5':  
					//a FILES tömb beli neve a fájlnak ez lesz: file-[feladat azonosító-]
                    answer['file-name'] = 'file-' + taskData['task-id'];
					//formDatahoz hozzáfűzzük a feltöltött fájlt
                    formData.append(answer['file-name'], $('input#'+answer['file-name']).get(0).files[0]);
                    break;
            }
        }

		//hozzáadjuk a feladatokat tároló tömbbhöz az answer objektumot
        answers.push(answer);
    }

	//a formDatahoz fűzzük az answers tömb JSON stringgé konvertált értékét
    formData.append('answers', JSON.stringify(answers));
    formData.append('test-submission', true);

	//ajax kérés a szervernek
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
