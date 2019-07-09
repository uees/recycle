<?php

namespace App\Api\V1;

use App\Transformers\EnteringWarehouseTransformer;
use App\Models\EnteringWarehouse;
use Illuminate\Http\Request;


class EnteringWarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = EnteringWarehouse::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['product_name', 'product_batch']);

        if ($search = $request->get('q')) {
            $name_condition = make_query_condition('product_name', $search);
            $batch_condition = make_query_condition('product_batch', $search);
            $query->where($name_condition)
                ->orWhere($batch_condition);
        }

        $query->orderBy($this->getSortBy(), $this->getOrder());

        $pagination = $query->paginate($this->getPerPage())
            ->appends($request->except('page'));

        return $this->response->paginator($pagination, new EnteringWarehouseTransformer());
    }

    public function show($id)
    {
        $product = EnteringWarehouse::query()->findOrFail($id);

        return $this->response->item($product, new EnteringWarehouseTransformer());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'product_name' => 'bail|required|max:256',
            'product_batch' => 'bail|required|max:256',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
            'made_at' => 'required',
            'entered_at' => 'nullable',
        ]);

        // $this->authorize('update-customers', Customer::class);

        $product = new EnteringWarehouse();
        $product->fill($request->all());
        $product->save();

        return $this->response
            ->item($product, new EnteringWarehouseTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'product_name' => 'bail|required|max:256',
            'product_batch' => 'bail|required|max:256',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
            'made_at' => 'required',
            'entered_at' => 'required',
        ]);

        $product = EnteringWarehouse::query()
            ->where('id', $id)
            ->firstOrFail();

        // $this->authorize('update-customers', $product);

        $product->fill($request->only([
            'product_name',
            'product_batch',
            'weight',
            'amount',
            'made_at',
            'entered_at'
        ]));
        $product->save();

        return $this->response
            ->item($product, new EnteringWarehouseTransformer())
            ->setStatusCode(201);
    }

    public function destroy($id)
    {
        $product = EnteringWarehouse::findOrFail($id);

        // $this->authorize('update-customers', $customer);

        if ($product->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}
