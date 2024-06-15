<?php

namespace App\Console\Commands;

use App\Models\Landowner;
use Illuminate\Console\Command;

class SimulateNpksensor extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'simulate:npksensor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $landowners = Landowner::all();

        foreach ($landowners as $landowner) {
            $latestLand = $landowner->lands()->latest()->first();

            if ($latestLand) {

                $crops = $latestLand->crop()->with('landHistory')->get();

                foreach ($crops as $crop) {
                    foreach ($crop->landHistory as $landHistory) {
                        $newN = $this->randomChange($landHistory->nitrogen_applied);
                        $newP = $this->randomChange($landHistory->phosphorus_applied);
                        $newK = $this->randomChange($landHistory->potassium_applied);

                        $optimalN = $crop->nitrogen;
                        $optimalP = $crop->phosphorus;
                        $optimalK = $crop->potassium;

                        $newN = $this->optimizeValues($newN, $optimalN);
                        $newP = $this->optimizeValues($newP, $optimalP);
                        $newK = $this->optimizeValues($newK, $optimalK);

                        $landHistory->update([
                            'nitrogen_applied' => $newN,
                            'phosphorus_applied' => $newP,
                            'potassium_applied' => $newK,
                        ]);

                        $this->info("Updated NPK values for LandHistory ID: {$landHistory->id}");
                    }
                }
            }
        }

        $this->info('NPK sensor simulation completed for all landowners.');
    }

    protected function randomChange($value)
    {
        return max(0, min(80, $value + rand(-5, 5)));
    }

    protected function optimizeValues($current, $optimal)
    {

        $threshold = 5;

        $difference = $current - $optimal;
        if (abs($difference) > $threshold) {

            if ($current > $optimal) {
                return max($optimal, $current - $threshold);
            } else {
                return min($optimal, $current + $threshold);
            }
        }

        return $current;
    }


}
