<?php

namespace App\Service;

use App\History;
use Carbon\Carbon;

class Historys {
    private $cryptsService;

    public function __construct(Crypts $cryptsService)
    {
        $this->cryptsService = $cryptsService;
    }

    public function get()
    {
        return History::get()
            ->map(function ($item) {
                return [
                    'buy' => $item->buy,
                    'sell' => $item->sell,
                    'createdAt' => $item->created_at
                ];
            });
    }

    public function store()
    {
        $this->cleanOldData();

        $price = $this->cryptsService->price();

        if (empty($price->ticker)) {
            return response()->json([
                'message' => 'An error occurred when trying to get crypt price'
            ], 503);
        }

        $buyPrice = (double) number_format($price->ticker->buy, 5, '.', '');
        $sellPrice = (double) number_format($price->ticker->sell, 5, '.', '');

        History::create([
            'buy' => $price->ticker->buy,
            'sell' => $price->ticker->sell,
        ]);
    }

    private function cleanOldData() {
        History::whereDate('created_at', '<=', Carbon::now()->subDays(90))
            ->delete();
    }
}