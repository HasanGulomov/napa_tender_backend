<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name'];

    public function tenders()
    {
        return $this->hasMany(Tender::class);
    }
}
