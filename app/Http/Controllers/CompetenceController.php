<?php

namespace App\Http\Controllers;

use App\Models\Competence;
use App\Http\Requests\StoreCompetenceRequest;
use App\Http\Requests\UpdateCompetenceRequest;
use Illuminate\Support\Facades\Auth;

class CompetenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */



    public function index()
    {
        $competences = Competence::all();

        return response()->json([
            'status' => true,
            'message' => 'available competence retrieved succefully',
            'data' => $competences
        ]);
    }


    public function getUserCompetences()
    {
        $user = Auth::user();
        $competences = $user->competences;


        return response()->json([
            'status' => true,
            'message' => 'get user competences success',
            'data' => $competences
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCompetenceRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Competence $competence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Competence $competence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCompetenceRequest $request, Competence $competence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Competence $competence)
    {
        //
    }
}
