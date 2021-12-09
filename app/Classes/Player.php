<?php

namespace App\Classes;

class Player extends Participant
{
    public string $name;
    /**
     * @param object|null $state
     */
    public function __construct( ?object $state)
    {
        if( !empty($state->player) ){
            $this->currentHand = $state->player->currentHand;
            $this->currentScore = $state->player->currentScore;
        }
    }

    /**
     * @return \stdClass get player state
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
