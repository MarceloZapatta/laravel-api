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
        'account_id', 'amount', 'price', 'investiment_type_id'
    ];

    public function type()
    {
        return $this->belongsTo('App\InvestimentType', 'investiment_type_id');
    }
}