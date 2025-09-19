<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Cart;
use App\Models\User;
use App\Models\Stock;
use App\Services\CartService;
use App\Services\CheckoutService;
use App\Jobs\SendThanksMail;
use App\Jobs\SendOrderedMail;
use App\Http\Requests\User\AddToCartRequest;

class CartController extends Controller
{
    public function index()
    {
        $user = User::findOrFail(Auth::id());
        $products = $user->products;
        $totalPrice = 0;

        foreach($products as $product){
            $totalPrice += $product->price * $product->pivot->quantity;
        }

        return view('user.cart', 
            compact('products', 'totalPrice'));
    }

    public function add(Request $request)
    {
        $userId = Auth::id();
        Cart::updateOrCreate(
            [
                'user_id' => $userId,
                'product_id' => $request->product_id,
            ],
            [
                'quantity' => \DB::raw('quantity + ' . (int) $request->quantity),
            ]
        );
        
        return redirect()->route('user.cart.index');
    }

    public function delete($id)
    {
        Cart::where('product_id', $id)
        ->where('user_id', Auth::id())
        ->delete();

        return redirect()->route('user.cart.index');
    }

    public function checkout(CheckoutService $checkoutService)
    {
        $user = Auth::user();
        $products = $user->products;

        $session = $checkoutService->createCheckoutSession($products);

        return view('user.checkout', [
            'session'   => $session,
            'publicKey' => config('services.stripe.key'),
        ]);
        
    }

    public function success()
    {
        $items = Cart::where('user_id', Auth::id())->get();
        $products = CartService::getItemsInCart($items);
        $user = User::findOrFail(Auth::id());

        SendThanksMail::dispatch($products, $user);
        foreach($products as $product){
            SendOrderedMail::dispatch($product, $user);
        }

        Cart::where('user_id', Auth::id())->delete();

        return redirect()->route('user.items.index');
    }

    public function cancel()
    {
        $user = User::findOrFail(Auth::id());

        foreach($user->products as $product){
            Stock::create([
                'product_id' => $product->id,
                'type' => \Constant::PRODUCT_LIST['add'],
                'quantity' => $product->pivot->quantity
            ]);
        }

        return redirect()->route('user.cart.index');
    }
}