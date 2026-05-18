<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Usertotalshiftcount extends Model
{
    use HasUuids;
    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'adminID',
        'userID',
        'callenderID',
        'totalShiftCount',
        
    ];
}
