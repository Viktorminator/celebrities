<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index()
    {
        return response()->json(Event::all());
    }

    public function show($id)
    {
        $event = Event::findOrFail($id);
        return response()->json($event);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string',
            'date' => 'required|string',
            'description' => 'required|string',
            'image_url' => 'required|string',
            'category' => 'required|string',
            'celebrity_id' => 'required|exists:celebrities,id',
        ]);

        $event = Event::create($data);
        return response()->json($event, 201);
    }
}
