<?php


namespace App\Api\V1;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Authorization;
use App\Transformers\AuthorizationTransformer;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = app('validator')->make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $credentials = $request->only('email', 'password');
        // 验证失败返回401
        if (!$token = Auth::attempt($credentials)) {
            $this->response->errorUnauthorized(trans('auth.incorrect'));
        }

        $authorization = new Authorization($token);

        return $this->response->item($authorization, new AuthorizationTransformer())
            ->setStatusCode(201);
    }

    public function refresh()
    {
        $authorization = new Authorization(Auth::refresh());

        return $this->response->item($authorization, new AuthorizationTransformer());
    }

    public function logout()
    {
        Auth::logout();

        return $this->response->noContent();
    }
}
