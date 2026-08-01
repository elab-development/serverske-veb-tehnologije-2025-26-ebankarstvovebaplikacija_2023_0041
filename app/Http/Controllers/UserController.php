<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json([
            'message' => 'OK',
            'data' => User::all()
        ],200);
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
        try{
            $validatedObject=$request->validate([
                'first_name' => 'required|string|max:30',
                'last_name' => 'required|string|max:30',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|min:7'
            ]);

        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }

        try{
            User::create($validatedObject);
        }catch(QueryException $exception){
            return response()->json([
                'message' => 'Internal Server Error',                
            ],500);
        }

        return response()->json([
                'message' => 'Created',                
            ],201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try{
            $user = User::findOrFail($id);
            return response()->json([
                'message' => 'OK',
                'data' => $user
            ],200);
        }catch(ModelNotFoundException $exception){
            return response()->json([
                'message' => 'Not Found',
            ],404);
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try{
            $user=User::findOrFail($id);
        }catch(ModelNotFoundException $exception){
             return response()->json([
                'message' => 'Not Found',                
            ],404);
        }
        
         try{
            $validatedObject=$request->validate([
                'first_name' => 'sometimes|string|max:30',
                'last_name' => 'sometimes|string|max:30',
                'email' => 'sometimes|email|unique:users,email,' . $id,
                'password' => 'sometimes|string|min:7'
            ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',                
            ],422);
        }
        
        

        if($request->has('first_name'))
            $user->first_name=$request->first_name;

        if($request->has('last_name'))
            $user->last_name=$request->last_name;

        if($request->has('email'))
            $user->email=$request->email;

        if($request->has('password'))
            $user->password=$request->password;




        $saved = $user->save();

        if(!$saved)
            return response()->json([
                'message' => 'Internal Server Error'
        ],500);

       
        return response()->json([
                'message' => 'OK'
        ],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {   
        try{
             $deletedRows=User::destroy($id);

            if($deletedRows==0)
            return response()->json([
                'message' => 'Not Found'
            ],404); 

            return response()->json([
                    'message' => 'OK'
            ],200); 

        }catch(QueryException $exception){
            return response()->json([
                    'message' => 'Internal Server Error'
            ],500); 
        }
       
        
        
    }

    public function changeRole(Request $request,string $id){
        try{
            $validatedRole=$request->validate([
            'role' => 'required|in:user,admin,super_admin'
        ]);
        }catch(ValidationException $exception){
            return response()->json([
                'message' => 'Unprocessable Entity',
            ],422);
        }

        try{
            $user=User::findOrFail($id);
        }catch(ModelNotFoundException $exception){
            return response()->json([
                'message' => 'Not Found'
            ],404);
        }

        $user->role=$validatedRole['role'];
        try{
            $user->save();
        }catch(QueryException $exception){
            return response()->json([
                'message' => $user->first_name
            ],500);
        }

        return response()->json([
                'message' => 'OK'
            ],200);
    }
}
