<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $expenseCategories = [
            'Transportation', 'Bills', 'Internet/Cable Tv', 'Gas', 'Credit Card', 'Loan (House, Car)', 
            'Maintenance (Car/Bike)', 'Insurance', 'Food', 'Drinks', 'Clothes', 'Hobby', 'Fitness', 
            'Household', 'Parking', 'Medicine', 'Entertainment', 'Investments', 'Saving', 'Stationery'
        ];

        $incomeCategories = [
            'Allowance', 'Bonus', 'Business', 'Investments Income', 'Pension', 'Salary'
        ];

        foreach ($expenseCategories as $category) {
            Category::create([
                'name' => $category,
                'type' => 'expense',
                'is_default' => true,
            ]);
        }

        foreach ($incomeCategories as $category) {
            Category::create([
                'name' => $category,
                'type' => 'income',
                'is_default' => true,
            ]);
        }
    }
}
