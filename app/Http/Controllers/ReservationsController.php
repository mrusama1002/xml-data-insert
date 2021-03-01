<?php

namespace App\Http\Controllers;

use App\Classes\mailAttach;
use App\Models\PmsReportConfig;
use App\Models\Reservation;
use App\Traits\mailSettings;
use Carbon\Carbon;

class ReservationsController extends Controller
{
    use mailSettings;

    public function data_insert()
    {
        try {
            $xml = simplexml_load_string(file_get_contents($this->get_data_from_mail()));
            $availabilitydata = json_decode(json_encode($xml), TRUE);
            $data = $availabilitydata['LIST_G_C6']['G_C6'];
            $pmsReportConfig = PmsReportConfig::first();
            $reservationCreate = null;
            foreach ($data as $key => $xmlData) {
                $where = [
                    'AccommodationId' => $pmsReportConfig['AccommodationId'],
                    'SourceId' => $pmsReportConfig['SourceId'],
                    'GroupId' => $pmsReportConfig['GroupId'],
                    'ConfNumber' => $xmlData['C12']
                ];
                $existReservation = Reservation::where($where)->first();

                if (empty($existReservation)) {
                    $reservationCreate[] = [
                        "GroupId" => $pmsReportConfig['GroupId'],
                        "SourceId" => $pmsReportConfig['SourceId'],
                        "AccommodationId" => $pmsReportConfig['AccommodationId'],
                        "Property" => is_array($xmlData['C6']) ? null : $xmlData['C6'],
                        "ConfNumber" => is_array($xmlData['C12']) ? null : $xmlData['C12'],
                        "ArrivalDate" => is_array($xmlData['C15']) ? null : $xmlData['C15'],
                        "ArrivalTime" => is_array($xmlData['C18']) ? null : $xmlData['C18'],
                        "DepartureDate" => is_array($xmlData['C21']) ? null : $xmlData['C21'],
                        "DepartureTime" => is_array($xmlData['C24']) ? null : $xmlData['C24'],
                        "CancellationReason" => is_array($xmlData['C27']) ? null : $xmlData['C27'],
                        "CancellationNo" => is_array($xmlData['C30']) ? null : $xmlData['C30'],
                        "CancellationDate" => is_array($xmlData['C33']) ? null : $xmlData['C33'],
                        "OriginCode" => is_array($xmlData['C36']) ? null : $xmlData['C36'],
                        "CompanyName" => is_array($xmlData['C39']) ? null : $xmlData['C39'],
                        "InsertDate" => is_array($xmlData['C42']) ? null : $xmlData['C42'],
                        "InsertUser" => is_array($xmlData['C45']) ? null : $xmlData['C45'],
                        "UpdateDate" => is_array($xmlData['C48']) ? null : $xmlData['C48'],
                        "UpdateUser" => is_array($xmlData['C51']) ? null : $xmlData['C51'],
                        "CurrencyCode" => is_array($xmlData['C54']) ? null : $xmlData['C54'],
                        "ResvStatus" => is_array($xmlData['C57']) ? null : $xmlData['C57'],
                        "GuestNameID" => is_array($xmlData['C60']) ? null : $xmlData['C60'],
                        "LastName" => is_array($xmlData['C63']) ? null : $xmlData['C63'],
                        "MarketCode" => is_array($xmlData['C66']) ? null : $xmlData['C66'],
                        "PaymentMethod" => is_array($xmlData['C69']) ? null : $xmlData['C69'],
                        "RateCode" => is_array($xmlData['C72']) ? null : $xmlData['C72'],
                        "RoomNo" => is_array($xmlData['C75']) ? null : $xmlData['C75'],
                        "RoomType" => is_array($xmlData['C78']) ? null : $xmlData['C78'],
                        "SourceCode" => is_array($xmlData['C81']) ? null : $xmlData['C81'],
                        "RateAmount" => is_array($xmlData['C84']) ? null : $xmlData['C84'],
                        "TravelAgentName" => is_array($xmlData['C87']) ? null : $xmlData['C87'],
                        "Custom1" => is_array($xmlData['C96']) ? null : $xmlData['C96'],
                        "ReservationNotes" => is_array($xmlData['C99']) ? null : $xmlData['C99'],
                        "Custom2" => is_array($xmlData['C102']) ? null : $xmlData['C102'],
                        "Custom3" => is_array($xmlData['C108']) ? null : $xmlData['C108'],
                        "created_at" => Carbon::now(),
                        "updated_at" => Carbon::now(),
                    ];
                } else {
                    // UPDATE RESERVATION
                    $existReservation->update([
                        "GroupId" => $pmsReportConfig['GroupId'],
                        "SourceId" => $pmsReportConfig['SourceId'],
                        "AccommodationId" => $pmsReportConfig['AccommodationId'],
                        "Property" => is_array($xmlData['C6']) ? $existReservation->Property : $xmlData['C6'],
                        "ConfNumber" => is_array($xmlData['C12']) ? $existReservation->ConfNumber : $xmlData['C12'],
                        "ArrivalDate" => is_array($xmlData['C15']) ? $existReservation->ArrivalDate : $xmlData['C15'],
                        "ArrivalTime" => is_array($xmlData['C18']) ? $existReservation->ArrivalTime : $xmlData['C18'],
                        "DepartureDate" => is_array($xmlData['C21']) ? $existReservation->DepartureDate : $xmlData['C21'],
                        "DepartureTime" => is_array($xmlData['C24']) ? $existReservation->DepartureTime : $xmlData['C24'],
                        "CancellationReason" => is_array($xmlData['C27']) ? $existReservation->CancellationReason : $xmlData['C27'],
                        "CancellationNo" => is_array($xmlData['C30']) ? $existReservation->CancellationNo : $xmlData['C30'],
                        "CancellationDate" => is_array($xmlData['C33']) ? $existReservation->CancellationDate : $xmlData['C33'],
                        "OriginCode" => is_array($xmlData['C36']) ? $existReservation->OriginCode : $xmlData['C36'],
                        "CompanyName" => is_array($xmlData['C39']) ? $existReservation->CompanyName : $xmlData['C39'],
                        "InsertDate" => is_array($xmlData['C42']) ? $existReservation->InsertDate : $xmlData['C42'],
                        "InsertUser" => is_array($xmlData['C45']) ? $existReservation->InsertUser : $xmlData['C45'],
                        "UpdateDate" => is_array($xmlData['C48']) ? $existReservation->UpdateDate : $xmlData['C48'],
                        "UpdateUser" => is_array($xmlData['C51']) ? $existReservation->UpdateUser : $xmlData['C51'],
                        "CurrencyCode" => is_array($xmlData['C54']) ? $existReservation->CurrencyCode : $xmlData['C54'],
                        "ResvStatus" => is_array($xmlData['C57']) ? $existReservation->ResvStatus : $xmlData['C57'],
                        "GuestNameID" => is_array($xmlData['C60']) ? $existReservation->GuestNameID : $xmlData['C60'],
                        "LastName" => is_array($xmlData['C63']) ? $existReservation->LastName : $xmlData['C63'],
                        "MarketCode" => is_array($xmlData['C66']) ? $existReservation->MarketCode : $xmlData['C66'],
                        "PaymentMethod" => is_array($xmlData['C69']) ? $existReservation->PaymentMethod : $xmlData['C69'],
                        "RateCode" => is_array($xmlData['C72']) ? $existReservation->RateCode : $xmlData['C72'],
                        "RoomNo" => is_array($xmlData['C75']) ? $existReservation->RoomNo : $xmlData['C75'],
                        "RoomType" => is_array($xmlData['C78']) ? $existReservation->RoomType : $xmlData['C78'],
                        "SourceCode" => is_array($xmlData['C81']) ? $existReservation->SourceCode : $xmlData['C81'],
                        "RateAmount" => is_array($xmlData['C84']) ? $existReservation->RateAmount : $xmlData['C84'],
                        "TravelAgentName" => is_array($xmlData['C87']) ? $existReservation->TravelAgentName : $xmlData['C87'],
                        "Custom1" => is_array($xmlData['C96']) ? $existReservation->Custom1 : $xmlData['C96'],
                        "ReservationNotes" => is_array($xmlData['C99']) ? $existReservation->ReservationNotes : $xmlData['C99'],
                        "Custom2" => is_array($xmlData['C102']) ? $existReservation->Custom2 : $xmlData['C102'],
                        "Custom3" => is_array($xmlData['C108']) ? $existReservation->Custom3 : $xmlData['C108'],
                        "updated_at" => Carbon::now(),
                    ]);
                }
            }
            if ($reservationCreate)
                Reservation::insert($reservationCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }
}
