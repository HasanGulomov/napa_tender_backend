<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable; 

    protected $fillable = ['username', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    public function favorites()
    {
        return $this->belongsToMany(Tender::class, 'favorite_tenders', 'user_id', 'tender_id')->withTimestamps();
    }
}