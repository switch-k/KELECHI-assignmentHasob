<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class SubscriptionController extends Controller
{
    public function create(Request $request, Product $product)
    {
        if($request->user()->subscribedToPlan($product->stripe_plan, 'main')) {
            
        }
        $product = Product::findOrFail($request->get('plan'));
        
        $request->user()
            ->newSubscription('main', $product->stripe_plan)
            ->create($request->stripeToken);
        
            return response()->json();
    }
}

