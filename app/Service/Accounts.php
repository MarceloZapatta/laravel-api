<?php

namespace App\Service;

use App\Account;

class Accounts {
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
}