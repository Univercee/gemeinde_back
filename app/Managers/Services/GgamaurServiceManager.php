<?php 
namespace App\Managers\Services;

use App\Managers\Events\Event;
use App\Managers\Events\EventList;
use Weidner\Goutte\GoutteFacade;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;

class GgamaurServiceManager extends ServiceManager
{
    protected int $SERVICE_ID = 6;
    protected int $TEMPLATE_ID = -1;
    const BASE_URL = 'https://www.gga-maur.ch/iframe/Support_Ereignisuebersicht.php';

    protected function beforeAdd(EventList $event_list): EventList
    {
        return $event_list;
    }

    public function getEvents(): EventList
    {
        $offset = 0;
        $amount = 10;
        $event_list = new EventList();
        $page = GoutteFacade::request('GET', self::BASE_URL.'?startline='.$offset.'&numlines='.$amount);

        $page->filter('a')->each(
            function ($node) use (&$event_list) {
                $data = $node->extract(['onclick']);       
                if(preg_match('/^DisplayTicket\((\d{4,6})\,\d{1,3}\,\d{1,3}\)$/ix', $data[0], $ticketId)) {
                    $event = $this->parseTicket($ticketId[1]);
                    if($event){
                        $event_list->merge($event);
                    }
                }
            });
        return $event_list;
    }

    // PRIVATE FUNCTIONS --------------------------------------------------------------------

    private function parseTicket(int $id): EventList
    {
        $ticket['external_id'] = $id;
        $ticket['url'] = self::BASE_URL.'?ticket='.$id;
        $ticket['ids'] = array();
        $page = GoutteFacade::request('GET', $ticket['url']);

        $page->filter('body > span.Lauftext01 > table')->eq(1)->children('tr > td')->each(
            function ($node, $i) use (&$ticket) {
                $content = $node->html();
                $content = str_replace("<br>", "\n", $content);
                if ($node->attr('bgcolor')) {
                    $ticket['title'] = trim(str_replace('nbsp', '', $node->text()));
                } else {
                    $matches = [];
                    if(preg_match('/^<b>Status:<\/b>(.+)$/mixsu', $content, $matches)) {
                        $ticket['status'] = trim($matches[1]);
                    } elseif (preg_match('/^<b>Datum:<\/b>(.+)$/mixsu', $content, $matches)) {
                        $ticket['timespan'] = trim($matches[1]);
                        list($ticket['from'], $ticket['till']) = $this->parseTimespan($ticket['timespan']);
                    } elseif (preg_match('/^<b>Betrifft:<\/b>(.+)$/mixsu', $content, $matches)) {
                        $location_name = trim($matches[1]);
                        $ticket['ids'] = $this->getLocation($location_name);
                    } elseif(trim($node->text())) {
                        $ticket['description'] = trim(str_replace(["\n", "\r"], '', $content));
                    }
                }
        });

        $event_list = array();
        foreach($ticket['ids'] as $location_id){
            $event = new Event
            (
                $location_id,
                $this->SERVICE_ID,
                $ticket['external_id'],
                $ticket['from'],
                $ticket['till'],
                $ticket['from'],
                $ticket['till'],
                $ticket['title'],
                $ticket['description'],
                $ticket['title'],
                $ticket['description']
            );
            array_push($event_list, $event);
        }
        
        return new EventList(...$event_list);
    }

    private function parseTimespan(string $timespan): array 
    {
        $from = $till = null;

        //02.04.2020 (02:00 - 03:00)
        $p0 = '(\d{2}.\d{2}.\d{4})\s+\((\d{2}:\d{2})\s+-\s+(\d{2}:\d{2})\)';

        //10.03.2020 (18:00) - 11.03.2020 (10:00)
        $p1 = '(\d{2}.\d{2}.\d{4})\s+\((\d{2}:\d{2})\)\s+-\s+(\d{2}.\d{2}.\d{4})\s+\((\d{2}:\d{2})\)';

        //03.11.2019 (03:00) -   ( )
        $p2 = '(\d{2}.\d{2}.\d{4})\s+\((\d{2}:\d{2})\)';

        if (preg_match("/$p0/mixsu", $timespan, $matches)) {
            $from = Date::createFromFormat('d.m.Y H:i', $matches[1].' '.$matches[2])->toDateTimeString();
            $till = Date::createFromFormat('d.m.Y H:i', $matches[1].' '.$matches[3])->toDateTimeString();      
        } elseif (preg_match("/$p1/mixsu", $timespan, $matches)) {
            $from = Date::createFromFormat('d.m.Y H:i', $matches[1].' '.$matches[2])->toDateTimeString();
            $till = Date::createFromFormat('d.m.Y H:i', $matches[3].' '.$matches[4])->toDateTimeString();      
        } elseif (preg_match("/$p2/mixsu", $timespan, $matches)) {
            $from = Date::createFromFormat('d.m.Y H:i', $matches[1].' '.$matches[2])->toDateTimeString();
        }

        return [$from, $till]; 
    }

    private function getLocation(string $location_name): array 
    {
        $ids = array();
        
        $places = [
            'null' => 'kabelnetz',
            8603 => 'schwerzenbach',
            8126 => 'zumikon', 
            8700 => 'küsnacht',
            8123 => 'ebmatingen',
            8706 => 'meilen',
            8704 => 'herrliberg',
            8117 => 'fällanden',
            8127 => 'forch',
            8617 => 'mönchaltorf',
            8132 => 'egg',
            8133 => 'esslingen',
            8605 => 'greifensee',
            8122 => 'binz',
            8610 => 'uster',
            8124 => 'maur',
        ];
        $location_name = strtolower($location_name);
        $location_name = str_replace([':', ',', '.', '-', ';'], ' ', $location_name);
        $matches = array_keys(array_intersect($places, explode(' ', $location_name)));    
        if($matches && $matches[0] == 'null'){
            return [null];
        }
        if(!empty($matches)){
            $zips = implode(',', $matches);
            $ids = DB::select(
                "SELECT DISTINCT id
                FROM locations
                WHERE zipcode IN ($zips)"
            );
        }
        $id_list = array();
        $ids = json_decode(json_encode($ids), true);
        foreach($ids as $item){
            array_push($id_list, $item["id"]);
        }
        return $id_list;
    }
}