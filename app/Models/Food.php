<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{   
    protected $table = 'foods';

    protected $fillable = [
        'name', 'price', 'image'
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => asset('/storage/foods/' . $image),
        );
    }

    public function detailTransaction()
    {
        return $this->hasMany(DetailTransaction::class);
    }
}
