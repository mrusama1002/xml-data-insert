<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    protected $table = 'countries';
    protected $primaryKey = 'CountryId';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    function cities()
    {
        return $this->hasMany(City::class,'CountryId','CountryId');
    }

    function profiles()
    {
        return $this->hasManyThrough(Profile::class,ProfileAddress::class, 'CountryId', 'ProfileId','CountryId','ProfileId');
    }

    function addresses()
    {
        return $this->hasMany(ProfileAddress::class,'ProfileId','ProfileId');
    }

}
