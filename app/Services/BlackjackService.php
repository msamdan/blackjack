<?php

namespace App\Services;

use App\Classes\Blackjack;
use App\Helpers\ServiceResponse;
use Illuminate\Support\Facades\Session;

class BlackjackService
{
    private object $game;

    /**
     * @return ServiceResponse
     * get current game state
     */
    public function getState(): ServiceResponse
    {
        $data = $this->prepareResponseState();

        return $this->setResponse(200, 'success', $data);
    }

    /**
     * @return ServiceResponse
     * start fresh game
     */
    public function start($params): ServiceResponse
    {
        $this->game = new Blackjack(null);
        $this->game->new();
        $this->game->player->name = $params['userName'];
        $this->game->state->delay = (int)$params['delay'];

        $this->saveState();
        $data = $this->prepareResponseState();

        return $this->setResponse(200, 'success', $data);
    }

    /**
     * @return ServiceResponse
     * player "stay" action
     */
    public function stay(): ServiceResponse
    {
        $state = Session::get('state');
        $this->game = new Blackjack($state);

        $this->game->state->status = Blackjack::GS_PL_STAY;

        $this->game->checkWinner();
        $this->saveState();
        $data = $this->prepareResponseState();

        return $this->setResponse(200, 'success', $data);
    }

    /**
     * @param $params
     * @return ServiceResponse
     * player or dealer hit card from deck
     */
    public function hit($params): ServiceResponse
    {
        try {
            $state = Session::get('state');

            //Sample error throw
            if( empty($state) ) throw new \Exception('Started game not found!', 400 );

            $this->game = new Blackjack($state);

            //Sample error throw
            if( time()- $this->game->state->lastActivity  > $this->game->state->delay ) {
                $this->game->cancel();
                Session::put('state', null);

                throw new \Exception('Time out! Game cancelled', 400 );
            }

            $card =  $this->game->deck->getTopCardFromDeck();

            //Check participant type is valid?
            if( !in_array(strtolower($params['participant']), ['player', 'dealer']) ) throw new \Exception('Invalid participant type.', 400);


            $this->game->{strtolower($params['participant'])}->hit($card);

            $this->game->checkWinner();
            $this->saveState();
            $data = $this->prepareResponseState();

            return $this->setResponse(200, 'success', $data);

        } catch (\Exception $e) {
            return $this->setResponse($e->getCode(), $e->getMessage(), null);
        }
    }

    /**
     * store state to session
     */
    private function saveState()
    {
        $this->game->state->lastActivity = time();
        $state = $this->game->getState();

        Session::put('state', $state);
    }

    /**
     * @param $status
     * @param $message
     * @param $data
     * @return ServiceResponse
     */
    public function setResponse($status, $message, $data): ServiceResponse {
        $response = new ServiceResponse();
        $response->status = $status;
        $response->message = $message;
        $response->data = $data;

        return $response;
    }

    /**
     * @return mixed
     * manipulate state for response (remove hidden values from response data)
     */
    private function prepareResponseState()
    {
        $state = Session::get('state');

        if( empty($state) ) return $state;

        $responseData = unserialize( serialize($state) );

        if( $responseData->game->status === Blackjack::GS_PL_HITS ) {
            unset($responseData->deck->cards);
            $responseData->dealer->blackjack = false;
            $responseData->dealer->blackjack = false;
            $responseData->dealer->currentHand = [$responseData->dealer->currentHand[0]];
            $responseData->dealer->currentScore = $responseData->dealer->currentHand[0]->value;
        }

        return $responseData;
    }
}
