<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;


class UserController extends Controller
{

     public function __construct()
        {
            $this->middleware('auth:api', ['except' => ['login','register']]);
          
        }


    public function register(Request $request)
    {

        // $validator = Validator::make($request->all(), 
        //             [
        //                 'name' => 'required',
        //                 'email' => 'required|email',
        //                 'password' => 'required',
        //             ]);

        //         if($validator->fails()) {
        //             // return $validator->errors();
        //             return response()->json(['error'=>$validator->errors()], 401);
        //         }

             
        $validator = Validator::make($request->all(), 
                    [
                        'name' => 'required',
                        'email' => 'required|email|unique:users.email',
                        'password' => 'required',
                    ]);

                if($validator->fails()) {
                    return response($validator->errors(), 401);
                }


                return response(['message' => 'Created'], 200);
        // $input = $request->all(); 
        // $input['password'] = bcrypt($input['password']); 
        // $user = User::create($input); 
        // $success['name'] =  $user->name;
        // return response()->json(['success'=>$success]); 
    	
    }

    /**
         * Create a new AuthController instance.
         *
         * @return void
         */
       

        /**
         * Get a JWT token via given credentials.
         *
         * @param  \Illuminate\Http\Request  $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function login(Request $request)
        {
           $credentials = $request->only('email', 'password');

           if ($token = $this->guard()->attempt($credentials)) {
               return $this->respondWithToken($token);
           }

           return response()->json(['error' => 'Unauthorized'], 401);
        }

        /**
         * Get the authenticated User
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function me()
        {
            return response()->json($this->guard()->user());
        }

        /**
         * Log the user out (Invalidate the token)
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function logout()
        {
            $this->guard()->logout();

            return response()->json(['message' => 'Successfully logged out']);
        }

        /**
         * Refresh a token.
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function refresh()
        {
            return $this->respondWithToken($this->guard()->refresh());
        }

        /**
         * Get the token array structure.
         *
         * @param  string $token
         *
         * @return \Illuminate\Http\JsonResponse
         */
        protected function respondWithToken($token)
        {
            return response()->json([
                'access_token' => $token,
                'token_type' => 'bearer',
                'expires_in' => auth('api')->factory()->getTTL() * 60
            ]);
        }

        /**
         * Get the guard to be used during authentication.
         *
         * @return \Illuminate\Contracts\Auth\Guard
         */
        public function guard()
        {
            return Auth::guard();
        }
}
