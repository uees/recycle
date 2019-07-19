<?php

namespace App\Api\V1;

use App\Models\RecycledStatistics;
use App\Models\EnteringWarehouse;
use App\Models\Shipment;
use App\Models\RecycledThing;
use App\Models\QcRecord;
use App\Models\Customer;
use App\Transformers\RecycledStatisticsTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;


class RecycledStatisticsController extends Controller
{
    public function index(Request $request)
    {
        $query = RecycledStatistics::query();
        if (!$request->filled('customer_id')) {
            if ($request->get('includecustomers') != 'true') {
                $query->whereNull('customer_id');
            }
        }
        $this->parseWhere($query, ['year', 'month', 'recyclable_type', 'customer_id']);
        $query->orderBy('year', 'desc')->orderBy('month', 'desc');
        $results = $query->get();

        return $this->response->collection($results, new RecycledStatisticsTransformer());
    }

    // 按时间范围查看统计数据, 数据不会持久化
    public function range(Request $request)
    {
        $date = $request->get('date');
        $customer_id = $request->get('customer_id');
        $recyclable_type = $request->get('recyclable_type');
        $recyclable_types = $recyclable_type ? [$recyclable_type] : ['bucket', 'box'];

        $minDate = '2019-01-01';
        $maxDate = Carbon::today()->toDateString();
        if (preg_match('/^date:(\d{4}-\d{2}-\d{2}),?(\d{4}-\d{2}-\d{2})?$/', $date, $matches)) {
            if (count($matches) == 2) {
                $minDate = $matches[1];
            } else {
                $minDate = $matches[1];
                $maxDate = $matches[2];
            }
        }

        if (!$customer_id) {
            $collection = $this->rangeTotal($recyclable_types, $minDate, $maxDate);
        } else {
            $collection = $this->rangeByCustomer($customer_id, $recyclable_types, $minDate, $maxDate);
        }

        return response()->json([
            'data' => $collection,
        ]);
    }

    // 创建所有客户的统计信息
    public function createAll(Request $request)
    {
        $this->validate($request, [
            'year' => 'bail|required|numeric',
            'month' => 'bail|required|numeric',
        ]);

        $year = (int)$request->get('year');
        $month = (int)$request->get('month');
        $recyclable_type = $request->get('recyclable_type');
        $recyclable_types = $recyclable_type ? [$recyclable_type] : ['bucket', 'box'];

        foreach (Customer::query()->get() as $customer) {
            $this->storeWithCustomer($customer->id, $recyclable_types, $year, $month);
        }

        $this->response->created();
    }

