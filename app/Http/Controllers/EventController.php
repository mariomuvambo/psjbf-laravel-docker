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
    // Verifica se já existe evento na mesma data
    $existingEvent = Event::whereDate('date', $request->date)->count();
    if ($existingEvent > 0) {
        // Se já houver evento na mesma data, retorna mensagem de erro
        return response()->json([
            'message' => '❌ Já existe um evento nesta data. Por favor, escolha outra data.'
        ], 400);  // Código HTTP 400 para erro de validação
    }

    // Validação dos dados de entrada
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

    return response()->json($event, 201);  // Retorna o evento criado com sucesso
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

    public function eventsForDate(Request $request)
{
    // Valida se a data foi fornecida e se é uma data válida
    $request->validate([
        'date' => 'required|date',
    ]);

    // Obtém os eventos para a data fornecida
    $date = $request->date;
    
    // Consulta os eventos para a data específica
    $events = Event::whereDate('date', $date)->get();

    // Retorna os eventos encontrados como uma resposta JSON, formatando a data corretamente
    return response()->json($events->map(function ($event) {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'date' => $event->date, // Data sem hora
            'time' => $event->time, // Hora
            'location' => $event->location,
            'description' => $event->description,
            'image' => $event->image,
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ];
    }));
}

  public function getEventsOfCurrentMonth()
    {
        $currentMonth = now()->month;
        $currentYear = now()->year;

        // Contar eventos do mês atual
        $eventosDoMes = Event::whereYear('date', $currentYear)
                             ->whereMonth('date', $currentMonth)
                             ->count();

        // Retornar como resposta JSON
        return response()->json([
            'stats' => [
                'label' => 'Eventos Ativos',
                'valor' => $eventosDoMes,
                'color' => 'bg-danger',  // Cor de fundo para o card
            ]
        ]);
    }


}
