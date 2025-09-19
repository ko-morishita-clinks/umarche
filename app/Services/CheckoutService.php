<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Stock;
use App\Constants\Common;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class CheckoutService
{
    public function createCheckoutSession($products)
    {
        $lineItems = [];

        foreach ($products as $product) {
            // 在庫チェック
            if ($product->pivot->quantity > $product->stockQuantity()) {
                throw new \Exception('在庫が不足しています');
            }

            $lineItems[] = [
                'price_data' => [
                    'unit_amount' => $product->price * 100,
                    'currency' => 'jpy',
                    'product_data' => [
                        'name' => $product->name,
                        'description' => $product->information,
                    ],
                ],
                'quantity' => $product->pivot->quantity,
            ];
        }

        Stripe::setApiKey(config('services.stripe.secret'));

        return Session::create([
            'payment_method_types' => ['card'],
            'line_items' => $lineItems,
            'mode' => 'payment',
            'success_url' => route('user.cart.success'),
            'cancel_url' => route('user.cart.cancel'),
        ]);
    }

    public function reduceStock($products)
    {
        foreach ($products as $product) {
            Stock::create([
                'product_id' => $product->id,
                'type'       => Common::PRODUCT_LIST['reduce'],
                'quantity'   => $product->pivot->quantity * -1,
            ]);
        }
    }
}