    // 保存月度数据的 action
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
            $collection = $this->storeTotal($recyclable_types, $year, $month);
        } else {
            $collection = $this->storeWithCustomer($customer_id, $recyclable_types, $year, $month);
        }

        return $this->response->collection($collection, new RecycledStatisticsTransformer);
    }

    protected function rangeByCustomer($customer_id, $recyclable_types, $minDate, $maxDate)
    {
        $collection = collect();

        foreach ($recyclable_types as $type) {
            $shipment_amount = Shipment::query()
                ->where('recyclable_type', $type)
                ->where('customer_id', $customer_id)
                ->whereBetween('created_at', [$minDate, $maxDate])
                ->sum('amount');

            $recycled_amount = RecycledThing::query()
                ->where('recyclable_type', $type)
                ->where('customer_id', $customer_id)
                ->whereBetween('confirmed_at', [$minDate, $maxDate])
                ->sum('confirmed_amount');

            $bad_amount = QcRecord::whereHas('recycled_thing', function ($query) use ($type, $customer_id) {
                $query->where('recyclable_type', $type)->where('customer_id', $customer_id);
            })
                ->whereBetween('created_at', [$minDate, $maxDate])
                ->sum('bad_amount');

            $good_amount = $recycled_amount - $bad_amount;

            $statistics = [
                'recyclable_type' => $type,
                'customer_id' => $customer_id,
                'shipment_amount' => $shipment_amount,
                'recycled_amount' => $recycled_amount,
                'bad_amount' => $bad_amount,
                'good_amount' => $good_amount,
            ];

            $collection->push($statistics);
        }

        return $collection;
    }

    protected function rangeTotal($recyclable_types, $minDate, $maxDate)
    {
        $collection = collect();

        foreach ($recyclable_types as $type) {
            $entering_warehouse_amount = EnteringWarehouse::query()
                ->where('recyclable_type', $type)
                ->whereBetween('entered_at', [$minDate, $maxDate])
                ->sum('amount');

            $shipment_amount = Shipment::query()
                ->where('recyclable_type', $type)
                ->whereBetween('created_at', [$minDate, $maxDate])
                ->sum('amount');

            $recycled_amount = RecycledThing::query()
                ->where('recyclable_type', $type)
                ->whereBetween('confirmed_at', [$minDate, $maxDate])
                ->sum('confirmed_amount');

            $bad_amount = QcRecord::query()
                ->where('recyclable_type', $type)
                ->whereBetween('created_at', [$minDate, $maxDate])
                ->sum('bad_amount');

            $good_amount = $recycled_amount - $bad_amount;

            $statistics = [
                'recyclable_type' => $type,
                'customer_id' => NULL,
                'entering_warehouse_amount' => $entering_warehouse_amount,
                'shipment_amount' => $shipment_amount,
                'recycled_amount' => $recycled_amount,
                'bad_amount' => $bad_amount,
                'good_amount' => $good_amount,
            ];

            $collection->push($statistics);
        }

        return $collection;
    }

    // 按月度保存统计数据
    protected function storeWithCustomer($customer_id, $recyclable_types, $year, $month)
    {
        $nextMonth = next_month($month);
        $minDate = vsprintf("%04d-%02d-%02d", [$year, $month, 1]);
        $maxDate = vsprintf("%04d-%02d-%02d", [$year, $nextMonth, 1]);

        $allStatistics = $this->rangeByCustomer($customer_id, $recyclable_types, $minDate, $maxDate);

        $collection = collect();

        foreach ($allStatistics as $item) {
            if ($item['shipment_amount'] || $item['recycled_amount']) {
                $statistics = RecycledStatistics::query()->updateOrCreate([
                    'recyclable_type' => $item['recyclable_type'],
                    'customer_id' => $item['customer_id'],
                    'year' => $year,
                    'month' => $month,
                ], [
                    'shipment_amount' => $item['shipment_amount'],
                    'recycled_amount' => $item['recycled_amount'],
                    'bad_amount' => $item['bad_amount'],
                    'good_amount' => $item['good_amount'],
                ]);

                $collection->push($statistics);
            }
        }

        return $collection;
    }

    // 按月度保存统计数据
    protected function storeTotal($recyclable_types, $year, $month)
    {
        $nextMonth = next_month($month);
        $minDate = vsprintf("%04d-%02d-%02d", [$year, $month, 1]);
        $maxDate = vsprintf("%04d-%02d-%02d", [$year, $nextMonth, 1]);
        $allStatistics = $this->rangeTotal($recyclable_types, $minDate, $maxDate);

        $collection = collect();

        foreach ($allStatistics as $item) {
            if ($item['entering_warehouse_amount'] || $item['shipment_amount'] || $item['recycled_amount']) {
                $statistics = RecycledStatistics::query()->updateOrCreate([
                    'recyclable_type' => $item['recyclable_type'],
                    'customer_id' => NULL,
                    'year' => $year,
                    'month' => $month,
                ], [
                    'entering_warehouse_amount' => $item['entering_warehouse_amount'],
                    'shipment_amount' => $item['shipment_amount'],
                    'recycled_amount' => $item['recycled_amount'],
                    'bad_amount' => $item['bad_amount'],
                    'good_amount' => $item['good_amount'],
                ]);

                $collection->push($statistics);
            }
        }

        return $collection;
    }
}