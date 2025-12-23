<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# Web Tabanlı Nöbet Yönetim Sistemi - Backend API

Bu proje, **Ondokuz Mayıs Üniversitesi Bilgisayar Mühendisliği** Bölümü Bitirme Projesi kapsamında geliştirilen **"Nöbet Asistanı"** uygulamasının sunucu tarafı (Backend) mimarisini oluşturmaktadır.

Frontend uygulamasına (Next.js) **RESTful API** servisleri sağlayarak; kimlik doğrulama, veri bütünlüğü, nöbet atama algoritmaları ve talep yönetim süreçlerini yönetir.

## 🎯 Projenin Teknik Amacı

Geleneksel nöbet yönetim sistemlerindeki veri tutarsızlığını ve güvenlik açıklarını gidermek amacıyla, **Laravel Framework** üzerine kurulu, **İlişkisel Veritabanı (RDBMS)** mimarisini kullanan güvenli bir altyapı sunmaktır. Sistem, **Süper Admin**, **Yönetici** ve **Personel** rolleri arasındaki veri akışını, tanımlanan kısıtlar (constraints) çerçevesinde işler.

## 🛠 Kullanılan Teknolojiler ve Mimari

Proje, **Tasarım Raporu (Design Report)** kapsamında belirlenen aşağıdaki teknoloji yığınını kullanmaktadır:

- **Core Framework:** Laravel 11.x
- **Language:** PHP 8.2+
- **Database:** MySQL / MariaDB (3NF Standartlarında)
- **Authentication:** Laravel Sanctum (Token Bazlı Güvenlik)
- **API Architecture:** RESTful Resource Controllers
- **Data Handling:** Eloquent ORM & API Resources

## ⚙️ Temel Özellikler ve Modüller

Backend servisi, aşağıdaki temel iş mantıklarını yürütür:

### 1. Kimlik Doğrulama ve Yetkilendirme (Auth & RBAC)
- **Laravel Sanctum** ile güvenli giriş ve çıkış işlemleri.
- Kullanıcı rollerine (Süper Admin, Yönetici, Personel) göre API uç noktalarının (endpoints) korunması (Middleware).

### 2. Nöbet Yönetim Modülü
- Nöbet çizelgelerinin JSON formatında yapılandırılarak saklanması.
- Geçmişe dönük nöbet kayıtlarının arşivlenmesi.
- *Planlanan:* Kısıt tabanlı (Constraint-Based) otomatik nöbet dağıtım algoritması.

### 3. Talep ve Onay Mekanizması (Workflow)
- Personel tarafından gönderilen `leave_requests` (İzin) ve `shift_preferences` (Tercih) verilerinin işlenmesi.
- Taleplerin durum takibi (`pending`, `approved`, `rejected`) ve veritabanı güncellemeleri.

### 4. Sistem Yapılandırması
- Departman (`departments`) ve kullanıcı (`users`) ilişkilerinin yönetimi.
- Dinamik ayar ve parametrelerin saklanması.

## 📂 Veritabanı Şeması (ER)

Proje veritabanı, veri bütünlüğünü sağlamak için normalize edilmiş tablolardan oluşur:

| Tablo Adı | Açıklama |
| :--- | :--- |
| `users` | Tüm kullanıcıların kimlik, rol ve iletişim bilgileri. |
| `departments` | Kurum içindeki birimler (Acil, Poliklinik vb.). |
| `shift_schedules` | Oluşturulan aylık nöbet listeleri ve atama verileri. |
| `leave_requests` | Personel izin ve mazeret talepleri. |
| `shift_preferences`| Personel nöbet değişim ve tercih istekleri. |
