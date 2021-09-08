<?php
namespace App\Http\Controllers;
use App\Managers\EventManager;
use App\Managers\Queues\QueueFactory;

//TODO: create some "admin middleware and use it here"
class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('enforceJson');
    }

    //
    public function dispatchEvent(int $event_id)
    {
        return EventManager::dispatch($event_id);
    }

    //
    public function consumeEmailQueue()
    {
        return QueueFactory::email()->consumeQueue();
    }

    //
    public function consumeTgQueue()
    {
        return QueueFactory::telegram()->consumeQueue();
    }

}
