<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Departmans;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Offdays;
use App\Models\Specialtask;
use App\Models\Pastcallender;
use App\Models\Userinventories;
use App\Models\Userinventorieswish;
use Illuminate\Support\Str;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Models\Usersdepartman;
use App\Models\Departmansoffdays;
use App\Models\Specialdepartman;
use App\Models\Usertotalshiftcount;
use App\Models\callenderusersshiftcounts;
use Illuminate\Pagination\Paginator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CallenderExport;


class AdminOprerationController extends Controller
{
    
    
    public function createDepartman(Request $request) 
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        // Doğrulama kuralları
        $validator = Validator::make($request->all(), [
        'departmanName' => ['required', 'string', 'max:255'],
        'priority' => ['required', 'string', Rule::in(['low', 'medium','urgent'])],
        'AuserCountsShift1' => 'required|integer',
        'AuserCountsShift2' => 'required|integer',
        'AuserCountsShift3' => 'required|integer',
        'AuserCountsShift4' => 'required|integer',
        'AuserCountsShift5' => 'required|integer',
        'BuserCountsShift1' => 'required|integer',
        'BuserCountsShift2' => 'required|integer',
        'BuserCountsShift3' => 'required|integer',
        'BuserCountsShift4' => 'required|integer',
        'BuserCountsShift5' => 'required|integer',
        'CuserCountsShift1' => 'required|integer',
        'CuserCountsShift2' => 'required|integer',
        'CuserCountsShift3' => 'required|integer',
        'CuserCountsShift4' => 'required|integer',
        'CuserCountsShift5' => 'required|integer',
        'DuserCountsShift1' => 'required|integer',
        'DuserCountsShift2' => 'required|integer',
        'DuserCountsShift3' => 'required|integer',
        'DuserCountsShift4' => 'required|integer',
        'DuserCountsShift5' => 'required|integer',
        'EuserCountsShift1' => 'required|integer',
        'EuserCountsShift2' => 'required|integer',
        'EuserCountsShift3' => 'required|integer',
        'EuserCountsShift4' => 'required|integer',
        'EuserCountsShift5' => 'required|integer',
        'FuserCountsShift1' => 'required|integer',
        'FuserCountsShift2' => 'required|integer',
        'FuserCountsShift3' => 'required|integer',
        'FuserCountsShift4' => 'required|integer',
        'FuserCountsShift5' => 'required|integer',
        'GuserCountsShift1' => 'required|integer',
        'GuserCountsShift2' => 'required|integer',
        'GuserCountsShift3' => 'required|integer',
        'GuserCountsShift4' => 'required|integer',
        'GuserCountsShift5' => 'required|integer',
        'RgbNumber' => 'required|string'

        ], [
        'departmanName.required' => 'Departman adı alanı zorunludur.',
        'departmanName.string' => 'Departman adı geçerli bir metin olmalıdır.',
        'departmanName.max' => 'Departman adı en fazla 255 karakter uzunluğunda olmalıdır.',
        'priority.required' => 'Öncelik alanı zorunludur.',
        'priority.string' => 'Öncelik geçerli bir metin olmalıdır.',
        'priority.in' => 'Öncelik alanı sadece şu seçeneklerden biri olabilir: low, medium, high, critical, urgent.',
        'AuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'AuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'AuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'AuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'AuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'AuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'AuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'AuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'AuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'AuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'BuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'BuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'BuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'BuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'BuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'BuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'BuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'BuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'BuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'BuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'CuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'CuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'CuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'CuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'CuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'CuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'CuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'CuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'CuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'CuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'DuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'DuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'DuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'DuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'DuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'DuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'DuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'DuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'DuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'DuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'EuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'EuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'EuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'EuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'EuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'EuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'EuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'EuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'EuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'EuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'FuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'FuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'FuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'FuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'FuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'FuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'FuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'FuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'FuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'FuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'GuserCountsShift1.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'GuserCountsShift1.integer' => 'sayisal bir deger girmeniz gerekir',
        'GuserCountsShift2.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'GuserCountsShift2.integer' => 'sayisal bir deger girmeniz gerekir',
        'GuserCountsShift3.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'GuserCountsShift3.integer' => 'sayisal bir deger girmeniz gerekir',
        'GuserCountsShift4.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'GuserCountsShift4.integer' => 'sayisal bir deger girmeniz gerekir',
        'GuserCountsShift5.required'=> 'departmanda kac kisinin calisacagini belirtmeniz gerekir',
        'GuserCountsShift5.integer' => 'sayisal bir deger girmeniz gerekir',
        'userCounts.RgbNumber'=>'renk kodu girilmelidir',
        'RgbNumber.string' => 'Rgb degeri string olmali',
        'RgbNumber.required' => 'Rgb degeri zorunludur',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }


        $departmanCounts = DB::table('departmans')->where('adminID',$adminID)->count();
        
        $typeOfAdmin = DB::table('admininventories')->where('adminID',$adminID)->value('typeofadmin');
        return $typeOfAdmin;
        switch ($typeOfAdmin) {
            case 0:
                // Süper admin için özel işlemler
                if($departmanCounts>=6)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum departman sayısına ulaştınız.'],404);
                }
                break;
        
            case 1:
                if($departmanCounts>=12)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum departman sayısına ulaştınız.'],404);
                }
                break;
        
