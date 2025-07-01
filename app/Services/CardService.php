<?php

namespace App\Services;

use App\Models\Card;
use App\Exceptions\NotFoundException;
use App\Models\Employee;
use Illuminate\Support\Str;

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

    public function createCard(array $data): Card
    {
        return Card::create($data);
    }

    public function updateCard(int $id, array $data): Card
    {
        $card = Card::find($id);

        if (!$card) {
            throw new NotFoundException('Card not found');
        }

        $card->update($data);
        return $card->fresh();
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
