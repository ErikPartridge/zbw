<?php

class Staffing extends Eloquent {
    protected $guarded = ['start', 'stop'];
    protected $table = 'zbw_staffing';
    protected $dates = ['start', 'stop'];
    public $rules = [
        'cid' => 'integer',
        'start' => 'date',
        'stop' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo('User', 'cid', 'cid');
    }

    public static function frontPage()
    {
        return Staffing::where('stop', null)->with(['user'])->get();
    }

    public static function getDaysOfStaffing($days = 3)
    {
        return \Staffing::where('updated_at', '<', Carbon::now()->subDays($days))->get();
    }
}
