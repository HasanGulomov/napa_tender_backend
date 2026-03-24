<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tender extends Model
{
    use HasFactory;

    protected $table = 'tender'; 

    protected $fillable = [
        'category',
        'status_percentage',
        'title',
        'description',
        'budget',
        'location',
        'deadline'
    ];

    protected $appends = ['is_favorite'];

    public function getIsFavoriteAttribute() 
    {
        // auth('sanctum')->user() orqali foydalanuvchini tekshiramiz
        $user = auth('sanctum')->user();
        if($user){
            return $this->favoritedBy()->where('user_id', $user->id)->exists();
        }
        return false;
    }

    public function favoritedBy() // Nomini to'g'riladim
    {
        return $this->belongsToMany(User::class, 'favorite_tenders', 'tender_id', 'user_id');
    }
}