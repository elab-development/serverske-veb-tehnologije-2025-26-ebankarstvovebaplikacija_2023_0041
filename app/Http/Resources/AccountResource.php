<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
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
            'account_number' => $this->account_number,
            'balance' => $this->when(
                ($request->user()->role === 'admin' || $request->user()->role === 'super_admin'),
                $this->balance
            ),
            'user' => new UserResource($this->whenLoaded('user')),
            'currency' => new CurrencyResource($this->whenLoaded('currency'))
        ];
    }
}
