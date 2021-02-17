<?php
   
namespace App\Http\Controllers\API;
   
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Product;
use App\Models\Cart;
use App\Models\checkOut;
use App\Models\Session;

use Validator;

   
class ProductController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::all();
        return response()->json(['data' => $products]);
    
        /* return [
            'id' => $this->product,
            'name' => $this->name,
            'detail' => $this->detail,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'created_at' => $this->created_at->format('d/m/Y'),
            'updated_at' => $this->updated_at->format('d/m/Y'),
        ]; */
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $product = Product::create($input);
   
        return response()->json(['data' => $product]);
    } 
   
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
  
        if (is_null($product)) {
            return $this->sendError('Product not found.');
        }
   
        return response()->json(['data' => $product]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name' => 'required',
            'detail' => 'required',
            'price' => 'required',
            'quantity' => 'required'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $product->name = $input['name'];
        $product->detail = $input['detail'];
        $product->price = $input['price'];
        $product->quantity = $input['quantity'];
        $product->save();
   
        return response()->json(['data' => $product]);
    }
   
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();
   
        return response()->json(['data' => $product]);
    }

    public function search(Request $request){
        $datas= Product::select("name")
        ->where("name","LIKE","%{$request->terms}%")
        ->orWhere('description', 'LIKE', "% {'id'} %")
        ->get();
            return response()->json($datas);

    
  
        }
      
    public function checkout(Request $request)  {
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'cartItems' => 'required|array',
            'total' => 'required|numeric',
        ]);
        
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }

       
        $checkout = checkOut::create([
            'total' => $request->total,
            'user_id' => auth()->id()
        ]);

        $cartItems = $request->cartItems;
        for ($i = 0; $i < count($cartItems); $i++) {
            $item = $cartItems[$i];
            Cart::create([
                'checkout_id' => $checkout['id'],
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'sub_total' => $item['sub_total']
            ]);
        }

        return response('Checkout was added successfuly');
    }
}