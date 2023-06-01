<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Aptitude;

class AptitudeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $aptitudes = [
            'General discipline and human relations',
            'Work skills and handling',
            'Initiative/entrepreneurship',
            'Imagination skills and innovation',
            'Knowledge acquired on the internship site',
        ];

        foreach ($aptitudes as $aptitude) {
            Aptitude::create([
                'name' => $aptitude,
            ]);
        }
    }
}