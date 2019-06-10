<?php

namespace App\Api\V1;

use App\Models\Shipment;
use App\Transformers\ShipmentTransformer;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $query = Shipment::query();

        $this->loadRelByQuery($query);

        $this->parseWhere($query, ['customer_id', 'created_user', 'created_at']);

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

    }
}