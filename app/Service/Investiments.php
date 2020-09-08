<?php

namespace App\Service;

use App\Account;
use App\Investiment;

class Investiments {
    public function getOpen(Account $account)
    {
        return Investiment::where('account_id', $account->id)
            ->whereNull('liquidated_at')
            ->get();
    }
}