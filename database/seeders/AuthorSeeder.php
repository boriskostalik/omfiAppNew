<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AuthorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        DB::table('authors')->insert([
            ['id' => 1, 'surname' => 'Chudá', 'von' => '', 'firstname' => 'Jana', 'email' => 'jana.chuda1@student.upjs.sk', 'url' => '', 'institute' => 'Ústav matematických vied, PF UPJŠ Košice', 'specialchars' => false, 'cleanname' => 'Chudá, Jana'],
            ['id' => 2, 'surname' => 'Vallo', 'von' => '', 'firstname' => 'Dušan', 'email' => 'dvallo@ukf.sk', 'url' => '', 'institute' => 'Katedra matematiky FPV UKF Nitra', 'specialchars' => false, 'cleanname' => 'Vallo, Dušan'],
            ['id' => 3, 'surname' => 'Gunčaga', 'von' => '', 'firstname' => 'Ján', 'email' => 'guncaga@fedu.ku.sk', 'url' => '', 'institute' => 'Katedra matematiky a fyziky PF KU Ružomberok', 'specialchars' => false, 'cleanname' => 'Gunčaga, Ján'],
            ['id' => 4, 'surname' => 'Šolcová', 'von' => '', 'firstname' => 'Alena', 'email' => 'solcova@mbox.cesnet.cz', 'url' => '', 'institute' => 'Katedra matematiky Stavební fakulta ČVUT Praha', 'specialchars' => false, 'cleanname' => 'Šolcová, Alena'],
            ['id' => 5, 'surname' => 'Křížek', 'von' => '', 'firstname' => 'Michal', 'email' => 'krizek@math.cas.cz', 'url' => '', 'institute' => 'Matematický ústav Akademie věd ČR Praha', 'specialchars' => false, 'cleanname' => 'Křížek, Michal'],
            ['id' => 6, 'surname' => 'Lendelová', 'von' => '', 'firstname' => 'Katarína', 'email' => 'lendelov@fpv.umb.sk', 'url' => '', 'institute' => 'Katedra matematiky FPV UMB Banská Bystrica', 'specialchars' => false, 'cleanname' => 'Lendelová, Katarína'],
            ['id' => 7, 'surname' => 'Jadroňová', 'von' => '', 'firstname' => 'Miriam', 'email' => 'miriam.jadronova@tuke.sk', 'url' => '', 'institute' => 'Katedra aplikovanej matematiky SjF TU Košice', 'specialchars' => false, 'cleanname' => 'Jadroňová, Miriam'],
            ['id' => 8, 'surname' => 'Krajčo', 'von' => '', 'firstname' => 'Ján', 'email' => 'krajco@fpv.umb.sk', 'url' => '', 'institute' => 'Katedra fyziky FPV UMB Banská Bystrica', 'specialchars' => false, 'cleanname' => 'Krajčo, Ján'],
            ['id' => 9, 'surname' => 'Ferencová', 'von' => '', 'firstname' => 'Elena', 'email' => 'ferencova@fmed.uniba.sk', 'url' => '', 'institute' => 'Lekárska fakulta UK Bratislava', 'specialchars' => false, 'cleanname' => 'Ferencová, Elena'],
            ['id' => 10, 'surname' => 'Chocholatý', 'von' => '', 'firstname' => 'Matúš', 'email' => '', 'url' => '', 'institute' => 'Lekárska fakulta UK Bratislava', 'specialchars' => false, 'cleanname' => 'Chocholatý, Matúš'],
            ['id' => 11, 'surname' => 'Kukurová', 'von' => '', 'firstname' => 'Elena', 'email' => 'kukurova@fmed.uniba.sk', 'url' => '', 'institute' => 'Lekárska fakulta UK Bratislava', 'specialchars' => false, 'cleanname' => 'Kukurová, Elena'],
            ['id' => 12, 'surname' => 'Kráľová', 'von' => '', 'firstname' => 'Eva', 'email' => 'kralova@fmed.uniba.sk', 'url' => '', 'institute' => 'Lekárska fakulta UK Bratislava', 'specialchars' => false, 'cleanname' => 'Kráľová, Eva'],
            ['id' => 13, 'surname' => 'Červeň', 'von' => '', 'firstname' => 'Ivan', 'email' => 'icerven@elf.stuba.sk', 'url' => '', 'institute' => 'Katedra fyziky FEI STU Bratislava', 'specialchars' => false, 'cleanname' => 'Červeň, Ivan']
        ]);
    }
}
