<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $table = 'tender'; 

    protected $fillable = [
        'source',
        'category',
        'title',
        'description',
        'budget',
        'location',
        'deadline'
    ];

    protected $appends = ['is_favorite'];

    public function getIsFavoriteAttribute() 
    {
       
        $user = auth('sanctum')->user();
        if($user){
            return $this->favoritedBy()->where('user_id', $user->id)->exists();
        }
        return false;
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorite_tenders', 'tender_id', 'user_id');
    }
}