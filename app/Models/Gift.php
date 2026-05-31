<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gift extends Model
{
    // Con esto guardamos datos en la base de datos
    protected $fillable = ['name', 'description', 'image_url', 'is_reserved', 'reserved_by'];
}
