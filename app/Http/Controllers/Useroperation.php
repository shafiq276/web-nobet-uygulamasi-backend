<?php

namespace App\Http\Controllers;

use App\Models\offdays;
use App\Models\specialtask;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use App\Models\Userinventorieswish;



class Useroperation extends Controller
{
    

    public function takeOffDays(Request $request)
    {
        // Validasyon
        
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
            $adminID =auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
        'startDate' => [
            'required', 
            'date_format:Y-m-d', // Tarih formatını kontrol et
            'after_or_equal:today', // Tarih bugüne eşit veya sonrası olmalı
            'before_or_equal:endDate', // Bitiş tarihine eşit veya önce olmalı
        ],
        'endDate' => [
            'required', 
            'date_format:Y-m-d', // Tarih formatını kontrol et
            'after_or_equal:startDate', // Başlangıç tarihine eşit veya sonra olmalı
        ],
        'offdayReason' => 'required'
        ], [
            'startDate.required' => 'baslangic tarihi bos birakilamaz',
            'startDate.date_format' => 'Başlangıç tarihi formatı geçerli değil. Lütfen Y-m-d formatında bir tarih girin.',
            'startDate.after_or_equal' => 'Başlangıç tarihi bugünden önce olamaz.',
            'startDate.before_or_equal' => 'Başlangıç tarihi, bitiş tarihinden önce veya eşit olmalıdır.',
            'endDate.required' => 'Bitiş tarihi zorunludur ve boş bırakılamaz.',
            'endDate.date_format' => 'Bitiş tarihi formatı geçerli değil. Lütfen Y-m-d formatında bir tarih girin.',
            'endDate.after_or_equal' => 'Bitiş tarihi, başlangıç tarihine eşit veya sonra olmalıdır.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        
        $startDate = Carbon::parse($request->startDate);
        $endDate = Carbon::parse($request->endDate);


        $pendingOffDay = DB::table('offdays')
        ->where('userID',$userID)
        ->where('status','pending')
        ->exists();
        
        if($pendingOffDay)
        {
            return response()->json(['success'=>false,'message'=>'bekleyen izin talebiniz bulunmakta']);
        }
 
        $threeMonthsAgo = Carbon::now()->subMonths(1);
        $sameOffDays = DB::table('offdays')
        ->where('userID',$userID)
        ->where('status','approved')
        ->where('created_at', '>', $threeMonthsAgo)
        ->get();
        
        foreach($sameOffDays as $sameOFF)
        {
            
            if($startDate->between(Carbon::parse($sameOFF->offdayStart),Carbon::parse($sameOFF->offdayEnd)) || $endDate->between(Carbon::parse($sameOFF->offdayStart),Carbon::parse($sameOFF->offdayEnd))|| Carbon::parse($sameOFF->offdayStart)->between(Carbon::parse($startDate),Carbon::parse($endDate)) || Carbon::parse($sameOFF->offdayEnd)->between(Carbon::parse($startDate),Carbon::parse($endDate)))
            {
                
                return response()->json(['success'=>'false','message'=>'girdiginiz tarihlerde kullanici zaten izinli'],400);
            }
        }

        // Tarih formatlama
        $startDate = Carbon::parse($request->startDate)->format('Y-m-d');
        $endDate = Carbon::parse($request->endDate)->format('Y-m-d');

        try{
        // Yeni offdays kaydı oluştur
            $offdays = offdays::create([
                'adminID' => $adminID,
                'userID' => $userID,
                'offdayStart' => $startDate,
                'offdayEnd' => $endDate,
                'offdayReason' => $request->offdayReason,
                'status' => 'pending',
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'izin gunu kaydedilirken hata oluştu','succes'=>'false'], 500);
        }
        return response()->json(['message' => 'İzin talebi başarıyla oluşturuldu.','success'=>'true', 'offdays' => $offdays], 201);
    }


    public function takeSpecialTask(Request $request){

        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        }
        
        
        
        $adminID = auth()->user()->adminID;
        

        $departmans= DB::table('departmans')
        ->where('adminID',$adminID)
        ->get();
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
       
