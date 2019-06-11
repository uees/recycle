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
        $this->authorize('create', Shipment::class);
        $this->validate($request, $this->validateRules());

        $customer_name = $request->get('customer');

        $customer = Customer::query()->firstOrCreate([
            'name' => $customer_name
        ]);

        $attributes = $request->only([
            'product_name',
            'product_batch',
            'weight',
            'amount'
        ]);
        $attributes['customer_id'] = $customer->id;
        $attributes['created_user_id'] = $this->user()->id;

        $shipment = Shipment::create($attributes);

        return $this->response
            ->item($shipment, new ShipmentTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validateRules());

        $shipment = Shipment::query()->findOrFail($id);
        $this->authorize('update', $shipment);

        $shipment->fill($request->all());

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
            'customer' => 'bail|required|max:255',
            'product_name' => 'bail|required|max:128',
            'product_batch' => 'nullable|max:64',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
        ];
    }
}