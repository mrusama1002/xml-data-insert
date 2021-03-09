<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';
    protected $primaryKey = 'CityId';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    function country()
    {
        return $this->belongsTo(Country::class, 'CountryId', 'CountryId');
    }

    function profiles()
    {
        return $this->hasManyThrough(Profile::class,ProfileAddress::class, 'CityId', 'ProfileId','CityId','ProfileId');
    }

    function addresses()
    {
        return $this->hasMany(ProfileAddress::class,'ProfileId','ProfileId');
    }

}
