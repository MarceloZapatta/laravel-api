<?php

namespace App\Service;

use App\Account;
use App\Investiment;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

use Exception;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

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

    /**
     * @var Extracts
     */
    private $extractsService;

    public function __construct(Extracts $extractsService)
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->baseUrl
        ]);

        $this->extractsService = $extractsService;
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
        $investiment = Investiment::create([
            'account_id' => $account->id,
            'amount' => $amount,
            'price' => number_format($buyPrice, 5, '.', '')
        ]);

        if (!$investiment) {
            return false;
        }

        $account->balance -= $amount;
        $account->save();

        $cryptQuantity = number_format(($amount / $buyPrice), 5, '.', '');

        $this->extractsService->storePurchase(
            $amount, 
            number_format($buyPrice, 5, '.', ''), 
            $cryptQuantity,
            $account);

        return $investiment;
    }

    public function sell($amount, $sellPrice, Account $account, Collection $investiments)
    {
        DB::beginTransaction();

        $totalAmountSell = $amount;

        foreach ($investiments as $investiment) {
            $investiment->liquidated_at = Carbon::now();
            $investiment->save();

            $cryptQuantity = ($investiment->amount / $investiment->price);
            $totalCryptSell = number_format($cryptQuantity * $sellPrice, 2, '.', '');

            $account->balance += $totalCryptSell;
            $account->save();

            $totalAmountSell -= $totalCryptSell;

            $this->extractsService->storeSell(
                $amount, 
                $investiment->price, 
                $sellPrice,
                $cryptQuantity,
                $account);

            if (number_format($totalAmountSell, 2, '.', '') <= 0) {
                break;
            }
        }

        $totalAmountSell = number_format($totalAmountSell, 2, '.', '');

        $parcialSell = $totalAmountSell < 0;

        if ($parcialSell) {
            $price = $this->price();

            if (empty($price->ticker)) {
                return false;
            }

            $buyPrice = $price->ticker->buy;

            $absoluteValue = abs($totalAmountSell);
            
            $this->purcharse($absoluteValue, $buyPrice, $account);
        }

        DB::commit();

        return true;
    }

    public function position($currentSellPrice)
    {
        $account = Auth::user()->account;

        if (!$account) {
            throw new Exception('User account not found.');
        }

        $investiments = Investiment::where('account_id', $account->id)
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
                'sellPrice' => $sellPrice,
                'liquidatedAt' => $item->liquidated_at
            ];
        });
    }

    public function enoughToSell(Collection $investiments, $currentSellPrice, $amount)
    {
        $totalInvestimentsSell = $investiments->reduce(function ($cont, $item) use ($currentSellPrice) {
            $cryptQuantity = ($item->amount / $item->price);
            $totalCryptSell = $cryptQuantity * $currentSellPrice;

            return $cont + $totalCryptSell;
        });

        if ($amount > $totalInvestimentsSell) {
            return false;
        }

        return true;
    }
}
