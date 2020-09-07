<?php

namespace App\Http\Controllers;

use App\Service\Accounts;
use App\Service\Crypts;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CryptController
{
    /**
     * @param Accounts
     */
    private $cryptsService;

    public function __construct(Crypts $cryptsService)
    {
        $this->cryptsService = $cryptsService;
    }

    /**
     * Get's the current BTC price
     * @return array
     */
    public function price()
    {
        try {
            $price = $this->cryptsService->price();

            if (empty($price->ticker)) {
                return response()->json([
                    'message' => 'An error occurred when trying to get crypt price'
                ], 503);
            }

            return response()->json([
                'buy' => (float) $price->ticker->buy,
                'sell' => (float) $price->ticker->sell
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred'
            ], 500);
        }
    }
}
