<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Http\Resources\V1\CustomerResource;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return CustomerResource::collection(Customer::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer = Customer::create($validated);

        return response()->json([
            'status' => true,
            'message' => 'Customer created successfully',
            'data' => new CustomerResource($customer)
        ], 201);
    }

    public function show(Customer $customer)
    {
        return new CustomerResource($customer);
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
        ]);

        $customer->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'Customer updated successfully',
            'data' => new CustomerResource($customer)
        ]);
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json([
            'status' => true,
            'message' => 'Customer deleted successfully',
            'data' => null
        ]);
    }
}
