<?php

namespace App\Models;

class Event
{
    public string $id;
    public ?string $summary;
    public DateTime $start;
    public DateTime $end;
    public ?string $description;
    public ?string $location;
    public ?array $creator;
    public ?array $organizer;

    public function __construct(
        string $id,
        ?string $summary,
        DateTime $start,
        DateTime $end,
        ?string $description,
        ?string $location,
        ?array $creator,
        ?array $organizer
    ) {
        $this->id = $id;
        $this->summary = $summary;
        $this->start = $start;
        $this->end = $end;
        $this->description = $description;
        $this->location = $location;
        $this->creator = $creator;
        $this->organizer = $organizer;
    }
}
