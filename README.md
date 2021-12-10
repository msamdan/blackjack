# BLACKJACK APP

INSTALLATION

STEP-1
Start the containers in detached mode:

```sh
docker-compose up -d
```

STEP-2 Install Composer:

```sh
docker exec blackjack-fpm curl -sS https://getcomposer.org/installer -o composer-setup.php
docker exec blackjack-fpm php composer-setup.php --install-dir=/usr/local/bin --filename=composer
docker exec blackjack-fpm rm -rf composer-setup.php
```

```sh
docker exec blackjack-fpm composer install
```
 
PROJECT DETAILS

Blackjack is a card game where the player(you) need to beat the dealer by getting closer to 21 higher than the dealer without exceeding 21.
In this project developed a blackjack app with laravel framework...

###API:
```html
Type   URL            Params                          Description  
GET    /              payload: {}                     Returns application ui static content 
GET    /state         payload: {}                     Returns current game state
POST   /start-game    payload: {userName, delay}      Starts a game
POST   /hit           payload: {}                     Hits a card from deck for player or dealer
POST   /stay          payload: {}                     Trigger player "stay" action
```

###Controller:
Receives API requests (Player actions) and calls the required services... 
```
App\Http\Controllers\BlackjackController
```
###Service:
Blackjack runner, manages game ( Player and Dealer Actions, Session, Response etc. )
```
App\Http\Services\BlackjackService
```

###Classes:

Game class. Blackjack service uses game class for create and manage game. Blackjack has several properties and methods
( such as player, dealer, deck, etc... class properties and game control methods such as new, checkWinner, clearHands, etc... )
...
```
App\Classes\Blackjack
```

Base classs for game participants (Players and Dealers). Participant contains common props and methots for participants.
Player actions (optimiseCurrentHandCardValues, clearHands, hit, etc...)
```
App\Classes\Participant
```
Extens from Participant class. Represents a player. Contains player specific methods and properties...
```
App\Classes\Player
```

Extens from Participant class. Represents a dealer. Contains dealder specific methods and properties...
```
App\Classes\Dealer
```

Deck class controls deck actions and properties. 
```
App\Classes\Deck
```

Represents game card... 
```
App\Classes\Deck
```

###UI
Controls ui elements...
```
public\js\blackjack.js
```

###Rules:
- The game has 6 decks of 52 classic playing cards (312 cards total).
- Goal: Get closer to 21 than the dealer without going over 21.
- If a player exceeds 21 he busts and it means that player loses the round.
- Card Values:
    - 2 through 10 has face value
    - Jack, Queen and King have values of 10
    - Ace has 1 or 11 values depending of the hand whichever is the best for the hand
- Initial Dealt:
    - Each player gets 2 initial random cards from the 6 decks of cards
        - Dealer gets 1 card facing down. Player doesn't know about this card.
        - Player gets 1 card facing up
        - Dealer gets 1 card facing up
        - Player gets 1 card facing up
- The facing down card gets shown when the player "stays" or "busts".
- The dealer has to hit (get card) until his hand has a value of 17 or more. 
 He can't get cards if his hand has a value of 17 or more even if he has a worse hand than the player.

Playing:
- The Player wants to get closer to 21.
- The player has 2 choices: "hit" or "stay"
- If the player hits he gets a new card
    - If the new hand value is less than 21 and satisfies the player he "stays"
        - The dealer shows his hidden card
        - If the dealer has a less then 17 value hand the dealer gets a new card until the hand has 17 or more value
        - If the dealer busts the player wins
        - The winner of the round is the closest one to 21
    - If the new hand value is 21 then the player has a blackjack hand
        - The dealer show the hidden card
        - If the dealer has a less then 17 value hand the dealer gets a new card until the hand has 17 or more value
        - If the dealer has a 21 value hand the round ends draw
        - If the dealer busts the player wins
        - The winner of the round is the closest one to 21
    - If the new hand value exceeds 21 then the player busts
        - The dealer automatically wins
