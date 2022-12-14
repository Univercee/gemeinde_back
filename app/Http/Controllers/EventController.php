<?php
namespace App\Http\Controllers;
use App\Managers\Events\EventManager;
use App\Managers\Queues\QueueFactory;
use App\Managers\Services\ServiceFactory;

//TODO: create some "admin middleware and use it here"
class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware('enforceJson');
    }

    //
    public function dispatchEvent(int $service_id)
    {
        return EventManager::dispatchById($service_id);
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

    //
    public function addGarbageEvents()
    {
        return ServiceFactory::garbage()->addEvents();
    }

    //
    public function addSwisscomEvents()
    {
        return ServiceFactory::swisscom()->addEvents();
    }

}
