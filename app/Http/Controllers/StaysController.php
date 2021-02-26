<?php

namespace App\Http\Controllers;

use App\Classes\mailAttach;
use App\Models\PmsReportConfig;
use App\Models\Stay;
use App\Traits\getFileFromMail;
use Carbon\Carbon;

class StaysController extends Controller
{
    use getFileFromMail;
    
    public function data_insert()
    {
        try {
            $xml = simplexml_load_string(file_get_contents($this->get_data_from_mail()));
            $availabilitydata = json_decode(json_encode($xml), TRUE);
            $data = $availabilitydata['LIST_G_C6']['G_C6'];
            $pmsReportConfig = PmsReportConfig::first();
            $stayCreate = null;

            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportConfig['AccommodationId'],
                    'SourceId' => $pmsReportConfig['SourceId'],
                    'GroupId' => $pmsReportConfig['GroupId'],
                    'ConfNumber' => $xmlData['C48']
                ];
                $existStay = Stay::where($where)->first();
                if (empty($existStay)) {
                    // CREATE STAY
                    $stayCreate[] = [
                        "GroupId" => $pmsReportConfig['GroupId'],
                        "SourceId" => $pmsReportConfig['SourceId'],
                        "AccommodationId" => $pmsReportConfig['AccommodationId'],
                        "RoomRevenue" => is_array($xmlData['C6']) ? null : $xmlData['C6'],
                        "FBRevenue" => is_array($xmlData['C9']) ? null : $xmlData['C9'],
                        "TotalRevenue" => is_array($xmlData['C12']) ? null : $xmlData['C12'],
                        "OriginCode" => is_array($xmlData['C15']) ? null : $xmlData['C15'],
                        "CompanyName" => is_array($xmlData['C18']) ? null : $xmlData['C18'],
                        "CurrencyCode" => is_array($xmlData['C21']) ? null : $xmlData['C21'],
                        "DepartureDate" => is_array($xmlData['C24']) ? null : $xmlData['C24'],
                        "GuestNameID" => is_array($xmlData['C27']) ? null : $xmlData['C27'],
                        "MarketCode" => is_array($xmlData['C30']) ? null : $xmlData['C30'],
                        "PaymentMethod" => is_array($xmlData['C33']) ? null : $xmlData['C33'],
                        "Resort" => is_array($xmlData['C36']) ? null : $xmlData['C36'],
                        "RateCode" => is_array($xmlData['C39']) ? null : $xmlData['C39'],
                        "InsertDate" => is_array($xmlData['C42']) ? null : $xmlData['C42'],
                        "UpdateDate" => is_array($xmlData['C45']) ? null : $xmlData['C45'],
                        "ConfNumber" => is_array($xmlData['C48']) ? null : $xmlData['C48'],
                        "RoomNo" => is_array($xmlData['C51']) ? null : $xmlData['C51'],
                        "RoomType" => is_array($xmlData['C54']) ? null : $xmlData['C54'],
                        "Custom1" => is_array($xmlData['C60']) ? null : $xmlData['C60'],
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ];
                } else {
                    // UPDATE STAY
                    $existStay->update([
                        "RoomRevenue" => is_array($xmlData['C6']) ? $existStay->RoomRevenue : $xmlData['C6'],
                        "FBRevenue" => is_array($xmlData['C9']) ? $existStay->FBRevenue : $xmlData['C9'],
                        "TotalRevenue" => is_array($xmlData['C12']) ? $existStay->TotalRevenue : $xmlData['C12'],
                        "OriginCode" => is_array($xmlData['C15']) ? $existStay->OriginCode : $xmlData['C15'],
                        "CompanyName" => is_array($xmlData['C18']) ? $existStay->CompanyName : $xmlData['C18'],
                        "CurrencyCode" => is_array($xmlData['C21']) ? $existStay->CurrencyCode : $xmlData['C21'],
                        "DepartureDate" => is_array($xmlData['C24']) ? $existStay->DepartureDate : $xmlData['C24'],
                        "GuestNameID" => is_array($xmlData['C27']) ? $existStay->GuestNameID : $xmlData['C27'],
                        "MarketCode" => is_array($xmlData['C30']) ? $existStay->MarketCode : $xmlData['C30'],
                        "PaymentMethod" => is_array($xmlData['C33']) ? $existStay->PaymentMethod : $xmlData['C33'],
                        "Resort" => is_array($xmlData['C36']) ? $existStay->Resort : $xmlData['C36'],
                        "RateCode" => is_array($xmlData['C39']) ? $existStay->RateCode : $xmlData['C39'],
                        "InsertDate" => is_array($xmlData['C42']) ? $existStay->InsertDate : $xmlData['C42'],
                        "UpdateDate" => is_array($xmlData['C45']) ? $existStay->UpdateDate : $xmlData['C45'],
                        "ConfNumber" => is_array($xmlData['C48']) ? $existStay->ConfNumber : $xmlData['C48'],
                        "RoomNo" => is_array($xmlData['C51']) ? $existStay->RoomNo : $xmlData['C51'],
                        "RoomType" => is_array($xmlData['C54']) ? $existStay->RoomType : $xmlData['C54'],
                        "Custom1" => is_array($xmlData['C60']) ? $existStay->Custom1 : $xmlData['C60'],
                        "updated_at" => Carbon::now(),
                    ]);
                }
            }
            if ($stayCreate)
                Stay::insert($stayCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
