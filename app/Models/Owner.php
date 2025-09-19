<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\Shop;
use App\Models\Image;

class Owner extends Authenticatable
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public static function createWithShop(array $data): self
    {
        return DB::transaction(function () use ($data) {
            $owner = self::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
            ]);

            $owner->shop()->create([
                'name' => '店名を入力してください',
                'information' => '',
                'filename' => '',
                'is_selling' => true,
            ]);

            return $owner;
        });
    }

    public function updateWithPassword(array $data): bool
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        if (!empty($data['password'])) {
            $this->password = Hash::make($data['password']);
        }
        return $this->save();
    }

    public function shop()
    {
        return $this->hasOne(Shop::class);
    }

    public function image()
    {
        return $this->hasMany(Image::class);
    }
}