            case 2:
                if($departmanCounts>=24)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum departman sayısına ulaştınız.'],404);
                }
                break;
            
            case 3:
                if($departmanCounts>=1000000)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum departman sayısına ulaştınız.'],404);
                }
                break;
            
            default:
                
                return response()->json(['success'=>false,'message'=>'hata gecersiz deger'],404);

            
        
        }
        
    
        // Departman kontrolü
        $departman = departmans::where('departmanName', $request->departmanName)->first();
        if ($departman) {
            return response()->json([
                'message' => 'Aynı isme sahip bir departman bulunmakta.',
                'success' => false
            ], 400);
        }
    
        try {
            // Departman oluşturma
            $departman = departmans::create([
                'adminID' => $adminID,
                'departmanName' => $request->departmanName,
                'priority' => $request->priority,
                'AuserCountsShift1' => $request->AuserCountsShift1,
                'AuserCountsShift2' => $request->AuserCountsShift2,
                'AuserCountsShift3' => $request->AuserCountsShift3,
                'AuserCountsShift4' => $request->AuserCountsShift4,
                'AuserCountsShift5' => $request->AuserCountsShift5,
                'BuserCountsShift1' => $request->BuserCountsShift1,
                'BuserCountsShift2' => $request->BuserCountsShift2,
                'BuserCountsShift3' => $request->BuserCountsShift3,
                'BuserCountsShift4' => $request->BuserCountsShift4,
                'BuserCountsShift5' => $request->BuserCountsShift5,
                'CuserCountsShift1' => $request->CuserCountsShift1,
                'CuserCountsShift2' => $request->CuserCountsShift2,
                'CuserCountsShift3' => $request->CuserCountsShift3,
                'CuserCountsShift4' => $request->CuserCountsShift4,
                'CuserCountsShift5' => $request->CuserCountsShift5,
                'DuserCountsShift1' => $request->DuserCountsShift1,
                'DuserCountsShift2' => $request->DuserCountsShift2,
                'DuserCountsShift3' => $request->DuserCountsShift3,
                'DuserCountsShift4' => $request->DuserCountsShift4,
                'DuserCountsShift5' => $request->DuserCountsShift5,
                'EuserCountsShift1' => $request->EuserCountsShift1,
                'EuserCountsShift2' => $request->EuserCountsShift2,
                'EuserCountsShift3' => $request->EuserCountsShift3,
                'EuserCountsShift4' => $request->EuserCountsShift4,
                'EuserCountsShift5' => $request->EuserCountsShift5,
                'FuserCountsShift1' => $request->FuserCountsShift1,
                'FuserCountsShift2' => $request->FuserCountsShift2,
                'FuserCountsShift3' => $request->FuserCountsShift3,
                'FuserCountsShift4' => $request->FuserCountsShift4,
                'FuserCountsShift5' => $request->FuserCountsShift5,
                'GuserCountsShift1' => $request->GuserCountsShift1,
                'GuserCountsShift2' => $request->GuserCountsShift2,
                'GuserCountsShift3' => $request->GuserCountsShift3,
                'GuserCountsShift4' => $request->GuserCountsShift4,
                'GuserCountsShift5' => $request->GuserCountsShift5,
                'RgbNumber' =>$request->RgbNumber,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Departman veritabanına kaydedilirken hata oluştu.',
                'details' => $e->getMessage(),
            ], 500);
        }
    
        return response()->json([
            'message' => 'Departman başarıyla oluşturuldu.',
            'success' => true
        ], 201);
    }

    public function addDepartmanOffday(Request $request){

        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = validator::make([
            'departmanID' => 'required|uuid',
        
            'offdayStart' => 'required|date',
            'offdayEnd' => 'required|date|after_or_equal:offdayStart',
        ], [
            // Özelleştirilmiş hata mesajları
            'departmanID.required' => 'Departman ID alanı boş bırakılamaz.',
            'departmanID.uuid' => 'Departman ID geçerli bir UUID formatında olmalıdır.',
        
            'offdayStart.required' => 'İzin başlangıç tarihi alanı boş bırakılamaz.',
            'offdayStart.date' => 'İzin başlangıç tarihi geçerli bir tarih formatında olmalıdır.',
        
            'offdayEnd.required' => 'İzin bitiş tarihi alanı boş bırakılamaz.',
            'offdayEnd.date' => 'İzin bitiş tarihi geçerli bir tarih formatında olmalıdır.',
            'offdayEnd.after_or_equal' => 'İzin bitiş tarihi, izin başlangıç tarihinden sonra veya aynı tarihte olmalıdır.',
        ]);
        
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $sameOffDay = DB::table('departmansoffdays')
        ->where('departmanID',$request->departmanID)
        ->where('offdayEnd',$request->offdayEnd)
        ->where('offdayStart',$request->offdayStart)
        ->exists();

        if($sameOffDay)
        {
            return response()->json(['success'=>'false','message'=>'bu departman icin bu tarihler arasinda izin alinmis'],400);
        }
       

        $startDate = Carbon::parse($request->offdayStart)->format('Y-m-d');
        $endDate = Carbon::parse($request->offdayEnd)->format('Y-m-d');
        
        $offdays = Departmansoffdays::create([
            'adminID'=>$adminID,
            'departmanID' => $request->departmanID,
            'offdayStart' => $startDate,
            'offdayEnd' => $endDate,
            'offDayofweek' => null,
            
        ]);

        return response()->json(['success'=>'true','message'=>'islem basari ile tamamlandi'],200);

    }

    public function addOffdayOfWeek(Request $request){


        $adminID = auth()->user()->adminID;
        $validator = validator::make([
            'departmanID' => 'required|uuid',
            'offDayofweek' => [
                'required',
                Rule::in(['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday']),
            ],
           ], [
            // Özelleştirilmiş hata mesajları
            'departmanID.required' => 'Departman ID alanı boş bırakılamaz.',
            'departmanID.uuid' => 'Departman ID geçerli bir UUID formatında olmalıdır.',
        
            'offDayofweek.required' => 'Haftanın günü alanı boş bırakılamaz.',
            'offDayofweek.in' => 'Geçerli bir hafta günü seçin (Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday).',
        
           ]);

        
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        
        $sameOffDay = DB::table('departmansoffdays')
        ->where('departmanID',$request->departmanID)
        ->where('offDayofweek',$request->offDayofweek)
        ->exists();

        if($sameOffDay)
        {
            return response()->json(['succsess'=>'false','message'=>'bu departman icin bu gun zaten izinli'],400);
        }

        $offdays = Departmansoffdays::create([
        'adminID'=>$adminID,
         'departmanID' => $request->departmanID,
         'offdayStart' => null,
         'offdayEnd' => null,
         'offDayofweek' => $request->offDayofweek,
            
        ]);

        return response()->json(['success'=>true,'message'=>'islem basari ile tamamlandi'],200);

    }
    
    
    public function userStore(Request $request)
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
            'countOfPaginate'=> 'required|integer'
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
        ]);
       if ($validator->fails()) {
           $errors = $validator->errors()->first(); 
           return response()->json([
               'message' => $errors,
               'success' => false,
           ], 422);
       }

       $users = DB::table('users')
       ->where('adminID', $adminID)
       ->paginate(1000); // Kullanıcıları al
    
    
       return response()->json([
           'message' => 'Bütün kullanıcılar getirildi',
           'success' => true,
           'users' => $users
       ], 200);

        
    }

    
    public function departmanStore(Request $request){

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
            'countOfPaginate'=> 'required|integer'
        ], [
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

        
        $departmans = Departmans::where('adminID', $adminID)
            ->paginate($request->countOfPaginate);
        
        return response()->json(['success'=>true,'message'=> 'departmanlar basari ile getirildi','data'=>$departmans],200);
        
    }

    
    public function responseOffDays(Request $request) {
        try {
           
            if(auth()->user()->role === 'creator')
            {
                $validator = Validator::make($request->all(), [
                    'adminID'=> 'required'
                ], [
                    'adminID.required' => 'admin ID degeri zorunludur',

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
    
           
            $userIDs = DB::table('users')
                ->where('adminID', $adminID)
                ->pluck('id') 
                ->toArray();
    
            if (empty($userIDs)) {
                return response()->json([
                    'message' => 'Admin altındaki kullanıcılar bulunamadı.',
                    'success' => false
                ], 404); 
            }
    
     
            $offdays = DB::table('offdays')
                ->whereIn('userID', $userIDs)
                ->where('status', 'pending') 
                ->get(); 
    
            if ($offdays->isEmpty()) {
                return response()->json([
                    'message' => 'Pending statüsünde izin bulunamadı.',
                    'success' => false
                ], 404); 
            }
        } catch (\Exception $e) {
            
            return response()->json([
                'message' => 'Bir hata oluştu.',
                'success' => false,
                'error' => $e->getMessage()
            ], 500); 
        }
    
        $validator = validator::make([
            'response' => ['required', 'string', Rule::in(['approved', 'rejected'])],
            'responseOfDayReason',
            'OffDayID' => ['required'],
        ], [
            'OffDayID.required' => 'OffDayID alanı zorunludur.',
            
            'response.required' => 'Cevap alanı zorunludur.',
            'response.string' => 'Cevap metin formatında olmalıdır.',
            'response.in' => 'Cevap yalnızca şu değerlerden biri olabilir: approved, rejected.',
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }
    
        try {
           
            $offdaysUpdate = DB::table('offdays')
                ->where('id', $request->OffDayID)
                ->where('status', 'pending')
                ->update(['status' => $request->response,'ResponseOffdayReason'=>$request->responseOfDayReason]); 
    
            
            if ($request->response == 'approved') {
                return response()->json([
                    'message' => 'İzin başarıyla onaylandı.',
                    'success' => true
                ], 201);
            } else if ($request->response == 'rejected') {
                return response()->json([
                    'message' => 'İzin reddedildi.',
                    'success' => true
                ], 201);
            }
    
        } catch (JWTException $e) {
            return response()->json([
                'message' => 'İzin günü cevaplanırken bir hata oluştu',
                'success' => false
            ], 500);
        }
    }  
    

    
    public function offdayStore(Request $request) {
        
        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'all'])],
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'status.required' => 'Durum zorunludur.',
            'status.in' => 'Geçersiz durum. Geçerli değerler: pending, approved, rejected.',
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
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
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
    
        try {
            // Kullanıcılara ait izinleri çekmek için ana sorguyu oluştur
            $query = DB::table('offdays')
            ->where('adminID',$adminID);
    
            // Durum filtresi (Eğer "all" seçili değilse)
            if ($request->status !== "all") {
                $query->where('offdays.status', $request->status);
            }
    
            // Tarih filtresi
            if ($request->filterType !== 'all') {
                $dateMap = [
                    'weekly'  => now()->subWeek(),
                    'monthly' => now()->subMonth(),
                    'yearly'  => now()->subYear(),
                ];
                $query->where('offdays.created_at', '>=', $dateMap[$request->filterType] ?? now());
            }
    
            // Verileri çek
            $offdays = $query->paginate($request->countOfPaginate);
    
            return response()->json(['success' => true,'message'=>'veriler basari ile getirildi', 'data' => $offdays]);
    
        } catch (\Exception $e) {
            return response()->json(['message' => 'İzin verileri getirilirken hata oluştu', 'success' => false, 'error' => $e->getMessage()], 500);
        }
    
        
    
        // Verileri al
        $offdays = $query->get();
    
        return response()->json($offdays);
    }
    

    
    public function responseSpecialTask(Request $request){
            if(auth()->user()->role === 'creator')
            {
                $validator = Validator::make($request->all(), [
                    'adminID'=> 'required'
                ], [
                    'adminID.required' => 'admin ID degeri zorunludur',
                ]);
                if ($validator->fails()) {
                    $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                    return response()->json([
                        'message' => $errors,
                        'success' => false,
                    ], 422);
                }
                $adminID = $request->adminID;
            }else
            {
                $adminID = auth()->user()->adminID;
            }
        
            $userIDs = DB::table('users')
            ->where('adminID', $adminID) // AdminID'ye göre filtreleme
            ->pluck('id') // Kullanıcı ID'lerini alıyoruz
            ->toArray();
    
            if (empty($userIDs)) {
                return response()->json([
                    'message' => 'Admin altındaki kullanıcılar bulunamadı.',
                    'success' => false
                ], 404); // Kullanıcı bulunamazsa hata mesajı döndürülür
            }
        
        
        

        $validator = Validator::make($request->all(), [
            'specialTaskID' => ['required'], 
            'response' => ['required', 'string', Rule::in(['approved', 'rejected'])],
            'ResponsSpecialtaskReason',
        ], [
            'specialTaskID.required' => 'Kullanıcı ID alanı zorunludur.',
            
            'response.required' => 'Cevap alanı zorunludur.',
            'response.string' => 'Cevap metin formatında olmalıdır.',
            'response.in' => 'Cevap yalnızca şu değerlerden biri olabilir: approved, rejected.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }

        
        try{
            
            $specialTaskUpdate= DB::table('specialTasks')
            ->where('id',$request->specialTaskID)
            ->update(['status'=>$request->response,'ResponsSpecialtaskReason'=>$request->ResponsSpecialtaskReason]);

        }catch (JWTException $e) {
            return response()->json(['message' => 'special TASK cevaplanirken bir hata oluştu','succes'=>'false'], 500);
        }


        if ($request->response == 'approved') {
            return response()->json([
                'message' => 'İzin başarıyla onaylandı.',
                'success' => true
            ], 201);
        } else if ($request->response == 'rejected') {
            return response()->json([
                'message' => 'İzin reddedildi.',
                'success' => true
            ], 201);
        }




    }
    

    public function specialtaskStore(Request $request) {
        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'all'])],
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'status.required' => 'Durum zorunludur.',
            'status.in' => 'Geçersiz durum. Geçerli değerler: pending, approved, rejected.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
    
        try {
            if(auth()->user()->role === 'creator')
            {
                $validator = Validator::make($request->all(), [
                    'adminID'=> 'required'
                ], [
                    'adminID.required' => 'admin ID degeri zorunludur',

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
    
            
            
            $query = DB::table('specialtasks')
                ->where('adminID',$adminID)
                ->where('status', $request->status);
    
        } catch (JWTException $e) {
            return response()->json(['message' => 'Specialtask verileri getirilirken hata oluştu', 'success' => false], 500);
        }
    
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
            default:
                $startDate = null;
                break;
        }
    
        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }
    
        // Sonuçları getir ve username, firstname, lastname ile birlikte döndür
        $specialtasks = $query->paginate($request->countOfPaginate);
    
        return response()->json(['success'=>true,'message'=>'veriler basariyla degistirildi','data'=>$specialtasks]);
    }
    

    public function createCallendar(Request $request)
    {

        


        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        $countOfCallender = DB::table('admininventories')
            ->where('adminID', $adminID)
            ->value('numberofcalendars');

        if ($countOfCallender <= 0)
        {
            return response()->json(['success' => 'false', 'message' => 'Takvim sayınız bitmiştir, maalesef yeni takvim oluşturamıyoruz'],400);
            
        }
        $countOfCallender = DB::table('admininventories')
            ->where('adminID', $adminID)
            ->value('numberofcalendars');

        if($countOfCallender <=0)
        {
            return response()->json(['success' => 'false', 'message' => 'Takvim sayınız bitmiştir, maalesef yeni takvim oluşturamıyoruz'],400);
        }

        $users = DB::table('users')
        ->where('adminID',$adminID)
        ->get();
        $usersIDs =  $users->pluck('id');


        $departmans = DB::table('departmans')
        ->where('adminID',$adminID)
        ->orderByRaw("FIELD(priority, 'urgent', 'medium', 'low')")
        ->get();
        $departmanIDs = $departmans->pluck('id');
        
        $typeOfAdmin= DB::table('admininventories')->where('adminID',$adminID)->value('typeofadmin');

        switch ($typeOfAdmin) {
            case 0:
                // Süper admin için özel işlemler
                $shiftCount = 1;
                break;
        
            case 1:
                
                $shiftCount = 3;
                break;
        
            case 2:
                $shiftCount = 5;
                break;

            case 3:

                $shiftCount = 5;
                break;
            default:
                return response()->json(['success'=>'false','message'=>'hatali veri girisi'],404);
        }
        
       
        $validator = Validator::make($request->all(), [
            'condution' => ['required', 'integer', 'min:0', 'max:5'],
            'startDate' => ['required', 'date', 'date_format:Y-m-d', 'before_or_equal:endDate','after_or_equal:today'],
            'endDate' => ['required', 'date', 'date_format:Y-m-d', 'after_or_equal:startDate'],
            'activeusers' => ['required', 'array',Rule::in($usersIDs)],
            'specialOffDays' => ['nullable', 'array'],
            'specialOffDays.*' => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:startDate', 'before_or_equal:endDate'],
            'workdays' => ['required', 'array'],
            'workdays.*' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            "shiftCounts" =>['required', 'integer', "max:$shiftCount"],
            'departmans' => ['required', 'array'],
            'departmans.*' => ['required', Rule::in($departmanIDs)],
            'callenderName' => 'required',
        ], [
            'condution.required' => 'Durum alanı zorunludur.',
            'condution.integer' => 'Durum alanı bir tam sayı olmalıdır.',
            'condution.min' => 'Durum en az 0 olmalıdır.',
            'condution.max' => 'Durum en fazla 10 olmalıdır.',
            'startDate.required' => 'Başlangıç tarihi zorunludur.',
            'startDate.date' => 'Başlangıç tarihi geçerli bir tarih olmalıdır.',
            'startDate.date_format' => 'Başlangıç tarihi Y-m-d formatında olmalıdır.',
            'startDate.before_or_equal' => 'Başlangıç tarihi, bitiş tarihinden önce veya ona eşit olmalıdır.',
            'startDate.after_or_equal' => 'Başlangıç tarihi bugünün tarihi veya daha sonraki bir tarih olmalıdır.',
            'endDate.required' => 'Bitiş tarihi zorunludur.',
            'endDate.date' => 'Bitiş tarihi geçerli bir tarih olmalıdır.',
            'endDate.date_format' => 'Bitiş tarihi Y-m-d formatında olmalıdır.',
            'endDate.after_or_equal' => 'Bitiş tarihi, başlangıç tarihinden sonra veya ona eşit olmalıdır.',
            'activeusers.required' => 'Aktif kullanıcılar alanı zorunludur.',
            'activeusers.array' => 'Aktif kullanıcılar bir dizi olmalıdır.',
            'specialOffDays.array' => 'Özel izin günleri bir dizi olmalıdır.',
            'specialOffDays.*.date' => 'Özel izin günleri geçerli bir tarih olmalıdır.',
            'specialOffDays.*.date_format' => 'Özel izin günleri Y-m-d formatında olmalıdır.',
            'specialOffDays.*.after_or_equal' => 'Özel izin günleri başlangıç tarihinden sonra veya ona eşit olmalıdır.',
            'specialOffDays.*.before_or_equal' => 'Özel izin günleri bitiş tarihinden önce veya ona eşit olmalıdır.',
            'workdays.required' => 'Çalışma günleri zorunludur.',
            'workdays.array' => 'Çalışma günleri bir dizi olmalıdır.',
            'workdays.*.string' => 'Çalışma günleri metin olmalıdır.',
            'workdays.*.in' => 'Çalışma günleri yalnızca Monday, Tuesday, Wednesday, Thursday, Friday, Saturday, Sunday değerlerinden biri olabilir.',
            'departmans.required' => 'Lütfen en az bir departman seçiniz.',
            'departmans.array' => 'Departman bilgisi geçersiz. Lütfen doğru formatta gönderin.',
            'departmans.*.required' => 'Seçilen departmanlar arasında boş değerler olamaz.',
            'departmans.*.in' => 'Seçtiğiniz ":input" departmanı sistemde kayıtlı değil.',
            'callenderName.required' => 'takvim ismi zorunludur',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        
        
        $condution = $request->condution;
        $startDate = Carbon::parse($request->startDate)->subDay();
        $endDate   = Carbon::parse($request->endDate)->subDay();
        
        $users = $users->whereIn('id',$request->activeusers)->values();

        $forUsers = $users->pluck('username','id')->toArray();
        
        $userIDs =  $users->pluck('id');
        
        $specialOffDays = $request->specialOffDays;
        

        $workDays =  $request->workdays;

        $shiftCount = $request->shiftCounts;
        
        $departmans = $departmans->whereIn('id',$request->departmans)->values();
      
        $departmanIDs = $departmans->pluck('id');
        $departmanName = $departmans->pluck('departmanName');
        

        $offDays = DB::table('offdays')
        ->whereIn('userID',$userIDs)
        ->where('status','approved')
        ->where('offdayEnd', '>', Carbon::parse($startDate))
        ->get();
       
        

        $specialTasks = DB::table('specialtasks')
        ->where('adminID',$adminID)
        ->where('status','approved')
        ->whereIn('userID',$userIDs)
        ->get();


        
        $departmanOffDays = DB::table('departmansoffdays')
        ->whereIn('departmanID',$departmanIDs)
        ->get();

        //return $departmanOffDays;
        
        $specialDepartMans = DB::table('specialdepartmen')
        ->where('adminID',$adminID)
        ->whereIn('departmanName',$departmanName)
        ->get();

        
        
        //return $specialDepartMans;
        $IDss = $specialDepartMans->pluck('userID')->toArray();
        $IDs = $specialTasks->pluck('userID')->toArray();

        $mergedList = array_merge($IDss, $IDs);
        $uniqueList = array_values(array_unique($mergedList));
        
        foreach ($uniqueList as $key) {
            if (array_key_exists($key, $forUsers)) {
                // Eşleşen elemanı listeden çıkar
                $value = $forUsers[$key];
                unset($forUsers[$key]);
        
                // Eşleşen elemanı en sona ekle
                $forUsers[$key] = $value;
            }
        }
        


        $nobetList = [] ;
        $userNobetCounts =  array_fill_keys(array_keys($forUsers), 0);
        
        while($startDate->lte($endDate)){
            $startDate->addDays();

            $currentDayOfWeek = Carbon::parse($startDate)->format('l');
            $currentDate = Carbon::parse($startDate)->format('Y-m-d');

            
            if((!in_array($currentDayOfWeek,$workDays)) || in_array($currentDate,$specialOffDays) ){
                continue;
            }
            
            
            $grouped = [];
            foreach ($departmans as $department) {
                $grouped[$department->priority][] = $department;
            }

            // 2. Her grubun içindeki elemanları rastgele karıştır
            foreach ($grouped as &$group) {
                shuffle($group);
            }
            foreach ($grouped as &$group) {
                shuffle($group);
            }
            foreach ($grouped as &$group) {
                shuffle($group);
            }
            $reversed = array_reverse($grouped);
            $half = ceil(count($grouped) / 2);

            $rightPart = array_splice($grouped, $half);
            $grouped = array_merge($rightPart, $grouped);

            $sortedDepartments = array_merge(
                $grouped['urgent'] ?? [],
                $grouped['medium'] ?? [],
                $grouped['low'] ?? []
            );
            

            $departmans  = collect($sortedDepartments)->flatten(1);
                        
            
            foreach($departmans as $departman)
            {
                $ada1 = $departmanOffDays->where('departmanID',$departman->id);
                foreach($ada1 as $DoffDays)
                {
                        if((Carbon::parse($currentDate)->between(Carbon::parse($DoffDays->offdayStart),Carbon::parse($DoffDays->offdayEnd)) || Carbon::parse($DoffDays->offDayofweek)->format('l') === ($currentDayOfWeek) ))
                        {
                            continue 2;
                        }
                }
                
                for ($SC=1;$SC<=$shiftCount;$SC++)
                {
                    if($currentDayOfWeek == 'Monday'){
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->AuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->AuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->AuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->AuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->AuserCountsShift5;
                                break;
                        }
                    }elseif ($currentDayOfWeek == 'Tuesday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->BuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->BuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->BuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->BuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->BuserCountsShift5;
                                break;
                        }                  
                    }elseif ($currentDayOfWeek == 'Wednesday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->CuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->CuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->CuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->CuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->CuserCountsShift5;
                                break;
                        }
                    }elseif ($currentDayOfWeek == 'Thursday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->DuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->DuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->DuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->DuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->DuserCountsShift5;
                                break;
                        }
                    }elseif ($currentDayOfWeek == 'Friday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->EuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->EuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->EuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->EuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->EuserCountsShift5;
                                break;
                        }
                    }elseif ($currentDayOfWeek == 'Saturday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->FuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->FuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->FuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->FuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->FuserCountsShift5;
                                break;
                        }
                    }elseif ($currentDayOfWeek == 'Sunday') {
                        switch ($SC) {
                            case 1:
                                $userCounts = $departman->GuserCountsShift1;
                                break;
                            case 2:
                                $userCounts = $departman->GuserCountsShift2;
                                break;
                            case 3:
                                $userCounts = $departman->GuserCountsShift3;
                                break;
                            case 4:
                                $userCounts = $departman->GuserCountsShift4;
                                break;
                            case 5:
                                $userCounts = $departman->GuserCountsShift5;
                                break;
                        }
                    }
                    
                    for ($i=1;$i<=$userCounts;$i++)
                    {
                        $ada1 = $specialTasks->where('departmanName',$departman->departmanName)->where('shiftDay',$currentDayOfWeek)->where('whichShift',$SC);
                        foreach($ada1 as $specialTask)
                        {
                            $ada2 = $offDays->where('userID',$specialTask->userID);
                            foreach($ada2 as $OffDay)
                            {
                                if(Carbon::parse($currentDate)->between(Carbon::parse($OffDay->offdayStart),Carbon::parse($OffDay->offdayEnd)))
                                {
                                    break 2;
                                    
                                }
                            }
                            $nobetList [] =[
                                'shiftDate'     => $currentDate,
                                'departman'     => $departman->departmanName,
                                'whichShift'    => $SC,
                                'username'      => $forUsers[$specialTask->userID],
                                'type'          =>'special',
                            ];
                            $userNobetCounts[$specialTask->userID]++;
                            continue 2;

                            
                        }
                        
                        $ada1  = $specialDepartMans;
                        
                        $reversed = $ada1->reverse();

                        
                        $half = ceil($reversed->count() / 2);
                        $rightPart = $reversed->slice($half);

                        
                        $grouped = $rightPart->merge($reversed->slice(0, $half));
                        
                        $ada1 = $grouped;
                        
                        //$ada1 = $ada1->where('departmanName',$departman->departmanName)->flatten();
                        asort($userNobetCounts);

                        
                        
                        // Sıralanmış atamalar
                        $ada1 = $this->siralaNobetAtamalari($userNobetCounts, $ada1);

                        $ada1 = collect($ada1)->where('departmanName',$departman->departmanName);
                        

                        foreach($ada1 as $SpecialDepartman)
                        {
                            $ada1 = $specialTasks->where('userID',$SpecialDepartman->userID)->where('shiftDay',$currentDayOfWeek)->where('whichShift',$SC);
                            
                            if($ada1->isNotEmpty())
                            {
                                continue;
                            }
                            


                            $ada3 = $offDays->where('userID',$SpecialDepartman->userID);
                            foreach($ada3 as $OffDay)
                            {
                                if(Carbon::parse($currentDate)->between(Carbon::parse($OffDay->offdayStart),Carbon::parse($OffDay->offdayEnd)))
                                {
                                    continue 2;
                                }
                            }
                            foreach(array_reverse($nobetList) as $nobet)
                            {
                                //return $forUsers[$SpecialDepartman->userID];
                                $diffDays = Carbon::parse($nobet['shiftDate'])->diffInDays(Carbon::parse($currentDate));
                                //return $diffDays;
                                if($nobet['username']=== $forUsers[$SpecialDepartman->userID] && (int)$diffDays<=$condution ) 
                                {
                                    continue  2;
                                }
                            }
                            $nobetList [] =[
                                'shiftDate'     => $currentDate,
                                'departman'     => $departman->departmanName,
                                'whichShift'    => $SC,
                                'username'      => $forUsers[$SpecialDepartman->userID],
                                'type'          =>'Halfspecial',
                            ];
                            $userNobetCounts[$SpecialDepartman->userID]++;
                            continue 2;

                            

                        }

                    
                    
                     
                        asort($userNobetCounts);
                        
                        //return $userNobetCounts;
                        foreach($userNobetCounts as $key => $value )
                        {
                            $ada4 = $specialTasks->where('userID',$key)->where('shiftDay',$currentDayOfWeek)->where('whichShift',$SC);

                            if($ada4->isNotEmpty())
                            {
                                continue;
                            }
                            
                            
                            $userSpecialDepartmans = $specialDepartMans->where('userID',$key);
                            $ada = count($userSpecialDepartmans);
                            
                            $ada1 = 0;
                            
                            foreach($userSpecialDepartmans as $SpecialDepartman)
                            {
                                foreach(array_reverse($nobetList) as $nobet)
                                { 
                                    if($nobet['departman'] == $SpecialDepartman->departmanName && $nobet['shiftDate'] == $currentDate)
                                    {
                                        $ada1++;
                                    }
                                }
                            }
                            
                            if($ada != $ada1)
                            {
                                continue;
                            }


                            $ada3 = $offDays->where('userID',$key);
                            foreach($ada3 as $OffDay)
                            {
                                if(Carbon::parse($currentDate)->between(Carbon::parse($OffDay->offdayStart),Carbon::parse($OffDay->offdayEnd)))
                                {
                                    continue 2;
                                }
                            }
                            


                            foreach(array_reverse($nobetList) as $nobet)
                            {
                                
                                $diffDays = Carbon::parse($nobet['shiftDate'])->diffInDays(Carbon::parse($currentDate));
                                
                                if($nobet['username']=== $forUsers[$key] && (int)$diffDays<=$condution ) 
                                {
                                   continue 2;
                                }

                            }
                            

                            $nobetList [] =[
                                'shiftDate'     => $currentDate,
                                'departman'     => $departman->departmanName,
                                'whichShift'    => $SC,
                                'username'      => $forUsers[$key],
                                'type'          =>'regular',
                            ];
                            $userNobetCounts[$key]++;
                            continue 2 ;


                        }
                        if($departman->priority == 'urgent')
                        {
                             return response()->json(['success'=>'false','message'=>'prioritysi urgent bolume elaman  atanamadigi icin takvim olusturulamamistir'],400);
                        }
                        $nobetList [] =[
                            'shiftDate'     => $currentDate,
                            'departman'     => $departman->departmanName,
                            'whichShift'    => $SC,
                            'username'      => null,
                            'type'          => 'notAsigned',
                        ];


                    }
                }
            }

            
        }

        $userNobetCountList = [];
        $countOfTotalShift = 0;
   
        foreach ($userNobetCounts as $key => $value) {
            $countOfTotalShift +=$value;
            $userNobetCountList[$forUsers[$key]] = $value;
        }
        $unAssignedShiftCount = count($nobetList)-$countOfTotalShift;
        $userNobetCountList['unAssigned'] = $unAssignedShiftCount;
        
        //return $unAssignedShiftCount;
        $countOfCallender = DB::table('admininventories')
            ->where('adminID', $adminID)
            ->value('numberofcalendars');

        if ($countOfCallender > 0) {
            DB::table('admininventories')
                ->where('adminID', $adminID)
                ->update([ 'numberofcalendars' => $countOfCallender - 1, ]);

            $callender = pastcallender::create([
                'adminID' => $adminID,
                'pastCallender' => json_encode($nobetList),
                'callenderName' =>$request->callenderName,
            ]);

            
            foreach($userNobetCounts as $key=>$value)
            {
               
                Usertotalshiftcount::create([
                    'callenderID' => $callender->id,
                    'adminID' => $adminID,
                    'userID' => $key,
                    'totalShiftCount' =>$value,
                ]);
            }

            $Callenderusersshiftcounts = Callenderusersshiftcounts::create([
                'callenderID' => $callender->id,
                'adminID' => $adminID,
                'userNobetCounts' => collect($userNobetCountList),
            ]);



            return response()->json([
                'success' => true,
                'message' => 'Takvim başarıyla oluşturuldu',
                'nobetList' => $nobetList,
                'userNobetCounts' => $userNobetCountList,
                'atanmayanNobetsayisi'=> $unAssignedShiftCount,
            ], 201);
        } else {
            return response()->json(['success' => 'false', 'message' => 'Takvim sayınız bitmiştir, maalesef yeni takvim oluşturamıyoruz'],400);
        }



    }


    public function assignOffDays(Request $request){

        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
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
            'userID' => 'required',
            'ResponseOffdayReason'=> 'required',
            
            ], [
                'startDate.required' => 'admin ID degeri zorunludur',
                'startDate.date_format' => 'Başlangıç tarihi formatı geçerli değil. Lütfen Y-m-d formatında bir tarih girin.',
                'startDate.after_or_equal' => 'Başlangıç tarihi bugünden önce olamaz.',
                'startDate.before_or_equal' => 'Başlangıç tarihi, bitiş tarihinden önce veya eşit olmalıdır.',
                'endDate.required' => 'Bitiş tarihi zorunludur ve boş bırakılamaz.',
                'endDate.date_format' => 'Bitiş tarihi formatı geçerli değil. Lütfen Y-m-d formatında bir tarih girin.',
                'endDate.after_or_equal' => 'Bitiş tarihi, başlangıç tarihine eşit veya sonra olmalıdır.',
                'ResponseOffdayReason.required'=>'izin gunu atama nedenini belirtmelisiniz'
            ]);

            
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            

            // userID'nin, adminID'si $adminID olan kullanıcılar arasında olup olmadığını kontrol et
            $userExists = DB::table('users')
            ->where('adminID', $adminID)
            ->where('id', $request->userID)
            ->exists();

            if (!$userExists){
                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı veya bu adminID\'ye ait değil.',
                ], 404);
            }

        

            // Tarih formatlama
            $startDate = Carbon::parse($request->startDate);
            $endDate = Carbon::parse($request->endDate);
            $userID = $request->userID;
            $threeMonthsAgo = Carbon::now()->subMonths(1);

            

            $sameOffDays = DB::table('offdays')
            ->where('userID',$userID)
            ->where('created_at', '>', $threeMonthsAgo)
            ->where('status','approved')
            ->get();
            
            foreach($sameOffDays as $sameOFF)
            {
                
                if($startDate->between(Carbon::parse($sameOFF->offdayStart),Carbon::parse($sameOFF->offdayEnd)) || $endDate->between(Carbon::parse($sameOFF->offdayStart),Carbon::parse($sameOFF->offdayEnd)))
                {
                    
                    return response()->json(['success'=>'false','message'=>'girdiginiz tarihlerde kullanici zaten izinli'],404);
                }
            }

          
            try{
            // Yeni offdays kaydı oluştur
                $offdays = offdays::create([
                    'adminID'=>$adminID,
                    'userID' => $request->userID,
                    'offdayStart' => $startDate,
                    'offdayEnd' => $endDate,
                    'offdayReason' => 'yonetici tarafindan atandi',
                    'ResponseOffdayReason'=>$request->ResponseOffdayReason,
                    'status' => 'approved',
                ]);
            } catch (JWTException $e) {
                return response()->json(['message' => 'izin gunu kaydedilirken hata oluştu',' success '=>'false'], 500);
            }
            return response()->json(['message' => 'İzin talebi başarıyla oluşturuldu.',' success '=>'true', 'offdays' => $offdays], 200 );

    


    }

    
    public function assingSpecialDepartman(Request $request){
       
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        
        $departmans = DB::table('departmans')
        ->where('adminID',$adminID)
        ->get();
        $departmanNames= $departmans->pluck('departmanName')
        ->toArray();

        
       
        $validator = Validator::make($request->all(), [
            'departmanNames' => ['required', 'array'],
            'departmanNames.*' => Rule::in($departmanNames),
            'userID' => 'required',    
            
        ], [
            'departmanNames.required' => 'Departman adı alanı zorunludur.',
            'departmanNames.array' => 'Departman adı bir dizi olmalıdır.',
            'departmanNames.*.in' => 'Seçilen departman adı geçerli değil. Sadece şu değerlerden biri olabilir: ' . implode(', ', $departmanNames),
            'userID.required' => 'Kullanıcı ID alanı zorunludur.',
        ]);
        

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }



        $userExists = DB::table('users')
        ->where('adminID', $adminID)
        ->where('id', $request->userID)
        ->exists();

        if (!$userExists){
            return response()->json([
                'success' => false,
                'message' => 'Kullanıcı bulunamadı veya bu adminID\'ye ait değil.',
            ], 404);
        }


        $departmanID = 0;
        foreach($request->departmanNames as $departman){

            $departmanID = $departmans->where('departmanName',$departman)->value('id');

            
            
            
            $samerecord = DB::table('specialdepartmen')
            ->where('userID',$request->userID)
            ->where('departmanName',$departman)
            ->exists();

            if($samerecord){
                continue;
            }
            
            $Specialdepartman = Specialdepartman::create([
                'departmanID'=>$departmanID,
                'userID'=>$request->userID,
                'departmanName'=> $departman,
                'departmanID' => $departmanID,
                'adminID'=>$adminID,
                
            ]);

        }

        return response()->json(['message' => 'kullanicinin calisabilecegi departmanlar basariyla kaydedildi','success'=>'true'], 200);

    }
    

    
    public function assingSpecialTask(Request $request){
       
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        

       $departmans= DB::table('departmans')
        ->where('adminID',$adminID)
        ->get();
        
        $departmanNames = $departmans->pluck('departmanName')->toArray();
        
        
        
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
       
        $validator = Validator::make($request->all(), 
        [
            'departmanName' => ['required', Rule::in($departmanNames)],
            'shiftDay' => ['required', Rule::in($days)],
            'whichShift' => ['required','string',
            Rule::in([
                'AuserCountsShift1', 'AuserCountsShift2', 'AuserCountsShift3', 'AuserCountsShift4', 'AuserCountsShift5',
                'BuserCountsShift1', 'BuserCountsShift2', 'BuserCountsShift3', 'BuserCountsShift4', 'BuserCountsShift5',
                'CuserCountsShift1', 'CuserCountsShift2', 'CuserCountsShift3', 'CuserCountsShift4', 'CuserCountsShift5',
                'DuserCountsShift1', 'DuserCountsShift2', 'DuserCountsShift3', 'DuserCountsShift4', 'DuserCountsShift5',
                'EuserCountsShift1', 'EuserCountsShift2', 'EuserCountsShift3', 'EuserCountsShift4', 'EuserCountsShift5',
                'FuserCountsShift1', 'FuserCountsShift2', 'FuserCountsShift3', 'FuserCountsShift4', 'FuserCountsShift5',
                'GuserCountsShift1', 'GuserCountsShift2', 'GuserCountsShift3', 'GuserCountsShift4', 'GuserCountsShift5',
            ])], 
            'SpecialtaskReason',
            'userID' => 'required',
            'ResponsSpecialtaskReason' => 'required',
        
        ], 
        [
            'departmanName.required' => 'Departman adı alanı zorunludur.',
            'departmanName.in' => 'Departman adı geçerli değil. Sadece şu değerlerden biri olabilir: ' . implode(', ', $departmanNames),
            'shiftDay.required' => 'Vardiya günü alanı zorunludur.',
            'shiftDay.in' => 'Vardiya günü geçerli değil. Sadece şu değerlerden biri olabilir: ' . implode(', ', $days),
            'whichShift.required' => 'Vardiya seçimi alanı zorunludur.',
            'whichShift.integer' => 'Vardiya seçimi bir tam sayı olmalıdır.',
            'whichShift.in' => 'yalnizca bu degerlerden biri olabilir AuserCountsShift1 AuserCountsShift2 
                AuserCountsShift3 AuserCountsShift4 AuserCountsShift5
                BuserCountsShift1 BuserCountsShift2 BuserCountsShift3 BuserCountsShift4 BuserCountsShift5
                CuserCountsShift1 CuserCountsShift2 CuserCountsShift3 CuserCountsShift4 CuserCountsShift5
                DuserCountsShift1 DuserCountsShift2 DuserCountsShift3 DuserCountsShift4 DuserCountsShift5
                EuserCountsShift1 EuserCountsShift2 EuserCountsShift3 EuserCountsShift4 EuserCountsShift5
                FuserCountsShift1 FuserCountsShift2 FuserCountsShift3 FuserCountsShift4 FuserCountsShift5
                GuserCountsShift1 GuserCountsShift2 GuserCountsShift3 GuserCountsShift4 GuserCountsShift5',
            'whichShift.min' => 'Vardiya seçimi en az 1 olmalıdır.',
            'ResponsSpecialtaskReason' => 'ozel atama sebebini girilmeli'
           
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        
        $theDayisFulls = DB::table('specialtasks')
        ->where('adminID',$adminID)
        ->where('departmanName', $request->departmanName)
        ->where('status','approved')
        ->where('shiftDay', $request->shiftDay)
        ->where('whichShift',$request->whichShift)
        ->get();
        
       
       if(!$theDayisFulls->isEmpty())
        {
            foreach($theDayisFulls as $theDayisFull)
            {
                if ( $theDayisFull->userID == $request->userID)
                {
                    return response()->json([
                        'message' => 'bu kullaniciyi zaten bu gun ,bu departmana ve bu vardiyaya atamissiniz',
                        'success' => false,
                        'data' => $theDayisFull,
                    ], 400);
                }
                
            }
        }  

        
        
        $controlDays = DB::table('specialtasks')
        ->where('userID',$request->userID)
        ->where('shiftDay',$request->shiftDay)
        ->get();

        if(!$controlDays->isEmpty()){
            return response()->json([
                'message' => 'atamaya calistiginiz kullanici o gun baska bir yere atanmis',
                'success' => 'false',
            ], 400);
        }

        if (count($theDayisFulls) >=$departmans->where('departmanName', $request->departmanName)->value($request->whichShift)) 
        {
            return response()->json([
                'message' => 'O departman ,O gün ve o vardiya icin bos yer yok.',
                'success' => false,
                'data' => $theDayisFulls,
            ], 400);
    
        }
        
        
        $departmanID = $departmans
        ->where('departmanName',$request->departmanName)
        ->value('id');

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

        

            
        $specialTask = Specialtask::create([
            'departmanID' => $departmanID,
            'userID'=>$request->userID,
            'adminID' =>$adminID,
            'departmanName'=> $request->departmanName,
            'whichShift' => $ada,
            'shiftDay' => $request->shiftDay,
            'ResponsSpecialtaskReason'=>$request->ResponsSpecialtaskReason,
            'SpecialtaskReason' => 'yonetici tarafdindan atandi',
            'status' =>'approved'
            
        ]);
        

        return response()->json(['message' => 'ozel is gunu basariyla kaydedildi','success'=>'true'], 200 );


    }

    
    public function deleteUser(Request $request){
        
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        
        $validator = Validator::make($request->all(), [
            
            'userID' => 'required',
            
            ], [
                'userID.required'=>'userID alani doldurulmak zorundadir' 
            ]);
        
            if(DB::table('users')->where('id',$request->userID)->value('role')== 'admin')
            {
                return response()->json(['success'=>false,'message'=>'yonetici hesabi silinemez'],404);
            }
            
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            

            // userID'nin, adminID'si $adminID olan kullanıcılar arasında olup olmadığını kontrol et
            $userExists = DB::table('users')
                            ->where('adminID', $adminID)
                            ->where('id', $request->userID)
                            ->exists();

            if (!$userExists){

                return response()->json([
                    'success' => false,
                    'message' => 'Kullanıcı bulunamadı veya bu adminID\'ye ait değil.',
                ], 404);
            }
            try{
            $userExists = DB::table('users')
                            ->where('adminID', $adminID)
                            ->where('id', $request->userID)
                            ->delete();
            }catch(Exception $e){

                return response()->json(['success'=>'false','message'=>'kullanici silinirken hata olustu'],500);
            }

            return response()->json(['success'=>'true','message'=>'kullanici basariyla silindi'],201);



    }

    
    public function countOfCallender(Request $request) {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',

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
        try {
            $countOfCallender = DB::table('admininventories')
                ->where('adminID', $adminID)
                ->value('numberofcalendars'); // Tek bir değer döndür
    
            return response()->json([
                'success' => true,
                'message' => 'Admin takvim sayısı getirildi',
                'countOfCallendars' => $countOfCallender
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Admin takvim sayısı getirilemedi, hata veritabanında'
            ], 500);
        }
    }

    
    public function assingUser(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'firstname' => 'nullable|string|max:255',
            'lastname' => 'nullable|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'gender' => 'nullable|in:male,female',
            'phoneNumber' => 'nullable|string|regex:/^\\+?[0-9]{10,15}$/',
            'email' => 'nullable|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'profilePhotoUrl' => 'nullable|url',
        ], [
            'firstname.string' => 'Ad sadece harflerden oluşmalıdır.',
            'firstname.max' => 'Ad en fazla 255 karakter olabilir.',
        
            'lastname.string' => 'Soyad sadece harflerden oluşmalıdır.',
            'lastname.max' => 'Soyad en fazla 255 karakter olabilir.',
        
            'username.required' => 'Kullanıcı adı zorunludur.',
            'username.string' => 'Kullanıcı adı sadece harflerden oluşmalıdır.',
            'username.max' => 'Kullanıcı adı en fazla 255 karakter olabilir.',
            'username.unique' => 'Bu kullanıcı adı zaten alınmış.',
        
            'gender.in' => 'Cinsiyet sadece "male" veya "female" olabilir.',
        
            'phoneNumber.string' => 'Telefon numarası geçerli bir formatta olmalıdır.',
            'phoneNumber.regex' => 'Telefon numarası 10 ila 15 rakam içermelidir ve isteğe bağlı olarak "+" ile başlayabilir.',
        
            'email.string' => 'E-posta adresi geçerli bir metin olmalıdır.',
            'email.email' => 'Geçerli bir e-posta adresi giriniz.',
            'email.max' => 'E-posta adresi en fazla 255 karakter olabilir.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
        
            'password.required' => 'Şifre zorunludur.',
            'password.string' => 'Şifre geçerli bir metin olmalıdır.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.regex' => 'Şifre en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir.',
        
            'profilePhotoUrl.url' => 'Profil fotoğrafı geçerli bir URL olmalıdır.',
        ]);
        
        

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        
        
        $userCounts = DB::table('users')->where('adminID',$adminID)->count();
        $typeOfAdmin = DB::table('admininventories')->where('adminID',$adminID)->value('typeofadmin');
        switch ($typeOfAdmin) {
            case 0:
                // Süper admin için özel işlemler
                if($userCounts>=6)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum kullanıcı sayısına ulaştınız.'],404);
                }
                break;
        
            case 1:
                if($userCounts>=12)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum kullanıcı sayısına ulaştınız.'],404);
                }
                break;
        
            case 2:
                if($userCounts>=24)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum kullanıcı sayısına ulaştınız.'],404);
                }
                break;

            case 3:
                if($userCounts>=100000)
                {
                    return response()->json(['success'=>false,'message'=>'Maksimum kullanıcı sayısına ulaştınız.'],404);
                }
                break;
            
            
            default:
                
                return response()->json(['success'=>false,'message'=>'hata gecersiz deger'],404);
                
        
        }
          
        
        try {
            $user = User::create([
                'firstname' => $request->firstname,
                'lastname' => $request->lastname,
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phoneNumber' => $request->phoneNumber,
                "gender"=>$request->gender,
                'profilePhotoUrl' => $request->profilePhotoUrl,
                'adminID'=> $adminID,
                'role'=>'user',
                
            ]);

            $Userinventories = Userinventories::create([
                'adminID' =>$user->adminID,
                'userID' => $user->id,
                'whichShift' => 0
            ]);

            
        } catch (JWTException $e) {
            return response()->json(['message' => 'Kullanici kaydedilirken hata oluştu','succes'=>'false'], 400);
        }

        

        return response()->json(['success'=>'true','message'=>'Kullanıcı başarıyla kaydedildi'],201);
    }

   

    public function updateDepartmansUserCounts(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $departmans = DB::table('departmans')
        ->where('adminID',$adminID)
        ->get();
        $departmanIDs = $departmans->pluck('id');

        $validator = Validator::make($request->all(), [
            'identifier' => [ 
                Rule::in([
                    'AuserCountsShift1', 'AuserCountsShift2', 'AuserCountsShift3', 'AuserCountsShift4', 'AuserCountsShift5',
                    'BuserCountsShift1', 'BuserCountsShift2', 'BuserCountsShift3', 'BuserCountsShift4', 'BuserCountsShift5',
                    'CuserCountsShift1', 'CuserCountsShift2', 'CuserCountsShift3', 'CuserCountsShift4', 'CuserCountsShift5',
                    'DuserCountsShift1', 'DuserCountsShift2', 'DuserCountsShift3', 'DuserCountsShift4', 'DuserCountsShift5',
                    'EuserCountsShift1', 'EuserCountsShift2', 'EuserCountsShift3', 'EuserCountsShift4', 'EuserCountsShift5',
                    'FuserCountsShift1', 'FuserCountsShift2', 'FuserCountsShift3', 'FuserCountsShift4', 'FuserCountsShift5',
                    'GuserCountsShift1', 'GuserCountsShift2', 'GuserCountsShift3', 'GuserCountsShift4', 'GuserCountsShift5',
                ]),
            ],
            'departmanID' => [
                'required', // Zorunlu alan
                Rule::in($departmanIDs), // Geçerli departman ID'lerinden biri olmalı
            ],
            'userCounts' => ['required','integer','min:1','max:300'],

            
        ], [
            'userCounts.required' => 'kullanici satyisi alanı zorunludur. Lütfen minimum 1, maksimum 30 olan sayısal bir değer girin.',
            'userCounts.integer' => 'kullanici sayısı bir tam sayı olmalıdır.',
            'userCounts.min' => 'kullanici sayısı en az 1 olmalıdır.',
            'userCounts.lte' => 'kullanici sayısı en fazla 300 olabilir.',
            'departmanID.required' => 'Departman ID alanı zorunludur.',
            'departmanID.in' => 'Seçtiğiniz departman sistemde kayıtlı değil.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $departmanUserCountUpdate = DB::table('departmans')
        ->where('adminID',$adminID)
        ->where('id',$request->departmanID)
        ->update([ $request->identifier =>$request->userCounts]);

        return response()->json(['success'=>true,'messega'=>'departman basi nobet tutatcak kullanici sayisi basariyla guncellendi'],200);



    }

    public function DepartmanOffDayStore(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
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
            'filterType' => [Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'countOfPaginate'=> 'required|integer',
            
        ], [
            
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $filterType = $request->filterType;
        

        $now = Carbon::now();
        if ($filterType === 'weekly') {
            $startDate = $now->subWeek();
        } elseif ($filterType === 'monthly') {
            $startDate = $now->subMonth();
        } elseif ($filterType === 'yearly') {
            $startDate = $now->subYear();
        } else {
            $startDate = null;
        }

        $query = DB::table('departmansoffdays');
        $query1 = DB::table('departmansoffdays');
            

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
            $query1->where('created_at', '>=', $startDate);
        }

        

        $departmansOffDays = $query1
        ->where('adminID', $adminID)
        ->whereNull('offDayofweek') // offDayofweek sütunu null olan kayıtları filtrele
        ->paginate($request->countOfPaginate);
        

        $departmansOffDayOfWeek = $query
        ->where('adminID', $adminID)
        ->whereNotNull('offDayofweek') // offDayofweek sütunu null olmayan kayıtları filtrele
        ->paginate($request->countOfPaginate);

        
        
        return response()->json(['success'=>'true','message'=>'departman izinleri basari ile getirildi','departmansOffDays'=>$departmansOffDays,'departmansOffDayOfWeek'=>$departmansOffDayOfWeek],201);


    }

    public function callanderDelete(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'callenderID' => ['required'],
        ], [
            'callenderID.required' => 'callenderID zorunludur'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('pastcallenders')
        ->where('adminID',$adminID)
        ->where('id',$request->callenderID)
        ->delete();

        return response()->json(['success'=>'true','message'=>'takvim basari ile silindi']);

    }

    public function deleteDepartman(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'admin ID degeri zorunludur',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'departmanID' => ['required'],
        ], [
            'departmanID.required' => 'depasrtmanID zorunludur'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('departmans')
        ->where('adminID',$adminID)
        ->where('id',$request->departmanID)
        ->delete();

        return response()->json(['success'=>'true','message'=>'departman basariyla silindi']);

        
    }

    public function updateDepartmanPriority(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            
            'departmanID' => ['required'],
            'priority' => ['required', 'string', Rule::in(['low', 'medium','urgent'])],
            
            ], [
           'priority.required' => 'Öncelik alanı zorunludur.',
            'priority.string' => 'Öncelik geçerli bir metin olmalıdır.',
            'priority.in' => 'Öncelik alanı sadece şu seçeneklerden biri olabilir: low, medium, high, critical, urgent.',
            'departmanID.required' => 'callenderID zorunludur'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('departmans')
        ->where('adminID',$adminID)
        ->where('id',$request->departmanID)
        ->update(['priority'=>$request->priority]);

        return response()->json(['success'=>'true','message'=>'departman prioritysi basariyla guncellendi']);

        
    }

    public function departmanOffDayDelete(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            
            'departmanOffDayID' => ['required'],
            
            ], [
           'departmanID.required' => 'callenderID zorunludur'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('departmansoffdays')
        ->where('id',$request->departmanOffDayID)
        ->delete();

        return response()->json(['success'=>true,'message'=>'izin basariyla silindi']);


    }

    public function usersTotalShiftCounts(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
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

        $users = DB::table('users')
        ->where('adminID',$adminID)
        ->get();

        $usersname = $users->pluck('username','id');
        $usersIDs =[];
        foreach($users as $user)
        {
            $usersIDs[] = $user->id;
        }
        $usersTotalShiftCount=[];
        $usersShiftCounts =DB::table('usertotalshiftcounts')
        ->whereIn('userID',$usersIDs)
        ->where('created_at', '>=', now()->subDays($subAmount))
        ->get();
        

        
        
        foreach($usersIDs as $userID)
        {
            $ada = $usersShiftCounts->where('userID',$userID)->isNotEmpty();
            
            if($ada){
                $usersTotalShiftCount [] = $usersShiftCounts->where('userID',$userID)->flatten();
            }
            
            
        }
     
        $data = [];
        foreach($usersTotalShiftCount as $UTSCS)
        {
            $ada =0;
            foreach($UTSCS as $UTSC)
            {
                $username = $usersname[$UTSC->userID];
                $ada += $UTSC->totalShiftCount; 
            }
            $data [] = [
                'username' =>$username,
                'totalNobetCounts' =>$ada,
            ];
            
        }

        return  response()->json(['success'=>'true','message'=>'kullanicilarin toplam nobetsayilari getirildi','data'=>$data]);



    }
    
    public function CallenderusersshiftcountsStore(Request $request)
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
            'callenderID' => ['required'],
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'callenderID' => ['required'],
          ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => 'false',
            ], 422);
        }

        $data = DB::table('Callenderusersshiftcounts')
        ->where('adminID',$adminID)
        ->where('callenderID',$request->callenderID)
        ->paginate($request->countOfPaginate);

        if(!$data)
        {
            return response()->json(['success'=>'false','message'=>'shiftCounts verisi bulunumadi'],404);
        }

        return response()->json(['success'=>'true','message'=>'callendera ait kullanici ShiftCounts verileri getirildi','data'=>$data],201);
        
        

    }

    public function assingSpecialDepartmanStore(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
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
            
        ], [
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


        $data = DB::table('specialdepartmen')
        ->where('adminID',$adminID)
        ->paginate($request->countOfPaginate);

        return response()->json(['success'=>true,'message'=>'veriler basari ile getirildi','data'=>$data]);

    }


    public function assingSpecialDepartmanDelete(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'assingSpecialDepartmanID' => ['required'],
        ], [
            'assingSpecialDepartmanID.required' => 'Filtre türü zorunludur.',
            
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => 'false',
            ], 422);
        }

       $delData = DB::table('specialdepartmen')
       ->where('id',$request->assingSpecialDepartmanID)
       ->where('adminID',$adminID)
       ->delete();

       return response()->json(['success'=>true,'message'=>'kullanici atamasi basari ile silindi'],200);
        


    }

    public function departmanColerUpdate(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }
        // Doğrulama kuralları
        $validator = Validator::make($request->all(), [
        'departmanID' => ['required'],
        'RgbNumber' => 'required|string'

        ], [
        'departmanID.required' => 'departmanID zorunludur',

        'RgbNumber.string' => 'Rgb degeri string olmali',
        'RgbNumber.required' => 'Rgb degeri zorunludur',
        ]);

        DB::table('departmans')
        ->where('id',$request->departmanID)
        ->where('adminID',$adminID)
        ->update(['RgbNumber' =>$request->RgbNumber]);

        return response()->json(['success'=>true,'message'=>'islem basariyla tamamlandi'],200);

    }

    public function updateUserinventoriesWhichShift(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'userID'=> 'required',
            'whichShift' =>['required','integer','min:0','max:5']
        ], [
            'userID.required' => 'userID degeri zorunludur',
            'whichShift.required' => 'sadece hangi vardiyada tutacagideger zorunludur',
            'whichShift.integer' => 'vardiya degeri sayisal olmak zorundadir',
            'whichShift.max' => 'maksimum deger 5 olabilir',
            'whichShift.min' => 'minumum deger 0 olabilir',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $userupdateWhichShift = DB::table('Userinventories')
        ->where('adminID',$adminID)
        ->where('userID',$request->userID)
        ->update(['whichShift'=>$request->whichShift]);

        return response()->json(['success'=>true,'message'=>'islem basariyla tamamlandi'],200);


    }

    public function adminUserInventoriesStore(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            
            
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
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

        $data = DB::table('userinventories')
        ->where('adminID',$adminID)
        ->paginate($request->countOfPaginate);

        return response()->json(['success'=>true,'message'=>'veriler basari ile getirdi','data'=>$data],200);

    }

    public function ResponseWhichShift(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'whichShiftID'=> 'required',
            'status' => [Rule::in('approved','rejected'),'required'],
            'ResponseReason' => 'required'
        ], [
            'whichShiftID.required' => 'whichShiftID zorunludur ve boş bırakılamaz.',
            'ResponseReason.required' => 'ResponseReason zorunludur ve boş bırakılamaz.',
            'status.required' => 'status zorunludur ve boş bırakılamaz.',
            'status.in' => 'status approved,rejected degerlerinden biri olabilir',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $ada = DB::table('userinventorieswishes')
        ->where('id',$request->whichShiftID)
        ->get();
        
        foreach($ada as $ada)
        {
           if($request->status == 'approved')
            {
                DB::table('userinventories')
                ->where('userID',$ada->userID)
                ->where('adminID',$adminID)
                ->update(['whichShift'=>$ada->whichShift]);
            } 
        }
        

        DB::table('userinventorieswishes')
        ->where('id',$request->whichShiftID)
        ->update(['status'=>$request->status,'ResponseReason'=>$request->ResponseReason]);

        return response()->json(['success'=>true,'message'=>'islem basari ile tamamlandi'],200);



    }

    public function whichShiftDelete(Request $request)
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'whichShiftID'=> 'required',
            
            'ResponseReason' => 'required'
        ], [
            'whichShiftID.required' => 'whichShiftID zorunludur ve boş bırakılamaz.',
            'ResponseReason.required' => 'ResponseReason zorunludur ve boş bırakılamaz.',
            'status.required' => 'status zorunludur ve boş bırakılamaz.',
            'status.in' => 'status approved,rejected degerlerinden biri olabilir',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $ada = DB::table('userinventorieswishes')
        ->where('id',$request->whichShiftID)
        ->value('userID');

        DB::table('userinventories')
        ->where('userID',$ada)
        ->where('adminID',$adminID)
        ->update(['whichShift'=>0]);

        DB::table('userinventorieswishes')
        ->where('id',$request->whichShiftID)
        ->update(['status'=>'rejected']);

        return response()->json(['success'=>true,'message'=>'islem basari ile tamamlandi'],200);


    }
    
    public function adminUserinventorieswishesStore(Request $request)
    {
        
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }

        $validator = Validator::make($request->all(), [
            'countOfPaginate'=> 'required|integer',
            'filterType' => ['required', Rule::in(['weekly', 'monthly', 'yearly', 'all'])],
            'status' => ['required', Rule::in(['pending', 'approved', 'rejected', 'all'])],
        ], [
            'countOfPaginate.required' => 'pagination degeri zorunludur',
            'countOfPaginate.integer' => 'pagination degeri tam sayi olamali',
            'filterType.required' => 'Filtre türü zorunludur.',
            'filterType.in' => 'Geçersiz filtre türü. Geçerli değerler: weekly, monthly, yearly, all.',
            'status.required' => 'Durum zorunludur.',
            'status.in' => 'Geçersiz durum. Geçerli değerler: pending, approved, rejected.',
        ]);
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        $query = DB::table('userinventorieswishes')
        ->where('adminID',$adminID);

        if ($request->status !== "all") {
            $query->where('userinventorieswishes.status', $request->status);
        }


        // Tarih filtresi
        if ($request->filterType !== 'all') {
            $dateMap = [
                'weekly'  => now()->subWeek(),
                'monthly' => now()->subMonth(),
                'yearly'  => now()->subYear(),
            ];
            $query->where('userinventorieswishes.created_at', '>=', $dateMap[$request->filterType] ?? now());
        }

        // Verileri çek
        $userinventorieswishes = $query->paginate($request->countOfPaginate);

        return response()->json(['success'=>true,'message'=>'veriler basari ile tamamlandi','data'=>$userinventorieswishes],200);


    }

    public function callenderExportJsonToExcel(Request $request)
    {

        // Validation: callenderID zorunlu
        $validator = Validator::make($request->all(), [
            'callenderID' => 'required',
        ], [
            'callenderID.required' => 'callenderID zorunludur.',
        ]);

        // Eğer validation hatalıysa hata mesajını döndür
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // İlk hatayı al
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        // Veritabanından 'pastCallender' verisini al
        $users = DB::table('pastcallenders')->where('id', $request->callenderID)->pluck('pastCallender')->first();
        
        // JSON verisini diziye çevir
        $data = json_decode($users, true); 
        
        // Eğer JSON hatalı veya boşsa hata döndür
        if (empty($data)) {
            return response()->json([
                'message' => 'Geçersiz veya boş JSON verisi',
                'success' => false,
            ], 400);
        }

        // Yeni bir Excel dosyası oluştur
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Başlıkları ekleyelim (ilk satır)
        $headers = [
            'Nobet Tarihi',
            'Departman',
            'vardiya',
            'kullanici adi',
            'atama turu'
            
        ]; 
        $sheet->fromArray([$headers], null, 'A1'); // Başlıkları A1 hücresinden itibaren yerleştir

        // JSON verisini Excel'e yazalım (ikinci satırdan itibaren)
        $sheet->fromArray($data, null, 'A2'); // Veriyi A2 hücresinden itibaren yaz

        // Excel dosyasını oluştur ve indirmeye hazırla
        $writer = new Xlsx($spreadsheet);
        $fileName = 'nobet_listesi.xlsx';

        // Yanıt olarak dosyayı göndermek için StreamedResponse kullan
        $response = new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        });

        // Yanıt başlıklarını ayarla ve dosyayı tarayıcıya gönder
        $response->headers->set('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $response->headers->set('Content-Disposition', 'attachment; filename="' . $fileName . '"');
        $response->headers->set('Cache-Control', 'max-age=0');

        return $response;
    }

    public function adminType(Request $request) 
    {
        if(auth()->user()->role === 'creator')
        {
            $validator = Validator::make($request->all(), [
                'adminID'=> 'required'
            ], [
                'adminID.required' => 'Başlangıç tarihi zorunludur ve boş bırakılamaz.',
            ]);
            if ($validator->fails()) {
                $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
                return response()->json([
                    'message' => $errors,
                    'success' => false,
                ], 422);
            }
            $adminID = $request->adminID;
        }else
        {
            $adminID = auth()->user()->adminID;
        }


        $data = (int)DB::table('admininventories')->where('adminID',$adminID)->value('typeofadmin');

        return response()->json(['success'=>true,'message'=>'veriler basariyla getirildi','data'=> $data],200);

        
    }

    function siralaNobetAtamalari($userNobetCounts, $ada1) {
        
        array_values($userNobetCounts);

        $list = [] ;

        $ada = array_keys($userNobetCounts);
        foreach($ada as $us)
        {
            $ada = $ada1->where('userID',"9e86b700-8440-4453-bb2f-1e23dcf19031");
            foreach($ada as $ada)
            {
                $list[] = $ada;
            }


        }

        return $list;
    }






}


    




