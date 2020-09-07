<?php

namespace App\Service;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class Authentication {
    /**
     * @var Accounts
     */
    private $accountService;

    public function __construct(Accounts $accountService)
    {
        $this->accountService = $accountService;
    }

    /**
     * Register a new user
     * @return User
     */
    public function register(Request $request)
    {
        DB::beginTransaction();
        
        $user = new User();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();
        
        $account = $this->accountService->store($user->id);

        if (!$account) {
            DB::rollBack();
            return false;
        }

        DB::commit();

        return $user;
    }

    /**
     * Retrives JWT token if user finded
     * @return string|null $token
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        return Auth::attempt($credentials);
    }
}