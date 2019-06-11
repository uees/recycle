<?php

namespace App\Api\V1;

use App\Models\Customer;
use App\Models\RecycledThing;
use App\Transformers\RecycledThingTransformer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


class RecycleController extends Controller
{
    public function index(Request $request)
    {
        $query = RecycledThing::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['customer_id', 'confirmed_user_id', 'recycled_user', 'created_at']);

        if ($search = $request->get('q')) {
            $recycled_users = make_query_condition('recycled_user', $search);

            $query->where($recycled_users)
                ->orWhereHas('customer', function (Builder $query) use ($search) {
                    $condition = make_query_condition('name', $search);
                    $query->where($condition);
                });
        }

        $query->orderBy($this->getSortBy(), $this->getOrder());

        $pagination = $query->paginate($this->getPerPage())
            ->appends($request->except('page'));

        return $this->response->paginator($pagination, new RecycledThingTransformer());
    }

    public function show($id)
    {
        $recycled = RecycledThing::query()->findOrFail($id);
        $this->loadRelByModel($recycled);

        return $this->response->item($recycled, new RecycledThingTransformer());
    }

    // 回收
    public function recycle(Request $request)
    {
        $this->authorize('recycle', RecycledThing::class);

        $this->validate($request, [
            'customer' => 'bail|required|max:255',
            'amount' => 'bail|required|numeric',
            'recycled_user' => 'bail|required|max:64',
        ]);

        $customer_name = $request->get('customer');
        $customer = Customer::whereName($customer_name)->firstOrFail();

        $recycled = new RecycledThing();
        $recycled->fill($request->only(['amount', 'recycled_user']));
        $recycled->customer()->associate($customer);
        $recycled->save();

        return $this->response
            ->item($recycled, new RecycledThingTransformer())
            ->setStatusCode(201);
    }

    // 回收修改
    public function updateRecycled(Request $request, $id)
    {
        $this->validate($request, [
            'customer' => 'bail|required|max:255',
            'amount' => 'bail|required|numeric',
            'recycled_user' => 'bail|required|max:64',
        ]);

        $customer_name = $request->get('customer');
        $customer = Customer::whereName($customer_name)->firstOrFail();

        $recycled = RecycledThing::whereId($id)->firstOrFail();

        $this->authorize('update', $recycled);

        $recycled->fill($request->only(['amount', 'recycled_user']));
        $recycled->customer()->associate($customer);
        $recycled->save();

        return $this->response->item($recycled, new RecycledThingTransformer());
    }

    // 确认，修改确认
    public function confirm(Request $request, $id)
    {
        $this->validate($request, [
            'confirmed_amount' => 'bail|required|max:255',
        ]);

        $recycled = RecycledThing::whereId($id)->firstOrFail();

        $this->authorize('confirm', $recycled);

        $recycled->confirmed_amount = $request->get('confirmed_amount');
        $recycled->confirmed_at = Carbon::now();
        $recycled->confirmed_user()->associate($this->user);
        $recycled->save();

        return $this->response->item($recycled, new RecycledThingTransformer());
    }

    public function destroy($id)
    {
        $recycled = RecycledThing::findOrFail($id);

        $this->authorize('delete', $recycled);

        if ($recycled->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}
