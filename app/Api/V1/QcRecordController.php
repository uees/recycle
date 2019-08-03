<?php

namespace App\Api\V1;

use App\Models\QcRecord;
use App\Models\RecycledThing;
use App\Transformers\QcRecordTransformer;
use Illuminate\Http\Request;


class QcRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = QcRecord::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['recyclable_type', 'created_at', 'recycled_thing_id']);

        $query->orderBy($this->getSortBy(), $this->getOrder());
        
        if ($request->filled('all')) {
            $records = $query->get();
            return $this->response->collection($records, new QcRecordTransformer());
        }

        $pagination = $query->paginate($this->getPerPage())
            ->appends($request->except('page'));

        return $this->response->paginator($pagination, new QcRecordTransformer());
    }

    public function show($id)
    {
        $record = QcRecord::query()->findOrFail($id);
        $this->loadRelByModel($record);

        return $this->response->item($record, new QcRecordTransformer());
    }

    public function store(Request $request)
    {
        $this->authorize('create', QcRecord::class);
        $this->validate($request, [
            'recycled_thing_id' => 'bail|required|numeric',
            'bad_amount' => 'bail|required|numeric',
        ]);

        $recycled = RecycledThing::whereId($request->get('recycled_thing_id'))
            ->firstOrFail();

        $record = new QcRecord;
        $record->fill($request->only(['bad_amount']));

        if ($created_at = $request->get('created_at')) {
            $record->created_at = $created_at;
        }

        $record->recycled_thing()->associate($recycled);
        $record->recyclable_type = $recycled->recyclable_type;
        
        if ($type = $request->get('type')) {
            $record->type = $type;
        } elseif ($this->user->hasRole('iqc')) {
            $record->type = 'IQC';
        } else {
            $record->type = 'SC';
        }

        $record->save();

        $this->loadRelByModel($record);

        return $this->response
            ->item($record, new QcRecordTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'recycled_thing_id' => 'bail|required|numeric',
            'bad_amount' => 'bail|required|numeric',
        ]);

        $record = QcRecord::whereId($id)->firstOrFail();

        $this->authorize('update', $record);

        $record->fill($request->only(['bad_amount']));
        
        if ($created_at = $request->get('created_at')) {
            $record->created_at = $created_at;
        }

        $recycled = RecycledThing::whereId($request->get('recycled_thing_id'))->firstOrFail();
        $record->recycled_thing()->associate($recycled);
        $record->recyclable_type = $recycled->recyclable_type;

        $record->save();
        $this->loadRelByModel($record);

        return $this->response->item($record, new QcRecordTransformer());
    }

    public function destroy($id)
    {
        $record = QcRecord::findOrFail($id);

        $this->authorize('delete', $record);

        if ($record->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}