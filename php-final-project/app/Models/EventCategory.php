<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    public $timestamps = false;

    protected $fillable = ['category_name'];

    public function events()
    {
        return $this->hasMany(Event::class, 'category_id');
    }
}
