<?php

namespace App\Service;

use App\Account;
use App\Extract;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Accounts {
    /**
     * @var Extracts
     */
    private $extractsService;

    public function __construct(Extracts $extractsService)
    {
        $this->extractsService = $extractsService;
    }

    /**
     * Store a new Account for the user
     * @param int $userId
     */
    public function store(int $userId)
    {
        if (!$userId) {
            return false;
        }

        $account = Account::create([
            'user_id' => $userId,
            'balance' => 0
        ]);

        return $account;
    }

    /**
     * Make a new deposit in account
     * @param double $amount
     */
    public function deposit($amount)
    {
        $user = Auth::user();

        $account = $user->account;

        if (!$account) {
            throw new Exception('Account not found.');
        }

        $oldBalance = $account->balance;

        $account->balance += $amount;
        $account->save();

        $newBalance = $account->balance;

        $this->extractsService->storeDeposit($amount, $oldBalance, $newBalance, $account);

        return true;
    }

    /**
     * Return the current account balance
     * @return double
     */
    public function balance()
    {
        $user = Auth::user();

        $account = $user->account;

        if (!$account) {
            throw new Exception('Account not found.');
        }

        return (double) $account->balance;
    }
}