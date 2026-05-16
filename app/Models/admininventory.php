<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class admininventory extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'adminID',
        'numberofcalendars',
        'balance',
        "numberofcalendars",
        'typeofadmin',
        'tymofShoppin',
    ];

    
}