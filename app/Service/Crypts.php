<?php

namespace App\Service;

use App\Account;
use App\Investiment;

use Illuminate\Support\Facades\Auth;

use Exception;

class Crypts
{
    /**
     * Base url of api
     * @var string
     */
    private $baseUrl = "https://www.mercadobitcoin.net/api/BTC/";

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl
        ]);
    }

    public function price()
    {
        $response = $this->client->get('ticker');

        return json_decode($response->getBody());
    }

    public function insufficientFunds($amount, Account $account)
    {
        if ((float) $account->balance < (float) $amount) {
            return true;
        }

        return false;
    }

    /**
     * Buy an amount of the crypt
     * @param double $amount
     * @param double $buyPrice
     * @param Account $account
     * @return Investiment|null
     */
    public function purcharse($amount, $buyPrice, Account $account)
    {
        $buyInvestimentPriceType = 1;

        $investiment = Investiment::create([
            'account_id' => $account->id,
            'amount' => $amount,
            'price' => number_format($buyPrice, 5, '.', ''),
            'investiment_type_id' => $buyInvestimentPriceType
        ]);

        if (!$investiment) {
            return false;
        }

        $account->balance -= $amount;
        $account->save();

        return $investiment;
    }

    public function position($currentSellPrice)
    {
        $account = Auth::user()->account;

        if (!$account) {
            throw new Exception('User account not found.');
        }

        $investiments = Investiment::where('account_id', $account->id)
            ->with('type')
            ->whereHas('type', function ($query) {
                $query->where('type', 'buy');
            })
            ->get();

        return $investiments->map(function ($item) use ($currentSellPrice) {
            $sellPrice = ($item->amount / $item->price) * $currentSellPrice;
            $variation  = (($currentSellPrice - $item->price) / $item->price) * 100;

            return [
                'id' => $item->id,
                'purchaseDate' => $item->created_at,
                'purchaseAmount' => (double) $item->amount,
                'purchasePrice' => (double) $item->price,
                'variation' => $variation,
                'sellPrice' => $sellPrice
            ];
        });
    }
}
