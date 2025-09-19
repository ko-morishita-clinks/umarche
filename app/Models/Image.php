<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\Owner;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'filename'
    ];

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public static function createForOwner(int $ownerId, string $filename): self
    {
        return self::create([
            'owner_id' => $ownerId,
            'filename' => $filename,
        ]);
    }

    public function updateTitle(string $title): bool
    {
        $this->title = $title;
        return $this->save();
    }

    public function deleteWithRelations(): void
    {
        $products = Product::where('image1', $this->id)
            ->orWhere('image2', $this->id)
            ->orWhere('image3', $this->id)
            ->orWhere('image4', $this->id)
            ->get();

        $products->each(function ($product) {
            foreach (['image1', 'image2', 'image3', 'image4'] as $column) {
                if ($product->$column === $this->id) {
                    $product->$column = null;
                }
            }
            $product->save();
        });

        $filePath = 'public/products/' . $this->filename;
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // DB削除
        $this->delete();
    }

}