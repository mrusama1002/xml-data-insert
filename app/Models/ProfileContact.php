<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileContact extends Model
{
    protected $table = 'profilecontacts';
    public $timestamps = false;
    protected $guarded = ['_token'];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId', 'ProfileId');
    }

    public function setPhoneNumberAttribute($value)
    {
        $this->attributes['PhoneNumber'] = str_replace(' ', '', $value);
    }
}
