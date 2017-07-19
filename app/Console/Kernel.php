<?php

namespace App\Console;

use App\Helpers\Meli;
use App\Models\ProductStorage;
use App\Models\TargetProduct;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function ()
        {
            $targets = TargetProduct::all();

            foreach ($targets as $target)
            {
                $response = Meli::search($target);
                foreach ($response as $item) {
                    if (!$product = ProductStorage::where('meli_id', $item->id)->first()) {
                        $product = new ProductStorage();
                        $product->title = $item->title;
                        $product->price = $item->price;
                        $product->meli_id = $item->id;
                        $product->permalink = $item->permalink;
                        $product->thumbnail = $item->thumbnail;
                        $product->target_id = $target->id;
                        $product->save();
                    } else
                    {
                        $product->found++;
                        $product->save();
                    }
                }
            }

        })->everyMinute();
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
