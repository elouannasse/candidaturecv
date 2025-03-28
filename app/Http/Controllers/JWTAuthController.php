<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;








class JWTAuthController extends Controller
{
    use AuthorizesRequests;










 /**
 * @OA\Post(
 *     path="/api/register",
 *     summary="Enregistrer un nouvel utilisateur",
 *     tags={"Authentification"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"name", "email", "password", "role_id"},
 *             @OA\Property(property="name", type="string", example="John Doe"),
 *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
 *             @OA\Property(property="password", type="string", example="password123"),
 *             @OA\Property(property="role_id", type="integer", example=3),
 *             @OA\Property(property="competence_ids", type="array", @OA\Items(type="integer"), example={1, 2})
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Utilisateur créé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="user", type="object"),
 *             @OA\Property(property="token", type="string")
 *         )
 *     ),
 *     @OA\Response(response=400, description="Erreur de validation")
 * )
 */


    public function register(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|max:255|email|unique:users',
            'password' => 'required|string|max:255',
            'role_id' => 'required|integer|exists:roles,id',
            'competence_ids' => 'nullable|array',
            'competence_ids.*' => 'exists:competences,id'

        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        $user = User::create([
            'name' => $request->get('name'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'role_id' => $request->get('role_id'),
        ]);

        if ($request->has('competence_ids') && !empty($request->competence_ids)) {

            $user->competences()->attach($request->competence_ids);
        }

        $token = JWTAuth::fromUser($user);

        $user->load('competences');


        return response()->json(compact('user', 'token'), 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'invalid credentials'], 401);
            }

            $user = auth()->user();
            return response()->json(compact('token', 'user'));
        } catch (JWTException $e) {
            return response()->json(['error' => 'could not create token'], 500);
        }
    }


    public function refresh()
    {
        try {
            $currentToken = JWTAuth::getToken();

            if (!$currentToken) {
                return response()->json(['error' => 'token not provided'], 401);
            }

            $token = JWTAuth::refresh($currentToken);

            $user = Auth::user();

            return response()->json([
                'status' => true,
                'message' => 'token succefully refreshed',
                'token' => $token,
                'user' => $user
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Could not refresh token',
                'error' => $e->getMessage()
            ], 401);
        }
    }



    public function getUser()
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['error' => 'User not found'], 404);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Invalid token'], 400);
        }
        return response()->json(compact('user'));
    }

    public function updateProfile(Request $request)
    {
        try {
            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found'
                ], 404);
            }


            $validateUser = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|max:255|email|unique:users,email,' . $user->id,
                'password' => 'required|string|max:255',
                'competence_ids' => 'nullable|array',
                'competence_ids.*' => 'exists:competences,id'
            ]);

            if ($validateUser->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Validation error',
                    'errors' => $validateUser->errors()
                ], 400);
            }

            if ($request->has('name')) {
                $user->name = $request->name;
            }
            if ($request->has('email')) {
                $user->email = $request->email;
            }
            if ($request->has('password')) {
                $user->password = bcrypt($request->password);
            }

            $user->save();

            if ($request->has('competence_ids')) {
                $user->competences()->sync($request->competence_ids);
            }

            $user->load('competences');

            return response()->json([
                'status' => true,
                'message' => 'profile updated successfully',
                'user' => $user
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => $th->getMessage()
            ], 500);
        }
    }


    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());

        return response()->json(['message' => 'Successfully logged out']);
    }
}
