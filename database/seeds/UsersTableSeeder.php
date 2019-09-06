<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\User;
use App\Watchlist;

class UsersTableSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $user = factory(User::class)->create([
            'username' => 'guest',
            'password' => bcrypt('1234')
        ]);

        foreach ([ 'GOOGL' , 'YHOO' , 'NDAQ' ,'TWTR' , 'DOW' , 'AAPL'] as $key => $symbol) {
            Watchlist::create([
               'user_id' => $user->id,
               'stock_symbol' => $symbol,
               'created_at' => Carbon::now()->format('Y-m-d H:i:s')
           ]);
        }
    }
}
