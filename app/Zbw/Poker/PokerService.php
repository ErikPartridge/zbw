<?php  namespace Zbw\Poker; 

use Zbw\Poker\Contracts\PokerServiceInterface;
use Zbw\Poker\Contracts\PokerRepositoryInterface;
use Curl\Curl;

class PokerService implements PokerServiceInterface
{

    private $cards;
    private $analyzer;
    private $curl;

    public function __construct(PokerRepositoryInterface $cards, PokerHandAnalyzer $analyzer, Curl $curl)
    {
        $this->cards = $cards;
        $this->analyzer = $analyzer;
        $this->curl = $curl;
    }

    /**
     * @name  draw
     * @description
     *
     * @param $input
     *
     * @return bool
     */
    public function draw($input)
    {
        if($this->cards->countCardsInHand($input['pid']) > 5) { return false; }
        $card = $this->cards->create([
              'pid' => $input['pid'],
              'card' => !empty($input['card']) ? $input['card'] : $this->generateCard()
          ]);
        if(\PokerPilot::where('pid', $card->pid)->count() === 0) {
            $pilot = $this->getPilot($card->pid);
            \PokerPilot::create([
                'pid' => $card->pid,
                'first_name' => $pilot['user']['name_first'],
                'last_name' => $pilot['user']['name_last'],
                'country' => $pilot['user']['country']
            ]);
        }
        return $card;
    }

    /**
     * @name  discard
     * @description
     *
     * @param $cardId
     *
     * @return mixed
     */
    public function discard($cardId)
    {
        return $this->cards->discard($cardId);
    }

    /**
     * @name generateCard
     * @description
     * @return string
     */
    private function generateCard()
    {
        $suite = mt_rand(1,4);
        $val = mt_rand(2,14);
        switch($suite) {
            case '1':
                $suite = 'D';
                break;
            case '2':
                $suite = 'H';
                break;
            case '3':
                $suite = 'S';
                break;
            case '4':
                $suite = 'C';
                break;
        }
        switch($val) {
            case '11':
                $card = 'J';
                break;
            case '12':
                $card = 'Q';
                break;
            case '13':
                $card = 'K';
                break;
            case '14':
                $card = 'A';
                break;
            default:
                $card = $val;
                break;
        }
        return $card.$suite;
    }

    /**
     * @name  getPilotCards
     * @description
     *
     * @param $pid
     *
     * @return mixed
     */
    public function getPilotCards($pid)
    {
        return $this->cards->getHandsByPilot($pid);
    }

    /**
     * @name getPilots
     * @description
     * @return mixed
     */
    public function getPilots()
    {
        return $this->cards->getPilotsList();
    }

    /**
     * @name getStandings
     * @description
     * @return array
     */
    public function getStandings()
    {
        //$hands[pid, array [card, id]]
        $hands = $this->cards->getValidHands();
        $graded_hands = $this->analyzer->analyzeHands($hands);
        return $this->analyzer->sortHands($graded_hands);
    }

    public function getPilot($pid)
    {
        $this->curl->get(\Config::get('zbw.controller_status').$pid);
        $pilot = simplexml_load_string($this->curl->response);
        return json_decode(json_encode((array)$pilot), 1);
    }
}

