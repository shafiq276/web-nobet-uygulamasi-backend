<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ProductPrices;
use Illuminate\Support\Facades\Validator;
class ProductPriceController extends Controller
{
    public function getProductPrice()
    {
        $productPrice = ProductPrices::all();
        return response()->json([
            'success' => true,
            'message' => 'Product Price List',
            'Data' => $productPrice]);
    }

    public function productPriceStore(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ProductPrices' => 'required|integer',
            'WhichStatus' => 'required|integer',
            'numberofcalender' => 'required|integer',
        ],
        [

            'ProductPrices.required' => 'Product Prices is required',
            'ProductPrices.integer' => 'Product Prices must be a integer',
            'WhichStatus.required' => 'Which Status is required',
            'WhichStatus.integer' => 'Which Status must be a integer',
            'numberofcalender.required' => 'Number of Calender is required',
            'numberofcalender.integer' => 'Number of Calender must be a integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false, 
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }

        $productPrice = ProductPrices::create($request->all());
        return response()->json([
            'success' => true,
            'message' => 'Product Price Created',
            'Data' => $productPrice
        ]);
    }

    public function updateProductPrice(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'productPriceId' => 'required|uuid|exists:product_prices,id',
            'ProductPrices' => 'required|integer',
            'WhichStatus' => 'required|integer',
            'numberofcalender' => 'required|integer',
        ],
        [
            'productPriceId.required' => 'Product Price ID is required',
            'productPriceId.uuid' => 'Product Price ID must be a valid UUID',
            'ProductPrices.required' => 'Product Prices is required',
            'ProductPrices.integer' => 'Product Prices must be a integer',
            'WhichStatus.required' => 'Which Status is required',
            'WhichStatus.integer' => 'Which Status must be a integer',
            'numberofcalender.required' => 'Number of Calender is required',
            'numberofcalender.integer' => 'Number of Calender must be a integer',
        ]);

        if ($validator->fails()) {
            return response()->json([   
                'success' => false, 
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }


        $productPrice = ProductPrices::find($request->productPriceId);
        $productPrice->update($request->all());
        return response()->json([
            'success' => true,  
            'message' => 'Product Price Updated',
            'Data' => $productPrice
        ]);
    }

    public function deleteProductPrice(Request $request)
    {   
        $validator = Validator::make($request->all(), [
            'productPriceId' => 'required|uuid|exists:product_prices,id',
        ],
        [
            'productPriceId.required' => 'Product Price ID is required',
            'productPriceId.uuid' => 'Product Price ID must be a valid UUID',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $validator->errors()
            ], 422);
        }


        $productPrice = ProductPrices::find($request->productPriceId);
        
        $productPrice->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product Price Deleted',
            'Data' => $productPrice
        ]);
    }
}
