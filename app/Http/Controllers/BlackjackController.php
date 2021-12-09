<?php
namespace App\Http\Controllers;

use App\Services\BlackjackService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class BlackjackController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index()
    {
        return view('game-table');
    }

    public function getState(BlackjackService $blackjackService)
    {
        $response = $blackjackService->getState();

        return response()->json($response, $response->status);
    }

    public function start(BlackjackService $blackjackService, Request $request)
    {
        $params = $request->all();
        $response = $blackjackService->start($params);

        return response()->json($response, $response->status);
    }

    public function stay(BlackjackService $blackjackService)
    {
        $response = $blackjackService->stay();

        return response()->json($response, $response->status);
    }

    public function hit(BlackjackService $blackjackService, Request $request)
    {
        $params = $request->all();
        $response = $blackjackService->hit($params);

        return response()->json($response, $response->status);
    }
}
