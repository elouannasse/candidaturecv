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


         /**
 * @OA\Get(
 *     path="/api/competences",
 *     summary="Lister toutes les compétences disponibles",
 *     tags={"Compétences"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des compétences récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data", 
 *                 type="array", 
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time")
 *                 )
 *             )
 *         )
 *     )
 * )
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


    /**
 * @OA\Get(
 *     path="/api/user/competences",
 *     summary="Obtenir les compétences de l'utilisateur connecté",
 *     tags={"Compétences"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Response(
 *         response=200,
 *         description="Compétences de l'utilisateur récupérées avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="boolean"),
 *             @OA\Property(property="message", type="string"),
 *             @OA\Property(
 *                 property="data", 
 *                 type="array", 
 *                 @OA\Items(
 *                     @OA\Property(property="id", type="integer"),
 *                     @OA\Property(property="name", type="string"),
 *                     @OA\Property(property="created_at", type="string", format="date-time"),
 *                     @OA\Property(property="updated_at", type="string", format="date-time"),
 *                     @OA\Property(property="pivot", type="object")
 *                 )
 *             )
 *         )
 *     )
 * )
 */


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
