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
public function __construct()
    {
        // Remove autenticação para o index (listar eventos)
        $this->middleware('auth:sanctum')->except(['index']);
    }

public function index()
{
    $events = Event::orderBy('date', 'asc')->get()->map(function ($event) {
        return [
            'id' => $event->id,
            'title' => $event->title,
            'date' => $event->date,
            'time' => $event->time,
            'location' => $event->location,
            'description' => $event->description,
            'image' => $event->image,
            // 🧠 Aqui montamos a URL completa para o Cloudflare R2:
            'image_url' => Storage::disk('s3')->url($event->image),
            'created_at' => $event->created_at,
            'updated_at' => $event->updated_at,
        ];
    });

    return response()->json($events);
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
        // Evita eventos duplicados na mesma data
        if (Event::whereDate('date', $request->date)->exists()) {
            return response()->json([
                'message' => '❌ Já existe um evento nesta data. Escolha outra data.'
            ], 400);
        }

        // Validação
        $request->validate([
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'required',
            'location' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'required|image|max:2048',
        ]);

        // 📸 Upload da imagem no Cloudflare R2
        $path = $request->file('image')->store('eventos', 's3'); // "eventos" é a pasta no bucket

        // Criar evento
        $event = Event::create([
            'title' => $request->title,
            'date' => $request->date,
            'time' => $request->time,
            'location' => $request->location,
            'description' => $request->description,
            'image' => $path,
        ]);

        return response()->json([
            'message' => '✅ Evento criado com sucesso!',
            'event' => $event
        ], 201);
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
        'title' => 'required|string|max:255',
        'date' => 'required|date',
        'time' => 'required',
        'location' => 'required|string|max:255',
        'description' => 'required|string',
        'image' => 'nullable|image|max:2048',
    ]);

    $event->fill($request->only(['title', 'date', 'time', 'location', 'description']));

    if ($request->hasFile('image')) {
        // Exclui a imagem antiga (opcional)
        if ($event->image && Storage::disk('s3')->exists($event->image)) {
            Storage::disk('s3')->delete($event->image);
        }

        // 📸 Envia nova imagem para R2
        $event->image = $request->file('image')->store('eventos', 's3');
    }

    $event->save();

    return response()->json([
        'message' => '✅ Evento atualizado com sucesso!',
        'event' => $event
    ]);
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
