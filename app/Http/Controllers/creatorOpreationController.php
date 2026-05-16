<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\PasswordReset;
use App\Models\admininventory;
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


class creatorOpreationController extends Controller
{
    
    public function usersStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'role' => 'required|in:user,admin,all',
            
        ], [
            'role.required' => 'Role alanı zorunludur.',
            'role.in' => 'Role değeri sadece "user" veya "admin" olabilir.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }

        if($request->role != 'all')
        {
           $users = DB::table('users')
            ->where('role', $request->role)
            ->get(); 
        }
        else{
            $users = DB::table('users')
            ->get();
        }
        
    
        return response()->json([
            'success' => true,
            'message' => 'Bütün kullanıcılar getirildi',
            'data' => $users
        ]);


    }

    public function updateUserRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            
            'userID' => 'required',   
            'role' => 'required|in:user,admin',
            
        ], [
            
            'userID.required' => 'Kullanıcı ID alanı zorunludur.',
            'role.in' => 'Role değeri sadece "user" veya "admin" olabilir.',

        ]);
        

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('users')
        ->where('id',$request->userID)
        ->update(['role'=>$request->role]);

        return response()->json(['success'=>true,'message'=>'kullanici rolu basariyla guncellendi']);

    }

   
    
    public function updateCallenderCount(Request $request)
    {
        // Validasyon işlemi
        $validator = Validator::make($request->all(), [
            'AdminID' => 'required|exists:admininventories,adminID',
            'callenderCount' => 'required|integer|min:1',
            'typeofadmin' => 'required|integer|in:1,2,3'
        ], [
            'AdminID.required' => 'AdminID alanı zorunludur.',
            'AdminID.exists' => 'Bu AdminID ile eşleşen bir kayıt bulunamadı.',
            'callenderCount.required' => 'callenderCount alanı zorunludur.',
            'callenderCount.integer' => 'callenderCount tam sayı olmak zorundadır.',
            'callenderCount.min' => 'callenderCount en az 1 olmalıdır.',
            'typeofadmin.required' => 'typeofadmin alanı zorunludur.',
            'typeofadmin.integer' => 'typeofadmin tam sayı olmak zorundadır.',
        ]);
    
        if ($validator->fails()) {
            return response()->json([
                'message' => $validator->errors()->first(),
                'success' => false,
            ], 422);
        }
    
        // Mevcut takvim sayısını al
        $adminInventory = DB::table('admininventories')
            ->where('adminID', $request->AdminID)
            ->first(); // first() kullanarak nesne olarak alıyoruz
    
        if (!$adminInventory) {
            return response()->json([
                'message' => 'Belirtilen AdminID için kayıt bulunamadı.',
                'success' => false,
            ], 404);
        }
    
        // Yeni takvim sayısını hesapla
        $newCalendarCount = $adminInventory->numberofcalendars + $request->callenderCount;
        $currentDate = Carbon::now(); // Şu anın tarihi
        // Güncelleme işlemi
        DB::table('admininventories')
            ->where('adminID', $request->AdminID)
            ->update([
                'numberofcalendars' => $newCalendarCount,
                'typeofadmin' => $request->typeofadmin,
                'tymofShoppin' => $currentDate
            ]);
    
        return response()->json([
            'success' => true,
            'message' => 'İşlem başarılı'
        ], 201);
    }
    


    public function updateCallender(Request $request)
    {

        $validator = Validator::make($request->all(), [
            
            'AdminID' => 'required',   
            'callenderID' => 'required',
            'newCallender' => 'required',
            
        ], [
            
            'AdminID.required' => 'Kullanıcı ID alanı zorunludur.',
            'callenderID.required' => 'callenderCount alani zorunludur',
            'newCallender.required' => 'newCallender alani zorunludur',
            

        ]);
        

        if ($validator->fails()) {
            $errors = $validator->errors()->first(); // Tüm hataları dizi olarak alır
            return response()->json([
                'message' => $errors,
                'success' => false,
            ], 422);
        }

        DB::table('pastcallenders')
        ->where('adminID',$adminID)
        ->where('id',$request->callenderID)
        ->update(['pastCallender'=>$request->newCallender]);

    }

    

   

}
