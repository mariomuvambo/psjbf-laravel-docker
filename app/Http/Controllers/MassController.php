<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Mass;
use Carbon\Carbon;


class MassController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
         return response()->json(Mass::orderBy('date', 'desc')->get());
        //
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
        //
        $validated = $request->validate([
            'date' => 'required|date', 
            'time' => 'required|date_format:H:i',
            'liturgical_day' => 'required|string|max:255',
            'first_reading' => 'nullable|string|max:255',
            'first_reader' => 'nullable|string|max:255',
            'psalm' => 'nullable|string|max:255',
            'psalm_reader' => 'nullable|string|max:255',
            'second_reading' => 'nullable|string|max:255',
            'second_reader' => 'nullable|string|max:255',
            'gospel' => 'nullable|string|max:255',
            'celebrant' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        $mass = Mass::create($validated);

        return response()->json([
            'message' => 'Missa registrada com sucesso!',
            'data' => $mass
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // Mostrar missa por data
    public function showByDate($date)
    {
        $masses = Mass::where('date', $date)->get();

        if ($masses->isEmpty()) {
            return response()->json(['message' => 'Nenhuma missa encontrada para esta data.'], 404);
        }

        return response()->json($masses);
    }



public function todayReadings()
{
    $today = Carbon::today()->toDateString();

    $mass = Mass::whereDate('date', $today)->first();

    if (!$mass) {
        return response()->json(['message' => 'Nenhuma missa encontrada para hoje.'], 404);
    }

    return response()->json([
        'date' => $mass->date,
        'time' => $mass->time,
        'liturgical_day' => $mass->liturgical_day,
        'first_reading' => $mass->first_reading,
        'first_reader' => $mass->first_reader,
        'psalm' => $mass->psalm,
        'psalm_reader' => $mass->psalm_reader,
        'second_reading' => $mass->second_reading,
        'second_reader' => $mass->second_reader,
        'gospel' => $mass->gospel,
        'celebrant' => $mass->celebrant,
    ]);
}



}
