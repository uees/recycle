<?php


namespace App\Api\V1;

use App\Models\Customer;
use App\Transformers\CustomerTransformer;
use Illuminate\Http\Request;


class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::query();
        $this->loadRelByQuery($query);
        $this->parseWhere($query, ['salesman']);

        if ($search = $request->get('q')) {
            $name_condition = make_query_condition('name', $search);
            $query->where($name_condition);
        }

        $query->orderBy($this->getSortBy(), $this->getOrder());

        $pagination = $query->paginate($this->getPerPage())
            ->appends($request->except('page'));

        return $this->response->paginator($pagination, new CustomerTransformer());
    }

    public function show($id)
    {
        $customer = Customer::query()->findOrFail($id);
        // $this->loadRelByModel($customer);

        return $this->response->item($customer, new CustomerTransformer());
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'bail|required|unique:customers|max:256',
            'address' => 'nullable|max:256',
            'salesman' => 'nullable|max:64',
        ]);

        $this->authorize('update-customers', Customer::class);

        $customer = new Customer();
        $customer->fill($request->only(['name', 'address', 'salesman']));
        $customer->save();

        return $this->response
            ->item($customer, new CustomerTransformer())
            ->setStatusCode(201);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'bail|required|max:256',
            'address' => 'nullable|max:256',
            'salesman' => 'nullable|max:64',
        ]);

        if (Customer::where('id', '<>', $id)->where('name', $request->get('name'))->exists()) {
            $this->response->errorBadRequest('已经存在的客户');
        }

        $customer = Customer::whereId($id)->firstOrFail();

        $this->authorize('update-customers', $customer);

        $customer->fill($request->only(['name', 'address', 'salesman']));
        $customer->save();

        return $this->response
            ->item($customer, new CustomerTransformer())
            ->setStatusCode(201);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);

        $this->authorize('update-customers', $customer);

        if ($customer->delete()) {
            return $this->response->noContent();
        }

        return $this->response->errorBadRequest('操作失败');
    }
}