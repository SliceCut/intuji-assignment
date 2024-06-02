<?php

namespace App\Controllers;

use App\Cores\Request;
use App\Exceptions\ModelNotFoundException;
use App\Requests\EventFilterRequest;
use App\Requests\EventRequest;
use App\Services\EventService;
use App\Services\Singleton\Auth;
use App\View;
use Exception;

class EventController extends BaseController
{
    protected array $limits = [
        [
            "label" => "10 Result",
            "value" => "10"
        ],
        [
            "label" => "25 Result",
            "value" => "20"
        ],
        [
            "label" => "50 Result",
            "value" => "50"
        ],
        [
            "label" => "100 Result",
            "value" => "100"
        ]
    ];

    public function __construct(
        protected Auth $auth,
        protected EventService $eventService,
        protected Request $request
    ) {
    }

    public function index()
    {
        try {
            $request = new EventFilterRequest();
            $request->validate();
            $prev = $request->get("prev");
            $per_page = $request->get("per_page", 10);
            $current = $request->get("current");
            $events = $this->eventService->index(
                request: $request
            );
            $next = $request->get("next", $events["nextPageToken"] ?? null);
            return View::make("events/index.php", [
                "limits" => $this->limits,
                "events" => $events,
                "next" => $next,
                "current" => $current,
                "prev" => $prev,
                "per_page" => $per_page,
                "search" => $request->get("search"),
                "start_time" => $request->get("start_time"),
                "end_time" => $request->get("end_time"),
            ]);
        } catch (Exception $ex) {
            return $this->exceptionResponse($ex);
        }
    }

    public function create()
    {
        return View::make("events/create.php");
    }

    public function store()
    {
        $request = new EventRequest();
        $request->validate();
        try {
            $this->eventService->create(
                request: $request
            );
            return redirect("event", [
                "success" => "Event created successfully"
            ]);
        } catch (Exception $exception) {
            return redirectBack([
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function edit()
    {
        try {
            $id = $this->request->get("id", "");
            if (!$id) {
                throw new ModelNotFoundException("Event not found");
            }
            $event = $this->eventService->show(
                id: $id
            );
            return View::make("events/edit.php", [
                "event" => objectToArray($event)
            ]);
        } catch (Exception $ex) {
            return $this->exceptionResponse($ex);
        }
    }

    public function update()
    {
        $request = new EventRequest();
        $request->validate();
        try {
            $id = $this->request->get("id");
            if (!$id) {
                throw new ModelNotFoundException("Event not found");
            }
            $event = $this->eventService->show(
                id: $id
            );
            $this->eventService->update(
                request: $request,
                event: $event
            );
            return redirect("event", [
                "success" => "Event updated successfully"
            ]);
        } catch (Exception $exception) {
            return redirectBack([
                "error" => $exception->getMessage()
            ]);
        }
    }

    public function destroy()
    {
        try {
            $id = $this->request->get("id");
            if (!$id) {
                throw new ModelNotFoundException("Event not found");
            }
            $event = $this->eventService->show(
                id: $id
            );
            $this->eventService->delete(
                event: $event
            );
            return redirect("event", [
                "success" => "Event deleted successfully"
            ]);
        } catch (Exception $exception) {
            return redirectBack([
                "error" => $exception->getMessage()
            ]);
        }
    }
}
