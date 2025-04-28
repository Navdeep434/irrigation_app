<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Device;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::with('devices')->latest()->paginate(10);
        return view('admin.admin-pages.customer-list', compact('customers'));
    }

    public function edit($id)
    {
        $customer = Customer::with('devices')->findOrFail($id);
        $devices = Device::all();
        $unattachedDevices = Device::whereNull('customer_id')
                           ->whereNull('user_id')
                           ->get();

        return view('admin.admin-pages.customer-edit', compact('customer', 'devices', 'unattachedDevices'));
    }

    public function update(Request $request, $id)
    {
        // Validate request
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email,' . $id,
            'gender'     => 'required|in:male,female,other',
            'dob'        => 'required|date',
            'country_code' => 'required|string|max:5',
            'contact_number' => 'required|string|max:15',
        ]);

        try {
            // Find customer
            $customer = Customer::findOrFail($id);

            // Update fields
            $customer->first_name = $validatedData['first_name'];
            $customer->last_name = $validatedData['last_name'];
            $customer->email = $validatedData['email'];
            $customer->gender = $validatedData['gender'];
            $customer->dob = $validatedData['dob'];
            $customer->country_code = $validatedData['country_code'];
            $customer->contact_number = $validatedData['contact_number'];

            $customer->save();

            // AJAX success response
            if ($request->ajax()) {
                return response()->json(['message' => 'Customer updated successfully!']);
            }

            return redirect()->back()->with('success', 'Customer updated successfully!');
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json(['message' => 'Failed to update customer.'], 500);
            }

            return redirect()->back()->withErrors(['error' => 'Failed to update customer.']);
        }
    }

    public function toggleStatus($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->status = $customer->status === 'active' ? 'inactive' : 'active';
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer status updated successfully.',
            'status' => $customer->status
        ]);
    }


    public function toggleBlock($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->is_blocked = !$customer->is_blocked;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Customer block status updated successfully.',
            'is_blocked' => $customer->is_blocked
        ]);
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully.'
        ]);
    }

    public function unassociateCustomer($id)
    {
        $device = Device::findOrFail($id);
        $device->user_id = null;
        $device->customer_id = null;
        $device->unassigned_at = now();
        $device->save();

        return response()->json(['success' => true]);
    }

    public function attachDeviceCustomer(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'customer_id' => 'required|exists:customers,id',
            'device_number' => 'required|exists:devices,device_number',
        ]);

        $device = Device::where('device_number', $request->device_number)->first();

        if ($device->user_id && $device->customer_id) {
            return response()->json([
                'success' => false,
                'message' => 'Device already associated with another user.'
            ]);
        }

        $device->user_id = $request->user_id;
        $device->customer_id = $request->customer_id;
        $device->assigned_at = now();
        $device->save();

        return response()->json([
            'success' => true,
            'message' => 'Device successfully attached.'
        ]);
    }

}
