<?php

namespace App\Http\Controllers;

use App\Http\Requests\CardRequest;
use App\Models\Card;

class CardController extends Controller
{

    public function index()
    {
        return Card::all();
    }

    public function store(CardRequest $request)
    {
        return Card::create($request->validated());
    }

    public function show(Card $card)
    {
        return $card;
    }

    public function update(CardRequest $request, Card $card)
    {
        $card->update($request->validated());

        return $card;
    }

    public function destroy(Card $card)
    {
        $card->delete();

        return response()->json();
    }
}
