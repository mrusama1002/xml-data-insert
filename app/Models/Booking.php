<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LoyaltyTrait;

class Booking extends Model
{
    protected $table = 'bookings';
    protected $primaryKey = 'BookingId';
    const CREATED_AT = 'CreateDate';
    const UPDATED_AT = 'LastModified';
    protected $guarded = [];

    public function toArray()
    {
        $toArray = parent::toArray();
//        $toArray['NetAmount'] = $this->NetAmount;
//        $toArray['TaxAmount'] = $this->TaxAmount;
//        $toArray['GrossAmount'] = $this->GrossAmount;
        return $toArray;
    }

    function scopeFilterAccommodation($model, $accommodationIds)
    {
        if (is_array($accommodationIds))
            $model->whereIn('AccommodationId', $accommodationIds);
        else
            $model->where('AccommodationId', $accommodationIds);
    }

    function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'AccommodationId', 'AccommodationId');
    }


    function profile()
    {
        return $this->belongsTo(Profile::class, 'ProfileId', 'MasterProfileId');
    }

    public static function getTableName()
    {
        return with(new static)->getTable();
    }
}
