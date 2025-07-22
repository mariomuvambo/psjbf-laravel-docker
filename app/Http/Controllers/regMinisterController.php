<?php

namespace App\Http\Controllers;

use App\Models\regMinister;
use Illuminate\Http\Request;

class regMinisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $ministers = RegMinister::all();

        // Retorna os dados dos ministros em formato JSON
        return response()->json($ministers);
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
        $request->validate([
            'newMinister' => 'required|string|max:255',
            'finally' => 'required|string',
            'responseMinister' => 'required|string',
            'responseAdjunto' => 'required|string',
            'SectorGeral' => 'required|string',
            'SectorMinister' => 'required|string',
        ]);

        $regMinister = RegMinister::create($request->all());

        return response()->json($regMinister, 201);
          
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $regMinister = RegMinister::findOrFail($id);
        return response()->json($regMinister);
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
        $regMinister = RegMinister::findOrFail($id);

        $request->validate([
            'newMinister' => 'sometimes|string|max:255',
            'finally' => 'sometimes|string',
            'responseMinister' => 'sometimes|string',
            'responseAdjunto' => 'sometimes|string',
            'SectorGeral' => 'sometimes|string',
            'SectorMinister' => 'sometimes|string',
        ]);

        $regMinister->update($request->all());

        return response()->json($regMinister);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $regMinister = RegMinister::findOrFail($id);
        $regMinister->delete();

        return response()->json(['message' => 'Registro deletado com sucesso.']);
    }
}
