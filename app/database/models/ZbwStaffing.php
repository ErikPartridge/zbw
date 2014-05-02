<?php

class ZbwStaffing extends Eloquent {
    protected $guarded = ['start', 'stop'];
    protected $table = 'zbw_staffing';
    public $timestamps = false;
    public $rules = [
        'cid' => 'integer',
        'start' => 'date',
        'stop' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo('User', 'cid', 'cid');
    }
}