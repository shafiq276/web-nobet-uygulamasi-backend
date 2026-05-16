<?php

namespace App\Http\Controllers;

use App\Events\PaymentStarted;
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
use App\Models\ProductPrices;
use App\Models\userOrders;
use Illuminate\Support\Facades\Http;


class PaymentController extends Controller
{
    
    public function generateHashKey($total, $installment, $currency_code,
    $merchant_key, $invoice_id, $app_secret)
    {
        $data = $total . '|' . $installment . '|' . $currency_code . '|' . $merchant_key . '|' . $invoice_id;
    
        $iv = substr(sha1(mt_rand()), 0, 16);
        $password = sha1($app_secret);
    
        $salt = substr(sha1(mt_rand()), 0, 4);
        $saltWithPassword = hash('sha256', $password . $salt);
    
        $encrypted = openssl_encrypt("$data", 'aes-256-cbc', "$saltWithPassword", null, $iv);
    
        $msg_encrypted_bundle = "$iv:$salt:$encrypted";
        $msg_encrypted_bundle = str_replace('/', '__', $msg_encrypted_bundle);
    
        return $msg_encrypted_bundle;
    }

    public function paymentProcessed($order_id)
    {
        event(new PaymentStarted([
            'order_id' => $order_id,
            'status' => 'success',
            'message' => 'Ödeme başarılı!',
        ]));   
        
    }

    public function makePayment(Request $request)
    {
        $rules = [
            'card_holder_name' => 'required|string|max:255|regex:/\s/',
            'card_number' => 'required|digits:16',
            'expiry_month' => 'required|integer|min:1|max:12', 
            'expiry_year' => 'required|integer|digits:2',
            'productPriceID' => 'required|uuid|exists:product_prices,id',
        ];
    
        // Validasyon mesajları
        $messages = [
            'card_number.required' => 'Kart numarası zorunludur.',
            'card_number.digits' => 'Kart numarası 16 haneli olmalıdır.',
            'card_holder_name.required' => 'Kart sahibi adı zorunludur.',
            'expiry_month.required' => 'Son kullanma ayı zorunludur.',
            'expiry_month.integer' => 'Son kullanma ayı bir tam sayı olmalıdır.',
            'expiry_month.min' => 'Son kullanma ayı en az 1 olmalıdır.',
            'expiry_month.max' => 'Son kullanma ayı en fazla 12 olmalıdır.',
            'expiry_year.required' => 'Son kullanma yılı zorunludur.',
            'expiry_year.integer' => 'Son kullanma yılı bir tam sayı olmalıdır.',
            'expiry_year.min' => 'Son kullanma yılı geçerli bir yıl olmalıdır.',
            'expiry_year.max' => 'Son kullanma yılı en fazla ' . (date('Y') + 10) . ' olmalıdır.',
            'productPriceID.required' => 'Ürün fiyatı ID zorunludur.',
            'productPriceID.uuid' => 'Geçerli bir UUID olmalıdır.',
            'productPriceID.exists' => 'Belirtilen ürün fiyatı bulunamadı.',
        ];
    
        // Validator::make ile validasyonu başlat
        $validator = Validator::make($request->all(), $rules, $messages);

        if ($validator->fails()) {
            $errors = $validator->errors()->first();
            return response()->json([
                'message' => $errors,
                'success' => 'false',
            ], 422);
        }
        


        // Verileri hazırla
        $merchant_key = env('MerchantKEY');
        $currency_code = 'TRY';
        $installments_number = 1;
        $invoice_id = uniqid();
        $invoice_description = " ";
        $nameParts = explode(' ', $request->card_holder_name);
        $Name = $nameParts[0];
        $surname = $nameParts[1];
        $expiry_year = $request->expiry_year; 
        $full_year = "20" . $expiry_year; 


        $ImportentData = ProductPrices::where('id', $request->productPriceID)->first();
        $total = $ImportentData->ProductPrices;

        
        userOrders::create([
            'userID' => auth()->user()->adminID,
            'invoiceID' => $invoice_id,
            'typeofadmin' => $ImportentData->WhichStatus,
            'numberofcalender' => $ImportentData->numberofcalender,
        ]);
        
        $this->paymentProcessed($invoice_id);


        $items = [
            [
                'name' => ".",
                'price' => $total,
                'quantity' => 1, 
                'description' => "."
            ]
        ];

        
        $cancel_url = 'http://localhost:8000/api/cancel_url';
        $return_url = 'http://localhost:8000/api/return_url';
        
        $app_secret = env('APPSECRET');
        
        $hash_key = $this->generateHashKey($total, $installments_number, $currency_code, $merchant_key, $invoice_id, $app_secret);
        
        $userEmail =  auth()->user()->email;

       
        
        
        $multipartData = [
            ['name' => 'expiry_month', 'contents' => $request->expiry_month],
            ['name' => 'expiry_year', 'contents' => $full_year], // Tam yıl kullanılıyor
            ['name' => 'cc_no', 'contents' => $request->card_number],
            ['name' => 'cc_holder_name', 'contents' => $request->card_holder_name],
            ['name' => 'cvv', 'contents' => $request->cvv],
            ['name' => 'merchant_key', 'contents' => $merchant_key],
            ['name' => 'currency_code', 'contents' => $currency_code],
            ['name' => 'installments_number', 'contents' => $installments_number],
            ['name' => 'invoice_id', 'contents' => $invoice_id],
            ['name' => 'invoice_description', 'contents' => $invoice_description],
            ['name' => 'name', 'contents' => $Name],
            ['name' => 'surname', 'contents' => $surname],
            ['name' => 'total', 'contents' => $total],
            ['name' => 'items', 'contents' => json_encode($items)], // JSON formatında gönder
            ['name' => 'cancel_url', 'contents' => $cancel_url],
            ['name' => 'return_url', 'contents' => $return_url],
            ['name' => 'hash_key', 'contents' => $hash_key],
            ['name' => 'bill_email', 'contents' =>$userEmail],
            ['name' => 'bill_phone', 'contents' => '5012345678'],
            ['name' => 'response_method', 'contents' => 'POST'],

        ];
        
        
                


        $response = Http::asMultipart()->post('https://provisioning.sipay.com.tr/ccpayment/api/paySmart3D', $multipartData);
        
        return response($response->body());

        
        

    }

    

    public function return_url(Request $request)
    {

        $data = $request->invoice_id;

        $data = userOrders::where('invoiceID', $data)->first();

        $adminID = $data->userID;

        $numberofcalender = $data->numberofcalender;

        

        $typeofadmin = (int) $data->typeofadmin+1;

        
       $now = Carbon::now();

       $data = admininventory::where('adminID', $adminID)->update(['typeofadmin' => $typeofadmin,'tymofShoppin' => $now,'numberofcalendars' => $numberofcalender]);
       

        
        
        
        
        return response()->json([
            'success' => true,
            'message' => 'Payment successful',
            
        ],201);
    }

    public function cancel_url(Request $request)
    {
       
        return response()->json([
            'success' => false,
            'message' => 'Payment failed',
            'data' => $request->all(),
        ], 400); // 400 Bad Request
    }






}


 