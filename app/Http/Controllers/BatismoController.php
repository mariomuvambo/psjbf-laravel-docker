<?php

namespace App\Http\Controllers;


use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Batismo;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Mail\EstadoBatismoAtualizado;

class BatismoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
          return Batismo::orderBy('created_at', 'desc')->get();
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
        'nome_batizando' => 'required|string',
        'data_nascimento' => 'required|date',
        'local_nascimento' => 'required|string',
        'nome_pai' => 'required|string',
        'nome_mae' => 'required|string',
        'nome_padrinho' => 'required|string',
        'nome_madrinha' => 'required|string',
        'documento_identificacao' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
    ]);
     $caminhoDocumento = $request->file('documento_identificacao')->store('documentos_batismo', 'public');

        
    $batismo = Batismo::create(array_merge(
        $validated,
            [
                'documento_identificacao' => $caminhoDocumento,
                'user_id' => Auth::id(),
                'estado' => 'pendente',
            ]
        ));
          return response()->json(['message' => 'Pedido enviado com sucesso.', 'batismo' => $batismo], 201);


    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $batismo = Batismo::with('sacerdote')->find($id);

        if (!$batismo) {
            return response()->json(['message' => 'Batismo não encontrado'], 404);
        }

        return response()->json($batismo);
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
         $batismo = Batismo::find($id);
        if (!$batismo) {
            return response()->json(['message' => 'Batismo não encontrado'], 404);
        }

        $validated = $request->validate([
            'nome_batizando' => 'sometimes|string',
            'data_nascimento' => 'sometimes|date',
            'local_nascimento' => 'sometimes|string',
            'nome_pai' => 'sometimes|string',
            'nome_mae' => 'sometimes|string',
            'nome_padrinho' => 'sometimes|string',
            'nome_madrinha' => 'sometimes|string',
            'data_batismo' => 'sometimes|date',
            'confirmado' => 'sometimes|boolean',
        ]);
         $batismo->update($validated);
        return response()->json($batismo);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
         $batismo = Batismo::find($id);
        if (!$batismo) {
            return response()->json(['message' => 'Batismo não encontrado'], 404);
        }

        $batismo->delete();
        return response()->json(['message' => 'Batismo removido com sucesso']);
    }

    public function atualizarEstado(Request $request, $id)
    {
        $request->validate([
            'estado' => 'required|in:em_analise,aprovado,rejeitado',
            'data_batismo' => 'nullable|date' // <- DEIXE OPCIONAL
        ]);


        $batismo = Batismo::findOrFail($id);
        $batismo->estado = $request->estado;

        // Atualiza data do batismo, se enviada
        if ($request->filled('data_batismo')) {
            $batismo->data_batismo = $request->data_batismo;
        }

        $batismo->save();

        // Envia e-mail de forma assíncrona
        Mail::to($batismo->user->email)->queue(new EstadoBatismoAtualizado($batismo));

        return response()->json(['message' => 'Estado atualizado e e-mail enviado com sucesso.']);
    }


   


    public function pendentes()
    {
        $batismos = Batismo::where('estado', 'pendente')->get();
        return response()->json($batismos, 200);
    }

    public function aprovados()
    {
        $batismos = Batismo::where('estado', 'aprovado')->get();
        return response()->json($batismos, 200);
    }
    public function rejeitados()
    {
        $batismos = Batismo::where('estado', 'rejeitado')->get();
        return response()->json($batismos, 200);
    }

    public function emAnalise()
    {
        $batismos = Batismo::whereIn('estado', ['em_analise'])->get();
        return response()->json($batismos, 200);
    }

    
 
}

