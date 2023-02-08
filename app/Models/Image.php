<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'file',
        'enable',
    ];

    protected $hidden = [
        'pivot',
    ];

    protected $casts = [
        'enable' => 'boolean',
    ];

    public $timestamps = false;

    public function products()
    {
        return $this->belongsToMany(Product::class);
    }
}
