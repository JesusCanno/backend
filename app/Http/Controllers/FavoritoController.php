<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorito;
use App\Models\Inmueble;

class FavoritoController extends Controller
{
    public function index(Request $request)
    {
        $favoritos = Favorito::with('inmueble')
            ->where('user_id', $request->user()->id)
            ->get();
        return response()->json($favoritos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'inmueble_id' => 'required|exists:inmueble,id'
        ]);
        $favorito = Favorito::firstOrCreate([
            'user_id' => $request->user()->id,
            'inmueble_id' => $request->inmueble_id
        ]);
        return response()->json($favorito, 201);
    }

    public function destroy(Request $request, $inmueble_id)
    {
        $favorito = Favorito::where('user_id', $request->user()->id)
            ->where('inmueble_id', $inmueble_id)
            ->first();
        if ($favorito) {
            $favorito->delete();
            return response()->json(['message' => 'Eliminado de favoritos']);
        }
        return response()->json(['message' => 'No encontrado'], 404);
    }
}