        $validator = Validator::make($request->all(), [
            'departmanName' => ['required'],
            'shiftDay' => ['required', Rule::in($days)],
            'whichShift' => ['required','string',Rule::in([
                'AuserCountsShift1', 'AuserCountsShift2', 'AuserCountsShift3', 'AuserCountsShift4', 'AuserCountsShift5',
                'BuserCountsShift1', 'BuserCountsShift2', 'BuserCountsShift3', 'BuserCountsShift4', 'BuserCountsShift5',
                'CuserCountsShift1', 'CuserCountsShift2', 'CuserCountsShift3', 'CuserCountsShift4', 'CuserCountsShift5',
                'DuserCountsShift1', 'DuserCountsShift2', 'DuserCountsShift3', 'DuserCountsShift4', 'DuserCountsShift5',
                'EuserCountsShift1', 'EuserCountsShift2', 'EuserCountsShift3', 'EuserCountsShift4', 'EuserCountsShift5',
                'FuserCountsShift1', 'FuserCountsShift2', 'FuserCountsShift3', 'FuserCountsShift4', 'FuserCountsShift5',
                'GuserCountsShift1', 'GuserCountsShift2', 'GuserCountsShift3', 'GuserCountsShift4', 'GuserCountsShift5',
            ])] ,
            'SpecialtaskReason',
        
        ], [
            'departmanName.required' => 'Departman adı alanı zorunludur.',
            'shiftDay.required' => 'Vardiya günü alanı zorunludur.',
            'shiftDay.in' => 'Vardiya günü geçerli değil. Sadece şu değerlerden biri olabilir: ' . implode(', ', $days),
            'whichShift.required' => 'Vardiya seçimi alanı zorunludur.',
            'whichShift.string' => 'whichShift string bir deger olmali',
            'whichShift.in' => 'yalnizca bu degerlerden biri olabilir userCountsShift1 userCountsShift2 userCountsShift3 userCountsShift4 userCountsShift5',
            
           
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $pendingSpecialTask = DB::table('specialtasks')
        ->where('userID',$userID)
        ->where('status','pending')
        ->exists();

        if ($pendingSpecialTask) {
            return response()->json(['message' => 'bekleyen bir ozel calisma talebiniz var','success'=>'false'], 400);
        }

        $whichShift = $request->whichShift; // Gelen değeri al

        $shift = substr($whichShift, 1);

        switch ($shift) {
            case 'userCountsShift1':
                $ada = 1;
                
                break;
            
            case 'userCountsShift2':
                $ada = 2;
                
                break;
            
            case 'userCountsShift3':
                $ada = 3;
                
                break;
            
            case 'userCountsShift4':
                $ada = 4;
                
                break;
            
            case 'userCountsShift5':
                $ada = 5;
                
                break;
            
            default:
                $ada = "Geçersiz vardiya seçimi.";
                
                break;
        }


        $theDayisFulls = DB::table('specialtasks')
        ->where('adminID',$adminID)
        ->where('departmanName', $request->departmanName)
        ->where('shiftDay', $request->shiftDay)
        ->where('whichShift',$ada)
        ->where('status','approved')
        ->get();
        
        
        foreach($theDayisFulls as $theDayisFull)
        {
            if (isset($theDayisFull) && $theDayisFull->userID == $userID)
            {
                return response()->json([
                    'success' => false,
                    'message' => 'belirtilen sartlarda zaten atanmissiniz.',
                    'data' => $theDayisFull,
                ], 400);

            }
        }
        
        if (count($theDayisFulls) >=$departmans->where('departmanName', $request->departmanName)->value($request->whichShift)) 
        {
            return response()->json([
                'success' => false,
                'message' => 'O departman icin O gün ve o vardiyada icin bos yer yok.',
                'data' => $theDayisFulls,
            ], 400);
        }
        

        $controlDays = DB::table('specialtasks')
        ->where('userID',$userID)
        ->where('shiftDay',$request->shiftDay)
        ->where('status','approved')
        ->exists();

        if($controlDays){
            return response()->json([
                'success' => 'false',
                'message' => 'O gün baska bir yerde nobet tutuyorsunuz.',
                
            ], 400);
        }

        
        $departmanID = $departmans->where('departmanName',$request->departmanName)->value('id');
        
        $whichShift = $request->whichShift; // Gelen değeri al

        $shift = substr($whichShift, 1);

        switch ($shift) {
            case 'userCountsShift1':
                $ada = 1;
                
                break;
            
            case 'userCountsShift2':
                $ada = 2;
                
                break;
            
            case 'userCountsShift3':
                $ada = 3;
                
                break;
            
            case 'userCountsShift4':
                $ada = 4;
                
                break;
            
            case 'userCountsShift5':
                $ada = 5;
                
                break;
            
            default:
                $ada = "Geçersiz vardiya seçimi.";
                
                break;
        }

        try{
            $specialTask = Specialtask::create([
                'userID'=>$userID,
                'adminID' =>$adminID,
                'departmanName'=> $request->departmanName,
                'shiftDay'=> $request->shiftDay,
                'whichShift' => $ada,
                'status'=> 'pending',
                'SpecialtaskReason'=>$request->SpecialtaskReason,
                'departmanID'=>$departmanID,
            ]);
        }catch (JWTException $e) {
            return response()->json(['message' => 'specialTask kaydedilirken hata oluştu','success'=>'false'], 500);
        }

        return response()->json(['message' => 'ozel is gunu basariyla kaydedildi','success'=>'true'], 201);


        
    }

    
    
    public function updateFirstname(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }
        
        
        

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:255'],
        ], [
            'firstname.required' => 'Firstname alanı zorunludur.',
            'firstname.string' => 'Firstname alanı bir metin olmalıdır.',
            'firstname.max' => 'Firstname alanı en fazla 255 karakter olabilir.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($user->firstname === $request->firstname) {
            return response()->json([
                'success' => true,
                'message' => 'Değişiklik yapılmadı.',
            ], 200);
        }

