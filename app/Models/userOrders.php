<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class userOrders extends Model
{
    use HasUuids;
    protected $table = 'user_orders';

    protected $primaryKey = 'id';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'userID',
        'invoiceID',
        'typeofadmin',
        'numberofcalender'
    ];
}
