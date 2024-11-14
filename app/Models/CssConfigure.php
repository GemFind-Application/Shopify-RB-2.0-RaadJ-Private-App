<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CssConfigure extends Model
{
    protected $table = 'css_configuration';

    protected $fillable = [
        'shop',
        'link',
        'header',
        'button',
        'slider',
        'hover',
    ];

    protected $hidden = [
        'created_at', 'updated_at',
    ];
}