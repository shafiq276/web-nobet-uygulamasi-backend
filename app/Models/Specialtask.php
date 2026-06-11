<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
class Specialtask extends Model
{
    use HasUuids;
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;
    
    protected $fillable = [
        'userID',
        'departmanName',
        'shiftDay',
        'status',
        'whichShift',
        'adminID',
        'SpecialtaskReason',
        'ResponsSpecialtaskReason',
        'departmanID',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function departman()
    {
        return $this->belongsTo(Departman::class, 'departmanName', 'departmanName')->onDelete('cascade');
    }
}