        // Alanı güncelle
        $user->firstname = $request->firstname;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Firstname başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

    public function updateLastname(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

    

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'lastname' => ['required', 'string', 'max:255'],
        ], [
            'lastname.required' => 'Lastname alanı zorunludur.',
            'lastname.string' => 'Lastname alanı bir metin olmalıdır.',
            'lastname.max' => 'Lastname alanı en fazla 255 karakter olabilir.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($user->lastname === $request->lastname) {
            return response()->json([
                'success' => true,
                'message' => 'Değişiklik yapılmadı.',
            ], 200);
        }

        // Alanı güncelle
        $user->lastname = $request->lastname;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Lastname başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

    
    public function updateUsername(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

        

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
        ], [
            'username.required' => 'Username alanı zorunludur.',
            'username.string' => 'Username alanı bir metin olmalıdır.',
            'username.max' => 'Username alanı en fazla 255 karakter olabilir.',
            'username.unique' => 'Bu kullanıcı adı zaten kullanılıyor.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Alanı güncelle
        $user->username = $request->username;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Username başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

   
    public function updateGender(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'gender' => ['required', 'in:male,female'],
        ], [
            'gender.required' => 'Gender alanı zorunludur.',
            'gender.in' => 'Gender alanı sadece "male" veya "female" olabilir.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Alanı güncelle
        $user->gender = $request->gender;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Gender başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

    
    public function updatePhoneNumber(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

        

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'phoneNumber' => 'required|string|regex:/^\\+?[0-9]{10,15}$/',
        ], [
            'phoneNumber.regex' => 'Telefon numarası geçerli bir formatta olmalıdır.',
            'phoneNumber.required' => 'Telefon numarasi zorunludur.'
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Alanı güncelle
        $user->phoneNumber = $request->phoneNumber;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'PhoneNumber başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

    
    public function updateEmail(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

        

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ], [
            'email.required' => 'Email alanı zorunludur.',
            'email.string' => 'Email alanı bir metin olmalıdır.',
            'email.email' => 'Geçerli bir email adresi giriniz.',
            'email.max' => 'Email alanı en fazla 255 karakter olabilir.',
            'email.unique' => 'Bu email adresi zaten kullanılıyor.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        if ($user->email === $request->email) {
            return response()->json([
                'success' => true,
                'message' => 'Değişiklik yapılmadı.',
            ], 200);
        }

        // Alanı güncelle
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Email başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }



    public function updatePassword(Request $request)
    {
        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }

        

        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'oldPassword' => ['required', 'string'], // Eski şifre
            'newPassword' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                'different:old_password', // Yeni şifre, eski şifreden farklı olmalı
            ],
            'rePassword' => ['required', 'string', 'same:newPassword'], // Yeni şifre tekrarı
        ], [
            'oldPassword.required' => 'Eski şifre alanı zorunludur.',
            'oldPassword.string' => 'Eski şifre alanı bir metin olmalıdır.',
            'newPassword.required' => 'Yeni şifre alanı zorunludur.',
            'newPassword.string' => 'Yeni şifre alanı bir metin olmalıdır.',
            'newPassword.min' => 'Yeni şifre en az 8 karakter olmalıdır.',
            'newPassword.regex' => 'Yeni şifre en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir.',
            'newPassword.different' => 'Yeni şifre, eski şifreden farklı olmalıdır.',
            'rePassword.required' => 'Yeni şifre tekrarı alanı zorunludur.',
            'rePassword.string' => 'Yeni şifre tekrarı alanı bir metin olmalıdır.',
            'rePassword.same' => 'Yeni şifre tekrarı, yeni şifre ile eşleşmelidir.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Eski şifreyi doğrula
        if (!Hash::check($request->oldPassword, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Eski şifre yanlış.',
            ], 422);
        }

