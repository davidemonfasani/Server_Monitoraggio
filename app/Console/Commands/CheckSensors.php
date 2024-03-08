<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sensor;
use App\Models\Monitoraggio;
use App\Traits\EmailTrait;


class CheckSensors extends Command
{
    use EmailTrait;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sensors:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check sensors and send mail if necessary';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()//funzione di keep alive
    {
     
        // Ottieni tutti i sensori
        $sensors = Sensor::all();

        foreach ($sensors as $sensor) {
            // Ottieni l'ultimo record di monitoraggio per questo sensore
            $lastRecord = Monitoraggio::where('id_Sensor', $sensor->id_Sensor)->orderBy('created_at', 'desc')->first();

            // Controlla se l'ultimo record è più vecchio del timer del sensore
            if ($lastRecord && $lastRecord->created_at->diffInSeconds() > $sensor->timer) {
                // Invia una mail
                $message = "Il sensore N {$sensor->id_Sensor} non ha più inviato messaggi dal {$lastRecord->created_at}";
                $users = $sensor->cellar->users;
                foreach ($users as $user) {
                    $this->sendEmail($user, $message, "Errore sul sensore N {$sensor->id_Sensor}");
                }
            }
            else if(!$lastRecord)
            {
                $message = "Il sensore N {$sensor->id_Sensor} non ha mai inviato messaggi";
                $users = $sensor->cellar->users;
                foreach ($users as $user) {
                    $this->sendEmail($user, $message, "Errore sul sensore N {$sensor->id_Sensor}");
                }
            }
        }
    }
}
