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
            dd($e->getMessage());
            return response()->json([
                'message' => 'An error occurred.'
            ], 500);
        }
    }

    public function volume()
    {
        return $this->extractsServices->getVolume();        
    }
}
