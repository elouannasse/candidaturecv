<?php

namespace App\Http\Controllers;

use App\Models\Offre;
use App\Http\Requests\StoreOffreRequest;
use App\Http\Requests\UpdateOffreRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class OffreController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create() {}

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [

            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'lieu' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => 'all the fields are required',
                'errors' => $validator->errors()
            ], 422);
        }

        $offre = Offre::create($request->all());
        return response()->json([
            'status' => true,
            'message' => 'The offre was created successfully',
            'data' => $offre
        ], 201);




        // try {
        //     $validated = $request->validate([
        //         'title' => 'required|string|max:255',
        //         'content' => 'required|string',
        //         'lieu' => 'required|string|max:255',
        //     ]);
        // } catch (ValidationException $e) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => 'Validation failed!',
        //         'errors' => $e->errors()
        //     ], 422);
        // }


        // $offre = Offre::create($validated);

        // return response()->json([
        //     'status' => true,
        //     'message' => 'The offre was created successfully.',
        //     'data' => $offre
        // ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {

        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json([
                'status' => false,
                'message' => 'offre not found ',
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'offre is found succefully ',
            'data' => $offre
        ], 200);
    }

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
    public function update(Request $request,  $id)
    {

        $validator = Validator::make($request->all(), [

            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'lieu' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {

            return response()->json([
                'status' => false,
                'message' => 'all the fields are required',
                'errors' => $validator->errors()
            ], 422);
        }

        $offre=Offre::find($id);

        if (!$offre) {
            return response()->json([
                'status' => false,
                'message' => 'offre not found ',
            ], 404);
        }

        $offre->update($request->all());

        return response()->json([
            'status'=>true,
            'message'=>'updated succefully',
            'data'=>$offre,
        ],200);

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $offre = Offre::find($id);

        if (!$offre) {
            return response()->json([
                'status' => false,
                'message' => ' offre not found ',
            ], 404);
        }

        $offre->delete();

        return response()->json([
            'status' => true,
            'message' => ' offre deleted succefully ',
        ], 200);
    }
}
