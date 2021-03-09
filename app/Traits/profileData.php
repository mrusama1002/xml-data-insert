<?php

namespace App\Traits;

use App\Models\City;
use App\Models\Country;
use App\Models\Email;
use App\Models\PmsReportConfig;
use App\Models\Profile;
use App\Models\ProfileAddress;
use App\Models\ProfileContact;
use App\Models\ProfileEmail;
use Carbon\Carbon;

trait profileData
{
    public $profileType = [
        'GUEST' => 1,
        'Individual' => 1,
        'COMPANY' => 2,
        'AGENT' => 3
    ];

    public function check_profiles_xml_type($get_file_path)
    {
        $xml = simplexml_load_string(file_get_contents($get_file_path));
        $availabilitydata = json_decode(json_encode($xml), TRUE);
        $data = @$availabilitydata['LIST_G_C6']['G_C6'];
        if ($data) {
            return $this->profiles_data_insert_in_RHOTEL($availabilitydata);
        } else {
            return $this->profiles_data_insert_in_Ewaa_Hotel($availabilitydata);
        }
    }

    public function profiles_data_insert_in_RHOTEL($availabilitydata)
    {
        try {
            $data = $availabilitydata['LIST_G_C6']['G_C6'];
            $pmsReportEmail = Email::first();
            $existedProfileCount = Profile::count();
            $profileCreate = null;
            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportEmail['AccommodationId'],
                    'SourceId' => $pmsReportEmail['SourceId'],
                    'GroupId' => $pmsReportEmail['GroupId'],
                    'Profile_PMSId' => $xmlData['C12']
                ];
                $existProfile = Profile::where($where)->first();

                if (empty($existProfile)) {
                    // CREATE PROFILE
                    $profile = Profile::create([
                        'OwnerId' => 1,
                        'MasterProfileId' => $existedProfileCount + 1,
                        "GroupId" => $pmsReportEmail['GroupId'],
                        "SourceId" => $pmsReportEmail['SourceId'],
                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
                        "Property" => is_array(@$xmlData['C6']) ? null : @$xmlData['C6'],
                        "Profile_PMSId" => is_array(@$xmlData['C12']) ? null : @$xmlData['C12'],
                        "ProfileTypeId" => is_array(@$xmlData['C15']) ? null : $this->profileType[$xmlData['C15']],
                        "Title" => is_array(@$xmlData['C18']) ? null : @$xmlData['C18'],
                        "FirstName" => is_array(@$xmlData['C21']) ? null : @$xmlData['C21'],
                        "MiddleName" => is_array(@$xmlData['C24']) ? null : @$xmlData['C24'],
                        "LastName" => is_array(@$xmlData['C27']) ? null : @$xmlData['C27'],
                        "Gender" => is_array(@$xmlData['C30']) ? null : @$xmlData['C30'],
                        "LanguageCode" => is_array(@$xmlData['C33']) ? null : @$xmlData['C33'],
                        "Language" => is_array(@$xmlData['C36']) ? null : @$xmlData['C36'],
                        "Nationality" => is_array(@$xmlData['C39']) ? null : @$xmlData['C39'],
                        "DateOfBirth" => is_array(@$xmlData['C42']) ? null : @$xmlData['C42'],
                        "BirthPlace" => is_array(@$xmlData['C45']) ? null : @$xmlData['C45'],
                        "Company" => is_array(@$xmlData['C48']) ? null : @$xmlData['C48'],
                        "Notes" => is_array(@$xmlData['C51']) ? null : json_encode(explode("\n", $xmlData['C51'])),
                        "Preferences" => is_array(@$xmlData['C54']) ? null : @$xmlData['C54'],
                        'PostalCode' => is_array(@$xmlData['C66']) ? null : @$xmlData['C66'],
                        "StatusId" => 1,
                        "created_at" => is_array(@$xmlData['C69']) ? null : new Carbon(@$xmlData['C69']),
                        "updated_at" => is_array(@$xmlData['C72']) ? null : new Carbon(@$xmlData['C72']),
                    ]);

                    $profileIdUnique = $profile->ProfileId;

                    if (!empty($xmlData['C57'])) {
                        ProfileContact::create([
                            'ProfileId' => $profileIdUnique,
                            'PhoneNumber' => is_array(@$xmlData['C57']) ? null : @$xmlData['C57'],
                            'SourceId' => 13,
                            'CreateDate' => is_array(@$xmlData['C69']) ? null : new Carbon(str_replace("/", "-", @$xmlData['C69'])),
                            'LastModified' => is_array(@$xmlData['C72']) ? null : new Carbon(str_replace("/", "-", @$xmlData['C72']))
                        ]);
                    }
                    if (!empty($xmlData['C54'])) {
                        ProfileEmail::create([
                            'ProfileId' => $profileIdUnique,
                            'Email' => is_array(@$xmlData['C54']) ? null : strtolower(@$xmlData['C54']),
                            'SourceId' => 13,
                            'CreateDate' => is_array(@$xmlData['C69']) ? null : new Carbon(str_replace("/", "-", @$xmlData['C69'])),
                            'LastModified' => is_array(@$xmlData['C72']) ? null : new Carbon(str_replace("/", "-", @$xmlData['C72']))
                        ]);
                    }
                    $country = Country::where('CountryCodeISO2', $xmlData['C63'])
                        ->orWhere('CountryName', $xmlData['C63'])
                        ->orWhere('CountryCode', $xmlData['C63'])->first();
                    $city = City::where('CityName', $xmlData['C60'])
                        ->orWhere('CityCode', $xmlData['C60'])->first();


                    ProfileAddress::create([
                        'ProfileId' => $profileIdUnique,
                        'CountryId' => is_array(@$country['CountryId']) ? null : @$country['CountryId'],
                        'CountryName' => is_array(@$country['CountryName']) ? null : @$country['CountryName'],
                        'CityId' => is_array(@$city['CityId']) ? null : @$city['CityId'],
                        'CityName' => is_array(@$city['CityName']) ? null : @$city['CityName'],
                        'ZipCode' => is_array(@$xmlData['ZipCode']) ? null : @$xmlData['ZipCode'],
                        'SourceId' => 13,
                        'CreateDate' => is_array(@$xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", @$xmlData['createdate'])),
                        'LastModified' => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", @$xmlData['updatedate']))
                    ]);
                    Profile::find($profileIdUnique)->update([
                        'MasterProfileId' => $profileIdUnique
                    ]);

                } else {
                    // Update Profile
                    $existProfile->update([
                        "Property" => is_array(@$xmlData['C6']) ? $existProfile->Property : $xmlData['C6'],
                        "ProfileId" => is_array(@$xmlData['C12']) ? $existProfile->ProfileId : $xmlData['C12'],
                        "ProfileTypeId" => is_array(@$xmlData['C15']) ? $existProfile->ProfileTypeId : $this->profileType[$xmlData['C15']],
                        "Title" => is_array(@$xmlData['C18']) ? $existProfile->Title : $xmlData['C18'],
                        "FirstName" => is_array(@$xmlData['C21']) ? $existProfile->FirstName : $xmlData['C21'],
                        "MiddleName" => is_array(@$xmlData['C24']) ? $existProfile->MiddleName : $xmlData['C24'],
                        "LastName" => is_array(@$xmlData['C27']) ? $existProfile->LastName : $xmlData['C27'],
                        "Gender" => is_array(@$xmlData['C30']) ? $existProfile->Gender : $xmlData['C30'],
                        "LanguageCode" => is_array(@$xmlData['C33']) ? $existProfile->LanguageCode : $xmlData['C33'],
                        "Language" => is_array(@$xmlData['C36']) ? $existProfile->Language : $xmlData['C36'],
                        "Nationality" => is_array(@$xmlData['C39']) ? $existProfile->Nationality : $xmlData['C39'],
                        "DateOfBirth" => is_array(@$xmlData['C42']) ? $existProfile->DateOfBirth : $xmlData['C42'],
                        "BirthPlace" => is_array(@$xmlData['C45']) ? $existProfile->BirthPlace : $xmlData['C45'],
                        "Company" => is_array(@$xmlData['C48']) ? $existProfile->Company : $xmlData['C48'],
                        "Notes" => is_array(@$xmlData['C51']) ? $existProfile->Notes : json_encode(explode("\n", $xmlData['C51'])),
                        "Preferences" => is_array(@$xmlData['C54']) ? $existProfile->Preferences : $xmlData['C54'],
                        'PostalCode' => is_array(@$xmlData['C66']) ? $existProfile->PostalCode : $xmlData['C66'],
                        "StatusId" => 1,
                    ]);
                }
            }
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

    public function profiles_data_insert_in_Ewaa_Hotel($availabilitydata)
    {
        try {
            $data = $availabilitydata['profile'];
            $pmsReportEmail = Email::first();
            $existedProfileCount = Profile::count();
            $profileCreate = null;
            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportEmail['AccommodationId'],
                    'SourceId' => $pmsReportEmail['SourceId'],
                    'GroupId' => $pmsReportEmail['GroupId'],
                    'Profile_PMSId' => $xmlData['profileid']
                ];
                $existProfile = Profile::where($where)->first();
                if (empty($existProfile)) {
                    // CREATE PROFILE
                    $profile = Profile::create([
                        'OwnerId' => 1,
                        "ProfileTypeId" => is_array(@$xmlData['profiletype']) ? null : $this->profileType[$xmlData['profiletype']],
                        "SourceId" => $pmsReportEmail['SourceId'],
                        "Profile_PMSId" => is_array(@$xmlData['profileid']) ? null : $xmlData['profileid'],
                        'MasterProfileId' => $existedProfileCount + 1,
                        "GroupId" => $pmsReportEmail['GroupId'],
                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
                        "Title" => is_array(@$xmlData['title']) ? null : $xmlData['title'],
                        "FirstName" => is_array(@$xmlData['firstname']) ? null : $xmlData['firstname'],
                        "LastName" => is_array(@$xmlData['lastname']) ? null : $xmlData['lastname'],
                        "Gender" => is_array(@$xmlData['gender']) ? null : $xmlData['gender'],
                        "LanguageCode" => is_array(@$xmlData['language']) ? null : $xmlData['language'],
                        "Nationality" => is_array(@$xmlData['nationality']) ? null : $xmlData['nationality'],
                        "StatusId" => 1,
                        "CreateDate" => is_array(@$xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", $xmlData['createdate'])),
                        "LastModified" => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", $xmlData['updatedate']))
                    ]);

                    $profileIdUnique = $profile->ProfileId;

                    if (!empty($xmlData['telephone'])) {
                        ProfileContact::create([
                            'ProfileId' => $profileIdUnique,
                            'PhoneNumber' => is_array(@$xmlData['telephone']) ? null : $xmlData['telephone'],
                            'SourceId' => 13,
                            'CreateDate' => is_array(@$xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", $xmlData['createdate'])),
                            'LastModified' => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", $xmlData['updatedate']))
                        ]);
                    }
                    if (!empty($xmlData['email'])) {
                        ProfileEmail::create([
                            'ProfileId' => $profileIdUnique,
                            'Email' => is_array(@$xmlData['email']) ? null : strtolower($xmlData['email']),
                            'SourceId' => 13,
                            'CreateDate' => is_array(@$xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", $xmlData['createdate'])),
                            'LastModified' => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", $xmlData['updatedate']))
                        ]);
                    }
                    $country = Country::where('CountryCodeISO2', $xmlData['country'])
                        ->orWhere('CountryName', $xmlData['country'])
                        ->orWhere('CountryCode', $xmlData['country'])->first();

                    ProfileAddress::create([
                        'ProfileId' => $profileIdUnique,
                        'CountryId' => is_array(@$country['CountryId']) ? null : @$country['CountryId'],
                        'CountryName' => is_array(@$country['CountryName']) ? null : @$country['CountryName'],
                        'CityId' => is_array(@$xmlData['city']) ? null : @$xmlData['city'],
                        'CityName' => is_array(@$xmlData['city']) ? null : @$xmlData['city'],
                        'ZipCode' => is_array(@$xmlData['ZipCode']) ? null : @$xmlData['ZipCode'],
                        'SourceId' => 13,
                        'CreateDate' => is_array(@$xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", @$xmlData['createdate'])),
                        'LastModified' => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", @$xmlData['updatedate']))
                    ]);
                    Profile::find($profileIdUnique)->update([
                        'MasterProfileId' => $profileIdUnique
                    ]);

                } else {
                    // Update Profile
                    $existProfile->update([
                        "Profile_PMSId" => is_array(@$xmlData['profileid']) ? $existProfile->ProfileId : $xmlData['profileid'],
                        "ProfileTypeId" => is_array(@$xmlData['profiletype']) ? $existProfile->ProfileTypeId : $this->profileType[$xmlData['profiletype']],
                        "Title" => is_array(@$xmlData['title']) ? $existProfile->Title : $xmlData['title'],
                        "FirstName" => is_array(@$xmlData['firstname']) ? $existProfile->FirstName : $xmlData['firstname'],
                        "LastName" => is_array(@$xmlData['lastname']) ? $existProfile->LastName : $xmlData['lastname'],
                        "Gender" => is_array(@$xmlData['gender']) ? $existProfile->Gender : $xmlData['gender'],
                        "Language" => is_array(@$xmlData['language']) ? $existProfile->Language : $xmlData['language'],
                        "Nationality" => is_array(@$xmlData['nationality']) ? $existProfile->Nationality : $xmlData['nationality'],
                        'Email' => is_array(@$xmlData['email']) ? $existProfile->Email : $xmlData['email'],
                        'PhoneNumber' => is_array(@$xmlData['telephone']) ? $existProfile->PhoneNumber : $xmlData['telephone'],
                        'Country' => is_array(@$xmlData['country']) ? $existProfile->Country : $xmlData['country'],
                        "StatusId" => 1,
                        "updated_at" => is_array(@$xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", $xmlData['updatedate']))
                    ]);
                }
                $existedProfileCount++;
            }
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
