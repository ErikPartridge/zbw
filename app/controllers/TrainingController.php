<?php

use Illuminate\Session\Store;
use Zbw\Training\Contracts\ExamsRepositoryInterface;
use Zbw\Training\Contracts\TrainingSessionRepositoryInterface;
use Zbw\Training\TrainingRequestRepository;
use Zbw\Users\Contracts\UserRepositoryInterface;
use Zbw\Training\Contracts\StaffAvailabilityRepositoryInterface;

use Zbw\Training\Commands\ProcessTrainingSessionCommand;
use Zbw\Training\Commands\AdoptUserCommand;

class TrainingController extends BaseController
{
    private $trainings;
    private $exams;
    private $requests;
    private $users;
    private $staffAvailability;

    function __construct(ExamsRepositoryInterface $exams,
        TrainingSessionRepositoryInterface $trainings,
        TrainingRequestRepository $requests,
        StaffAvailabilityRepositoryInterface $staffAvailability,
        UserRepositoryInterface $users, Store $session)
    {
        $this->exams = $exams;
        $this->trainings = $trainings;
        $this->requests = $requests;
        $this->users = $users;
        $this->staffAvailability = $staffAvailability;
        parent::__construct($session);
    }

    public function getIndex()
    {
        $this->setData('availableExams', $this->exams->availableExams($this->current_user->cid));
        $this->setData('progress', $this->users->trainingProgress($this->current_user->cid));
        $this->setData('student', $this->current_user);
        $this->setData('available', $this->staffAvailability->upcoming(10));
        $this->view('training.index');
    }

    public function getAdminIndex()
    {
        $this->setData('reports', $this->trainings->recentReports(10));
        $this->setData('exams', $this->exams->recentExams(10));
        $this->setData('staffings', \Staffing::recentStaffings(10));
        $this->setData('requests', $this->requests->getRecent(10));
        $this->view('staff.training.index');
    }

    //all training reports
    public function getAll()
    {
        if(empty(\Input::all())) {
            $this->setData('sessions', $this->trainings->indexPaginated(10));
            $this->setData('paginate', true);
        } else {
            $this->setData('sessions', $this->trainings->indexFiltered(\Input::all()));
            $this->setData('paginate', false);
        }

        $this->view('staff.training.all');
    }

    //all training requests
    public function getAllRequests()
    {
        if(empty(\Input::all())) {
            $this->setData('requests', $this->requests->indexPaginated(10));
            $this->setData('paginate', false);
        } else {
            $this->setData('requests', $this->requests->indexFiltered(\Input::all()));
            $this->setData('paginate', true);
        }

        $this->view('staff.training.all-requests');
    }

    public function showAdmin($id)
    {
        $this->setData('tsession', $this->trainings->with(['student', 'staff', 'facility', 'weatherType', 'trainingReport'], $id));
        $this->view('staff.training.session');
    }

    public function getRequest()
    {
        if($this->current_user->cert == 0) {
            $this->setFlash(['flash_error' => 'You must pass the ZBW class C ground exam to request training!']);
            $this->redirectRoute('training');
        }

        $this->setData('available', $this->staffAvailability->upcoming(10));
        $this->view('training.request');
    }

    public function showRequest($tid)
    {
        $this->setData('request', $this->requests->getWithAll($tid));
        $this->view('training.show-request');
    }

    public function getLiveSession($tsid)
    {
        $this->setData('student', $this->requests->get($tsid)->cid);
        $this->setData('staff', $this->current_user);
        $this->view('staff.training.live');
    }

    public function testLiveSession($tsid)
    {
        $request = $this->requests->get($tsid);
        $this->setData('staff', $this->current_user);
        $this->setData('student', $this->users->get($request->cid));
        $this->setData('facilities', \TrainingFacility::all());
        $this->setData('types', \TrainingType::all());
        $this->view('staff.training.live');
    }

    public function postLiveSession($tsid)
    {
        $success = $this->execute(ProcessTrainingSessionCommand::class, ['input' => \Input::all(), 'tsid' => $tsid]);

        if($success) {
            $this->setFlash(['flash_success' => 'Training session completed']);
            return $this->redirectRoute('staff/training');
        } else {
            $this->setFlash(['flash_error' => 'Error submitting training session! Please email admin@bostonartcc.net immediately']);
            return $this->redirectBack()->withInput();
        }
    }

    public function getAdopt($cid)
    {
        $this->setData('student', $this->users->get($cid));
        $this->view('staff.training.adopt');
    }

    public function postAdopt()
    {
        $input = \Input::only(['meeting','message','subject','sid','cid']);
        $success = $this->execute(AdoptUserCommand::class, $input);

        if($success === true) { $this->setFlash(['flash_success' => 'User adopted successfully']); }
        else { $this->setFlash(['flash_error' => $success]); }

        return $this->redirectBack();
    }

    public function postDropAdopt($cid)
    {
        if($this->users->dropAdopt($cid)) {
            $this->setFlash(['flash_success' => 'Adoption dropped successfully']);
        } else {
            $this->setFlash(['flash_error' => $this->users->getErrors()]);
        }
        return $this->redirectBack();
    }

    public function viewSession($id)
    {
        $session = $this->trainings->get($id);
        if($this->current_user->cid !== $session->cid) {
            return $this->redirectRoute('training');
        } else {
            $this->setData('tsession', $session);
            $this->view('training.session');
        }
    }

    public function getStaffStaffAvailability()
    {
        $this->setData('available', $this->staffAvailability->all());
        return $this->view('staff.training.availability');
    }

    public function postStaffAvailability()
    {
        $input = \Input::all();
        $input['cid'] = $this->current_user->cid;
        $this->staffAvailability->create($input);
        $this->setFlash(['flash_success' => 'Availability posted successfully']);
        return $this->redirectBack();
    }

    public function getDeleteAvailability($id)
    {
        $session = $this->staffAvailability->get($id);
        if($session->cid !== $this->current_user->cid) {
            $this->setFlash(['flash_error' => 'Operation not allowed']);
        } else {
            $this->staffAvailability->delete($id);
            $this->setFlash(['flash_success' => 'Availability deleted successfully']);
        }

        return $this->redirectBack();
    }

}
