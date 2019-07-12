<?php

namespace App\Transformers;

use App\Models\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    protected $authorization;

    protected $availableIncludes = [
        'roles', 'authorization',
    ];

    protected $defaultIncludes = [
        'roles'
    ];

    public function transform(User $user)
    {
        return [
            'id' => (int)$user->id,
            'phone' => $user->phone,
            'email' => $user->email,
            'name' => $user->name,
            'avatar' => $user->avatar,
            'created_at' => $user->created_at->toDateTimeString(),
            'updated_at' => $user->updated_at->toDateTimeString(),
        ];
    }

    public function setAuthorization($authorization)
    {
        $this->authorization = $authorization;

        return $this;
    }

    public function includeAuthorization(User $user)
    {
        return $this->item($this->authorization, new AuthorizationTransformer());
    }

    public function includeRoles(User $user)
    {
        return $this->collection($user->roles, app(RoleTransformer::class));
    }
}
