<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Investiment extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'amount', 'price'
    ];
}