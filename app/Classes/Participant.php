<?php

namespace App\Classes;

class Participant
{
    /**
     * @var array Array of Card objects representing cards in Participant's hand
     */
    public $currentHand = [];

    /**
     * @var int Sum of card values in Participant's hand
     */
    public $currentScore = 0;

    /**
     * @var bool blackjack flag
     */
    public $blackjack = false;

    /**
     * @var bool bust flag
     */
    public $bust = false;

    /**
     * Clear Current Hand and flags
     */
    public function clearCurrentHand()
    {
        $this->currentHand = [];
        $this->currentScore = 0;
        $this->blackjack = false;
        $this->bust = false;
    }

    /**
     * Optimise Current Hand Card Values.
     * Aces has 1 or 11 values depending of the hand whichever is the best for currentScore
     */
    public function optimiseCurrentHandCardValues()
    {
        foreach($this->currentHand as &$card) {
            if($this->currentScore > Blackjack::BLACKJACK && $card->name == 'Ace' && $card->value == 11 ){
                $this->currentScore -= 11;
                $this->currentScore += $card->getValueOfCard($this->currentScore);
            }
        }
    }

    /**
     * @param Card $card hit card from deck
     */
    public function hit($card)
    {
        $this->currentScore += $card->getValueOfCard($this->currentScore);
        $this->currentHand[] = $card;

        $this->optimiseCurrentHandCardValues();
        $this->isBlackjack();
        $this->isBusts();
    }

    /**
     * @return bool check if blackjack
     */
    public function isBlackjack()
    {
        $this->blackjack = $this->currentScore == Blackjack::BLACKJACK;
         return $this->blackjack;
    }

    /**
     * @return bool check if busts
     */
    public function isBusts()
    {
        $this->bust = $this->currentScore > Blackjack::BLACKJACK;
        return $this->bust;
    }
}
