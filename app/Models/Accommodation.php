<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use Illuminate\Database\Eloquent\Model;

class Accommodation extends Model
{
    protected $table = 'Accommodations';
    protected $primaryKey = 'AccommodationId';
    protected $hidden = ['pivot'];
    const CREATED_AT = 'CreateDate';
    const UPDATED_AT = 'LastModified';

    public static function getTableName()
    {
        return with(new static)->getTable();
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'UserAccommodationMappings', 'AccommodationId', 'UserId', 'AccommodationId', 'UserId');
    }

    function services()
    {
        return $this->belongsToMany(Service::class, ServiceMapping::getTableName(), 'AccommodationId', 'ServiceId', 'AccommodationId', 'ServiceId');
    }

    function profiles()
    {
        return $this->hasMany(Profile::class, 'AccommodationId', 'AccommodationId');
    }

    function bookings()
    {
        return $this->hasMany(Booking::class, 'AccommodationId', 'AccommodationId');
    }

    function satisfaction_categories()
    {
        return $this->belongsToMany(SatisfactionCategory::class, SatisfactionCategoryMapping::getTableName(), 'AccommodationId', 'SatisfactionCategoryId', 'AccommodationId', 'SatisfactionCategoryId');
    }

    function city()
    {
        return $this->belongsTo(City::class, 'CityId', 'CityId');
    }

    function country()
    {
        return $this->belongsTo(Country::class, 'CountryId', 'CountryId');
    }

    function group()
    {
        return $this->belongsTo(AccommodationGroup::class, 'GroupId', 'GroupId');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'StatusId', 'StatusId');
    }

    function c_journey_templates()
    {
        return $this->hasMany(CJourneyTemplate::class, 'AccommodationId', 'AccommodationId');
    }

    function scopeFilterId($model, $id)
    {
        $model->where($this->primaryKey, $id);
    }

    public static function getSurveyUrl($accommodationId, $showHttp = true)
    {
        $surveyDomain = self::select('sd.Domain', 'sd.IsHttps')->where('AccommodationId', $accommodationId)
            ->join(SurveyDomain::getTableName() . ' as sd', 'sd.Id', self::getTableName() . '.SurveyDomainId')
            ->limit(1)->first();
        $url = $surveyDomain->Domain;
        if ($showHttp)
            $url = ($surveyDomain->IsHttps ? 'https' : 'http') . '://' . $url;
        return $url;
    }

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new FilterDeleteScope);
    }
}
