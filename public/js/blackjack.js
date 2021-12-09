const Blackjack = function(){
    const GS_READY = 1;
    const GS_PL_HITS = 2;
    const GS_PL_STAY = 3;
    const GS_OVER = 4;

    let userName = null;
    let delay = null;

    let $timer = $('#timer');
    let $mdStartGame = $('#md-start-game');
    let $mdGameInfo = $('#gameInfo');
    let $spinner = $('.spinner-border');
    let $player = $('.player');
    let $dealer = $('.dealer');
    let $start = $('#btn-start');
    let $hit = $('#btn-hit');
    let $stay = $('#btn-stay');
    let state = null;
    let interval;

    let countDown = function (){
        let counter = delay;
        $timer.text(delay).fadeIn('slow');
        interval = setInterval(function() {
            counter--;
            if (counter <= 0) {
                clearInterval(interval);
                $timer.html("<h3>0</h3>");
                return true;
            }
            $timer.text(counter);
        }, 1000 );
    }

    let startGame = function(){
        if( delay == null || userName == null ){
            $mdGameInfo.modal('show');
        } else {
            $spinner.fadeIn('slow');
            $.ajax({
                url: '/start-game',
                type: 'POST',
                data : {_token: _token, delay: delay, userName, userName},
                success: function (e){
                    state = e.data;
                    $mdGameInfo.modal('hide');
                    $spinner.fadeOut('slow');
                    render();
                }
            });
        }
    }

    let render = function () {

        clearInterval(interval);

        /**
         * Return if state is null
         */
        if( state === null ) {
            $player.html('<h4>ready<h4>');
            $dealer.html('<h4>ready<h4>');
            $start.fadeIn('slow');
            $hit.hide();
            $stay.hide();
            $timer.hide();

            return true;
        }

        $player.html('<div class="hand" style="text-align: left"></div><br/><br/><div class="score" style="color: blue; font-size: 18px"></div>');
        renderPlayerHand();
        $dealer.html('<div class="hand" style="text-align: left"></div><br/><div class="score" style="color: blue; font-size: 18px"></div>');
        renderDealerHand();
        switch (state.game.status) {
            case GS_READY:
                break;
            case GS_PL_HITS:
                $start.hide();
                $hit.fadeIn('slow');
                $stay.fadeIn('slow');
                countDown();
                break;
            case GS_PL_STAY:
                $start.hide();
                $hit.hide();
                $stay.hide();
                break;
            case GS_OVER:
                $timer.hide();
                $start.fadeIn('slow');
                $hit.hide();
                $stay.hide();
                break;
        }

        return true;
    }

    let renderPlayerHand = function () {
        let html = [];
        $.each(state.player.currentHand, function (key, value){
            html.push(`<div>${value.type} - ${value.name}</div>`)
        });

        $player.find('.hand').html(html.join(''));

        $player.find('.score').html('Score: ' + state.player.currentScore );
        if( state.player.blackjack === true ) {
            $player.find('.score').append('<h1 style="color: green">BLACKJACK!</h1>')
        }

        if( state.player.bust === true ) {
            $player.find('.score').append('<h1 style="color: red">BUST!</h1>')
        }

        if( state.game.winner === 'player') {
            $player.find('.score').append('<h1 style="color: green">WINS!</h1>')
        }

        if( state.game.draw !== null) {
            $player.find('.score').append('<h1 style="color: blue">DRAW!</h1>')
        }
    }

    let renderDealerHand = function () {
       let html = [];

        $.each(state.dealer.currentHand, function (key, value){
            html.push(`<div>${value.type} - ${value.name}</div>`)
        });

       if( state.game.status === GS_PL_HITS ) {
           html.push(`<div style="color: #d60f29">Hidden card</div>`);
       }

        $dealer.find('.hand').html(html.join(''));

        $dealer.find('.score').html('Score: ' + state.dealer.currentScore );

        if( state.dealer.blackjack === true ) {
            $dealer.find('.score').append('<h1 style="color: green">BLACKJACK!</h1>')
        }

        if( state.dealer.bust === true ) {
            $dealer.find('.score').append('<h1 style="color: red">BUST!</h1>')
        }

        if( state.game.winner === 'dealer') {
            $dealer.find('.score').append('<h1 style="color: green">WINS!</h1>')
        }

        if( state.game.draw !== null) {
            $dealer.find('.score').append('<h1 style="color: blue">DRAW!</h1>')
        }
    }

    let getState = function () {
        $.ajax({
            url: '/state',
            type: 'GET',
            data : {},
            success: function (e){
                state = e.data;
                render();
                $spinner.fadeOut('slow');
            }
        });
    }

    let dealerHits = function (){
        $.ajax({
            url: '/hit',
            type: 'POST',
            data: {'participant': 'dealer', _token: _token},
            success: function (e){
                state = e.data;
                render();
                if( state.game.winner === null && state.game.draw === null ){
                    dealerHits();
                }
            }
        });
    }

    let bindEvents = function () {
        $start.on('click', function (e) {
            startGame();
            e.preventDefault();
        });

        $mdStartGame.on('click', function () {
            userName = $('#player-name').val();
            delay = $('#game-delay').val();
            $('#dv-player-name').html(userName);
            startGame();
        });

        $hit.on('click', function (e){
            $spinner.fadeIn('slow');
            $.ajax({
                url: '/hit',
                type: 'POST',
                data: {'participant': 'player', _token: _token},
                success: function (e){
                    state = e.data;
                    $spinner.fadeOut('slow');
                    render();
                }
            });
        });

        $stay.on('click', function (e){
            $spinner.fadeIn('slow');
            $.ajax({
                url: '/stay',
                type: 'GET',
                success: function (e){
                    state = e.data;
                    $spinner.fadeOut('slow');
                    render();
                    if( state.game.winner === null && state.game.draw === null ) {
                        dealerHits();
                    }
                }
            });
        });

        $( document ).ajaxError(function( event, jqxhr ) {
            getState();
            toastr["error"](jqxhr.responseJSON.message);
        });
    }

    return {
        'init' : function(){
            getState();
            bindEvents();
        }
    }
};
