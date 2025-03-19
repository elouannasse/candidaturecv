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
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class OffreController extends Controller
{
    use AuthorizesRequests;



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
        $this->authorize('create', Offre::class);

        $details = [
            'title' => $request->title,
            'lieu' => $request->lieu,
            'content' => $request->content,
            'email' => $request->email,
            'recruter_id'=>Auth::id()

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
        $offre = $this->offreRepositoryInterface->getById($id);
        $this->authorize('update', $offre);

        $updateDetails = [
            'title' => $request->title,
            'lieu' => $request->lieu,
            'content' => $request->content,
            'email' => $request->email,
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
        $offre = $this->offreRepositoryInterface->getById($id);
        $this->authorize('delete', $offre);
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


        $offre = $this->offreRepositoryInterface->getById($offre_id);
        $this->authorize('apply',$offre );

        $user = auth()->user();


        if ($user->offres()->where('offre_id', $offre_id)->exists()) {
            return response()->json([
                'message' => ' deja postule a cette offre',
            ], 422);
        }

        // $offre = Offre::findOrFail($offre_id);


        $user->offres()->attach($offre_id, ['cv' => $request->cv]);

        $this->sendEmailNotification($offre->email, $user, $request->cv);



        return response()->json([
            'message' => 'submitted successfully',
            'cv' => $request->cv
        ], 201);
    }




    private function sendEmailNotification($offreEmail, $user, $cvContent)
    {

        $mail = new PHPMailer(true);

        try {

            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = config('mail.mailers.smtp.username');
            $mail->Password = 'dhva riqj jhsa zwem';
            $mail->SMTPSecure = 'ssl';
            $mail->Port = 465;

            $mail->setFrom(config('mail.mailers.smtp.username'), 'healthCare ');
            $mail->addAddress($offreEmail);

            $mail->isHTML(true);
            $mail->Subject = 'New Job ' . $user->name;
            $mail->Body    = "<h3>New Application Received</h3>
                          <p><strong>Applicant Name:</strong> {$user->name}</p>
                          <p><strong>Email:</strong> {$user->email}</p>
                          <p><strong>CV Content:</strong> {$cvContent}</p>";

            $mail->send();
        } catch (Exception $e) {
            dd('Email could not be sent. Mailer Error: ');
        }
    }



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
