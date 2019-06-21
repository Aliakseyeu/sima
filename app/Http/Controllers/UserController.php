<?php

namespace App\Http\Controllers;

use App\Exceptions\NotAuthorizedException;
use Auth;
use App\User;
use App\Http\Requests\UserSaveRequest;

class UserController extends Controller
{
    
    public function edit(User $userModel){
        $user = $userModel->find(Auth::id());
        return view('user.user', compact('user'));
    }

    public function update(UserSaveRequest $request, User $userModel){
        if($request->id != Auth::id()){
            throw new NotAuthorizedException();
        }
        $user = $userModel->findOrFail($request->id);
        $user->fill($request->all());
        if($user->save()){
            return back()->withSuccess([__('messages.updated', ['name'=>__('messages.user')])]);
        }
        return back()->withErrors(__('messages.error.updated'));
    }
    
}
