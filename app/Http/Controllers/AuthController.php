<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;

use JWTAuth;
use Validator, DB, Hash, Mail;

use App\User;

class AuthController extends Controller
{
	/**
     *	API Register
     *
     *	@return \Illuminate\Http\JsonResponse
     */
    public function register()
    {
        $credentials = request()->only('name', 'email', 'password');
        $valid = Validator::make($credentials, [
            'name'  => 'required|max:255',
            'email' => 'required|email|max:255|unique:users'
        ]);
        if ($valid->fails() ){
            return response()->json([
				'success' => false,
				'error'	  => $valid->messages()
			]);
        }
        $name 	  = request()->name;
        $email 	  = request()->email;
        $password = request()->password;

        $user = User::create([
			'name' 	   => $name,
			'email'    => $email,
			'password' => Hash::make($password)
		]);
        $verification_code = str_random(30);
		DB::table('user_verifications')->insert([
			'user_id' => $user->id,
			'token'   => $verification_code
		]);
/*
        Mail::send('email.verify', ['name' => $name, 'verification_code' => $verification_code],
            function($mail) use ($email, $name){
                $mail->from(getenv('FROM_EMAIL_ADDRESS'), "From User/Company Name Goes Here");
                $mail->to($email, $name);
                $mail->subject("Please verify your email address.");
        });
*/
        return response()->json([
			'success' => true,
			'message' => 'Thanks for signing up! Please check your email to complete your registration.'
		]);
    }


	/**
	 *	API Verify User
	 *
	 *	@return \Illuminate\Http\JsonResponse
	 */
	public function verifyUser($verification_code)
	{
		$check = DB::table('user_verifications')->where('token', $verification_code)->first();
		if ( ! is_null($check) ){
			$user = User::find($check->user_id);
			if ($user->is_verified == 1){
				return response()->json([
					'success'=> true,
					'message'=> 'Account already verified..'
				]);
			}
			$user->update(['is_verified' => 1]);
			DB::table('user_verifications')->where('token', $verification_code)->delete();

			return response()->json([
				'success'=> true,
				'message'=> 'You have successfully verified your email address.'
			]);
		}

		return response()->json([
			'success' => false,
			'error'	  => "Verification code is invalid."
		]);
	}


	/**
     * API Login, on success return JWT Auth token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request()->only('email', 'password');
        $validator = Validator::make($credentials, [
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails() ){
            return response()->json([
				'success' => false,
				'error'   => $validator->messages()
			]);
        }

        $credentials['is_verified'] = 1;
        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
        	'success' => true,
            'message' => 'success login',
        	'data'	  => [
                'access_token' => $token,
                // 'token_type'   => 'bearer',
                // 'expires_in'   => auth()->getTTL() * 60
            ]
        ]);
        // try {
        //     // attempt to verify the credentials and create a token for the user
        //     if ( ! $token = JWTAuth::attempt($credentials) ){
        //         return response()->json([
		// 			'success' => false,
		// 			'error'   => 'We cant find an account with this credentials.'
		// 		], 401);
        //     }
        // } catch (JWTException $e) {
        //     // something went wrong whilst attempting to encode the token
        //     return response()->json([
		// 		'success' => false,
		// 		'error'   => 'Failed to login, please try again.'
		// 	], 500);
        // }
        //
        // return response()->json([
		// 	'success' => true,
		// 	'data'	  => ['token' => $token]
		// ]);
    }


    /**
     * Log out
     * Invalidate the token, so user cannot use it anymore
     * They have to relogin to get a new token
     */
    public function logout() {
        $this->validate(request(), ['token' => 'required']);

        try {
            JWTAuth::invalidate(request()->input('token') );
            return response()->json([
				'success' => true,
				'message' => "You have successfully logged out."
			]);
        } catch (JWTException $e){
            // something went wrong whilst attempting to encode the token
            return response()->json([
				'success' => false,
				'error'   => 'Failed to logout, please try again.'
			], 500);
        }
    }


	/**
     * API Recover Password
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function recover()
    {
        $user = User::where('email', request()->email)->first();
        if ( ! $user){
            $error_message = "Your email address was not found.";
            return response()->json([
				'success' => false,
				'error'   => ['email'=> $error_message]
			], 401);
        }

        try {
            Password::sendResetLink(request()->only('email'), function (Message $message) {
                $message->subject('Your Password Reset Link');
            });
        } catch (\Exception $e) {
            $error_message = $e->getMessage();
            return response()->json([
				'success' => false,
				'error'   => $error_message
			], 401);
        }

        return response()->json([
            'success' => true,
			'data'    => [
				'message'=> 'A reset email has been sent! Please check your email.'
			]
        ]);
    }


    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }


}
