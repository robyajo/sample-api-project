<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'contacts';
    protected $fillable = [
        'photo',
        'name',
        'email',
        'phone',
        'city',
        'country',
        'zip_code',
        'address',
        'notes',
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) str()->uuid();
            }
        });
    }
}
