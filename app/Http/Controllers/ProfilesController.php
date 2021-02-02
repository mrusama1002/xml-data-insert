<?php

namespace App\Http\Controllers;

use App\Models\PmsReportConfig;
use App\Models\Profile;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ProfilesController extends Controller
{
    public $profileType = [
        'GUEST' => 1,
        'COMPANY' => 2,
        'AGENT' => 3
    ];

    public function data_insert()
    {
        try {
            echo 'Please Wait It Will Take Few Minutes.';
            $xml = simplexml_load_string(file_get_contents(storage_path() . "/app/public/profiles.xml"));
            $availabilitydata = json_decode(json_encode($xml), TRUE);
            $data = $availabilitydata['LIST_G_C6']['G_C6'];
            $pmsReportConfig = PmsReportConfig::first();
            $existedProfileCount = Profile::count();
            $profileCreate = null;
            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportConfig['AccommodationId'],
                    'SourceId' => $pmsReportConfig['SourceId'],
                    'GroupId' => $pmsReportConfig['GroupId'],
                    'ProfileId' => $xmlData['C12']
                ];
                $existProfile = Profile::where($where)->first();

                if (empty($existProfile)) {
                    // CREATE PROFILE
                    $profileCreate[] = [
                        'MasterProfileId' => $existedProfileCount + 1,
                        "GroupId" => $pmsReportConfig['GroupId'],
                        "SourceId" => $pmsReportConfig['SourceId'],
                        "AccommodationId" => $pmsReportConfig['AccommodationId'],
                        "Property" => is_array($xmlData['C6']) ? null : $xmlData['C6'],
                        "ProfileId" => is_array($xmlData['C12']) ? null : $xmlData['C12'],
                        "ProfileTypeId" => is_array($xmlData['C15']) ? null : $this->profileType[$xmlData['C15']],
                        "Title" => is_array($xmlData['C18']) ? null : $xmlData['C18'],
                        "FirstName" => is_array($xmlData['C21']) ? null : $xmlData['C21'],
                        "MiddleName" => is_array($xmlData['C24']) ? null : $xmlData['C24'],
                        "LastName" => is_array($xmlData['C27']) ? null : $xmlData['C27'],
                        "Gender" => is_array($xmlData['C30']) ? null : $xmlData['C30'],
                        "LanguageCode" => is_array($xmlData['C33']) ? null : $xmlData['C33'],
                        "Language" => is_array($xmlData['C36']) ? null : $xmlData['C36'],
                        "Nationality" => is_array($xmlData['C39']) ? null : $xmlData['C39'],
                        "DateOfBirth" => is_array($xmlData['C42']) ? null : $xmlData['C42'],
                        "BirthPlace" => is_array($xmlData['C45']) ? null : $xmlData['C45'],
                        "Company" => is_array($xmlData['C48']) ? null : $xmlData['C48'],
                        "Notes" => is_array($xmlData['C51']) ? null : json_encode(explode("\n", $xmlData['C51'])),
                        "Preferences" => is_array($xmlData['C54']) ? null : $xmlData['C54'],
                        'Email' => is_array($xmlData['C54']) ? null : $xmlData['C54'],
                        'PhoneNumber' => is_array($xmlData['C57']) ? null : $xmlData['C57'],
                        'City' => is_array($xmlData['C60']) ? null : $xmlData['C60'],
                        'Country' => is_array($xmlData['C63']) ? null : $xmlData['C63'],
                        'PostalCode' => is_array($xmlData['C66']) ? null : $xmlData['C66'],
                        "StatusId" => 1,
                        "created_at" => is_array($xmlData['C69']) ? null : new Carbon($xmlData['C69']),
                        "updated_at" => is_array($xmlData['C72']) ? null : new Carbon($xmlData['C72']),
                    ];
                } else {
                    // Update Profile
                    $existProfile->update([
                        "Property" => is_array($xmlData['C6']) ? $existProfile->Property : $xmlData['C6'],
                        "ProfileId" => is_array($xmlData['C12']) ? $existProfile->ProfileId : $xmlData['C12'],
                        "ProfileTypeId" => is_array($xmlData['C15']) ? $existProfile->ProfileTypeId : $this->profileType[$xmlData['C15']],
                        "Title" => is_array($xmlData['C18']) ? $existProfile->Title : $xmlData['C18'],
                        "FirstName" => is_array($xmlData['C21']) ? $existProfile->FirstName : $xmlData['C21'],
                        "MiddleName" => is_array($xmlData['C24']) ? $existProfile->MiddleName : $xmlData['C24'],
                        "LastName" => is_array($xmlData['C27']) ? $existProfile->LastName : $xmlData['C27'],
                        "Gender" => is_array($xmlData['C30']) ? $existProfile->Gender : $xmlData['C30'],
                        "LanguageCode" => is_array($xmlData['C33']) ? $existProfile->LanguageCode : $xmlData['C33'],
                        "Language" => is_array($xmlData['C36']) ? $existProfile->Language : $xmlData['C36'],
                        "Nationality" => is_array($xmlData['C39']) ? $existProfile->Nationality : $xmlData['C39'],
                        "DateOfBirth" => is_array($xmlData['C42']) ? $existProfile->DateOfBirth : $xmlData['C42'],
                        "BirthPlace" => is_array($xmlData['C45']) ? $existProfile->BirthPlace : $xmlData['C45'],
                        "Company" => is_array($xmlData['C48']) ? $existProfile->Company : $xmlData['C48'],
                        "Notes" => is_array($xmlData['C51']) ? $existProfile->Notes : json_encode(explode("\n", $xmlData['C51'])),
                        "Preferences" => is_array($xmlData['C54']) ? $existProfile->Preferences : $xmlData['C54'],
                        'Email' => is_array($xmlData['C54']) ? $existProfile->Email : $xmlData['C54'],
                        'PhoneNumber' => is_array($xmlData['C57']) ? $existProfile->PhoneNumber : $xmlData['C57'],
                        'City' => is_array($xmlData['C60']) ? $existProfile->City : $xmlData['C60'],
                        'Country' => is_array($xmlData['C63']) ? $existProfile->Country : $xmlData['C63'],
                        'PostalCode' => is_array($xmlData['C66']) ? $existProfile->PostalCode : $xmlData['C66'],
                        "StatusId" => 1,
                    ]);
                }
                $existedProfileCount++;
            }
            if ($profileCreate)
                Profile::insert($profileCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
