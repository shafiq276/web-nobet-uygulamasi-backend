<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Web Tabanlı Nöbet Yönetim Sistemi - Backend API

Bu proje, **"Nöbet Asistanı"** uygulamasının sunucu tarafını oluşturmaktadır.

Backend servisi; frontend uygulamasıyla haberleşen **RESTful API** yapısını sağlar ve sistem içerisinde **kimlik doğrulama, rol bazlı yetkilendirme, kullanıcı ve yönetici işlemleri, nöbet/takvim yönetimi, talep süreçleri ve veritabanı bütünlüğü** gibi temel işlevleri yürütür.

## 🎯 Projenin Teknik Amacı

Bu backend yapısının amacı, geleneksel nöbet yönetim süreçlerinde karşılaşılan **veri tutarsızlığı, erişim karmaşası ve manuel işlem yükünü** azaltmaktır. Sistem; farklı kullanıcı rollerinin aynı yapı üzerinde kontrollü şekilde çalışmasını sağlayarak daha güvenli, sürdürülebilir ve yönetilebilir bir altyapı sunmayı hedeflemektedir.

Uygulama kapsamında:
- kullanıcı giriş ve kimlik doğrulama işlemleri,
- rol bazlı erişim kontrolü,
- nöbet ve takvim işlemleri,
- izin ve görev talepleri,
- yönetici onay mekanizmaları,
- departman ve kullanıcı ilişkileri

tek bir backend mimarisi altında toplanmıştır.

## 🛠 Kullanılan Teknolojiler ve Mimari

Projede kullanılan temel teknolojiler aşağıdaki gibidir:

- **Core Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL / MariaDB
- **Authentication:** JWT Authentication + Laravel Sanctum bağımlılığı
- **API Architecture:** RESTful API
- **ORM:** Eloquent ORM
- **Routing:** Laravel API Routes
- **Middleware:** Token doğrulama ve rol bazlı yetkilendirme

## 🧱 Mimari Yapı

Proje, katmanlı backend mimarisi ile geliştirilmiştir:

- **Routes:** API uç noktaları `routes/api.php` içinde tanımlanır.
- **Controllers:** İş mantıkları controller katmanında yürütülür.
- **Models:** Veritabanı tabloları ve ilişkiler model katmanında yönetilir.
- **Middleware:** JWT kontrolü ve rol bazlı erişim denetimi sağlar.
- **Migrations / Seeders:** Veritabanı şeması ve başlangıç verileri için kullanılır.

## ⚙️ Temel Özellikler ve Modüller

Backend servisi aşağıdaki temel modülleri içermektedir:

### 1. Kimlik Doğrulama ve Yetkilendirme
- Kullanıcı kayıt olma ve giriş yapma işlemleri
- JWT tabanlı token üretimi ve doğrulama
- Çıkış yapma işlemleri
- Şifre sıfırlama kodu gönderme, doğrulama ve parola yenileme
- Korunan route yapıları ve middleware kullanımı

### 2. Rol Bazlı Erişim Kontrolü
Sistem farklı kullanıcı rollerine göre çalışmaktadır. Kod yapısında rol bazlı erişim kontrolü bulunmaktadır.

Örnek roller:
- **Admin**
- **Creator / Yönetici**
- **User / Personel**

Belirli API uç noktalarına sadece ilgili rol grubunun erişmesi sağlanmaktadır.

### 3. Kullanıcı İşlemleri
- Kullanıcı bilgilerini görüntüleme
- İzin / off-day talepleri oluşturma
- Özel görev veya tercih taleplerini iletme
- Kullanıcıya ait takvim ve görev verilerini görüntüleme

### 4. Yönetici İşlemleri
- Departman oluşturma ve düzenleme
- Kullanıcı atama işlemleri
- Talepleri onaylama / reddetme
- Sistem üzerindeki yönetimsel süreçleri kontrol etme

### 5. Nöbet ve Takvim Yönetimi
- Takvim oluşturma ve kayıt altına alma
- Nöbet verilerini saklama
- Geçmiş takvim kayıtlarını görüntüleme
- Planlama süreçlerini API üzerinden yönetme

### 6. Ek Modüller
Projede ana nöbet yönetim yapısına ek olarak bazı yardımcı modüller de bulunmaktadır:
- Ürün fiyatı yönetimi
- Ödeme işlemleri ile ilişkili backend akışları

## 📂 Veritabanı Yapısı

Proje veritabanı ilişkisel yapı kullanılarak tasarlanmıştır. Sistemde yer alan temel tablolar aşağıdaki işlevleri karşılamaktadır:

| Tablo Adı | Açıklama |
| :--- | :--- |
| `users` | Kullanıcı kimlik, rol ve iletişim bilgileri |
| `departments` | Kurum içindeki birim ve departman bilgileri |
| `shift_schedules` | Nöbet ve takvim kayıtları |
| `leave_requests` | Kullanıcı izin ve mazeret talepleri |
| `shift_preferences` | Kullanıcı tercih ve görev talepleri |

> Not: Veritabanı yapısı proje geliştirme sürecine bağlı olarak migration dosyalarına göre genişletilebilmektedir.

## 🔐 Kimlik Doğrulama Yapısı

Projede kimlik doğrulama akışı token tabanlı olarak tasarlanmıştır. Kod yapısında özellikle **JWT tabanlı doğrulama** kullanılmaktadır. Ayrıca projede Laravel Sanctum bağımlılığı da yer almaktadır.

Auth sürecinde bulunan temel işlemler:
- kullanıcı kaydı
- kullanıcı girişi
- token ile korumalı route erişimi
- logout
- şifre sıfırlama ve doğrulama işlemleri

## 🌐 Örnek API İşlevleri

Backend tarafında bulunan bazı temel API işlevleri:

### Auth
- `POST /api/register`
- `POST /api/login`
- `POST /api/logout`
- `POST /api/sendResetCode`
- `POST /api/verifyResetCode`
- `POST /api/resetPassword`

### User Operations
- izin talebi oluşturma
- özel görev / tercih işlemleri
- kullanıcı verilerini görüntüleme
- takvim verilerine erişim

### Admin Operations
- departman oluşturma
- kullanıcı atama
- talepleri onaylama / reddetme
- nöbet/takvim yönetimi

## 🚀 Kurulum Adımları

Projeyi yerel ortamda çalıştırmak için:

1. Repoyu klonlayın:
```bash
git clone https://github.com/shafiq276/web-nobet-uygulamasi-backend.git
cd web-nobet-uygulamasi-backend