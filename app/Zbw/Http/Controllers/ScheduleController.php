<?php namespace Zbw\Http\Controllers;

use Zbw\Users\Contracts\ScheduleRepositoryInterface;
use Illuminate\Session\Store;

class ScheduleController extends BaseController
{
    private $schedules;

    public function __construct(ScheduleRepositoryInterface $schedules, Store $session)
    {
        parent::__construct($session);
        $this->schedules = $schedules;
    }

    public function getIndex()
    {
        $this->setData('schedules', $this->schedules->upcoming(100));
        return $this->view('zbw.schedules.index');
    }

    public function postIndex()
    {
        $input = $this->request->all();
        $input['cid'] = $this->current_user->cid;
        $this->schedules->create($input);
        $this->setFlash(['flash_success' => 'Scheduled for ' . $input['start']]);
        return $this->redirectBack();
    }

    public function getDelete($id)
    {
        $schedule = $this->schedules->get($id);
        if ($schedule->cid !== $this->current_user->cid) {
            $this->setFlash(['flash_error' => 'Operation not allowed']);
            return $this->redirectBack();
        }

        $this->schedules->delete($id);
        $this->setFlash(['flash_success' => 'Schedule entry deleted successfully']);
        return $this->redirectBack();
    }
}
