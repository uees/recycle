<?php

namespace App\Api\V1;

use App\Transformers\EnteringWarehouseTransformer;
use App\Models\EnteringWarehouse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class EnteringWarehouseController extends Controller
{
    public function index(Request $request)
    {
        $query = EnteringWarehouse::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['product_name', 'product_batch', 'recyclable_type']);

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
        $this->validate($request, $this->validateRules());

        $this->authorize('create', EnteringWarehouse::class);

        $attributes = $request->only(['product_name', 'product_batch', 'spec', 'weight', 'entered_at']);
        if (empty($attributes['entered_at'])) {
            $attributes['entered_at'] = Carbon::today()->toDateTimeString();
        }

        // 使用 updateOrCreate 防止 Excel 导入时重复
        $product = EnteringWarehouse::query()->updateOrCreate(
            $attributes,
            $request->only(['recyclable_type', 'amount', 'made_at'])
        );

        // 设置可回收类别
        if (!$product->recyclable_type) {
            $product->recyclable_type = recyclable_type($product->product_name);
        }

        if (!$product->amount) {
            $product->amount = calc_amount($product->weight, $product->spec, $product->recyclable_type);
        }

        $product->save();

        return $this->response
            ->item($product, new EnteringWarehouseTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, $this->validateRules());

        $product = EnteringWarehouse::query()
            ->where('id', $id)
            ->firstOrFail();

        $this->authorize('update', $product);

        $product->fill($request->all());

        // 设置可回收类别
        if (!$product->recyclable_type) {
            $product->recyclable_type = recyclable_type($product->product_name);
        }

        if (!$product->amount) {
            $product->amount = calc_amount($product->weight, $product->spec, $product->recyclable_type);
        }

        $product->save();

        return $this->response
            ->item($product, new EnteringWarehouseTransformer())
            ->setStatusCode(201);
    }

    public function destroy($id)
    {
        $product = EnteringWarehouse::findOrFail($id);

        $this->authorize('delete', $product);

        if ($product->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }

    private function validateRules()
    {
        return [
            'product_name' => 'bail|required|max:256',
            'product_batch' => 'bail|required|max:256',
            'spec' => 'required',
            'weight' => 'bail|required|numeric',
            'amount' => 'nullable|numeric',
        ];
    }
}
