<?php

namespace App\Http\Controllers;

use App\Classes\ApiResponseClass;
use App\Exports\ExportApplication;
use App\Exports\ExportUser;
use App\Models\Offre;
use App\Http\Requests\StoreOffreRequest;
use App\Http\Requests\UpdateOffreRequest;
use App\Http\Resources\OffreResource;
use App\Interfaces\OffreRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Maatwebsite\Excel\Excel;

class OffreController extends Controller
{
    use AuthorizesRequests;



    private OffreRepositoryInterface $offreRepositoryInterface;

    public function __construct(OffreRepositoryInterface $offreRepositoryInterface)
    {

        $this->offreRepositoryInterface = $offreRepositoryInterface;
    }
  /**
 * @OA\Get(
 *     path="/api/offres",
 *     summary="Lister toutes les offres d'emploi",
 *     tags={"Offres"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des offres récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/Offre"))
 *         )
 *     )
 * )
 */
    public function index()
    {

        $data = $this->offreRepositoryInterface->index();
        return ApiResponseClass::sendResponse(OffreResource::collection($data), '', 200);
    }
    // * @OA\Post(
    //     *     path="/api/offres",
    //     *     summary="Créer une nouvelle offre d'emploi",
    //     *     tags={"Offres"},
    //     *     security={{"bearerAuth":{}}},
    //     *     @OA\RequestBody(
    //     *         required=true,
    //     *         @OA\JsonContent(
    //     *             required={"title", "lieu", "content", "email"},
    //     *             @OA\Property(property="title", type="string", example="Développeur Laravel"),
    //     *             @OA\Property(property="lieu", type="string", example="Casablanca"),
    //     *             @OA\Property(property="content", type="string", example="Nous recherchons un développeur Laravel expérimenté..."),
    //     *             @OA\Property(property="email", type="string", format="email", example="contact@entreprise.com")
    //     *         )
    //     *     ),
    //     *     @OA\Response(
    //     *         response=201,
    //     *         description="Offre créée avec succès",
    //     *         @OA\JsonContent(ref="#/components/schemas/Offre")
    //     *     ),
    //     *     @OA\Response(response=403, description="Non autorisé")
    //     * )

