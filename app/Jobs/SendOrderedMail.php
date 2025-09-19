<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\OrderedMail;
use App\Models\Product;
use App\Models\User;

class SendOrderedMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $product;
    public $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($product, $user)
    {
        $this->product = $product;
        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Mail::to($this->product['email'])
        // ->send(new OrderedMail($this->product, $this->user));

        // 使用しているサービス（Mailtrap）の送信数上限にひっかかる。
        \Log::info('SendOrderedMail START', [
            'product' => $this->product,
            'user'    => $this->user->id ?? null,
        ]);

        try {
            Mail::to($this->product['email'])
                ->send(new OrderedMail($this->product, $this->user));

            \Log::info('SendOrderedMail SUCCESS', [
                'to'      => $this->product['email'],
                'product' => $this->product['id'] ?? null,
                'user'    => $this->user->id ?? null,
            ]);
        } catch (\Throwable $e) {
            \Log::error('SendOrderedMail FAILED', [
                'error'   => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
                'product' => $this->product,
                'user'    => $this->user,
            ]);
            throw $e;
        }
    }
}
