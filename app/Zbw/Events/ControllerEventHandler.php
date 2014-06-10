<?php  namespace Zbw\Events; 

use Zbw\Bostonjohn\Emailer;

class ControllerEventHandler {

    private $emailer;

    public function __construct()
    {
        $this->emailer = new Emailer();
    }

    public function subscribe($events)
    {
        $list = [
        'controller.welcomeNew',
        'controller.welcomeMentor',
        'controller.welcomeStaff',
        'controller.suspend',
        'controller.terminate',
        ];

        foreach($list as $event) {
            $method = 'Zbw\Events\ControllerEventHandler@'.explode('.', $event)[1];
            $events->listen($event, $method);
        }
    }

    public function welcomeNew(\User $user)
    {
        $this->emailer->newUser($user);
    }

    public function welcomeMentor($event)
    {

    }

    public function welcomeStaff($event)
    {

    }

    public function suspend($event)
    {

    }

    public function terminate($event)
    {

    }
} 