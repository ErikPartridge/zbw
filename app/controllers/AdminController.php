<?php 

use Zbw\Repositories\UserRepository as UserRepository;

class AdminController extends BaseController
{
    private $uRepo;

    public function __construct()
    {
        $this->uRepo = new UserRepository();
    }
    public function getAdminIndex()
    {
        $data = [
            'title' => 'vZBW Staff Area'
        ];
        return View::make('staff.index', $data);
    }
    public function getTrainingIndex()
    {
        $data = [
            'reports' => ControllerTraining::recentReports(5),
            'sessions' => ['a', 'b'],
            'requests' => ['c', 'd'],
            'exams' => ControllerExam::recentExams(5),
            'title' => 'vZBW Training Home'
        ];
        JavaScript::put(['foo' => 'mike']);
        return View::make('staff.training.index', $data);
    }

    public function getForumIndex()
    {
        $data = [
            'title' => 'ZBW Forum Admin'
        ];
        return View::make('staff.forum.index', $data);
    }

    public function getCmsIndex()
    {
        $data = [
            'title' => 'ZBW CMS Admin'
        ];
        return View::make('staff.cms.index', $data);
    }

    public function getNewsIndex()
    {
        $data = [
            'title' => 'ZBW News Admin'
        ];
        return View::make('staff.news.index', $data);
    }

    public function getRosterIndex()
    {
        $ur = new UserRepository();
        $data = [
            'title' => 'ZBW Roster Admin',
            'users' => $ur->all()
        ];
        return View::make('staff.roster.index', $data);
    }

    public function getSearchResults()
    {
        $ur = new UserRepository();
        $results = $ur->search(Input::all());
        $data = [
            'title' => 'Roster Search Results',
            'stype' => 'roster',
            'results' => $results
        ];
        return View::make('staff.roster.results', $data);
    }

    public function getTsIndex()
    {
        $data = [ 'title' => 'ZBW TS Admin'];
        return View::make('staff.ts', $data);
    }

    public function showUser($id)
    {
        $data = [
            'title' => 'View Controller',
            'user' => $this->uRepo->find($id)
        ];
        return View::make('staff.roster.view', $data);
    }
} 