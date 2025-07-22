<?php

namespace App\Http\Controllers;

use App\Models\Casamento;
use App\Models\DocumentoCasamento;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CasamentoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $casamentos = Casamento::with('documentos')
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($casamentos);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        //   return view('casamentos.create');
          
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
            // 🔍 Validação dos dados
        $validator = Validator::make($request->all(), [
            'nome_noivo' => 'required|string|max:255',
            'nome_noiva' => 'required|string|max:255',
            'data_casamento' => 'required|date|after:today',
            'local_casamento' => 'nullable|string|max:255',

            // Precisam ser exatamente 4 documentos (2 do noivo, 2 da noiva)
            'documentos' => 'required|array|size:4',
            'documentos.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',

            'tipos_documentos' => 'required|array|size:4',
            'tipos_documentos.*' => 'required|string|in:BI,Certidão de Batismo',
        ]);

        // Se falhar a validação, retorna erro
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Erro de validação',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Criação do registro de casamento com o usuário autenticado
        $casamento = Casamento::create([
            'user_id' => Auth::id(), 
            'nome_noivo' => $request->nome_noivo,
            'nome_noiva' => $request->nome_noiva,
            'data_casamento' => $request->data_casamento,
            'local_casamento' => $request->local_casamento,
        ]);

        // Upload e associação de documentos
        $documentos = $request->file('documentos');
        $tipos = $request->input('tipos_documentos');

        foreach ($documentos as $index => $arquivo) {
            $path = $arquivo->store('documentos_casamentos', 'public');

            DocumentoCasamento::create([
                'casamento_id' => $casamento->id,
                'tipo_documento' => $tipos[$index],
                'arquivo' => $path,
            ]);
        }

        return response()->json([
            'message' => 'Solicitação registrada com sucesso!',
            'casamento_id' => $casamento->id,
            'nome_noivo' => $casamento->nome_noivo,
            'nome_noiva' => $casamento->nome_noiva,
            'documentos_enviados' => count($documentos),
        ], 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Casamento  $casamento
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $casamento = Casamento::with('documentos')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json($casamento);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Casamento  $casamento
     * @return \Illuminate\Http\Response
     */
    public function edit(Casamento $casamento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Casamento  $casamento
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //

        $casamento = Casamento::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        $request->validate([
            'nome_noivo' => 'sometimes|required|string|max:255',
            'nome_noiva' => 'sometimes|required|string|max:255',
            'data_casamento' => 'sometimes|required|date',
            'local_casamento' => 'nullable|string|max:255',
            'documentos' => 'sometimes|array',
            'documentos.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'tipos_documentos' => 'sometimes|array',
            'tipos_documentos.*' => 'nullable|string|in:BI,Certidão de Batismo',
        ]);

        $casamento->update($request->only(['nome_noivo', 'nome_noiva', 'data_casamento', 'local_casamento']));

        if ($request->hasFile('documentos') && is_array($request->file('documentos'))) {
            $tipos = $request->input('tipos_documentos', []);

            foreach ($request->file('documentos') as $index => $arquivo) {
                if (!$arquivo) continue;

                // Atualiza ou cria novo documento
                $documento = DocumentoCasamento::where('casamento_id', $casamento->id)
                    ->skip($index)
                    ->take(1)
                    ->first();

                $path = $arquivo->store('documentos_casamentos', 'public');

                if ($documento) {
                    if (Storage::disk('public')->exists($documento->arquivo)) {
                        Storage::disk('public')->delete($documento->arquivo);
                    }

                    $documento->update([
                        'tipo_documento' => $tipos[$index] ?? $documento->tipo_documento,
                        'arquivo' => $path,
                    ]);
                } else {
                    DocumentoCasamento::create([
                        'casamento_id' => $casamento->id,
                        'tipo_documento' => $tipos[$index] ?? 'BI',
                        'arquivo' => $path,
                    ]);
                }
            }
        }

        return response()->json(['message' => 'Registro atualizado com sucesso!']);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Casamento  $casamento
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $casamento = Casamento::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        foreach ($casamento->documentos as $doc) {
            if (Storage::disk('public')->exists($doc->arquivo)) {
                Storage::disk('public')->delete($doc->arquivo);
            }
            $doc->delete();
        }

        $casamento->delete();

        return response()->json(['message' => 'Registro removido com sucesso!']);
    }
    
    
}
