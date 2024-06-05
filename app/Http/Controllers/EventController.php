<?php
// app/Http/Controllers/EventController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EventController extends Controller
{
    public function getEvents()
    {
        $events = [
            ['title' => 'Event 1', 'start' => '2024-06-01'],
            ['title' => 'Event 2', 'start' => '2024-06-02'],
        ];

        return response()->json($events);
    }
}
