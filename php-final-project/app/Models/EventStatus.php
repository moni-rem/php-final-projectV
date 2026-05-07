<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventStatus extends Model
{
    public $timestamps = false;

    protected $fillable = ['status_name'];

    public function events()
    {
        return $this->hasMany(Event::class, 'status_id');
    }
}
