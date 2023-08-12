<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailTransaction extends Model
{
    protected $table = 'detail_transactions';

    protected $fillable = [
        'transaction_id',
        'food_id',
        'amount',
        'price',
        'subtotal'
    ];

    public $timestamps = false;

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
