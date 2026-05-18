<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Specialdepartman extends Model
{
    use HasUuids;
    
    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        
        'userID',
        'departmanName',
        'adminID',
        'departmanID'
        
    ];

}
