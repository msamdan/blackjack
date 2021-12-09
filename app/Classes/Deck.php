<?php
namespace App\Classes;

class Deck
{
    const CARDTYPES = ['Heart', 'Spade', 'Diamond', 'Club'];

    const CARDNAMES = ['2', '3', '4', '5', '6', '7', '8', '9', '10', 'Jack', 'Queen', 'King', 'Ace'];

    const CARDVALUES = [2, 3, 4, 5, 6, 7, 8, 9, 10, 10, 10, 10, 11]; // Ace 1 || 11 conditional

    const NUMBER_OF_DECK = 6;

    public $cards = [];

    public function __construct($state)
    {
        if( empty($state->deck->cards) ){
            $this->shuffleDeck();
        } else {
            $this->cards = $state->deck->cards;
        }
    }

    /**
     * Shuffles and returns a fresh deck of cards
     * @static
     * @access public
     * @return array
     */
    public function shuffleDeck(): array
    {
        for($i=0; $i < self::NUMBER_OF_DECK; $i++){
            foreach (self::CARDTYPES as $type) {
                foreach (self::CARDVALUES as $key => $value) {
                    $this->cards[] = new Card($type, $value, self::CARDNAMES[$key]);
                }
            }
        }

        shuffle($this->cards);

        return $this->cards;
    }

    public function getState()
    {
        $state = new \stdClass();
        $state->cards = $this->cards;

        return $state;
    }

    public function getTopCardFromDeck()
    {
        return array_shift($this->cards);
    }
}
