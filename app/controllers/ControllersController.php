<?php
use Zbw\Cms\MessagesRepository;
use Zbw\Users\UserRepository;

class ControllersController extends BaseController {

	public function getIndex()
	{
		$data = [
		];
		return View::make('zbw.controllers');
	}

    public function getController($id)
    {
        $data = [
            'controller' => UserRepository::find($id, ['certification'])
        ];

        return View::make('users.show', $data);

    }

	/**
	 * this route specifically handles when the logged in user navigates to their own profile
	 */
	public function getMe()
	{
		$data = [
			'messages' => MessagesRepository::to(Auth::user()->cid),
      'view' => \Input::get('v'),
      'me' => \Auth::user()->with(['Exam','TrainingSession'])
		];
		return View::make('users.me.index', $data);
	}

	public function getSettings()
	{
		$data = [
      'view' => \Input::get('v')
		];
		return View::make('users.me.index', $data);
	}

	public function view($cid)
	{

	}

}
