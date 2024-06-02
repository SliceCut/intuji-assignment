<?php

namespace App\Services;

use App\Cores\Request;
use App\Models\DateTime;
use App\Models\Event;
use App\Services\HttpService;
use App\Services\Singleton\Auth;
use App\Traits\HandleException;

class EventService
{
    use HandleException;

    public function __construct(
        protected HttpService $httpService,
        protected Auth $auth
    ) {
    }

    public function index(Request $request): array
    {
        $query_params = [
            "key" => config("oauth.api_key"),
            "maxResults" => $request->get("per_page", 10)
        ];

        if ($request->get("current")) {
            $query_params["pageToken"] = $request->get("current");
        }

        if ($request->get("search")) {
            $query_params["q"] = $request->get("search");
        }

        if ($request->get("start_time")) {
            $query_params["timeMin"] = convertDateTimeToRFC3339Format($request->get("start_time"));
        }

        if ($request->get("end_time")) {
            $query_params["timeMax"] = convertDateTimeToRFC3339Format($request->get("end_time"));
        }

        $response = $this->httpService->get(
            url: config("services.event_url"),
            query: $query_params,
            headers: [
                'Authorization: Bearer ' . $this->auth->token(),
            ]
        );

        $this->throwException($response);

        return $response["payload"];
    }

    public function show(string $id): Event
    {
        $response = $this->httpService->get(
            url: config("services.event_url") . "/" . $id,
            headers: [
                'Authorization: Bearer ' . $this->auth->token()
            ]
        );

        $this->throwException($response);

        $payload = $response["payload"];

        $event = new Event(
            id: $payload["id"],
            summary: $payload["summary"] ?? "",
            description: $payload["description"] ?? "",
            location: $payload["location"] ?? "",
            start: new DateTime(
                dateTime: formatDateTime($payload["start"]["dateTime"]),
                timeZone: $payload["start"]["timeZone"]
            ),
            end: new DateTime(
                dateTime: formatDateTime($payload["end"]["dateTime"]),
                timeZone: $payload["end"]["timeZone"]
            ),
            creator: $payload["creator"],
            organizer: $payload["organizer"]
        );

        return $event;
    }

    public function create(Request $request, string $timeZone = "UTC"): array
    {
        $response = $this->httpService->post(
            url: config("services.event_url"),
            request_data: [
                "start" => [
                    "dateTime" => convertDateTimeToRFC3339Format($request->start_time),
                    "timeZone" => $request->start_time_zone ?? $timeZone
                ],
                "end" => [
                    "dateTime" => convertDateTimeToRFC3339Format($request->end_time),
                    "timeZone" => $request->end_time_zone ?? $timeZone
                ],
                "summary" => $request->summary,
                "description" => $request->description,
                "location" => $request->location,
            ],
            headers: [
                'Authorization: Bearer ' . $this->auth->token()
            ]
        );

        $this->throwException($response);

        return $response["payload"];
    }

    public function update(Request $request, Event $event, string $timeZone = "UTC")
    {
        $response = $this->httpService->update(
            url: config("services.event_url") . "/" . $event->id,
            request_data: [
                "start" => [
                    "dateTime" => convertDateTimeToRFC3339Format($request->start_time),
                    "timeZone" => $request->start_time_zone ?? $timeZone
                ],
                "end" => [
                    "dateTime" => convertDateTimeToRFC3339Format($request->end_time),
                    "timeZone" => $request->end_time_zone ?? $timeZone
                ],
                "summary" => $request->summary,
                "description" => $request->description,
                "location" => $request->location,
            ],
            headers: [
                'Authorization: Bearer ' . $this->auth->token()
            ]
        );

        $this->throwException($response);

        return $response["payload"];
    }

    public function delete(Event $event): void
    {
        $response = $this->httpService->delete(
            url: config("services.event_url") . "/" . $event->id,
            headers: [
                'Authorization: Bearer ' . $this->auth->token()
            ]
        );
        $this->throwException($response);
    }
}
