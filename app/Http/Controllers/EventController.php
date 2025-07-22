<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Event::all());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
   public function store(Request $request)
{
    $request->validate([
        'title' => 'required|string',
        'date' => 'required|date',
        'time' => 'required',
        'location' => 'required|string',
        'description' => 'required|string',
        'image' => 'required|image|max:2048',
    ]);

    // Salvar imagem na pasta storage/app/public/events
    $image = $request->file('image');
    $imageName = $image->hashName(); // nome único
    $image->storeAs('public/events', $imageName); // armazenamento correto

    // Criar evento
    $event = Event::create([
        'title' => $request->title,
        'date' => $request->date,
        'time' => $request->time,
        'location' => $request->location,
        'description' => $request->description,
        'image' => $imageName, // apenas o nome
    ]);

    return response()->json($event, 201);
}


    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
     public function edit($id)
    {
        $event = Event::find($id);
        if (!$event) {
            return response()->json(['message' => 'Evento não encontrado'], 404);
        }
        return response()->json($event);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
  

public function update(Request $request, $id)
{
    $event = Event::findOrFail($id);

    $request->validate([
        'title' => 'required|string',
        'date' => 'required|date',
        'time' => 'required',
        'location' => 'required|string',
        'description' => 'required|string',
        'image' => 'nullable|image|max:2048',
    ]);

    // Atualiza campos básicos
    $event->title = $request->title;
    $event->date = $request->date;
    $event->time = $request->time;
    $event->location = $request->location;
    $event->description = $request->description;

    // Se nova imagem for enviada
    if ($request->hasFile('image')) {
        // Deleta imagem antiga, se existir
        if ($event->image && Storage::disk('public')->exists("events/{$event->image}")) {
            Storage::disk('public')->delete("events/{$event->image}");
        }

        // Salva nova imagem
        $image = $request->file('image');
        $imageName = $image->hashName();
        $image->storeAs('public/events', $imageName);

        $event->image = $imageName;
    }

    $event->save();

    return response()->json($event);
}


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $event = Event::findOrFail($id);

        // Deleta a imagem, se existir
        if ($event->image && Storage::disk('public')->exists("events/{$event->image}")) {
            Storage::disk('public')->delete("events/{$event->image}");
        }

        // Deleta o evento do banco
        $event->delete();

        return response()->json(['message' => 'Evento deletado com sucesso']);
    }

    
}
