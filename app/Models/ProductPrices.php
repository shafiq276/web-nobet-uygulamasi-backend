<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class ProductPrices extends Model
{
    use HasUuids;

    protected $table = 'product_prices';

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    


    
    protected $fillable = [
        'ProductPrices',
        'WhichStatus',
        'numberofcalender'
    ];
}
