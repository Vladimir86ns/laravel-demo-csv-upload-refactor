<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CustomerInformation extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'gender',
        'birth_day',
        'country',
        'city',
        'address',
        'address_number',
        'email',
        'contact_phone',
    ];
}
