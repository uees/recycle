<?php


namespace App\Api\V1;

use App\Models\Role;
use App\Transformers\RoleTransformer;
use Illuminate\Http\Request;


class RoleController extends Controller
{
    public function index(Request $request)
    {
        $query = Role::query();
        $this->loadRelByQuery($query);

        if ($search = $request->get('q')) {
            $name_condition = make_query_condition('name', $search);
            $display_name_condition = make_query_condition('display_name', $search);
            $query->where($name_condition)->orWhere($display_name_condition);
        }

        $query->orderBy($this->getSortBy(), $this->getOrder());

        if ($request->filled('all')) {
            $roles = $query->get();
            return $this->response->collection($roles, new RoleTransformer());
        }

        $pagination = $query->paginate($this->getPerPage())->appends($request->except('page'));
        return $this->response->paginator($pagination, new RoleTransformer());
    }

    public function show($id)
    {
        $role = Role::query()->findOrFail($id);
        $this->loadRelByModel($role);

        return $this->response->item($role, new RoleTransformer());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'bail|required|unique:roles|max:64',
            'display_name' => 'required|max:64',
        ]);

        $this->authorize('create', Role::class);

        $role = new Role();
        $role->fill($request->all());
        $role->save();
        $this->loadRelByModel($role);

        return $this->response->item($role, new RoleTransformer())->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'bail|required|max:64',
            'display_name' => 'required|max:64',
        ]);

        if (Role::where('id', '<>', $id)->where('name', $request->get('name'))->exists()) {
            $this->response->errorBadRequest('已经存在的角色');
        }

        $role = Role::whereId($id)->firstOrFail();

        $this->authorize('update', $role);

        $role->fill($request->all());
        $role->save();

        return $this->response
            ->item($role, new RoleTransformer())
            ->setStatusCode(201);
    }

    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        $this->authorize('delete', $role);

        // 首先角色下的用户去除该角色
        foreach ($role->users as $user) {
            $user->roles()->detach($role->id);
        }

        if ($role->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}