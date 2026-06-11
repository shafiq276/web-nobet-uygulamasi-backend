<?php

namespace App\Http\Controllers;

use App\Jobs\SendMailJob;
use App\Models\User;
use App\Models\PasswordReset;
use App\Models\admininventory;
use App\Models\Userinventories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Mail\SendEmail;



class JWTAuthController extends Controller
{
    // User registration
    public function registerProfilePhoto(Request $request)
    {


        $request->validate([
            'photo' => 'required|image|mimes:jpg,png,jpeg,gif|max:2048', // 2MB sınır
        ]);

        $user = auth()->user();
        $fileName = $user->id . '_' . time() . '.' . $request->photo->extension(); 

        
        $path = $request->photo->storeAs('profile_photos', $fileName, 'public');

        
        $user->profilePhotoUrl = $path;
        $user->save();

        //return response()->json(['message' => 'Fotoğraf başarıyla yüklendi!', 'path' => asset('storage/' . $path)]);



    }
    public function register(Request $request)
    {
        

        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:255',
            'lastname' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'gender' => 'required|in:male,female',
            'phoneNumber' => 'required|string|regex:/^\\+?[0-9]{10,15}$/',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&+])[A-Za-z\d@$!%*?&]{8,}$/'
            ],
            'adminID' => [
                'required',
                Rule::when($request->role === 'admin', Rule::unique('users', 'adminID')),
                Rule::when($request->role === 'user', Rule::exists('users', 'adminID'))
            ],
            'adminID' => [
                'required',
                'string',
                'min:6',
                'max:20',
                function ($attribute, $value, $fail) {
                    if (!preg_match('/[!@#$%^&*(),.?":{}|<>]/', $value)) {
                        $fail( $attribute . ' en az bir ozel karakter icermelidir');
                    }
                },
                Rule::when($request->role === 'admin', Rule::unique('users', 'adminID')),
                Rule::when($request->role === 'user', Rule::exists('users', 'adminID'))
            ],

            
            
            'role' => ['required', 'string', Rule::in(['admin', 'user'])],
            
        ], [
            'firstname.required' => 'Ad alanı zorunludur.',
            'lastname.required' => 'Soyad alanı zorunludur.',
            'username.required' => 'Kullanıcı adı zorunludur.',
            'username.unique' => 'Bu kullanici adi zaten alinmiş.',
            'gender.required' => 'Cinsiyet alanı zorunludur.',
            'gender.in' => 'Cinsiyet sadece "male" veya "female" olabilir.',
            'phoneNumber.regex' => 'Telefon numarası geçerli bir formatta olmalıdır.',
            'phoneNumber.required' => 'Telefon numarasi zorunludur.',
            'email.required' => 'E-posta adresi zorunludur.',
            'email.email' => 'E-posta adresi geçerli bir formatta olmalıdır.',
            'email.unique' => 'Bu e-posta adresi zaten kayıtlı.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.min' => 'Şifre en az 8 karakterden oluşmalıdır.',
            'password.regex' => 'Şifre en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir.',
            'adminID.required' => 'The adminID field is required.',
            'adminID.string' => 'The adminID must be a string.',
            'adminID.min' => 'The adminID must be at least 6 characters.',
            'adminID.max' => 'The adminID may not be greater than 14 characters.',
            'adminID.unique' => 'The adminID has already been taken.',
            'adminID.exists' => 'Lutfen gecerli bir adminID giriniz',
            'profilePhotoUrl.url' => 'Profil fotoğrafı geçerli bir URL olmalıdır.',
            'role.required' => 'Role alanı zorunludur.',
            'role.string' => 'Role alanı metin olmalıdır.',
            'role.in' => 'Role alanı sadece admin veya user değerlerini alabilir.',
            'adminID.unique' => 'Bu adminID zaten kullanımda. Lütfen farklı bir değer giriniz.',
            'adminID.exists' => 'Belirtilen adminID bulunamadı. Lütfen geçerli bir adminID giriniz.',
        ]);
        

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }
        

        if($request->role == 'user')
        {
            $userCounts = DB::table('users')->where('adminID',$request->adminID)->count();
            $typeOfAdmin = DB::table('admininventories')->where('adminID',$request->adminID)->value('typeofadmin');
            

            switch ($typeOfAdmin) {
                case 0:
                    // Süper admin için özel işlemler
                    if($userCounts>=6)
                    {
                        return response()->json(['success'=>false,'message'=>'Bu yönetici maksimum personel sayısına ulaşmıştır.'],404);
                    }
                    break;
            
                case 1:
                    if($userCounts>=12)
                    {
                        return response()->json(['success'=>false,'message'=>'Bu yönetici maksimum personel sayısına ulaşmıştır.'],404);
                    }
                    break;
            
                case 2:
                    if($userCounts>=24)
                    {
                        return response()->json(['success'=>false,'message'=>'Bu yönetici maksimum personel sayısına ulaşmıştır.'],404);
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
                'adminID'=> $request->adminID,
                'role'=>$request->role,
            ]);
            

            if($request->role == 'admin')
            {
                $admininventory = admininventory::create([
                    'adminID'=> $request->adminID,
                    'numberofcalendars' => 1,
                ]);
            }
            
            
            $Userinventories = Userinventories::create([
                'adminID' =>$user->adminID,
                'userID' => $user->id,
                'whichShift' => 0
            ]);

            
        } catch (JWTException $e) {
            return response()->json(['message' => 'Kullanici kaydedilirken hata oluştu','succes'=>'false'], 200);
        }
        
        
        $token = JWTAuth::fromUser($user);

        return $this->createNewToken($token, $user, 'Kullanıcı başarıyla kaydedildi');
    }

    // User login
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'identifier' => 'required|string', // Email veya username
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }

        $identifier = $request->input('identifier');
        $password = $request->input('password');

        // Kullanıcıyı email veya username ile bul
        $user = User::where('email', $identifier)
                    ->orWhere('username', $identifier)
                    ->first();

        if (!$user) {
            return response()->json(['error' => 'Geçersiz kimlik bilgileri'], 401);
        }

        // Kullanıcının email adresi üzerinden kimlik doğrulama yap
        $credentials = ['email' => $user->email, 'password' => $password];

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Geçersiz kimlik bilgileri'], 401);
            }

            return $this->createNewToken($token, $user, 'Başarıyla giriş yapıldı');
        } catch (JWTException $e) {
            return response()->json(['error' => 'Token oluşturulamadı'], 500);
        }
    }


    // User logout
    public function logout()
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());

            return response()
                ->json(['message' => 'Başarıyla çıkış yapıldı'])
                ->cookie('jwt', '', -1, '/', null, true, true);
        } catch (JWTException $e) {
            return response()->json(['error' => 'Çıkış yapılamadı'], 500);
        }
    }

    public function sendResetCode(Request $request){
        
        $request->validate(['email' => 'required|email']);

        // Kullanıcı var mı kontrol et
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Bu e-posta adresi kayıtlı değil.'], 404);
        }

        // 6 haneli rastgele kod oluştur
        $resetCode = random_int(100000, 999999);

        // Kodun veritabanına kaydedilmesi
        PasswordReset::Create([
            'userID' => $user->id,
            'code' => $resetCode,
            'email' => $request->email
            
        ]);

        SendMailJob::dispatch($user->email, $resetCode);
        // Kullanıcıya e-posta gönderme


        return response()->json(['success'=> 'true' ,'message' => 'Şifre sıfırlama kodu e-postanıza gönderildi.','email'=> $request->email],201);
    }
    
    public function verifyResetCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'code' => 'required|digits:6',
        ], [
             'code.required' => 'Doğrulama kodu gereklidir.',
            'code.digits' => 'Doğrulama kodu 6 haneli olmalıdır.',
        ]);
    
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // İlk hata mesajını döndürür
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }


        $resetData = DB::table("password_resets")
        ->where('code', $request->code)
        ->first();

        if (!$resetData) {
            return response()->json(['success'=>'false','message' => 'Kod geçersiz.'], 400);
        }

        
        if (Carbon::parse($resetData->created_at)->addMinutes(5)->isPast()) {
            return response()->json(['message' => 'Kodun süresi dolmuş. Lütfen tekrar deneyin.'], 400);
        }

        return response()->json(['success'=>'true' ,'message' => 'Kod doğrulandı. Şifrenizi sıfırlayabilirsiniz.','code'=>$request->code]);
    }

    
    public function resetPassword(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'code' => 'required|digits:6',
            'password' => [
                'required',
                'string',
                'min:8',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'r_password' => 'required|same:password',
        ], [
            'code.required' => 'Doğrulama kodu alanı zorunludur.',
            'code.digits' => 'Doğrulama kodu 6 haneli olmalıdır.',
            'password.required' => 'Şifre alanı zorunludur.',
            'password.string' => 'Şifre bir metin olmalıdır.',
            'password.min' => 'Şifre en az 8 karakter olmalıdır.',
            'password.regex' => 'Şifre en az bir büyük harf, bir küçük harf, bir rakam ve bir özel karakter içermelidir.',
            'r_password.required' => 'Şifre tekrar alanı zorunludur.',
            'r_password.same' => 'Şifreler eşleşmiyor.',
        ]);

  
        if ($validator->fails()) {
            $errors = $validator->errors()->first(); 
            return response()->json([
                'message' => $errors,
                'success' => 'false',
            ], 422); 
        }

        
        $resetData = DB::table('password_resets')->where('code', $request->code)->first();

        if (!$resetData) {
            return response()->json([
                'message' => 'Kod geçersiz veya süresi dolmuş.',
                'success' => 'false',
            ], 400); 
        }

     
        DB::table('users')
            ->where('id', $resetData->userID)
            ->update(['password' => Hash::make($request->password)]);

        
        DB::table('password_resets')->where('code', $request->code)->delete();

        return response()->json([
            'message' => 'Şifreniz başarıyla güncellendi.',
            'success' => 'true',
        ],201);
    }

    
    protected function createNewToken($token, $user, $message)
    {
        $cookie = cookie('jwt', $token, config('jwt.ttl'), '/', null, true, true);

        return response()
            ->json([
                'message' => $message,
                'user' => $user,
                'token_type' => 'bearer',
                'expires_in' => config('jwt.ttl') * 60,
            ])
            ->withCookie($cookie);
    }



    


}
