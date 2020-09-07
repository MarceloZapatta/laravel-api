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
    private $accountService;

    public function __construct(Accounts $accountService)
    {
        $this->accountService = $accountService;
    }

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

            $deposit = $this->accountService->deposit($request->amount);

            if (!$deposit) {
                return response()->json([
                    'message' => 'Error when trying to make a deposit.'
                ], 500);
            }

            return response()->json([
                'message' => 'Success.'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error ocurred.'
            ], 500);
        }
    }

    public function balance()
    {
        try {
            $balance = $this->accountService->balance();

            if (!$balance) {
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
