<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class offdays extends Model
{
    use HasUuids;

    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'adminID',
        'userID',
        'offdayStart',
        'offdayEnd',
        'offdayReason',
        'status',
        'ResponseOffdayReason'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId')->onDelete('cascade');
    }
}