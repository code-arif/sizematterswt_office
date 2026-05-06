<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'key',
        'content',
        'is_active',
        'meta_title',
        'meta_description',
    ];
}
