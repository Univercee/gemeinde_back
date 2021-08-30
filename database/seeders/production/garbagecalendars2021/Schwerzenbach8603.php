<?php

namespace Database\Seeders\Production\GarbageCalendars2021;

use Illuminate\Database\Seeder;
use DB;
class Schwerzenbach8603 extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /****           TYPES          ****/
        /* A = Abfall, Kehricht, Sperrgut */
        /* G = Grüngut */
        /* P = Papier */
        /* K = Karton */
        /* M = Metall */
        /* M = Deponiegut */
        /* H = Häckselservice */
        /***********************************/

        DB::table('garbage_calendar')->insert([
            //location_id = 183 = Schwerzenbach 8603
            [
                'location_id' => 183,
                'date' => '2021-09-02',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-09',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-16',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-23',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-30',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-07',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-14',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-21',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-28',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-04',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-11',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-18',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-25',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-09',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-23',
                'type' => 'G'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-03',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-10',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-17',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-24',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-01',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-08',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-15',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-22',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-29',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-05',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-12',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-19',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-26',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-03',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-10',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-17',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-24',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-31',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-08',
                'type' => 'M'
            ],
            [
                'location_id' => 183,
                'date' => '2021-09-22',
                'type' => 'K'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-03',
                'type' => 'A'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-13',
                'type' => 'D'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-20',
                'type' => 'K'
            ],
            [
                'location_id' => 183,
                'date' => '2021-10-23',
                'type' => 'P'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-10',
                'type' => 'H'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-17',
                'type' => 'K'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-17',
                'type' => 'M'
            ],
            [
                'location_id' => 183,
                'date' => '2021-11-27',
                'type' => 'P'
            ],
            [
                'location_id' => 183,
                'date' => '2021-12-15',
                'type' => 'K'
            ],
        ]);
    }
}
