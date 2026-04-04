<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $table = 'tenders';

    protected $fillable = [
        'source_id',
        'category_id',
        'title',
        'description',
        'budget',
        'region_id',
        'deadline'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function region()
    {
        return $this->belongsTo(Region::class);
    }
    public function source()
    {
        return $this->belongsTo(Source::class);
    }

    protected $appends = ['is_favorite'];

    public function getIsFavoriteAttribute()
    {

        $user = auth('sanctum')->user();
        if ($user) {
            return $this->favoritedBy()->where('user_id', $user->id)->exists();
        }
        return false;
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_tenders', 'tender_id', 'user_id');
    }
}
