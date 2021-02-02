<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PmsReportConfig extends Model
{
    protected $table = 'pmsreportconfig';
    protected $guarded = ['_token'];

    function accommodation()
    {
        return $this->belongsTo(Accommodation::class, 'AccommodationId', 'AccommodationId');
    }
}
