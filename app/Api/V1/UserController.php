<?php

namespace App\Api\V1;

use App\Models\User;
// use App\Models\Authorization;
// use App\Jobs\SendRegisterEmail;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    public function index()
    {
        $this->authorize('system');

        $users = User::paginate();

        return $this->response->paginator($users, new UserTransformer());
    }

    public function show($id)
    {
        $user = User::findOrFail($id);

        return $this->response->item($user, new UserTransformer());
    }

    public function store(Request $request)
    {
        $this->authorize('system');

        $validator = Validator::make($request->input(), [
            'email' => 'required|email|unique:users',
            'name' => 'required|string',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $email = $request->get('email');
        $password = $request->get('password');
        $attributes = [
            'email' => $email,
            'name' => $request->get('name'),
            'password' => app('hash')->make($password),
        ];

        $user = User::create($attributes);

        $this->loadRelByModel($user);

        // 用户注册成功后发送邮件
        // dispatch(new SendRegisterEmail($user));
        // 201 with location
        // $location = dingo_route('v1', 'users.show', $user->id);

        return $this->response->item($user, new UserTransformer())
            // ->header('Location', $location)
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->authorize('system');

        $this->validate($request, [
            'email' => 'required|email',
            'name' => 'required|string',
        ]);

        if (User::where('id', '<>', $id)->where('email', $request->get('email'))->exists()) {
            $this->response->errorBadRequest('邮箱已经注册');
        }

        $user = User::whereId($id)->firstOrFail();

        $user->fill($request->only(['email', 'name']));
        if ($password = $request->get('password')) {
            $user->password = app('hash')->make($password);
        }
        $user->save();

        $this->loadRelByModel($user);

        return $this->response
            ->item($user, new UserTransformer())
            ->setStatusCode(201);
    }

    public function destroy($id)
    {
        $this->authorize('system');

        $user = User::findOrFail($id);

        if ($user->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }

    public function me()
    {
        $user = $this->user;

        $this->loadRelByModel($user);

        return $this->response->item($user, new UserTransformer());
    }

    public function updateMe(Request $request)
    {
        $validator = Validator::make($request->input(), [
            'name' => 'string|max:50',
            'avatar' => 'url',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $user = $this->user;
        $attributes = array_filter($request->only('name', 'avatar'));
        if ($attributes) {
            $user->update($attributes);
        }

        $this->loadRelByModel($user);

        return $this->response->item($user, new UserTransformer());
    }

    public function password(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required|confirmed|different:old_password',
            'password_confirmation' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $user = $this->user;
        $auth = Auth::once([
            'email' => $user->email,
            'password' => $request->get('old_password'),
        ]);
        if (! $auth) {
            return $this->response->errorUnauthorized();
        }

        $password = app('hash')->make($request->get('password'));
        $user->update(['password' => $password]);

        return $this->response->noContent();
    }
}
