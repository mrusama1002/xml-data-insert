<?php

namespace App\Console\Commands;

use App\Traits\bookingData;
use App\Traits\mailSettings;
use App\Traits\profileData;
use App\Traits\stayData;
use Illuminate\Console\Command;

class GetXmlDataFromMail extends Command
{
    use mailSettings;
    use profileData;
    use stayData;
    use bookingData;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cron:get_xml_data';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Xml Data From Mail';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $get_file_path = $this->get_data_from_mail();
        if ($get_file_path != false) {
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
            print_r('Success');
        }
        print_r('Data Not Found!');
    }
}
