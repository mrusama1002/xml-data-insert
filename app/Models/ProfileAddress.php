<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProfileAddress extends Model
{
    protected $table = 'profileaddresses';
    public $timestamps = false;
    protected $guarded = ['_token'];
    public $incrementing = false;
    protected $primaryKey = null;

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId', 'ProfileId');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'CountryId', 'CountryId');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'CityId', 'CityId');
    }
}
