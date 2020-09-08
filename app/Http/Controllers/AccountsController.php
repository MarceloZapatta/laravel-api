<?php

namespace App\Http\Controllers;

use App\Service\Accounts;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AccountsController
{
    /**
     * @param Accounts
     */
    private $accountsService;

    public function __construct(Accounts $accountsService)
    {
        $this->accountsService = $accountsService;
    }

    /**
     * Make a new deposit in the account
     * @param \Illuminate\Http\Request
     * @return \Illuminate\Http\JsonResponse
     */
    public function deposit(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric|min:1|max:9999999999999999999'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'errors' => $validator->errors()
                ], 400);
            }

            $deposit = $this->accountsService->deposit($request->amount);

            if (!$deposit) {
                return response()->json([
                    'message' => 'Error when trying to make a deposit.'
                ], 500);
            }

            return response()->json([
                'message' => 'Success.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error ocurred.'
            ], 500);
        }
    }

    /**
     * Get the balance of the account
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance()
    {
        try {
            $balance = $this->accountsService->balance();

            if (!is_numeric($balance)) {
                return response()->json([
                    'message' => 'Error when trying to get the balance.'
                ], 500);
            }

            return response()->json([
                'balance' => $balance
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error ocurred.'
            ], 500);
        }
    }
}
