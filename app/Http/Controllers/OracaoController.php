<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Oracoes;

class OracaoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $oracoes = Oracoes::all();
        return response()->json($oracoes);
    }

    /** 
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
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
            'mensagem' => 'required|string|max:1000',
        ]);

        $sacerdotes = User::where('role', 'sacerdote')->get();

        $oracoesCriadas = [];
 
        foreach ($sacerdotes as $sacerdote) {
            $oracao = Oracoes::create([
                'user_id' => Auth::id(),
                'sacerdote_id' => $sacerdote->id,
                'mensagem' => $request->mensagem,
            ]);

            $oracoesCriadas[] = $oracao;
        }

        return response()->json([
            'message' => 'Pedido de oração enviado.',
            'total_sacerdotes' => count($oracoesCriadas),
            'oracoes' => $oracoesCriadas,
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
    public function marcarComoLida($id)
    {
        $oracao = Oracoes::find($id);
        if (!$oracao) {
            return response()->json(['erro' => 'Oração não encontrada'], 404);
        }
        $oracao->delete(); // ou $oracao->update(['lida' => true]);
        return response()->json(['mensagem' => 'Marcada como lida']);
    }
    
    public function ultimasOracoes()
    {
        $oracoes = Oracoes::latest()->take(3)->get();
        return response()->json($oracoes);
    }

}
