<?php namespace Zbw\Training; 

use Zbw\Core\EloquentRepository;
use Zbw\Training\Contracts\TrainingRequestRepositoryInterface;

class TrainingRequestRepository extends EloquentRepository implements TrainingRequestRepositoryInterface
{
    public $model = '\TrainingRequest';

    /**
     * @param $input
     * @return mixed
     */
    public function update($input)
    {
    }

    /**
     * Create a new training request
     *
     * @param array $input
     * @return bool
     */
    public function create(array $input)
    {
        $tr = new \TrainingRequest();
        $tr->cid = $input['user'];
        $start = \Carbon::createFromFormat('m-d-Y H:i:s', $input['start']);
        if($start->lt(\Carbon\Carbon::now())) {
            $start = \Carbon\Carbon::now();
        }
        $tr->start = $start;
        $tr->end = \Carbon::createFromFormat('m-d-Y H:i:s', $input['end']);
        $tr->cert_id = $input['cert'];
        $tr->comment = $input['comment'];
        if($this->save($tr)) {
            $this->flushCache();
            \Queue::push('Zbw\Queues\QueueDispatcher@trainingNewRequest', $tr);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Staff member accept a training request
     *
     * @param $tsid
     * @param $cid
     * @return bool
     */
    public function accept($tsid, $cid, $input)
    {
        $tr = $this->get($tsid);
        if($tr->accepted_by) {
            return false;
        }

        $tr->sid = $cid;
        $tr->accepted_at = \Carbon::now();

        if($this->save($tr)) {
            $this->flushCache();
            \Queue::push('Zbw\Queues\QueueDispatcher@trainingAcceptRequest', ['request' => $tr, 'comment' => $input['comment']]);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Staff member drops previously accepted request
     *
     * @param $tsid
     * @param $cid
     * @return bool
     */
    public function drop($tsid, $cid)
    {
        $tr = $this->get($tsid);
        if(! $tr->sid === $cid) return false;
        $tr->sid = null;
        $tr->accepted_at = null;
        if($this->save($tr)) {
            $this->flushCache();
            \Queue::push('Zbw\Queues\QueueDispatcher@trainingDropRequest', $tr);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Marks a training request as complete
     *
     * @param $tsid
     * @param $report_id
     * @return bool
     */
    public function complete($tsid, $report_id)
    {
        $tr = $this->get($tsid);
        if($tr->is_completed) { return false; }
        $tr->is_completed = true;
        $tr->training_session_id= $report_id;
        $tr->completed_at = \Carbon::now();
        $this->flushCache();

        return $this->save($tr);
    }

    /**
     * Retrieves a paginated set of training requests
     *
     * @param int $n
     * @return \Illuminate\Pagination\Paginator
     */
    public function indexPaginated($n = 10)
    {
        return $this->make()->with(['student','staff','certType'])->paginate($n);
    }

    /**
     * Returns a filtered set of training requests
     *
     * Filters: initials, before, after
     *
     * @param array $input
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function indexFiltered($input)
    {
        $users = \App::make('Zbw\Users\UserRepository');
        $ret = $this->make()->with(['student','staff','certType']);

        if(array_key_exists('initials', $input)) {
            $user = $users->findByInitials($input['initials']);
            if($user) $ret->where('cid', $user->cid);
        }

        if(array_key_exists('before', $input) && ! empty($input['before'])) {
            $ret->where('start', '<', \Carbon::createFromFormat('m-d-Y H:i:s', $input['before']));
        }

        if(array_key_exists('after', $input) && ! empty($input['after'])) {
            $ret->where('start', '>', \Carbon::createFromFormat('m-d-Y H:i:s', $input['after']));
        }

        return $ret->get();
    }

    /**
     * Returns a single request with all related resources
     *
     * @param $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null|static
     */
    public function getWithAll($id)
    {
        return $this->make()->with(['student', 'certType', 'staff'])->where('is_cancelled', 0)->find($id);
    }

    /**
     * Returns recent requests with all related resources
     *
     * @param int $n
     * @return mixed
     */
    public function getRecent($n = 10)
    {
        return $this->make()->with(['student', 'certType', 'staff'])
            ->where('start', '>', \Carbon::now()->subHours(4))
            ->where('is_cancelled', 0)
            ->orderBy('start', 'asc')->limit($n)->get();
    }

    public function getCurrentByCid($cid)
    {
        return $this->make()->with(['certType', 'staff'])
               ->where('start', '>', \Carbon::now()->subHours(1))
               ->where('is_completed', 0)
               ->where('is_cancelled', 0)
               ->where('cid', $cid)
               ->orderBy('start', 'asc')
               ->get();
    }
}
