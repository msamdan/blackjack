<?php

namespace App\Classes;

class Blackjack
{
    /**
     * Blackjack point
     */
    const BLACKJACK = 21;

    /**
     * Game Status ready position
     */
    const GS_READY = 1;

    /**
     * Game Status Player hits
     */
    const GS_PL_HITS = 2;

    /**
     * Game Status Player stays
     */
    const GS_PL_STAY = 3;

    /**
     *  Game Status Game over
     */
    const GS_OVER = 4;

    /**
     * @var Player
     */
    public $player;

    /**
     * @var Dealer
     */
    public $dealer;

    /**
     * @var Deck
     */
    public $deck;

    /**
     * @var \stdClass
     */
    public object|null $state;

    /**
     * @var array
     * initial game state
     */
    private $initialState = [
        'game' => ['delay' => 60, 'lastActivity' => 0, 'status' => self::GS_READY, 'winner' => null, 'winnerHand' => null, 'drawHand' => null],
        'dealer' => [ 'currentScore' => 0, 'currentHand' => [] ],
        'player' => [ 'currentScore' => 0, 'currentHand' => []],
        'deck' => [ 'cards' => [] ]
    ];

    /**
     * @param object|null $state
     */
    public function __construct( ?object $state)
    {
        $state = empty($state) ? json_decode( json_encode( $this->initialState) ) : $state;
        $this->state =  $state->game;
        $this->dealer = new Dealer($state);
        $this->player = new Player($state);
        $this->deck = new Deck($state);
    }

    /**
     * Start new game
     * Clear all state
     * shuffle deck
     * hit first cards
     */
    public function new()
    {
        $this->state->winner = null;
        $this->state->draw = null;
        $this->state->status = self::GS_PL_HITS;

        $this->clearHands();
        $this->deck->shuffleDeck();

        $card = $this->deck->getTopCardFromDeck();
        $this->player->hit($card);

        $card = $this->deck->getTopCardFromDeck();
        $this->dealer->hit($card);

        $card = $this->deck->getTopCardFromDeck();
        $this->player->hit($card);

        $card = $this->deck->getTopCardFromDeck();
        $this->dealer->hit($card);
    }

    /**
     * Clear player and dealer hands
     */
    public function clearHands()
    {
        $this->player->clearCurrentHand();
        $this->dealer->clearCurrentHand();
    }

    /**
     * @return object
     */
    public function getState(): object
    {
        $state = new \stdClass();
        $state->game = $this->state;
        $state->dealer = $this->dealer->getState();
        $state->player = $this->player->getState();
        $state->deck = $this->deck->getState();

        return $state;
    }

    public function cancel()
    {
        $this->clearHands();
        $this->state = json_decode( json_encode( $this->initialState) );
    }

    /**
     * Update winner state
     */
    private function playerWins()
    {
        $this->state->winner = 'player';
        $this->state->winnerHand = $this->player->currentHand;
        $this->state->status = self::GS_OVER;

        return true;
    }

    /**
     * Update winner state
     */
    private function dealerWins()
    {
        $this->state->winner = 'dealer';
        $this->state->winnerHand = $this->dealer->currentHand;
        $this->state->status = self::GS_OVER;

        return true;
    }

    /**
     * Update draw state
     */
    private function draw()
    {
        $this->state->drawHand = $this->player->currentHand;
        $this->state->status = self::GS_OVER;;

        return true;
    }

    /**
     * check winner if
     */
    public function checkWinner()
    {

        if( $this->player->isBusts() ) return $this->dealerWins();

        if( $this->dealer->isBusts() ) return $this->playerWins();

        if( $this->dealer->isBlackjack() && $this->player->isBlackjack() ) return $this->draw();

        if( $this->state->status === self::GS_PL_STAY && $this->dealer->currentScore >= Dealer::DEALERSTANDS ){
            if( $this->dealer->currentScore < $this->player->currentScore ){
                $this->playerWins();
            } else if($this->dealer->currentScore == $this->player->currentScore) {
                $this->draw();
            } else {
                $this->dealerWins();
            }
        }

        return true;
    }
}
