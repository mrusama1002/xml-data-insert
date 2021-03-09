<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use App\Traits\LoyaltyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Profile extends Model
{
    protected $table = 'profiles';
    protected $primaryKey = 'ProfileId';
    const CREATED_AT = 'CreateDate';
    const UPDATED_AT = 'LastModified';
    protected $guarded = ['_token'];

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'AccommodationId', 'AccommodationId');
    }


    function addresses()
    {
        return $this->hasMany(ProfileAddress::class, 'ProfileId', 'ProfileId');
    }

    function address()
    {
        return $this->hasOne(ProfileAddress::class, 'ProfileId', 'ProfileId');
    }

    function master_profile()
    {
        return $this->belongsTo(MasterProfile::class, 'MasterProfileId', 'MasterProfileId');
    }

    function city()
    {
        return $this->hasOneThrough(City::class, ProfileAddress::class, 'ProfileId', 'CityId', 'ProfileId', 'CityId');
    }

    function country()
    {
        return $this->hasOneThrough(Country::class, ProfileAddress::class, 'ProfileId', 'CountryId', 'ProfileId', 'CountryId');
    }

    function emails()
    {
        return $this->hasMany(ProfileEmail::class, 'ProfileId', 'ProfileId');
    }

    function email()
    {
        return $this->hasOne(ProfileEmail::class, 'ProfileId', 'ProfileId');
    }

    function contacts()
    {
        return $this->hasMany(ProfileContact::class, 'ProfileId', 'ProfileId');
    }

    function contact()
    {
        return $this->hasOne(ProfileContact::class, 'ProfileId', 'ProfileId');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'ProfileId', 'MasterProfileId');
    }
}
