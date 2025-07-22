<?php

namespace App\Http\Controllers;

use App\Models\Doacao;
use Illuminate\Http\Request;

class DoacaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
            public function index()
        {
            return response()->json(
                Doacao::where('user_id', auth()->id())
                    ->orderBy('data_doacao', 'desc')
                    ->get()
            );
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
            'nome_doador' => 'nullable|string|max:100',
            'valor' => 'required|numeric|min:0.01',
            'meio' => 'required|in:Dinheiro,Transferência,M-Pesa,eFectivo',
        ]);

        $doacao = Doacao::create([
            'nome_doador' => $request->nome_doador,
            'valor' => $request->valor,
            'data_doacao' => now(), 
            'meio' => $request->meio,
            'user_id' => auth()->id(),
        ]);

        return response()->json($doacao, 201);
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
        $doacao = Doacao::findOrFail($id);
        $doacao->delete();
        return response()->noContent();
    }
}
