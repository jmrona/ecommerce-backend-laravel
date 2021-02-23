<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Intervention\Image\ImageManagerStatic as Image;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('isAdmin');
        $this->middleware('userStatus');
    }

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

    public function setAvatar(Request $request, $id)
    {
        if(!$request->hasFile('file')){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Image no found'
            ]);
        }

        $dir = '/'.$id;
        $full_path = public_path('storage/img').$dir;
        // localhost:8000/storage/img/$id/$image
        $file = $request->file('file');
        $file_ext = trim($file->getClientOriginalExtension());
        $file_name = time().'.'.$file_ext;

        // Checking if the directory exist
        if (!file_exists($full_path)) {
            mkdir($full_path, 666, true);
        }

        // Save a thumbnail of the image (200x200)
        $final_file = $full_path.'/'.$file_name;
        $img = Image::make($file);
        $img->resize(200,200, function( $constraint) {
            $constraint->aspectRatio();
        });
        $img->save($final_file);

        // Getting user's avatar
        $user = User::where('id', $id)->first();

        if( $user->avatar !== null){
            // $path_old_file = resource_path('img').$dir.'/'.$user->avatar;
            unlink($full_path.'/'.$user->avatar);
        }

        $user->avatar = $file_name;

        if($user->save()){
            return response()->json([
                'status' => 200,
                'ok' => true,
                'msg' => 'Avatar updated',
                'user' => $user
            ]);
        }

    }

    public function deleteAvatar(Request $request, $id)
    {
        $user = User::where('id', $id)->first();

        $dir = '/'.$id;
        $full_path = public_path('storage/img').$dir;

        if( $user->avatar === null){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Avatar no found',
                'user' => $user
            ]);
        }else{
            unlink($full_path.'/'.$user->avatar);
            $user->avatar = null;
            if($user->save()){
                return response()->json([
                    'status' => 200,
                    'ok' => true,
                    'msg' => 'Avatar deleted successfully',
                    'user' => $user
                ]);
            }else{
                return response()->json([
                    'status' => 400,
                    'ok' => false,
                    'msg' => 'Something was wrong! Please, contact to the admin'
                ]);
            }
        }
    }

    public function setBanUser($id){
        $user = User::where('id', $id)->where('status', '0')->first();
        if(!$user){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'The user doesn\'t exist or is already banned'
            ]);
        }

        $user->status = User::USER_BANNED;

        if(!$user->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something was wrong! Please, contact to the admin'
            ]);
        }

        $userList = User::orderBy('name', 'Asc')->get();

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'User banned successfully',
            'users' => $userList
        ]);
    }

    public function removeBanUser($id){
        $user = User::where('id', $id)->where('status', '1')->first();
        if(!$user){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'The user doesn\'t exist or is already banned'
            ]);
        }

        $user->status = User::USER_NOBANNED;

        if(!$user->save()){
            return response()->json([
                'status' => 400,
                'ok' => false,
                'msg' => 'Something was wrong! Please, contact to the admin'
            ]);
        }

        $userList = User::orderBy('name', 'Asc')->get();

        return response()->json([
            'status' => 200,
            'ok' => true,
            'msg' => 'User unbanned successfully',
            'users' => $userList
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
