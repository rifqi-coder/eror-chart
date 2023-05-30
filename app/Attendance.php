<?php

namespace App;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $guarded = [];

    

    public function detail ()
    {
        return $this->hasMany(AttendanceDetail::class);
    }

}
