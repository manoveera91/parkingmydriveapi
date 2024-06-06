<?php

namespace App\Http\Controllers;

use App\Models\AuthOwner;
use App\Models\AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Log;

class AuthOwnerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function login(Request $request)
    {

        // dd($request->all());
        // Validate request data
        $validatedData = $request->validate([
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::guard('owner')->attempt(['email' => $validatedData['email'], 'password' => $validatedData['password']]) && 
        Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            // User Login
            $user = AuthUser::where('email', $request->email)->first();
            $userToken = $user->createToken('api_token')->plainTextToken;

            // Owner Login
            $owner = Auth::guard('owner')->user(); // Retrieve the authenticated admin user
            $parkingSpotsLength = $owner->parkingSpots()->count();
            $ownerToken = $owner->createToken('api_token')->plainTextToken; // Generate access token

            return response()->json([
                'user' => $user,
                'owner' => $owner,
                'user_access_token' => $userToken,
                'owner_access_token' => $ownerToken,
                'token_type' => 'Bearer',
                'spot_length' => $parkingSpotsLength
            ], 200);
        } else {
            // Authentication failed
            return response()->json(['error' => 'Username and password is incorrect'], 400);
        }
    }

    // public function register(Request $request)
    // {
    //     // Validate request data
    //     $validatedData = $request->validate([
    //         'username' => 'required|string',
    //         'password' => 'required|string',
    //         'email' => 'required|email|unique:users',
    //     ]);

    //     // Create a new user
    //     $user = new AuthOwner();
    //     $user->username = $validatedData['username'];
    //     $user->password = bcrypt($validatedData['password']); // Hash the password for security
    //     $user->email = $validatedData['email'];

    //     if ($user->save()) {
    //         Auth::guard('owner')->login($user);
    //         $mail_status = $this->sendRegisterEmail(
    //             $user->username,
    //             $user->email
    //         );
    //         // Generate access token
    //         $token = $user->createToken('api_token')->plainTextToken;

    //         return response()->json(['message' => 'Owner User created successfully', 'access_token' => $token], 201);

    //     } else {
    //         return response()->json(['error' => 'Provide proper details']);
    //     }
    // }

    public function sociallogin(Request $request) {
        try {
            // Validate request data
            $validatedData = $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
                'email' => 'required|email',
            ]);

            // Check existing User
            $userExist = AuthUser::where('email', $validatedData['email'])->first();

            // Check existing Owner
            $ownerExist = AuthOwner::where('email', $validatedData['email'])->first();
            if ($userExist && $ownerExist) {
                    // User
                    $userExist->save();
                    Auth::login($userExist);
                    $userToken = $userExist->createToken('api_token')->plainTextToken;

                    // Onwer
                    Auth::guard('owner')->login($ownerExist);
                    $parkingSpotsLength = $ownerExist->parkingSpots()->count();
                    $ownerToken = $ownerExist->createToken('api_token')->plainTextToken; // Generate access token
        
                    return response()->json([
                        'message' => 'User registered successfully!',
                        'owner' => $ownerExist,
                        'user' => $userExist,
                        'user_access_token' => $userToken,
                        'token_type' => 'Bearer',
                        'owner_access_token' => $ownerToken,
                        'spot_length' => $parkingSpotsLength
                    ], 200);
            } else {

                // Create a new user

                $user = new AuthUser([
                    'name' => $request->username,
                    'email' => $request->email,
                    'password' => bcrypt($request->password),
                    'mobile' => $request->mobile,
                    'role' => $request->filled('role') ? $request->role : 0,
                    'socialID' => $request->password, // Hash the password for security
                ]);

                // Create a new owner
                $owner = AuthOwner::create([
                    'username' => $validatedData['username'],
                    'socialID' => $validatedData['password'], // Hash the password for security
                    'email' => $validatedData['email'],
                    'password' => bcrypt($validatedData['password'])
                ]);

                if ($user && $owner) {
                    // user Login
                    $user->save();
                    Auth::login($user);
                    $mail_status = $this->sendRegisterEmail(
                        $request->username,
                        $request->email
                    );
                    $userToken = $user->createToken('Personal Access Token')->plainTextToken;

                    // Owner Login
                    Auth::guard('owner')->login($owner);

                    $parkingSpotsLength = 0;
                    // Generate access token
                    $ownerToken = $owner->createToken('api_token')->plainTextToken;

                    return response()->json([
                        'message' => 'User registered successfully!',
                        'owner' => $owner,
                        'user' => $user,
                        'user_access_token' => $userToken,
                        'token_type' => 'Bearer',
                        'owner_access_token' => $ownerToken,
                        'spot_length' => $parkingSpotsLength
                    ], 200);
                } else {
                    return response()->json([
                        'message' => 'Registered failed!',
                    ], 400);
                }
            }
          
        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // MySQL unique constraint violation
                return response()->json(['error' => 'Email already exists'], 409);
            }
            return response()->json(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function register(Request $request) {
        try {

            $validatedData = $request->validate([
                'name' => 'required|string',
                'email' => 'required|string',
                'password' => 'required|string|confirmed',
                'password_confirmation' => 'required',
                'mobile' => 'required|string|min:10|max:10',
                'role' => 'number',
            ]);

            // Create a new User
            $user = new AuthUser([
                'name' => $request->name,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'mobile' => $request->mobile,
                'role' => $request->filled('role') ? $request->role : 0,
                'socialID' => $request->password
            ]);

            $user->save();

            Auth::login($user);

            $mail_status = $this->sendRegisterEmail(
                $request->name,
                $request->email
            );

            $userToken = $user->createToken('Personal Access Token')->plainTextToken;

            // Create a new owner
            $owner = AuthOwner::create([
                'username' => $validatedData['name'],
                'password' => bcrypt($validatedData['password']), // Hash the password for security
                'email' => $validatedData['email'],
                'socialID' => $validatedData['password']
            ]);

            // Login the user
            Auth::guard('owner')->login($owner);

            $parkingSpotsLength = 0;
            // Generate access token
            $ownerToken = $owner->createToken('api_token')->plainTextToken;

            return response()->json([
                'message' => 'Owner User created successfully',
                'user_access_token' => $userToken,
                'owner_access_token' => $ownerToken,
                'user' => $user,
                'owner' => $owner,
                'spot_length' => $parkingSpotsLength
            ], 201);

        } catch (QueryException $e) {
            if ($e->errorInfo[1] == 1062) { // MySQL unique constraint violation
                return response()->json(['error' => 'Email already exists'], 409);
            }
            return response()->json(['error' => 'Database error'], 500);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Email already exists'], 409);
        }
    }

    public function sendRegisterEmail($name, $email)
    {
        try {
            $recipientName = $name;
            $data = [
                'recipientName' => $recipientName,
                'recipientEmail' => $email,
                'name' => $recipientName,
            ];

            Mail::send('emails.owner_register', $data, function ($message) use ($recipientName, $email) {
                $message->to($email, $recipientName)
                    ->subject('Owner Registration');
            });

            return response()->json(['status' => 'success', 'message' => 'Email sent successfully']);
        } catch (\Exception $e) {
            Log::error('Error sending email: '.$e->getMessage());

            return response()->json(['status' => 'error', 'message' => 'Failed to send email']);
        }
    }

    public function index()
    {
        //
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
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(AuthOwner $authOwner)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AuthOwner $authOwner)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, AuthOwner $authOwner)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AuthOwner $authOwner)
    {
        //
    }
}
