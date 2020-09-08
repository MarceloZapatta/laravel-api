<?php

namespace App\Service;

use App\Account;
use App\Extract;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Extracts {
    /**
     * Store's a extract of deposit type
     * @param double $amount
     * @param double $oldAmount
     * @param double $newAmount
     * @return Extract
     */
    public function storeDeposit($amount, $oldAmount, $newAmount, Account $account)
    {
        $depositExtractType = 1;

        $data = [
            'amount' => (double) $amount,
            'oldAmount' => (double) $oldAmount,
            'newAmount' => (double) $newAmount
        ];

        $extract = Extract::create([
            'account_id' => $account->id,
            'extract_type_id' => $depositExtractType,
            'data' => json_encode($data)
        ]);

        return $extract;
    }

    /**
     * Store's a extract of investiment type
     * @param double $amount
     * @param double $oldAmount
     * @param double $newAmount
     * @return Extract
     */
    public function storePurchase($amount, $price, $cryptQuantity, Account $account)
    {
        $depositExtractType = 2;

        $data = [
            'amount' => (double) $amount,
            'price' => (double) $price,
            'cryptQuantity' => (double) $cryptQuantity
        ];

        $extract = Extract::create([
            'account_id' => $account->id,
            'extract_type_id' => $depositExtractType,
            'data' => json_encode($data)
        ]);

        return $extract;
    }



    /**
     * Store's a extract of liquidation type
     * @param double $amount
     * @param double $oldAmount
     * @param double $newAmount
     * @return Extract
     */
    public function storeSell($amount, $buyPrice, $sellPrice, $cryptQuantity, Account $account)
    {
        $depositExtractType = 3;

        $data = [
            'amount' => (double) $amount,
            'buyPrice' => (double) $buyPrice,
            'sellPrice' => (double) $sellPrice,
            'cryptQuantity' => (double) $cryptQuantity
        ];

        $extract = Extract::create([
            'account_id' => $account->id,
            'extract_type_id' => $depositExtractType,
            'data' => json_encode($data)
        ]);

        return $extract;
    }

    public function get(Request $request, Account $account)
    {        
        $extracts = Extract::select('id', 'data', 'extract_type_id', 'created_at')
            ->with('type:id,type')
            ->where('account_id', $account->id)
            ->orderBy('created_at', 'desc');
        
        if (!empty($request->begin_date)) {
            $extracts->whereDate('created_at', '>=', $request->begin_date);
        }
        
        if (!empty($request->end_date)) {
            $extracts->whereDate('created_at', '<=', $request->end_date);
        }

        if (empty($request->begin_date) && empty($request->end_date)) {
            $extracts->whereDate('created_at', '>=', Carbon::now()->subDays(90));
        }

        $extracts = $extracts->get();

        return $extracts->map(function ($item) {
            return [
                'id' => $item->id,
                'type' => $item->type->type ?? null,
                'data' => json_decode($item->data),
                'createdAt'=> $item->created_at
            ];
        });
    }

    public function getVolume()
    {
        $today = Carbon::now()->format('Y-m-d');

        $extractTypeBuy = 2;
        $extractTypeSell = 3;

        $extracts = Extract::whereDate('created_at', $today)
            ->whereIn('extract_type_id', [$extractTypeBuy, $extractTypeSell])
            ->get();

        $totalBuy = 0;
        $totalSell = 0;

        foreach ($extracts as $extract) {
            $data = json_decode($extract->data);

            if ($extract->extract_type_id === $extractTypeBuy) {
                $totalBuy = $data->cryptQuantity * $data->price;
            } else {
                $totalSell = $data->cryptQuantity * $data->sellPrice;
            }
        }

        return [
            'buy' => $totalBuy,
            'sell' => $totalSell
        ];
    }
}