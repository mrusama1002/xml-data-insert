<?php

namespace App\Traits;

use App\Models\Booking;
use App\Models\Email;
use App\Models\Profile;
use Carbon\Carbon;

trait stayData
{
    public $bookingStatus = [
        'Confirmed' => 'EXPECTED',
        'Expected Today' => 'EXPECTED',
        'Checked in today' => 'CHECKED_IN',
        'In house' => 'CHECKED_IN',
        'Exp dep today' => 'CHECKED_IN',
        'Departed today' => 'CHECKED_OUT',
        'Cancelled' => 'CANCELLED',
        'Noshow' => 'NO_SHOW',
        'TRANSFERRED' => 'TRANSFERRED',
        'OTHER' => 'OTHER',
        'Modified' => 'Modified',
        'Pending' => 'Pending'
    ];

    public function check_stay_xml_type($get_file_path)
    {
        $xml = simplexml_load_string(file_get_contents($get_file_path));
        $availabilitydata = json_decode(json_encode($xml), TRUE);
        $data = @$availabilitydata['LIST_G_C6']['G_C6'];
        if ($data) {
            return $this->stay_data_insert_in_RHOTEL($availabilitydata);
        } else {
            return $this->stay_data_insert_in_ewaa_hotel($availabilitydata);
        }
    }

    public function stay_data_insert_in_RHOTEL($availabilitydata)
    {
        try {
            $data = $availabilitydata['LIST_G_C6']['G_C6'];
            $pmsReportConfig = Email::first();
            $stayCreate = null;

            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportConfig['AccommodationId'],
                    'SourceId' => $pmsReportConfig['SourceId'],
                    'GroupId' => $pmsReportConfig['GroupId'],
                    'ConfNumber' => $xmlData['C48']
                ];
                $existStay = Booking::where($where)->first();
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
                Booking::insert($stayCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return response($exception->getMessage());
        }
    }

    public function stay_data_insert_in_ewaa_hotel($availabilitydata)
    {
        try {
            $data = $availabilitydata['stay'];
            $pmsReportEmail = Email::first();
            $reservationCreate = null;

            foreach ($data as $key => $xmlData) {
                $whereProfile = [
                    'AccommodationId' => $pmsReportEmail['AccommodationId'],
                    'SourceId' => $pmsReportEmail['SourceId'],
                    'GroupId' => $pmsReportEmail['GroupId'],
                    'Profile_PMSId' => $xmlData['profileid'],
                ];
                $profileId = Profile::select('ProfileId')->where($whereProfile)->first();

                $where = [
                    'AccommodationId' => $pmsReportEmail['AccommodationId'],
                    'SourceId' => $pmsReportEmail['SourceId'],
                    'PmsBookingId' => $xmlData['resno'],
                ];
                $existReservation = Booking::where($where)->first();
                if (empty($existReservation)) {
                    $reservationCreate[] = [
                        "ProfileId" => @$profileId->ProfileId,
                        "SourceId" => $pmsReportEmail['SourceId'],
                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
                        "Reference" => is_array($xmlData['refid']) ? null : $xmlData['refid'],
                        "PmsBookingId" => is_array($xmlData['resno']) ? null : $xmlData['resno'],
                        "CheckInDate" => is_array($xmlData['arrdate']) ? null : new Carbon(date('d-m-Y', strtotime($xmlData['arrdate']))),
                        "CheckOutDate" => is_array($xmlData['depdate']) ? null : new Carbon(date('d-m-Y', strtotime($xmlData['depdate']))),
                        "GuestIds" => is_array($xmlData['profileid']) ? null : $xmlData['profileid'],
                        "MarketCode" => is_array($xmlData['marketcode']) ? null : $xmlData['marketcode'],
                        "RateCode" => is_array($xmlData['ratecode']) ? null : $xmlData['ratecode'],
                        "AreaId" => is_array($xmlData['roomno']) ? null : $xmlData['roomno'],
                        "RequestedAreaTypeId" => is_array($xmlData['roomtype']) ? null : $xmlData['roomtype'],
                        "SourceCode" => is_array($xmlData['sourcecode']) ? null : $xmlData['sourcecode'],
                        "RoomRevenue" => is_array($xmlData['roomrevnet']) ? null : $xmlData['roomrevnet'],
                        "FnBRevenue" => is_array($xmlData['fbrevnet']) ? null : $xmlData['fbrevnet'],
                        "OtherRevenue" => is_array($xmlData['otherrevnet']) ? null : $xmlData['otherrevnet'],
                        "GrossAmount" => is_array($xmlData['totalnet']) ? null : $xmlData['totalnet'],
                        "CreateDate" => Carbon::now(),
                        "LastModified" => Carbon::now(),
                    ];
                } else {
                    // UPDATE RESERVATION
                    $existReservation->update([
                        "SourceId" => $pmsReportEmail['SourceId'],
                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
                        "PmsBookingId" => is_array($xmlData['resno']) ? $existReservation->PmsBookingId : $xmlData['resno'],
                        "CheckInDate" => is_array($xmlData['arrdate']) ? $existReservation->CheckInDate : new Carbon(date('d-m-Y', strtotime($xmlData['arrdate']))),
                        "CheckOutDate" => is_array($xmlData['depdate']) ? $existReservation->CheckOutDate : new Carbon(date('d-m-Y', strtotime($xmlData['depdate']))),
                        "GuestIds" => is_array($xmlData['profileid']) ? $existReservation->GuestIds : $xmlData['profileid'],
                        "MarketCode" => is_array($xmlData['marketcode']) ? $existReservation->MarketCode : $xmlData['marketcode'],
                        "RateCode" => is_array($xmlData['ratecode']) ? $existReservation->RateCode : $xmlData['ratecode'],
                        "AreaId" => is_array($xmlData['roomno']) ? $existReservation->AreaId : $xmlData['roomno'],
                        "RequestedAreaTypeId" => is_array($xmlData['roomtype']) ? $existReservation->RoomType : $xmlData['roomtype'],
                        "SourceCode" => is_array($xmlData['sourcecode']) ? $existReservation->SourceCode : $xmlData['sourcecode'],
                        "RoomRevenue" => is_array($xmlData['roomrevnet']) ? $existReservation->RoomRevenue : $xmlData['roomrevnet'],
                        "FnBRevenue" => is_array($xmlData['fbrevnet']) ? $existReservation->FnBRevenue : $xmlData['fbrevnet'],
                        "OtherRevenue" => is_array($xmlData['otherrevnet']) ? $existReservation->OtherRevenue : $xmlData['otherrevnet'],
                        "GrossAmount" => is_array($xmlData['totalnet']) ? $existReservation->GrossAmount : $xmlData['totalnet'],
                        "LastModified" => Carbon::now(),
                    ]);
                }
            }
            if ($reservationCreate)
                Booking::insert($reservationCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return response($exception->getMessage());
        }
    }
}
