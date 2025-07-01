<?php

namespace App\Services;

use App\Models\Card;
use App\Exceptions\NotFoundException;

class CardService
{

    public function getCardById(int $id): Card
    {
        $card = Card::with(['employee', 'transactions'])->find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        return $card;
    }

    public function deleteCard(int $id): bool
    {
        $card = Card::find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        return $card->delete();
    }
}