    public function store(StoreOffreRequest $request)
    {
        $this->authorize('create', Offre::class);

        $details = [
            'title' => $request->title,
            'lieu' => $request->lieu,
            'content' => $request->content,
            'email' => $request->email,
            'recruter_id' => Auth::id()

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

    // * @OA\Get(
    //     *     path="/api/offres/{id}",
    //     *     summary="Obtenir les détails d'une offre",
    //     *     tags={"Offres"},
    //     *     @OA\Parameter(
    //     *         name="id",
    //     *         in="path",
    //     *         required=true,
    //     *         description="ID de l'offre",
    //     *         @OA\Schema(type="integer")
    //     *     ),
    //     *     @OA\Response(
    //     *         response=200,
    //     *         description="Détails de l'offre récupérés avec succès",
    //     *         @OA\JsonContent(ref="#/components/schemas/Offre")
    //     *     ),
    //     *     @OA\Response(response=403, description="Non autorisé"),
    //     *     @OA\Response(response=404, description="Offre non trouvée")
    //     * )


    public function show($id)
    {
        $offre=$this->offreRepositoryInterface->getById($id);
        $this->authorize('view', $offre);


        $product = $this->offreRepositoryInterface->getById($id);

        return ApiResponseClass::sendResponse(new OffreResource($product), 'the offre is retrieved with success', 200);
    }


    // * @OA\Put(
    //     *     path="/api/offres/{id}",
    //     *     summary="Mettre à jour une offre",
    //     *     tags={"Offres"},
    //     *     security={{"bearerAuth":{}}},
    //     *     @OA\Parameter(
    //     *         name="id",
    //     *         in="path",
    //     *         required=true,
    //     *         description="ID de l'offre",
    //     *         @OA\Schema(type="integer")
    //     *     ),
    //     *     @OA\RequestBody(
    //     *         required=true,
    //     *         @OA\JsonContent(
    //     *             @OA\Property(property="title", type="string", example="Développeur Laravel Senior"),
    //     *             @OA\Property(property="lieu", type="string", example="Rabat"),
    //     *             @OA\Property(property="content", type="string", example="Mise à jour: Nous recherchons un développeur Laravel senior..."),
    //     *             @OA\Property(property="email", type="string", format="email", example="rh@entreprise.com")
    //     *         )
    //     *     ),
    //     *     @OA\Response(
    //     *         response=200,
    //     *         description="Offre mise à jour avec succès",
    //     *         @OA\JsonContent(ref="#/components/schemas/Offre")
    //     *     ),
    //     *     @OA\Response(response=403, description="Non autorisé"),
    //     *     @OA\Response(response=404, description="Offre non trouvée")
    //     * )


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

      
/**
 * @OA\Delete(
 *     path="/api/offres/{id}",
 *     summary="Supprimer une offre",
 *     tags={"Offres"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID de l'offre",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Offre supprimée avec succès"
 *     ),
 *     @OA\Response(response=403, description="Non autorisé"),
 *     @OA\Response(response=404, description="Offre non trouvée")
 * )
 */


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
        $this->authorize('apply', $offre);

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
    
    


    public function usersapplication()
    {
        $user = Auth::user();

        $application = $user->offres()->get();
        return response()->json($application);
    }



    /**
 * @OA\Get(
 *     path="/api/offres/{offre_id}/candidates",
 *     summary="Obtenir les candidats pour une offre spécifique",
 *     tags={"Recrutement"},
 *     security={{"bearerAuth":{}}},
 *     @OA\Parameter(
 *         name="offre_id",
 *         in="path",
 *         required=true,
 *         description="ID de l'offre",
 *         @OA\Schema(type="integer")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des candidats récupérée avec succès",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(type="object")
 *         )
 *     )
 * )
 */



    public function offrescontientUser($offre_id)
    {

        $offre = Offre::find($offre_id);
        $userss = $offre->users()->get();
        return response()->json($userss);
    }
    public function export_excel()
    {
        try {
            $fileName = 'users_' . time() . '.xlsx';
            $filePath = 'exports/' . $fileName;

            // Store the file in the public disk
            app('excel')->store(new ExportUser, $filePath, 'public');

            // Generate download URL
            $downloadUrl = url('storage/' . $filePath);

            return response()->json([
                'success' => true,
                'message' => 'Export generated successfully',
                'file_name' => $fileName,
                'download_url' => $downloadUrl,
                'user_count' => \App\Models\User::count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Export error: ' . $e->getMessage()
            ], 500);
        }
    }


/**
 * @OA\Schema(
 *     schema="Offre",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="title", type="string", example="Développeur Laravel"),
 *     @OA\Property(property="lieu", type="string", example="Casablanca"),
 *     @OA\Property(property="content", type="string", example="Description détaillée du poste..."),
 *     @OA\Property(property="email", type="string", format="email", example="contact@entreprise.com"),
 *     @OA\Property(property="recruter_id", type="integer", example=2),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="John Doe"),
 *     @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=3),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */

/**
 * @OA\Schema(
 *     schema="Competence",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="name", type="string", example="PHP"),
 *     @OA\Property(property="created_at", type="string", format="date-time"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
    public function export_applications()
{
    try {
        $fileName = 'applications_' . time() . '.xlsx';
        $filePath = 'exports/' . $fileName;

        // Store the file in the public disk
        app('excel')->store(new ExportApplication, $filePath, 'public');

        // Generate download URL
        $downloadUrl = url('storage/' . $filePath);

        return response()->json([
            'success' => true,
            'message' => 'Export generated successfully',
            'file_name' => $fileName,
            'download_url' => $downloadUrl,
            'applications_count' => \DB::table('user_offre')->count()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Export error: ' . $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine()
        ], 500);
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
