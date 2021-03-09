<?php

namespace App\Http\Controllers;

use App\Traits\mailSettings;
use App\Traits\profileData;
use App\Traits\bookingData;
use App\Traits\stayData;

class ReportsController extends Controller
{
    use mailSettings;
    use profileData;
    use stayData;
    use bookingData;

    public function index()
    {
        $get_file_path = $this->get_data_from_mail();
        $explode_file_name = explode('-', $get_file_path);
        $file_name = @$explode_file_name[1];

        if ($file_name == 'profile.xml') {
            return $this->check_profiles_xml_type($get_file_path);
        } elseif ($file_name == 'Booking.xml') {
            return $this->check_bookings_xml_type($get_file_path);
        } elseif ($file_name == 'stay.xml') {
            return $this->check_stay_xml_type($get_file_path);
        } else {
            return 'Mail file could not match in our credentials';
        }
    }
}
