<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class Departmans extends Model
{
    use Notifiable ,HasUuids;

    
    protected $primaryKey = 'id';
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'departmanName',
        'priority',
        'adminID',
        'AuserCountsShift1',
        'AuserCountsShift2',
        'AuserCountsShift3',
        'AuserCountsShift4',
        'AuserCountsShift5',
        'BuserCountsShift1',
        'BuserCountsShift2',
        'BuserCountsShift3',
        'BuserCountsShift4',
        'BuserCountsShift5',
        'CuserCountsShift1',
        'CuserCountsShift2',
        'CuserCountsShift3',
        'CuserCountsShift4',
        'CuserCountsShift5',
        'DuserCountsShift1',
        'DuserCountsShift2',
        'DuserCountsShift3',
        'DuserCountsShift4',
        'DuserCountsShift5',
        'EuserCountsShift1',
        'EuserCountsShift2',
        'EuserCountsShift3',
        'EuserCountsShift4',
        'EuserCountsShift5',
        'FuserCountsShift1',
        'FuserCountsShift2',
        'FuserCountsShift3',
        'FuserCountsShift4',
        'FuserCountsShift5',
        'GuserCountsShift1',
        'GuserCountsShift2',
        'GuserCountsShift3',
        'GuserCountsShift4',
        'GuserCountsShift5',
        'RgbNumber'
    ];

    public function specialtasks()
    {
        return $this->hasMany(Specialtask::class, 'departmanName', 'departmanName');
    }
}