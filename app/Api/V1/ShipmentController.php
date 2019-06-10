<?php

namespace App\Api\V1;

use App\Models\Shipment;
use App\Transformers\ShipmentTransformer;
use Illuminate\Http\Request;


class ShipmentController extends Controller
{
    public function index(Request $request)
    {
        $paginator = Shipment::query()->paginate();

        return $this->response->paginator($paginator, new ShipmentTransformer());
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