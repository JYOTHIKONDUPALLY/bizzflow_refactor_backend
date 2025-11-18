<?php

namespace App\Domains\Auth\Actions;

use App\Domains\Auth\Models\Customer;
use App\Domains\Auth\DataTransferObjects\RegisterCustomerData;
use App\Domains\Auth\Repositories\CustomerRepository;
use App\Domains\Auth\Events\CustomerRegistered;
use Illuminate\Support\Facades\Hash;

class RegisterCustomerAction
{
    public function __construct(
        private CustomerRepository $customerRepo
    ) {}

    public function execute(RegisterCustomerData $data): Customer
    {
        $customer = $this->customerRepo->create([
            'franchise_id' => $data->franchise_id,
            'business_unit_id' => $data->business_unit_id,
            'first_name' => $data->first_name,
            'last_name' => $data->last_name,
            'email' => $data->email,
            'phone' => $data->phone,
            'password_hash' => Hash::make($data->password),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // event(new CustomerRegistered($customer));

        return $customer;
    }
}
