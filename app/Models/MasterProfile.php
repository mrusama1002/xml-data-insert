<?php

namespace App\Models;

use App\Scopes\FilterDeleteScope;
use App\Traits\LoyaltyTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MasterProfile extends Model
{
    protected $table = 'masterprofiles';
    protected $primaryKey = 'MasterProfileId';
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


    function country()
    {
        return $this->belongsTo(Country::class, 'CountryId', 'CountryId');
    }

    function city()
    {
        return $this->belongsTo(City::class, 'CountryId', 'CountryId');
    }

    /**
     * Filtering profile that the profile is belongs to the current
     * @param $model
     */

    /**
     * Join and get one email
     */
    function scopeJoinEmail($model)
    {
        $profilesTableName = Profile::getTableName();
        $profileEmailsTable = ProfileEmail::getTableName();
        $model->leftJoin(DB::raw("(SELECT e.ProfileId, MIN(e.Email) as Email FROM {$profileEmailsTable} as e
                    WHERE e.Email IS NOT NULL and e.Email != '' and LOWER(e.Email) != 'null'
                GROUP BY e.ProfileId)
               emails"),
            function ($join) use ($profilesTableName) {
                $join->on($profilesTableName . '.ProfileId', '=', 'emails.ProfileId');
            });
    }

    /**
     * Join and get one contact
     */
    function scopeJoinContact($model)
    {
        $profilesTableName = Profile::getTableName();
        $profileContactsTable = ProfileContact::getTableName();
        $model->leftJoin(DB::raw("(SELECT c.ProfileId, MIN(c.PhoneNumber) as PhoneNumber FROM {$profileContactsTable} as c
                    WHERE c.PhoneNumber IS NOT NULL and c.PhoneNumber != '' and LOWER(c.PhoneNumber) != 'null'
                GROUP BY c.ProfileId)
               contacts"),
            function ($join) use ($profilesTableName) {
                $join->on($profilesTableName . '.ProfileId', '=', 'contacts.ProfileId');
            });
    }

    public static function makeMasterProfile($profile)
    {
        if ($profile->ProfileId == $profile->MasterProfileId) {
            $masterProfileId = $profile->MasterProfileId;

            $profileData = collect($profile->toArray())->only(['MasterProfileId', 'OwnerId', 'ProfileTypeId', 'SourceId', 'Profile_PMSId', 'AccommodationId', 'GroupId', 'Title', 'FirstName', 'MiddleName', 'LastName', 'Gender', 'LanguageCode', 'LanguageDescription', 'Nationality', 'BirthDate', 'BirthPlace', 'MarketCode', 'CompanyName', 'TravelAgentName', 'RecordId', 'CanonId', 'Notes', 'HasLoyalty', 'LoyaltyPoints', 'MemberShipTypeId', 'MemberShipId', 'CardId', 'CardNo', 'ProfileImage', 'StatusId', 'Comment', 'ProfileCreateDate', 'ProfileLastModified', 'CustomId'])->toArray();

            $address = ProfileAddress::whereIn('ProfileId', function ($q) use ($profile) {
                $q->select('ProfileId')->from(Profile::getTableName())
                    ->where('MasterProfileId', $profile->MasterProfileId);
            })->orderBy('CityId', 'desc')->orderBy('CountryId', 'desc')->orderBy('Line1', 'desc')->orderBy('IsPrimary', 'desc')->first();

            $address = $address ? $address->only(['Line1', 'Line2', 'Line3', 'Line4', 'CountryId', 'CityId', 'CountryCode', 'ZipCode']) : [];
            $Email = ProfileEmail::where('ProfileId', $masterProfileId)->orderBy('IsPrimary', 'desc')->limit(1)->value('Email');
            $PhoneNumber = ProfileContact::where('ProfileId', $masterProfileId)->orderBy('IsPrimary', 'desc')->limit(1)->value('PhoneNumber');

            ProfileLoyaltyCard::where('CardId', $profile->CardId)
                ->update(['ProfileId' => $profile->MasterProfileId]);

            $profileIds = Profile::where('MasterProfileId', $masterProfileId)->pluck('ProfileId');
            $bookingData = Booking::select(
                DB::raw('SUM(GrossAmount) AS TotalRevenueSum'),
                DB::raw('SUM(DATEDIFF(CheckOutDate,CheckInDate)) as TotalNumberOfNights'),
                DB::raw('SUM(OtherRevenue) as TotalOtherRevenue'),
                DB::raw('count(ProfileId) as TotalNumberOfBookings'),
                DB::raw('SUM(RoomRevenue) as TotalRoomRevenue'),
                DB::raw('sum(FnBRevenue) as TotalFnBRevenue')
            )->whereIn('ProfileId', $profileIds)->groupBy('ProfileId')->first();

            $bookingData = $bookingData ? $bookingData->toArray() : [];

            $revenueByGroup = Booking::select('Status', DB::raw('SUM(GrossAmount) as revenue'))
                ->whereIn('Status', ['EXPECTED', 'CHECKED_OUT'])
                ->whereIn('ProfileId', $profileIds)
                ->groupBy('Status')
                ->get()->keyBy('Status')->toArray();

            $TotalConfirmRevenue = @$revenueByGroup['CHECKED_OUT']['revenue'] ?? 0;
            $TotalPendingRevenue = @$revenueByGroup['EXPECTED']['revenue'] ?? 0;

            $masterProfileData = array_merge($profileData, $address, compact('Email', 'PhoneNumber'), compact('TotalPendingRevenue', 'TotalConfirmRevenue'), $bookingData);

            MasterProfile::updateOrCreate(['MasterProfileId' => $masterProfileId], $masterProfileData);
        }
    }

}