        // Yeni şifreyi hashle ve güncelle
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Şifre başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }

    
    public function updateAdminID(Request $request)
    {
       

        // Kullanıcıyı bul
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $user = DB::table('users')->where('id',$request->userID)->get();
        }else{
            $user = auth()->user();
        }



        // Validasyon kuralları
        $validator = Validator::make($request->all(), [
            'adminID' => [
                'required',
                'string',
                'max:255',
                Rule::when($user->role === 'admin', 'unique:users,adminID,' . $user->id),
            ],
        ], [
            'adminID.required' => 'AdminID alanı zorunludur.',
            'adminID.string' => 'AdminID alanı bir metin olmalıdır.',
            'adminID.max' => 'AdminID alanı en fazla 255 karakter olabilir.',
            'adminID.unique' => 'Bu AdminID zaten kullanılıyor.',
        ]);

        // Validasyon hatalarını kontrol et
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        // Güncelleme işlemi
        $user->adminID = $request->adminID;
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'AdminID başarıyla güncellendi.',
            'data' => $user,
        ], 200);
    }


    public function userOfdayStore(Request $request) {
        // Gelen veriyi doğrulama
        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'all'])],
        ], [
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'status.required' => 'Durum zorunludur.',
            'status.in' => 'Geçersiz durum. Geçerli değerler: pending, approved, rejected, all.',
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
        ]);


        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
       
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        }    
        
    
    
        
            
        
        $query = DB::table('offdays')
            ->where('userID', $userID);
        if ($request->status !== 'all') {
            $query->where('status', $request->status);
        }
    
        
    
        // Tarih filtrelemesi
        $startDate = null;
        switch ($request->filterType) {
            case 'weekly':
                $startDate = now()->subWeek();
                break;
            case 'monthly':
                $startDate = now()->subMonth();
                break;
            case 'yearly':
                $startDate = now()->subYear();
                break;
            case 'all':
                // Tüm izinler
                break;
        }
    
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
    
        // Verileri al
        $offdays = $query->paginate($request->countOfPaginate);
    
        return response()->json(['success'=>true,'message'=>'veriler basari ile getirildi','offdays'=>$offdays]);
    }

    
    public function userSpecialTaskStore(Request $request) {
        // Validasyon
        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected','all'])],
        ], [
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'status.required' => 'Durum zorunludur.',
            'status.in' => 'Geçersiz durum. Geçerli değerler: pending, approved, rejected, all.',
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }
        
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        } 

       
            // Specialtasks için filtreleri al
        $query = DB::table('specialtasks')
            ->where('userID', $userID);

        // Durum filtresi
        if ($request->status !== 'all') {
            $query->where('status', $request->status);
        }

        // Tarih filtresi
        $startDate = null;
        switch ($request->filterType) {
            case 'weekly':
                $startDate = Carbon::now()->subWeek();
                break;
            case 'monthly':
                $startDate = Carbon::now()->subMonth();
                break;
            case 'yearly':
                $startDate = Carbon::now()->subYear();
                break;
            case 'all':
            default:
                $startDate = null; // Tüm kayıtları getir
                break;
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        // Sonuçları getir
        $specialtasks = $query->paginate($request->countOfPaginate);

        // JSON yanıt döndür
        return response()->json([
            'success' => 'true',
            'message' => 'Specialtask verileri başarıyla getirildi.',
            'data' => $specialtasks
        ]);
        
    }
   
    public function callenderStore(Request $request)
    {

        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else{
            $adminID = auth()->user()->adminID;
        } 
        
        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filter' => ['nullable', 'string', 'in:new,weekly,monthly,yearly,all'],
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'filter.in' => 'Invalid filter parameter. Valid values: weekly, monthly, yearly, all.',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        // Get the filter parameter (weekly, monthly, yearly, all)
         // Default value: 'all'

        
        $query = DB::table('pastcallenders')
            ->where('adminID', $adminID);

        // Define the date range based on the filter
        switch ($request->filter) {

            case 'new':
                // En yeni kaydı al
                $query->orderBy('created_at', 'desc')->limit(1);
                break;

            case 'weekly':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(), // Start of the week
                    Carbon::now()->endOfWeek()    // End of the week
                ]);
                break;

            case 'monthly':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfMonth(), // Start of the month
                    Carbon::now()->endOfMonth()    // End of the month
                ]);
                break;

            case 'yearly':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfYear(), // Start of the year
                    Carbon::now()->endOfYear()    // End of the year
                ]);
                break;

            
        }

        // Fetch the data
        $pastCallenders = $query->paginate($request->countOfPaginate);

        return response()->json([
            'success' => 'true',
            'message' => 'Takvimler getirildi',
            'data' => $pastCallenders,
        ], 200);
    }


    public function countOfOffDays()
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        } 

        $offdays = DB::table('offdays')
            ->where('userID', $userID)
            ->where('status', 'approved')
            ->whereBetween('created_at', [
                Carbon::now()->startOfYear(),
                Carbon::now()->endOfYear()
            ])
            ->get(); // toArray() KALDIRILDI!

        $countOffOffDays = 0;

        foreach ($offdays as $offday) {
            $startDate = Carbon::parse($offday->offdayStart);
            $endDate = Carbon::parse($offday->offdayEnd);

            $difference = $startDate->diffInDays($endDate) + 1; // BAŞLANGIÇ GÜNÜ DAHİL EDİLDİ!

            $countOffOffDays += $difference;
        }

        return response()->json([
            'success' => 'true',
            'message'=>'bilgiler basariyla getirildi',
            'total_off_days' => $countOffOffDays
        ],201);



    }

    public function userData(Request $request){

        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'username'=> 'required'
            ], [
                'username.required' => 'username Degeri zorunludur ',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $username = $request->username;
            $user= DB::table('users')
            ->where('username',$username)
            ->get();
        }else{
            $user = auth()->user();
        } 
        

        return response()->json(['success'=>'true','message'=>'bilgiler basariyla getirildi','data'=>['userData'=>$user]],201);
    }
    
    public function deleteOffDays(Request $request)
    {

        if(auth()->user()->role === 'creator')
            {
                $validator = Validator::make($request->all(), [
                    'userID'=> 'required'
                ], [
                    'userID.required' => 'adminID degeri zorunludur',
                    
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                    return response()->json([
                        'message' => $errors,
                        'success' => false,
                    ], 422);
                }
                $userID = $request->userID;
            }else{
                $userID = auth()->user()->id;
            }

        $pendingOffDay = DB::table('offdays')
        ->where('userID',$userID)
        ->where('status','pending')
        ->get();
        if ($pendingOffDay->count() === 0) {
            return response()->json(['succes' => 'false', 'message' => 'bekleyen izniniz bulunamadi'],);
        }

        $pendingOffDay = DB::table('offdays')
        ->where('userID', $userID)
        ->where('status', 'pending')
        ->delete();
        return response()->json(['succes' => 'true', 'message' => 'izin basariyla silindi'],201);
    }


    public function deleteSpecialTask(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        }

        

        DB::table('specialtasks')
        ->where('userID',$userID)
        ->where('status','pending')
        ->delete();

        return response()->json(['success'=>'true','message'=>'special task basariyla silindi'],201);

    }


    public function userTotalShiftCounts(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required'
            ], [
                'userID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
        }else{
            $userID = auth()->user()->id;
        } 

        $validator = Validator::make($request->all(), [
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
        ], [
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        $filterType = $request->input('filterType');

        switch ($filterType) {
            case 'weekly':
                $subAmount = 7; // Örneğin, haftalık için 7 gün
                break;
            case 'monthly':
                $subAmount = 30; // Örneğin, aylık için 30 gün
                break;
            case 'yearly':
                $subAmount = 365; // Örneğin, yıllık için 365 gün
                break;
            case 'all':
                $subAmount = 100000; // Tüm veriler için sub miktarı gerekmez
                break;
            default:
                $subAmount = 0; // Varsayılan değer
                break;
        }

        $user = DB::table('users')
        ->where('id',$userID)
        ->get();

        //return $user;
        

        $userShiftCounts =DB::table('usertotalshiftcounts')
        ->where('userID',$userID)
        ->where('created_at', '>=', now()->subDays($subAmount))
        ->get();

     
        $ada  = 0;
        foreach($userShiftCounts as $UTSC)
        {
            
                $ada += $UTSC->totalShiftCount; 
            
        }

        $data = [
            'username' =>$user->pluck('username'),
            'totalNobetCounts' =>$ada,
        ];
        return  response()->json(['success'=>'true','message'=>'kullanicinin toplam nobetsayisi getirildi','data'=>$data]);


    }


    public function takeWhichShift(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required',
                'adminID' =>'requored',
            ], [
                'userID.required' => 'userID degeri zorunludur',
                'adminID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
            $adminID = $request->adminID;
        }else{
            $userID = auth()->user()->id;
            $adminID = auth()->user()->adminID;
        } 

        $validator = Validator::make($request->all(), [
            'whichShift'=> 'required|integer|min:1|max:5',
            'Reason' => 'required|string'
        ], [
            'whichShift.required' => 'userID degeri zorunludur',
            'whichShift.integer' => 'userID degeri zorunludur',
            'whichShift.min' => 'minumum 1 olabilir',
            'whichShift.max' => 'maksimim 5 olabilir',
            'Reason.required' => 'reason zorunludur',
            'Reason.string' => 'reason string olmali',
            
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }


        Userinventorieswish::create([
            'adminID'=>$adminID,
            'userID'=>$userID,
            'whichShift'=>$request->whichShift,
            'Reason'=>$request->Reason,
            "ResponseReason"=>'null',
            'status' => 'pending'
        ]);

        return response()->json(['success'=>true,'message'=>'islem basari ile tamamlandi'],200);

    }

    public function UserinventorieswishStore(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required',
                'adminID' =>'requored',
            ], [
                'userID.required' => 'userID degeri zorunludur',
                'adminID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
            $adminID = $request->adminID;
        }else{
            $userID = auth()->user()->id;
            $adminID = auth()->user()->adminID;
        } 

        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'status'=>[Rule::in('approved','rejected','pending'),'nullable'],
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'status.in' => 'status degeri approved yada rejected olmalidir',
            'status.required' => 'status degeri zorunludur',
            
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        $query = DB::table('userinventorieswishes')
        ->where('adminID',$adminID)
        ->where('userID',$userID);
        
        if($request->status)
        {
            $query = $query->where('status',$request->status);
            

        }
        if ($request->filterType !== 'all') {
            $dateMap = [
                'weekly'  => now()->subWeek(),
                'monthly' => now()->subMonth(),
                'yearly'  => now()->subYear(),
            ];
            $query->where('userinventorieswishes.created_at', '>=', $dateMap[$request->filterType] ?? now());
        }
        $data = $query->paginate($request->countOfPaginate);

        return response()->json(['success'=>true,'message'=>'veriler basari ile getirildi','data'=>$data],200);

    }

    public function userDeleteWishList(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'userID'=> 'required',
                'adminID' =>'requored',
            ], [
                'userID.required' => 'userID degeri zorunludur',
                'adminID.required' => 'adminID degeri zorunludur',
                
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $userID = $request->userID;
            $adminID = $request->adminID;
        }else{
            $userID = auth()->user()->id;
            $adminID = auth()->user()->adminID;
        } 

        DB::table('userinventorieswishes')
        ->where('userID',$userID)
        ->where('status','pending')
        ->delete();

        
        return response()->json(['success'=>true,'message'=>'islem basari ile tamamlandi'],200);

    }

   





}
