<?php

namespace App\Http\Controllers;

use App\Service\Accounts;
use App\Service\Crypts;
use App\Service\Extracts;
use App\Service\Investiments;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CryptController
{
    /**
     * @param Accounts
     */
    private $cryptsService;

    /**
     * @param Investiments
     */
    private $investimentsService;

    public function __construct(
        Crypts $cryptsService, 
        Investiments $investimentsService
    ) {
        $this->cryptsService = $cryptsService;
        $this->investimentsService = $investimentsService;
    }

    /**
     * Get's the current BTC price
     * @return Illuminate\Http\JsonResponse
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

    public function purchase(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|max:9999999999999999999'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $account = Auth::user()->account;

            if (!$account) {
                return response()->json([
                    'message' => 'Account not found.'
                ], 500);
            }

            $price = $this->price();

            $buyPrice = $price->getData()->buy;

            if (!$buyPrice) {
                return response()->json([
                    'message' => 'Error when trying to get crypt price.'
                ], 500);
            }

            $insufficientFunds = $this->cryptsService->insufficientFunds($request->amount, $account);

            if ($insufficientFunds) {
                return response()->json([
                    'message' => 'Insufficient Funds.'
                ], 400);
            }

            $purchase = $this->cryptsService->purcharse($request->amount, $buyPrice, $account);

            if (!$purchase) {
                return response()->json([
                    'message' => 'Error when trying to purchase the crypt'
                ], 400);
            }
            
            $purchaseAmount = (double) number_format(($request->amount / $buyPrice), 5, '.', '');

            return response()->json([
                'id' => $purchase->id,
                'userId' => Auth::user()->id,
                'purchasePrice' => $buyPrice,
                'amount' => $request->amount,
                'purchasedAmount' => $purchaseAmount
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    public function sell(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|max:9999999999999999999'
            ]);
    
            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $account = Auth::user()->account;

            if (!$account) {
                return response()->json([
                    'message' => 'Account not found.'
                ], 500);
            }

            $price = $this->price();

            $sellPrice = $price->getData()->sell;

            if (!$sellPrice) {
                return response()->json([
                    'message' => 'Error when trying to get crypt price.'
                ], 500);
            }

            $investiments = $this->investimentsService->getOpen($account);

            $enoughToSell = $this->cryptsService->enoughToSell($investiments, $sellPrice, $request->amount);

            if (!$enoughToSell) {
                return response()->json([
                    'message' => 'You don\'t have the amount sufficient to sell.'
                ], 400);
            }

            $sell = $this->cryptsService->sell($request->amount, $sellPrice, $account, $investiments);

            if (!$sell) {
                return response()->json([
                    'message' => 'Error when trying to sell the crypt'
                ], 500);
            }

            return response()->json([
                'message' => 'Success.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    public function position()
    {
        try {
            $price = $this->price();
            $sellPrice = $price->getData()->sell;

            if (!$sellPrice) {
                return response()->json([
                    'message' => 'An error occurred when trying to get crypt price'
                ], 503);
            }

            $position = $this->cryptsService->position($sellPrice);

            return response()->json([
                'position' => $position
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }
}
