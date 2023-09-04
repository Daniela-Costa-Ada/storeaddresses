<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    use HasFactory;
    public $timestamps = false;
    protected $fillable = [
        'postal_code',
        'street_number',
        'complement'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id'
    ];
    public function store()
    {
        return $this->belongsTo(Store::class, 'foreing_id', 'id');
    }
}
