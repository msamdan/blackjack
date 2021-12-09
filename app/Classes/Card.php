<?php

namespace App\Classes;

class Card
{
    public $type;
    public $value;
    public $name;

    /**
     * @param $type
     * @param $value
     * @param $name
     */
    public function __construct( $type, $value, $name )
    {
        $this->type = $type;
        $this->value = $value;
        $this->name = $name;
    }

    /**
     * @param $currentScore
     * @return int
     */
    public function getValueOfCard($currentScore): int
    {
        if ($this->name === 'Ace' && ( $currentScore + 11 ) > Blackjack::BLACKJACK ) {
            $this->value = 1;
        }

        return $this->value;
    }
}
