<?php

namespace App\Api\V1;

use App\Models\Shipment;
use App\Models\Customer;
use App\Transformers\ShipmentTransformer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::query();

        $this->loadRelByQuery($query);

        $this->parseWhere($query, ['customer_id', 'created_user_id', 'created_at']);

        if ($search = $request->get('q')) {
            $product_names = make_query_condition('product_name', $search);
            $product_batches = make_query_condition('product_batch', $search);

            $query->where($product_names)
                ->orWhere($product_batches)
                ->orWhereHas('customer', function (Builder $query) use ($search) {
                    $condition = make_query_condition('name', $search);
                    $query->where($condition);
                });
        }

        $query->orderBy($this->getSortBy(), $this->getOrder());

        $pagination = $query->paginate($this->getPerPage())
            ->appends($request->except('page'));

        return $this->response->paginator($pagination, new ShipmentTransformer());
    }

    public function show($id)
    {
        $shipment = Shipment::query()->findOrFail($id);
        $this->loadRelByModel($shipment);

        return $this->response->item($shipment, new ShipmentTransformer());
    }

    // 发货
    public function store(Request $request)
    {
        $this->validate($request, $this->validateRules());
        $this->authorize('create', Shipment::class);

        $customer = $request->get('customer_id');

        // 不是数字或数字字符串，则是新客户名称
        if (!is_numeric($customer)) {
            $customer = Customer::firstOrCreate(['name' => $customer]);
        } else {
            $customer = Customer::where('id', $customer)->firstOrFail();
        }

        // 使用 updateOrCreate 防止 Excel 导入时重复
        $shipment = Shipment::query()->updateOrCreate($request->only([
            'product_name', 'product_batch', 'spec', 'weight', 'created_at'
        ]), [
            $request->only(['recyclable_type'])
        ]);

        // created_at 设计的作用是发货日期, 但不是 fillable, 所以要再手动赋值
        if ($created_at = $request->get('created_at')) {
            $shipment->created_at = $created_at;
        }

        // 设置可回收类别
        if (!$shipment->recyclable_type) {
            $shipment->recyclable_type = recyclable_type($shipment->product_name);
        }

        if (!$shipment->amount && $shipment->spec) {
            $shipment->amount = calc_amount($shipment->weight, $shipment->spec, $shipment->recyclable_type);
        }

        $shipment->customer()->associate($customer);
        $shipment->created_user()->associate($this->user);

        $shipment->save();

        $this->loadRelByModel($shipment);

        return $this->response
            ->item($shipment, new ShipmentTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validateRules());

        $shipment = Shipment::query()->findOrFail($id);
        $this->authorize('update', $shipment);

        $customer = $request->get('customer_id');

        // 不是数字或数字字符串，则是新客户名称
        if (!is_numeric($customer)) {
            $customer = Customer::firstOrCreate(['name' => $customer]);
        } else {
            $customer = Customer::whereId( $customer)->firstOrFail();
        }

        $shipment->fill($request->all());

        // created_at 设计的作用是发货日期, 但不是 fillable, 所以要再手动赋值
        if ($created_at = $request->get('created_at')) {
            $shipment->created_at = $created_at;
        }

        // 设置可回收类别
        if (!$shipment->recyclable_type) {
            $shipment->recyclable_type = recyclable_type($shipment->product_name);
        }

        if (!$shipment->amount && $shipment->spec) {
            $shipment->amount = calc_amount($shipment->weight, $shipment->spec, $shipment->recyclable_type);
        }

        $shipment->customer()->associate($customer);

        $shipment->save();

        $this->loadRelByModel($shipment);

        return $this->response->item($shipment, new ShipmentTransformer());
    }

    public function destroy($id)
    {
        $shipment = Shipment::findOrFail($id);
        $this->authorize('delete', $shipment);

        if ($shipment->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }

    private function validateRules()
    {
        return [
            'customer_id' => 'bail|required|max:255',
            'product_name' => 'bail|required|max:128',
            'product_batch' => 'nullable|max:64',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
        ];
    }
}