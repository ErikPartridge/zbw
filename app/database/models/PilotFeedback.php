<?php

class PilotFeedback extends Eloquent {
	protected $guarded = ['ip', 'email'];
	protected $table = 'pilot_feedback';
	public $rules;

    public function __construct()
    {
        $cids = \Zbw\Helpers::getCids(true);
        $this->rules = [
            'controller' => 'in:' . $cids,
            'rating' => 'integer|max:5',
            'name' => 'alpha|max:50',
            'email' => 'email|max:50',
            'ip' => 'ip'
        ];
    }
}
