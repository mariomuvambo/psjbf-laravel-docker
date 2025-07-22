<?php

namespace App\Http\Controllers;

use App\Models\Aviso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\AvisoNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;



class AvisoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
    
        // Carrega todos os avisos com a relação usersLidos para o usuário atual
        $avisos = Aviso::with(['usersLidos' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }])->get();
    
        // Listas separadas
        $avisosLidos = [];
        $avisosNaoLidos = [];
    
        foreach ($avisos as $aviso) {
            $avisoFormatado = [
                'id' => $aviso->id,
                'title' => $aviso->title,
                'date_notify' => $aviso->date_notify,
                'date_realize' => $aviso->date_realize,
                'hora' => $aviso->hora,
                'address' => $aviso->address,
                'description' => $aviso->description,
                'lido' => $aviso->usersLidos->isNotEmpty(),
            ];
    
            if ($avisoFormatado['lido']) {
                $avisosLidos[] = $avisoFormatado;
            } else {
                $avisosNaoLidos[] = $avisoFormatado;
            }
        }
    
        return response()->json([
            'total_lidos' => count($avisosLidos),
            'total_nao_lidos' => count($avisosNaoLidos),
            'avisos_lidos' => $avisosLidos,
            'avisos_nao_lidos' => $avisosNaoLidos,
        ]);
    }
    


     // Marcar um aviso como lido pelo usuário autenticado
     public function marcarComoLido($id)
     {
         $user = Auth::user();
         $aviso = Aviso::findOrFail($id);
 
         // Se já foi marcado como lido, retorna sem modificar
         if ($aviso->usersLidos()->where('user_id', $user->id)->exists()) {
             return response()->json(['message' => 'Aviso já foi lido'], 200);
         }
 
         // Marca como lido
         $aviso->usersLidos()->attach($user->id);
 
         return response()->json(['message' => 'Aviso marcado como lido com sucesso'], 200);
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
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'date_notify' => 'required|date',
        'address' => 'required|string|max:255',
        'date_realize' => 'required|date',
        'hora' => 'required|date_format:H:i',
        'description' => 'nullable|string|max:500',
    ]);

    $aviso = Aviso::create($validatedData);

    // Enviar e-mail para os usuários registrados
    $usuarios = \App\Models\User::all();
    foreach ($usuarios as $usuario) {
        // Enviar o e-mail para o usuário
        Mail::to($usuario->email)->queue(new AvisoNotification($aviso));
    }

    return response()->json(['message' => 'Aviso criado e e-mail enviado com sucesso', 'data' => $aviso], 201);
}
    


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $aviso = Aviso::find($id);

        if (!$aviso) {
            return response()->json(['message' => 'Aviso não encontrado'], 404);
        }

        return response()->json($aviso);
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
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'date_notify' => 'required|date',
            'date_realize' => 'required|date',
            'hora' => 'required|date_format:H:i',
            'address' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);
    
        $aviso = Aviso::findOrFail($id);
        $aviso->update($validatedData);
    
        return response()->json(['message' => 'Aviso atualizado com sucesso!']);
    }
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $aviso = Aviso::find($id);

        if (!$aviso) {
            return response()->json(['message' => 'Aviso não encontrado'], 404);
        }

        $aviso->delete();

        return response()->json(['message' => 'Aviso excluído com sucesso']);
    }




        public function estatisticas()
        {
            $user = Auth::user();

            $avisosPorMes = Aviso::select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                    DB::raw("COUNT(*) as total")
                )
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();

            $avisosLidos = DB::table('aviso_user')
                ->select(
                    DB::raw("DATE_FORMAT(created_at, '%Y-%m') as mes"),
                    DB::raw("COUNT(*) as total")
                )
                ->where('user_id', $user->id)
                ->groupBy('mes')
                ->orderBy('mes')
                ->get();

            return response()->json([
                'avisos_por_mes' => $avisosPorMes,
                'avisos_lidos' => $avisosLidos
            ]);
        }




}
