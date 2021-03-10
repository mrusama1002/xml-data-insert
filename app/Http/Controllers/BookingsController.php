<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Email;
use App\Models\PmsReportConfig;
use App\Models\Profile;
use App\Models\Reservation;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BookingsController extends Controller
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


    public function index()
    {
        $xml = simplexml_load_string(file_get_contents(storage_path() . "/app/public/Booking.xml"));
        $availabilitydata = json_decode(json_encode($xml), TRUE);
        $data = @$availabilitydata['LIST_G_C6']['G_C6'];
        if ($data) {
            return $this->bookings_data_insert_in_RHOTEL($availabilitydata);
        } else {
            return $this->bookings_data_insert_in_ewaa_hotel($availabilitydata);
        }
    }

//    public function bookings_data_insert_in_RHOTEL($availabilitydata)
//    {
//        try {
//            $data = $availabilitydata['LIST_G_C6']['G_C6'];
//            $pmsReportEmail = Email::first();
//            $reservationCreate = null;
//            foreach ($data as $key => $xmlData) {
//                $where = [
//                    'AccommodationId' => $pmsReportEmail['AccommodationId'],
//                    'SourceId' => $pmsReportEmail['SourceId'],
//                    'GroupId' => $pmsReportEmail['GroupId'],
//                    'ConfNumber' => $xmlData['C12']
//                ];
//                $existReservation = Reservation::where($where)->first();
//
//                if (empty($existReservation)) {
//                    $reservationCreate[] = [
//                        "GroupId" => $pmsReportEmail['GroupId'],
//                        "SourceId" => $pmsReportEmail['SourceId'],
//                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
//                        "Property" => is_array($xmlData['C6']) ? null : $xmlData['C6'],
//                        "ConfNumber" => is_array($xmlData['C12']) ? null : $xmlData['C12'],
//                        "ArrivalDate" => is_array($xmlData['C15']) ? null : $xmlData['C15'],
//                        "ArrivalTime" => is_array($xmlData['C18']) ? null : $xmlData['C18'],
//                        "DepartureDate" => is_array($xmlData['C21']) ? null : $xmlData['C21'],
//                        "DepartureTime" => is_array($xmlData['C24']) ? null : $xmlData['C24'],
//                        "CancellationReason" => is_array($xmlData['C27']) ? null : $xmlData['C27'],
//                        "CancellationNo" => is_array($xmlData['C30']) ? null : $xmlData['C30'],
//                        "CancellationDate" => is_array($xmlData['C33']) ? null : $xmlData['C33'],
//                        "OriginCode" => is_array($xmlData['C36']) ? null : $xmlData['C36'],
//                        "CompanyName" => is_array($xmlData['C39']) ? null : $xmlData['C39'],
//                        "InsertDate" => is_array($xmlData['C42']) ? null : $xmlData['C42'],
//                        "InsertUser" => is_array($xmlData['C45']) ? null : $xmlData['C45'],
//                        "UpdateDate" => is_array($xmlData['C48']) ? null : $xmlData['C48'],
//                        "UpdateUser" => is_array($xmlData['C51']) ? null : $xmlData['C51'],
//                        "CurrencyCode" => is_array($xmlData['C54']) ? null : $xmlData['C54'],
//                        "ResvStatus" => is_array($xmlData['C57']) ? null : $xmlData['C57'],
//                        "GuestNameID" => is_array($xmlData['C60']) ? null : $xmlData['C60'],
//                        "LastName" => is_array($xmlData['C63']) ? null : $xmlData['C63'],
//                        "MarketCode" => is_array($xmlData['C66']) ? null : $xmlData['C66'],
//                        "PaymentMethod" => is_array($xmlData['C69']) ? null : $xmlData['C69'],
//                        "RateCode" => is_array($xmlData['C72']) ? null : $xmlData['C72'],
//                        "RoomNo" => is_array($xmlData['C75']) ? null : $xmlData['C75'],
//                        "RoomType" => is_array($xmlData['C78']) ? null : $xmlData['C78'],
//                        "SourceCode" => is_array($xmlData['C81']) ? null : $xmlData['C81'],
//                        "RateAmount" => is_array($xmlData['C84']) ? null : $xmlData['C84'],
//                        "TravelAgentName" => is_array($xmlData['C87']) ? null : $xmlData['C87'],
//                        "Custom1" => is_array($xmlData['C96']) ? null : $xmlData['C96'],
//                        "ReservationNotes" => is_array($xmlData['C99']) ? null : $xmlData['C99'],
//                        "Custom2" => is_array($xmlData['C102']) ? null : $xmlData['C102'],
//                        "Custom3" => is_array($xmlData['C108']) ? null : $xmlData['C108'],
//                        "created_at" => Carbon::now(),
//                        "updated_at" => Carbon::now(),
//                    ];
//                } else {
//                    // UPDATE RESERVATION
//                    $existReservation->update([
//                        "GroupId" => $pmsReportEmail['GroupId'],
//                        "SourceId" => $pmsReportEmail['SourceId'],
//                        "AccommodationId" => $pmsReportEmail['AccommodationId'],
//                        "Property" => is_array($xmlData['C6']) ? $existReservation->Property : $xmlData['C6'],
//                        "ConfNumber" => is_array($xmlData['C12']) ? $existReservation->ConfNumber : $xmlData['C12'],
//                        "ArrivalDate" => is_array($xmlData['C15']) ? $existReservation->ArrivalDate : $xmlData['C15'],
//                        "ArrivalTime" => is_array($xmlData['C18']) ? $existReservation->ArrivalTime : $xmlData['C18'],
//                        "DepartureDate" => is_array($xmlData['C21']) ? $existReservation->DepartureDate : $xmlData['C21'],
//                        "DepartureTime" => is_array($xmlData['C24']) ? $existReservation->DepartureTime : $xmlData['C24'],
//                        "CancellationReason" => is_array($xmlData['C27']) ? $existReservation->CancellationReason : $xmlData['C27'],
//                        "CancellationNo" => is_array($xmlData['C30']) ? $existReservation->CancellationNo : $xmlData['C30'],
//                        "CancellationDate" => is_array($xmlData['C33']) ? $existReservation->CancellationDate : $xmlData['C33'],
//                        "OriginCode" => is_array($xmlData['C36']) ? $existReservation->OriginCode : $xmlData['C36'],
//                        "CompanyName" => is_array($xmlData['C39']) ? $existReservation->CompanyName : $xmlData['C39'],
//                        "InsertDate" => is_array($xmlData['C42']) ? $existReservation->InsertDate : $xmlData['C42'],
//                        "InsertUser" => is_array($xmlData['C45']) ? $existReservation->InsertUser : $xmlData['C45'],
//                        "UpdateDate" => is_array($xmlData['C48']) ? $existReservation->UpdateDate : $xmlData['C48'],
//                        "UpdateUser" => is_array($xmlData['C51']) ? $existReservation->UpdateUser : $xmlData['C51'],
//                        "CurrencyCode" => is_array($xmlData['C54']) ? $existReservation->CurrencyCode : $xmlData['C54'],
//                        "ResvStatus" => is_array($xmlData['C57']) ? $existReservation->ResvStatus : $xmlData['C57'],
//                        "GuestNameID" => is_array($xmlData['C60']) ? $existReservation->GuestNameID : $xmlData['C60'],
//                        "LastName" => is_array($xmlData['C63']) ? $existReservation->LastName : $xmlData['C63'],
//                        "MarketCode" => is_array($xmlData['C66']) ? $existReservation->MarketCode : $xmlData['C66'],
//                        "PaymentMethod" => is_array($xmlData['C69']) ? $existReservation->PaymentMethod : $xmlData['C69'],
//                        "RateCode" => is_array($xmlData['C72']) ? $existReservation->RateCode : $xmlData['C72'],
//                        "RoomNo" => is_array($xmlData['C75']) ? $existReservation->RoomNo : $xmlData['C75'],
//                        "RoomType" => is_array($xmlData['C78']) ? $existReservation->RoomType : $xmlData['C78'],
//                        "SourceCode" => is_array($xmlData['C81']) ? $existReservation->SourceCode : $xmlData['C81'],
//                        "RateAmount" => is_array($xmlData['C84']) ? $existReservation->RateAmount : $xmlData['C84'],
//                        "TravelAgentName" => is_array($xmlData['C87']) ? $existReservation->TravelAgentName : $xmlData['C87'],
//                        "Custom1" => is_array($xmlData['C96']) ? $existReservation->Custom1 : $xmlData['C96'],
//                        "ReservationNotes" => is_array($xmlData['C99']) ? $existReservation->ReservationNotes : $xmlData['C99'],
//                        "Custom2" => is_array($xmlData['C102']) ? $existReservation->Custom2 : $xmlData['C102'],
//                        "Custom3" => is_array($xmlData['C108']) ? $existReservation->Custom3 : $xmlData['C108'],
//                        "updated_at" => Carbon::now(),
//                    ]);
//                }
//            }
//            if ($reservationCreate)
//                Reservation::insert($reservationCreate);
//            return 'Success';
//        } catch (\Exception $exception) {
//            return $exception->getMessage();
//        }
//    }

    public function bookings_data_insert_in_ewaa_hotel($availabilitydata)
    {
        try {
            $data = $availabilitydata['booking'];
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
                        "CheckInTime" => is_array($xmlData['arrtime']) ? null : @$xmlData['arrtime'],
                        "CheckOutDate" => is_array($xmlData['depdate']) ? null : new Carbon(date('d-m-Y', strtotime($xmlData['depdate']))),
                        "CheckOutTime" => is_array($xmlData['deptime']) ? null : @$xmlData['deptime'],
                        "CancellationComment" => is_array($xmlData['cancelreason']) ? null : $xmlData['cancelreason'],
                        "CancellationNumber" => is_array($xmlData['cancelno']) ? null : $xmlData['cancelno'],
                        "CancelledAt" => is_array($xmlData['canceldate']) ? null : new Carbon(date('d-m-Y', strtotime($xmlData['canceldate']))),
                        "CompanyIds" => is_array($xmlData['company']) ? null : $xmlData['company'],
                        "BookingCreateDate" => is_array($xmlData['createdate']) ? null : new Carbon(str_replace("/", "-", $xmlData['createdate'])),
                        "BookingLastModified" => is_array($xmlData['updatedate']) ? null : new Carbon(str_replace("/", "-", $xmlData['updatedate'])),
                        "CurrencyCode" => is_array($xmlData['curreny']) ? null : $xmlData['curreny'],
                        "Status" => is_array($xmlData['resstatus']) ? null : $this->bookingStatus[$xmlData['resstatus']],
                        "GuestIds" => is_array($xmlData['profileid']) ? null : $xmlData['profileid'],
                        "MarketCode" => is_array($xmlData['marketcode']) ? null : $xmlData['marketcode'],
                        "RateCode" => is_array($xmlData['ratecode']) ? null : $xmlData['ratecode'],
                        "AreaId" => is_array($xmlData['roomno']) ? null : $xmlData['roomno'],
                        "RequestedAreaTypeId" => is_array($xmlData['roomtype']) ? null : $xmlData['roomtype'],
                        "SourceCode" => is_array($xmlData['sourcecode']) ? null : $xmlData['sourcecode'],
                        "GrossAmount" => is_array($xmlData['rateamount']) ? null : $xmlData['rateamount'],
                        "TravelAgent" => is_array($xmlData['agent']) ? null : $xmlData['agent'],
                        "ChannelManager" => is_array($xmlData['channelcode']) ? null : $xmlData['channelcode'],
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
                        "CheckInTime" => is_array($xmlData['arrtime']) ? $existReservation->CheckInTime : @$xmlData['arrtime'],
                        "CheckOutDate" => is_array($xmlData['depdate']) ? $existReservation->CheckOutDate : new Carbon(date('d-m-Y', strtotime($xmlData['depdate']))),
                        "CheckOutTime" => is_array($xmlData['deptime']) ? $existReservation->CheckOutTime : @$xmlData['deptime'],
                        "CancellationComment" => is_array($xmlData['cancelreason']) ? $existReservation->CancellationComment : $xmlData['cancelreason'],
                        "CancellationNumber" => is_array($xmlData['cancelno']) ? $existReservation->CancellationNumber : $xmlData['cancelno'],
                        "CancelledAt" => is_array($xmlData['canceldate']) ? $existReservation->CancelledAt : $xmlData['canceldate'],
                        "CompanyIds" => is_array($xmlData['company']) ? $existReservation->CompanyIds : $xmlData['company'],
                        "BookingCreateDate" => is_array($xmlData['createdate']) ? $existReservation->InsertDate : new Carbon(str_replace("/", "-", $xmlData['createdate'])),
                        "BookingLastModified" => is_array($xmlData['updatedate']) ? $existReservation->UpdateDate : new Carbon(str_replace("/", "-", $xmlData['updatedate'])),
                        "CurrencyCode" => is_array($xmlData['curreny']) ? $existReservation->CurrencyCode : $xmlData['curreny'],
                        "Status" => is_array($xmlData['resstatus']) ? $existReservation->Status : $this->bookingStatus[$xmlData['resstatus']],
                        "GuestIds" => is_array($xmlData['profileid']) ? $existReservation->GuestIds : $xmlData['profileid'],
                        "MarketCode" => is_array($xmlData['marketcode']) ? $existReservation->MarketCode : $xmlData['marketcode'],
                        "RateCode" => is_array($xmlData['ratecode']) ? $existReservation->RateCode : $xmlData['ratecode'],
                        "AreaId" => is_array($xmlData['roomno']) ? $existReservation->AreaId : $xmlData['roomno'],
                        "RequestedAreaTypeId" => is_array($xmlData['roomtype']) ? $existReservation->RoomType : $xmlData['roomtype'],
                        "SourceCode" => is_array($xmlData['sourcecode']) ? $existReservation->SourceCode : $xmlData['sourcecode'],
                        "GrossAmount" => is_array($xmlData['rateamount']) ? $existReservation->GrossAmount : $xmlData['rateamount'],
                        "TravelAgent" => is_array($xmlData['agent']) ? $existReservation->TravelAgentName : $xmlData['agent'],
                        "ChannelManager" => is_array($xmlData['channelcode']) ? null : $xmlData['channelcode'],
                        "LastModified" => Carbon::now(),
                    ]);
                }
            }
            if ($reservationCreate)
                Booking::insert($reservationCreate);
            return 'Success';
        } catch (\Exception $exception) {
            return $exception->getMessage();
        }
    }

}
