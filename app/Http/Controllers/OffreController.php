<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Models\Offre;
use App\Http\Requests\StoreOffreRequest;
use App\Http\Requests\UpdateOffreRequest;
use App\Http\Resources\OffreResource;
use App\Interfaces\OffreRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OffreController extends Controller
{


    private OffreRepositoryInterface $offreRepositoryInterface;

    public function __construct(OffreRepositoryInterface $offreRepositoryInterface)
    {

        $this->offreRepositoryInterface = $offreRepositoryInterface;
    }

    public function index()
    {

        $data = $this->offreRepositoryInterface->index();
        return ApiResponseClass::sendResponse(OffreResource::collection($data), '', 200);
    }

    public function store(StoreOffreRequest $request)
    {

        $details = [
            'title' => $request->title,
            'lieu' => $request->lieu,
            'content' => $request->content,
        ];

        DB::beginTransaction();
        try {

            $offre = $this->offreRepositoryInterface->store($details);
            DB::commit();
            return ApiResponseClass::sendResponse(new OffreResource($offre), 'offre create succefully', 201);
        } catch (\Throwable $th) {

            return ApiResponseClass::rollback($th);
        }
    }

    public function show($id)
    {
        $product = $this->offreRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new OffreResource($product), '', 200);
    }


    public function update(UpdateOffreRequest $request, $id)
    {
        $updateDetails = [
            'title' => $request->title,
            'lieu' => $request->lieu,
            'content' => $request->content,
        ];

        DB::beginTransaction();
        try {

            $offre = $this->offreRepositoryInterface->update($updateDetails, $id);
            DB::commit();
            return ApiResponseClass::sendResponse(new OffreResource($offre), 'offre Update  successfully', 200);
        } catch (\Throwable $th) {

            return ApiResponseClass::rollback($th);
        }
    }


    public function destroy($id)
    {
        try {
            $offre = $this->offreRepositoryInterface->getById($id);
            $this->offreRepositoryInterface->delete($id);
            return ApiResponseClass::sendResponse(null, 'Offre deleted successfully', 200);
        } catch (ModelNotFoundException $e) {
            return ApiResponseClass::sendResponse(null, 'Offre not found', 404);
        }
    }




    public function apply(Request $request, $offre_id)
    {

        $request->validate([
            'cv' => 'required'
        ]);

        // $user=Auth::user();
        $user=auth()->user();


        $user->offres()->attach($offre_id,['cv'=>$request->cv]);

        return response()->json([
            'message'=>'submitted successfully',
            'cv'=>$request->cv
        ],201);
    }

    // public function userApplications(){

    //     $user=auth()->user();
    //     $applications=$user->offres()->get();
    //     return response()->json($applications);

    // }



    /**
     * Display a listing of the resource.
     */

    /**
     * Show the form for creating a new resource.
     */

    /**
     * Store a newly created resource in storage.
     */



    //  -----------------------------------------------------------------------------------
    // public function store(Request $request)
    // {

    //     $validator = Validator::make($request->all(), [

    //         'title' => 'required|string|max:255',
    //         'content' => 'required|string',
    //         'lieu' => 'required|string|max:255',
    //     ]);

    //     if ($validator->fails()) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'all the fields are required',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $offre = Offre::create($request->all());
    //     return response()->json([
    //         'status' => true,
    //         'message' => 'The offre was created successfully',
    //         'data' => $offre
    //     ], 201);

    // }


    /**
     * Display the specified resource.
     */
    // public function show($id)
    // {

    //     $offre = Offre::find($id);

    //     if (!$offre) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'offre not found ',
    //         ], 404);
    //     }

    //     return response()->json([
    //         'status' => true,
    //         'message' => 'offre is found succefully ',
    //         'data' => $offre
    //     ], 200);
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Offre $offre)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request,  $id)
    // {

    //     $validator = Validator::make($request->all(), [

    //         'title' => 'required|string|max:255',
    //         'content' => 'required|string',
    //         'lieu' => 'required|string|max:255',
    //     ]);

    //     if ($validator->fails()) {

    //         return response()->json([
    //             'status' => false,
    //             'message' => 'all the fields are required',
    //             'errors' => $validator->errors()
    //         ], 422);
    //     }

    //     $offre=Offre::find($id);

    //     if (!$offre) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => 'offre not found ',
    //         ], 404);
    //     }

    //     $offre->update($request->all());

    //     return response()->json([
    //         'status'=>true,
    //         'message'=>'updated succefully',
    //         'data'=>$offre,
    //     ],200);

    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy($id)
    // {
    //     $offre = Offre::find($id);

    //     if (!$offre) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => ' offre not found ',
    //         ], 404);
    //     }

    //     $offre->delete();

    //     return response()->json([
    //         'status' => true,
    //         'message' => ' offre deleted succefully ',
    //     ], 200);
    // }
}
