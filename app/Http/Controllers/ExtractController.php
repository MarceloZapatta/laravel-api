<?php

namespace App\Http\Controllers;

use App\Extract;
use App\Service\Extracts;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ExtractController
{
    /**
     * @param Extracts
     */
    private $extractsServices;

    public function __construct(Extracts $extractsServices)
    {
        $this->extractsServices = $extractsServices;
    }

    /**
     * Get the extract of account by date
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function extract(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'begin_date' => 'nullable|date_format:Y-m-d',
                'end_date' => 'nullable|date_format:Y-m-d',
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
    
            return $this->extractsServices->get($request, $account);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    /**
     * Get the volume of buy and sell in the day
     * @return \Illuminate\Http\JsonResponse
     */
    public function volume()
    {
        return $this->extractsServices->getVolume();        
    }
}
