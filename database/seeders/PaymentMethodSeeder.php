<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PaymentMethod;

class PaymentMethodSeeder extends Seeder
{
    public function run()
    {
        $paymentMethods = [
            'Cash', 'Credit Card', 'Debit Card', 'Bank Transfer', 'Mobile Payment'
        ];

        foreach ($paymentMethods as $method) {
            PaymentMethod::create([
                'name' => $method,
                'is_default' => true,
            ]);
        }
    }
}

