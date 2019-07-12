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
        $this->parseWhere($query, ['year', 'month', 'recyclable_type']);
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

        $recyclable_types = ['bucket', 'box'];
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

        return $this->response->collection($collection, new RecycledStatisticsTransformer);
    }
}