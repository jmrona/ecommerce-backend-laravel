<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

use function PHPUnit\Framework\isEmpty;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::orderBy('name', 'Asc')->get();
        if(!$users){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'No users found'
            ]);
        }

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'Users loaded',
            'users' => $users
        ]);

    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user_updated = User::where('id', $id)->first();
        if(!$user_updated){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'User no found'
            ]);
        }

        $user_updated->name = $request->name;
        $user_updated->lastname = $request->lastname;
        $user_updated->email = $request->email;

        if(!$user_updated->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something was wrong, please contact with admin'
            ]);
        }

        $users = User::orderBy('name', 'Asc')->get();
        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'User updated successfully',
            'user_updated' => $user_updated,
            'users' => $users
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
