<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Product;
use App\Models\PrimaryCategory;
use App\Models\Stock;
use App\Constants\Common;

class ItemController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:users');
        $this->middleware('ensure.product.available')->only(['show']);
    }
        
    public function index(Request $request)
    {
        $categories = PrimaryCategory::with('secondary')
        ->get();

        $products = Product::availableItems()
        ->selectCategory($request->category ?? '0')
        ->searchKeyword($request->keyword)
        ->sortOrder($request->sort)
        ->paginate($request->pagination ?? '20');

        return view('user.index', 
        compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $quantity = min($product->stockQuantity(),
            Common::MAX_PURCHASE_QUANTITY
        );

        return view('user.show', 
        compact('product', 'quantity'));
    }
}
