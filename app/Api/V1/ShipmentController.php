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

    }

    public function update($id)
    {

    }

    public function destroy(Request $request)
    {

    }

    // 发货
    public function store(Request $request)
    {
        $validator = app('validator')->make($request->input(), [
            'customer' => 'bail|required|string|max:256',
            'product_name' => 'bail|required|string|max:128',
            'product_batch' => 'nullable|string|max:64',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator);
        }

        $custom = $request->get('customer');
        $customer = Customer::whereName($custom)->first();
        if (is_null($customer)) {
            $customer = Customer::create([
                'name' => $custom
            ]);
        }


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
}