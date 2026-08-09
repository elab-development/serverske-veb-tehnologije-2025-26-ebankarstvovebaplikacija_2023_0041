<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'amount' => $this->amount,
            'date_time' => $this->date_time->format('Y-m-d H:i:s'),
            'sender_account' => new AccountResource($this->whenLoaded('senderAccount')),
            'receiver_account' => new AccountResource($this->whenLoaded('receiverAccount')),
            'category' => new CategoryResource($this->whenLoaded('category'))
        ];
    }
}
