<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = ['title', 'description', 'date', 'color'];
    protected $dates = ['date'];
}
