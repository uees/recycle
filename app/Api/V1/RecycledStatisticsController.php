<?php

namespace App\Api\V1;

use App\Models\RecycledStatistics;
use App\Models\EnteringWarehouse;
use App\Models\Shipment;
use App\Models\RecycledThing;
use App\Models\QcRecord;
use App\Transformers\RecycledStatisticsTransformer;
use Illuminate\Http\Request;

class RecycledStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $query = RecycledStatistics::query();
        if (!$request->filled('customer_id')) {
            $query->whereNull('customer_id');
        }
        $this->parseWhere($query, ['year', 'month', 'recyclable_type', 'customer_id']);
        $query->orderBy($this->getSortBy(), $this->getOrder());
        $results = $query->get();

        return $this->response->collection($results, new RecycledStatisticsTransformer());
    }

    public function create(Request $request)
    {
        $this->validate($request, [
            'year' => 'bail|required|numeric',
            'month' => 'bail|required|numeric',
        ]);

        $year = (int)$request->get('year');
        $month = (int)$request->get('month');
        $customer_id = $request->get('customer_id');
        $recyclable_type = $request->get('recyclable_type');
        $recyclable_types = $recyclable_type ? [$recyclable_type] : ['bucket', 'box'];

        if (!$customer_id) {
            $collection = $this->makeTotal($recyclable_types, $year, $month);
        } else {
            $collection = $this->makeByCustomer($customer_id, $recyclable_types, $year, $month);
        }

        return $this->response->collection($collection, new RecycledStatisticsTransformer);
    }

    protected function makeByCustomer($customer_id, $recyclable_types, $year, $month)
    {
        $shipment_amount = [];
        $recycled_amount = [];
        $bad_amount = [];
        $good_amount = [];

        $collection = collect();

        foreach ($recyclable_types as $type) {
            $shipment_amount[$type] = Shipment::query()
                ->where('recyclable_type', $type)
                ->where('customer_id', $customer_id)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('amount');

            $recycled_amount[$type] = RecycledThing::query()
                ->where('recyclable_type', $type)
                ->where('customer_id', $customer_id)
                ->whereYear('confirmed_at', $year)
                ->whereMonth('confirmed_at', $month)
                ->sum('confirmed_amount');

            $bad_amount[$type] = QcRecord::whereHas('recycled_thing', function ($query) use ($type, $customer_id) {
                $query->where('recyclable_type', $type)
                    ->where('customer_id', $customer_id);
            })
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('bad_amount');

            $good_amount[$type] = $recycled_amount[$type] - $bad_amount[$type];

            $statistics = RecycledStatistics::query()->updateOrCreate([
                'recyclable_type' => $type,
                'customer_id' => $customer_id,
                'year' => $year,
                'month' => $month,
            ], [
                    'shipment_amount' => $shipment_amount[$type],
                    'recycled_amount' => $recycled_amount[$type],
                    'bad_amount' => $bad_amount[$type] ,
                    'good_amount' => $good_amount[$type],
                ]
            );

            $collection->push($statistics);
        }

        return $collection;
    }

    protected function makeTotal($recyclable_types, $year, $month)
    {
        $entering_warehouse_amount = [];
        $shipment_amount = [];
        $recycled_amount = [];
        $bad_amount = [];
        $good_amount = [];

        $collection = collect();

        foreach ($recyclable_types as $type) {
            $entering_warehouse_amount[$type] = EnteringWarehouse::query()
                ->where('recyclable_type', $type)
                ->whereYear('entered_at', $year)
                ->whereMonth('entered_at', $month)
                ->sum('amount');

            $shipment_amount[$type] = Shipment::query()
                ->where('recyclable_type', $type)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('amount');

            $recycled_amount[$type] = RecycledThing::query()
                ->where('recyclable_type', $type)
                ->whereYear('confirmed_at', $year)
                ->whereMonth('confirmed_at', $month)
                ->sum('confirmed_amount');

            $bad_amount[$type] = QcRecord::query()
                ->where('recyclable_type', $type)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->sum('bad_amount');

            $good_amount[$type] = $recycled_amount[$type] - $bad_amount[$type];

            $statistics = RecycledStatistics::query()->updateOrCreate([
                'recyclable_type' => $type,
                'customer_id' => NULL,
                'year' => $year,
                'month' => $month,
            ], [
                    'entering_warehouse_amount' => $entering_warehouse_amount[$type],
                    'shipment_amount' => $shipment_amount[$type],
                    'recycled_amount' => $recycled_amount[$type],
                    'bad_amount' => $bad_amount[$type] ,
                    'good_amount' => $good_amount[$type],
                ]
            );

            $collection->push($statistics);
        }

        return $collection;
    }
}