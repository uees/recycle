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
            'customer_id' => 'bail|required',
            'amount' => 'bail|required|numeric',
			'recyclable_type' => 'required',
            'recycled_user' => 'bail|required|max:64',
        ]);

        $customer = $request->get('customer_id');

        // 不是数字或数字字符串，则是新客户名称
        if (!is_numeric($customer)) {
            $customer = Customer::firstOrCreate([
                'name' => $customer
            ]);
        } else {
            $customer = Customer::where('id', $customer)
                ->firstOrFail();
        }

        $recycled = new RecycledThing();
        $recycled->fill($request->only(['amount', 'recycled_user', 'recyclable_type']));
        // created_at 设计的作用是回收日期, 是可填充的
        if ($created_at = $request->get('created_at')) {
            $recycled->created_at = $created_at;
        }
        $recycled->customer()->associate($customer);
        $recycled->save();

        $this->loadRelByModel($recycled);

        return $this->response
            ->item($recycled, new RecycledThingTransformer())
            ->setStatusCode(201);
    }

    // 回收修改
    public function updateRecycled(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'bail|required',
            'amount' => 'bail|required|numeric',
			'recyclable_type' => 'required',
            'recycled_user' => 'bail|required|max:64',
        ]);

        $customer = $request->get('customer_id');

        // 不是数字或数字字符串，则是新客户名称
        if (!is_numeric($customer)) {
            $customer = Customer::firstOrCreate([
                'name' => $customer
            ]);
        } else {
            $customer = Customer::where('id', $customer)
                ->firstOrFail();
        }

        $recycled = RecycledThing::whereId($id)->firstOrFail();

        $this->authorize('update', $recycled);

        $recycled->fill($request->only(['amount', 'recycled_user', 'recyclable_type']));
        // created_at 设计的作用是回收日期, 是可填充的
        if ($created_at = $request->get('created_at')) {
            $recycled->created_at = $created_at;
        }
        $recycled->customer()->associate($customer);
        $recycled->save();

        $this->loadRelByModel($recycled);

        return $this->response->item($recycled, new RecycledThingTransformer());
    }

    // 确认，修改确认
    public function confirm(Request $request, $id)
    {
        $this->validate($request, [
            'confirmed_amount' => 'bail|required|numeric',
        ]);

        $recycled = RecycledThing::whereId($id)->firstOrFail();

        $this->authorize('confirm', $recycled);

        $recycled->confirmed_amount = $request->get('confirmed_amount');
        $recycled->confirmed_at = Carbon::now();
        $recycled->confirmed_user()->associate($this->user());
        $recycled->save();

        $this->loadRelByModel($recycled);

        return $this->response->item($recycled, new RecycledThingTransformer());
    }

    public function destroy($id)
    {
        $recycled = RecycledThing::findOrFail($id);

        $this->authorize('delete', $recycled);

        foreach ($recycled->qc_records as $record) {
            $record->delete();
        }

        if ($recycled->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}
