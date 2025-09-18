<?php

namespace App\Http\Controllers;

use App\Models\Carteira;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCarteiraRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarteiraController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $owner = $user->tipo_usuario === 'pf' ? $user->pessoaFisica : $user->pessoaJuridica;

        return response()->json($owner->carteiras);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCarteiraRequest $request)
    {
        $user = Auth::user();
        $owner = $user->tipo_usuario === 'pf' ? $user->pessoaFisica : $user->pessoaJuridica;

        $carteira = $owner->carteiras()->create($request->validated());

        return response()->json($carteira, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Carteira $carteira)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Carteira $carteira)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Carteira $carteira)
    {
        //
    }
}
