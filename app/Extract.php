<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Extract extends Model {
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id', 'extract_type_id', 'data',
    ];

    public function getDataAttibute($value) {
        return json_decode($value);
    }

    public function type()
    {
        return $this->belongsTo('App\ExtractType', 'extract_type_id');
    }
}