<?php

namespace App\Classes;

class Dealer extends Participant
{
    /**
     * @var int The mininum card value a dealer will stand on
     */
    const DEALERSTANDS = 17;

    /**
     * Constructor
     * @param object|null $state Participant state
     */
    public function __construct( ?object $state)
    {
        if( !empty($state->dealer) ){
            $this->currentHand = $state->dealer->currentHand;
            $this->currentScore = $state->dealer->currentScore;
        }
    }

    /**
     * @return \stdClass Participant last state
     */
    public function getState()
    {
        $state = new \stdClass();

        $state->currentScore = $this->currentScore;
        $state->currentHand = $this->currentHand;
        $state->bust = $this->bust;
        $state->blackjack = $this->blackjack;

        return $state;
    }
}
