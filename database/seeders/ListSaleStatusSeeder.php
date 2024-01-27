<?php

namespace Database\Seeders;

use App\Models\ListSaleStatus;
use Illuminate\Database\Seeder;

class ListSaleStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run() : void
    {
        ListSaleStatus::create([
            'id' => ListSaleStatus::PENDING,
            'name' => 'Pending',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::PROCESSING,
            'name' => 'Processing',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::PAID,
            'name' => 'Paid',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::IN_TRANSIT_SHIPPED,
            'name' => 'In Transit/Shipped',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::DELIVERED,
            'name' => 'Delivered',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::CANCELED,
            'name' => 'Canceled',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::RETURNED,
            'name' => 'Returned',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::PARTIALLY_REFUNDED,
            'name' => 'Partially Refunded',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::PAYMENT_FAILED,
            'name' => 'Payment Failed',
        ]);

        ListSaleStatus::create([
            'id' => ListSaleStatus::REFUNDED,
            'name' => 'Refunded',
        ]);
    }
}
