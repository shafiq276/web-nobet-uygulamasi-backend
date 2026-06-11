<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class SipayToken extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'sipaytokens'; // Veritabanı tablosunun adı

    protected $primaryKey = 'id'; // Birincil anahtar olarak `uuid` kullanıyoruz
    public $incrementing = false; // UUID olduğu için otomatik artışı kapatıyoruz
    protected $keyType = 'string'; // UUID string türünde olduğu için belirtiyoruz
    public $timestamps = false;
    protected $fillable = [
        
        'adminID',
        'token'
        
    ];

     // `created_at` zaten `useCurrent()` ile belirleniyor, bu yüzden Laravel'in timestamps özelliğini kapatıyoruz.

    /**
     * Kullanıcı ile ilişki (Eğer user tablon ile bağlantılıysa)
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'userID');
    }
}

