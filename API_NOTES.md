# Backend API Notes

Bu doküman, Web Tabanlı Nöbet Yönetim Sistemi backend servislerinin genel API yapısını ve modül gruplarını özetlemek amacıyla hazırlanmıştır.

## API Yapısı

Backend API servisleri Laravel Framework üzerinde geliştirilmiştir. API endpointleri temel olarak `routes/api.php` dosyası üzerinden yönetilmektedir.

Sistem genelinde aşağıdaki modül grupları bulunmaktadır:

## 1. Authentication

Kullanıcı kayıt, giriş, çıkış ve şifre sıfırlama işlemlerini kapsar.

Temel işlemler:

- Kullanıcı kaydı
- Kullanıcı girişi
- Kullanıcı çıkışı
- Şifre sıfırlama kodu gönderme
- Şifre sıfırlama kodu doğrulama
- Yeni şifre belirleme

## 2. Role-Based Access Control

Sistem içerisinde farklı kullanıcı rolleri için erişim kontrolü sağlanır.

Kullanılan temel roller:

- Admin
- Creator
- User

Bu yapı sayesinde belirli API endpointleri yalnızca yetkili kullanıcılar tarafından kullanılabilir.

## 3. User Operations

Kullanıcı tarafındaki temel işlemleri kapsar.

Örnek işlemler:

- Kullanıcı profil bilgileri
- İzin günü talepleri
- Özel görev talepleri
- Takvim verileri
- Nöbet tercihleri

## 4. Admin Operations

Admin rolüne sahip kullanıcıların sistem yönetimi işlemlerini kapsar.

Örnek işlemler:

- Departman yönetimi
- Kullanıcı yönetimi
- Nöbet planlama
- İzin ve özel görev taleplerini değerlendirme
- Takvim oluşturma ve silme

## 5. Creator Operations

Creator rolüne sahip kullanıcılar için sistem geneli yönetim işlemlerini kapsar.

Örnek işlemler:

- Kullanıcı rol yönetimi
- Takvim sayısı yönetimi
- Ürün fiyatlandırma işlemleri

## 6. Payment and Pricing

Sistemdeki ödeme ve ürün fiyatlandırma işlemleri ayrı controller yapıları üzerinden yönetilir.

Örnek işlemler:

- Ödeme başlatma
- Ödeme dönüş işlemleri
- Ürün fiyatı ekleme
- Ürün fiyatı güncelleme
- Ürün fiyatı silme
- Ürün fiyatı listeleme

## 7. Database Layer

Veritabanı tarafında Laravel model ve migration yapısı kullanılmıştır.

Kullanılan yapı grupları:

- Models
- Migrations
- Seeders
- Factories

Bu yapı sayesinde veritabanı tabloları, ilişkili model yapıları ve test/geliştirme verileri daha düzenli yönetilebilir.

## Not

Bu dosya, backend servislerinin genel modül yapısını özetlemek için hazırlanmıştır.