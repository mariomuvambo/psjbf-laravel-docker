<?php

namespace App\Http\Controllers;

use App\Models\Profiluser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfiluserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        return response()->json(Profiluser::all(), 200);
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
            'name' => 'required|string',
            'surname' => 'required|string',
            'date_birth' => 'required|date',
            'nucleo' => 'required|string',
            'minister' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('profilusers', 'public'); 

        }
       

        $profiluser = Profiluser::create([
            'name' => $request->name,
            'surname' => $request->surname,
            'date_birth' => $request->date_birth,
            'nucleo' => $request->nucleo,
            'minister' => $request->minister,
            'image' => asset('storage/' . $imagePath),

        ]);

        return response()->json($profiluser, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Profiluser $profiluser)
    {
        $profiluser->image = $profiluser->image ? asset('storage/' . $profiluser->image) : null;
        return response()->json($profiluser, 200);
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
    public function update(Request $request, Profiluser $profiluser)
    {
        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'surname' => 'nullable|string|max:255',
            'date_birth' => 'nullable|date',
            'nucleo' => 'nullable|string|max:255',
            'minister' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($profiluser->image && Storage::disk('public')->exists(str_replace('/storage/', '', $profiluser->image))) {
                Storage::disk('public')->delete(str_replace('/storage/', '', $profiluser->image));
            }

            $imagePath = $request->file('image')->store('profilusers', 'public');
            $validated['image'] = '/storage/' . $imagePath;
        }

        $profiluser->update($validated);

        return response()->json($profiluser, 200);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Profiluser $profiluser)
    {
        if ($profiluser->image) {
            $imagePath = str_replace('/storage/', '', $profiluser->image);
            if (Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        $profiluser->delete();

        return response()->json(['message' => 'Perfil excluído com sucesso!'], 200);
    }


    public function processo()
    {
        $user = auth()->user();
        $processo = $user->processos()->latest()->first(); 
        return response()->json($processo);
    }


}
