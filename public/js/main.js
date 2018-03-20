$(document).ready(() => {

	//minden inputnak kikapcsoljuk az autocomplete-ját
	$('body input').attr('autocomplete', 'off');

	//modalok megnyitása
    $('.modal-opener').click((e) => {
		//a kattintott gomb data-modal-id-jének megszerzése
        let modalId = $(e.currentTarget).data('modal-id');

		//a megfelelő id-jű modal megnyitása
        $('.page-overlay').fadeIn(300);
        $('.page-overlay #'+modalId).show();
    });

    //modalok szekciói közötti váltás
    $('.modal .section-switcher button').click((e) => {
        let sectionId = $(e.currentTarget).data('section-id');

        $(e.currentTarget).addClass('active-section').siblings('button').removeClass('active-section');
        $('.modal #'.sectionId).show().siblings('.modal-body').hide();
    });

    //naptár események lenyitása
    $('#event-calendar .day i').click((e) => {
        $(e.currentTarget).siblings('.panel').slideToggle(200);
    });

    //csoporttagok felvétele modalnál csoport ID megszerzése
    $('button.btn-add-members').click((e) => {
        //amelyik csoportra kattintunk, annak az id-jét eltároljuk sessionStorage-ba
        sessionStorage.setItem('group-id', $(e.currentTarget).attr('data-group-id'));
    });

    //feladatlap ID-jének eltárolása, amikor a user lenyitja lenyiló menüt
    $('i.open-test-options').click((e) => {
        sessionStorage.setItem('test-id', $(e.currentTarget).attr('data-test-id'));
    });
    
    //feladatlap nyitása/zárása (formkülés)
    $('.btn-open-close-test').click((e) => {
        $(e.currentTarget).parent('form.open-close-test-form').submit();
    });

    //feladatlapok lenyíló menüjének nyitása/zárása
    $('td.tool-cell i').click((e) => {
        let menu = $(e.currentTarget).siblings('ul.table-menu');
        //ha a lenyíló ablakban nincs semmi opció még, mert a diák még semmit sem kezdhet a teszttel, akkor return
        if ( menu.children().length < 1 ) return false;

        menu.slideToggle(200);
    });
	
	//keresés sáv kinyitása
	$('button#btn-show-search').click(() => {
        $('#search-form-container').toggle();
	});
	
    //a kiválasztott feladattípusnak megfelelő formrészlet megjelenítése
    $('select#task-type').change(() => {
        let taskType = $('select#task-type').val();
        $('section.section-add-options').slideUp(300);

        if ( taskType == '1' ) { $('section.quiz-options').slideDown(300); }
        else if( taskType == '3' ){ $('section.pairing-options').slideDown(300); }
        else if( taskType == '4' ){ $('section.truefalse-options').slideDown(300); }

    });
	
    //üzenetek popup nyitása/csukása
    $('button#btn-messages').click(() => {
        $('.messages-popup').slideToggle();
        $('button#btn-messages').removeClass('has-new-message');
    });


    //file dialogok megnyitása
    $('button.btn-open-file-dialog').click((e) => {
        e.preventDefault();

        let inputId = $(e.currentTarget).data('input-id');
        $('input#' + inputId).click();
    });

    //fájlnév megjelenítése feltöltés előtt
    $('input[type=file]').change((e) => {
        $(e.currentTarget).siblings('span.uploaded-file-name').html(e.target.files[0].name);
    });
    
    //modal megnyitása (beállítások)
    $('button#btn-settings').click((e) => {
        $('.page-overlay').fadeIn(300);
        $('.page-overlay .settings-modal').show();
    });  

    //modal bezárása
    $('.modal .close-modal').click(() => {
        $('.page-overlay').fadeOut(100);
        $('.modal').hide();
    }); 

    //beállítások törlése
    $('button#btn-reset-settings').click((e) => {
        e.preventDefault();
        $('form#user-settings-form')[0].reset();
        $('span.uploaded-file-name').html('');
    });

    //új üres opció hozzáadása a feladathoz
    $('button.btn-add-option').click(() => {
        let taskType = $('#task-type').val();

        switch( taskType ){
            case '1':
                createNewTaskOption('quiz-options', 'option-text-', 'option-ans-', 'checkbox');
                break;
            case '3':
                createNewTaskOption('pairing-options', 'option-text-', 'option-ans-', 'text');
                break;
            case '4':
                createNewTaskOption('truefalse-options', 'option-text-', 'option-ans-', 'radio');
                break;
        }
    });

    //beállításoknál email feliratkozás státuszának változtatása checkbox kattintáskor
    $('input#new-email-subscription').change((e) => {
        let status = $(e.currentTarget).is(':checked');
        sessionStorage.setItem('subscription-changed', true);

        if (status)
            $('span#email-subscription-status').html('Értesítések bekapcsolva');
        else
            $('span#email-subscription-status').html('Értesítések kikapcsolva');     
    });

    //billentyűlenyomások
    $(window).keydown((e) => {
        if( e.keyCode == 27 ){
            $('.page-overlay').fadeOut(100);
            $('.modal').hide();
        }
    });

    //azon mezők deaktiválása, amelyek üresek a feladatlap szűrő formban --> ezáltal ezek nem jelennek meg az URL-ben GET kéréskor
    $('form#search-test-form').submit((e) => {
        $('.search-form-input').each(function(index){
            if( $(this).val() == '0' || !$(this).val() ){
                $(this).attr('name', '');
            }
        });

    });
	
});

//paraméterek --> szülő elem, opciószöveg input neve, opcióválasz neve, input típusa
function createNewTaskOption(parentContainer, inputTextName, inputAnsName, inputType){
    //lekérjük, hogy hányadik opciót adjuk épp hozzá a konténerhez
    let optionNumber = $('section.'+parentContainer).children().length+1;

    //opciót tartalmazó szülő li elem létrehozása
    let li = $('<li>', { class: 'input-container' });

    //input labeljének létrehozása
    let label = $('<label>', { class: 'label-small', html: 'Opció '+optionNumber+':' })
    label.appendTo(li);

    //opciók szövegét tartó textinput létrehozása
    let inputText = $('<input>', { type: 'text', name: inputTextName+optionNumber, required: true, placeholder: 'A válaszlehetőség szövege' });
    inputText.appendTo(li);

    //ha rádiógomb típusú az elem, akkor 2 rádiógombot hozunk létre (egyiket 1, másikat 0-ás értékkel --> igaz/hamis)
    if( inputType == 'radio' ){
        let inputAns1 = $('<input>', { type: inputType, name: inputAnsName+optionNumber, value: 1 });
        inputAns1.appendTo(li);

        let inputAns2 = $('<input>', { type: inputType, name: inputAnsName+optionNumber, value: 0 });
        inputAns2.appendTo(li);
    }
    //egyébként pedig létrehozunk egy darab input element, a megadott inputtípusban (checkbox/text)
    else{
        let inputAns1 = $('<input>', { type: inputType, name: inputAnsName+optionNumber, maxlength: 1, placeholder: 'A helyes válasz betűjele' });
        inputAns1.appendTo(li);
    }

    li.appendTo('section.'+parentContainer).css('opacity', 0).fadeTo('fast', 1);
}

//70600016-15292209