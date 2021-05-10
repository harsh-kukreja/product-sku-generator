<?php
namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductDetail;
use App\Models\ProductVariantType;
use App\Models\User;
use App\Models\VariantType;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {

        $this->call([UsersTableSeeder::class]);
        $createdById = User::all()->random()->id;
       
    }
}
