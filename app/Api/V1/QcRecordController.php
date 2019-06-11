<?php

namespace App\Api\V1;

use App\Models\QcRecord;
use App\Transformers\QcRecordTransformer;
use Illuminate\Http\Request;


class QcRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = QcRecord::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['type', 'created_at']);

        $query->orderBy($this->getSortBy(), $this->getOrder());

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

        $record = new QcRecord;
        $record->fill($request->only(['recycled_thing_id', 'bad_amount']));

        if ($this->user->hasRole('iqc')) {
            $record->type = 'IQC';
        } else {
            $record->type = 'SC';
        }

        $record->save();

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

        $record->fill($request->only(['recycled_thing_id', 'bad_amount']));
        $record->save();

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