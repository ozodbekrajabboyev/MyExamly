<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\District;

class DistrictSeeder extends Seeder
{
    public function run(): void
    {
        $districts = [
            // 1. Qoraqalpog‘iston Respublikasi
            1 => [
                "Amudaryo tumani",
                "Beruniy tumani",
                "Chimboy tumani",
                "Ellikqal’a tumani",
                "Kegeyli tumani",
                "Mo‘ynoq tumani",
                "Nukus shahri",   // ➕ qo‘shildi
                "Nukus tumani",
                "Qonliko‘l tumani",
                "Qo‘ng‘irot tumani",
                "Qorao‘zak tumani",
                "Shumanay tumani",
                "Taxtako‘pir tumani",
                "To‘rtko‘l tumani",
                "Xo‘jayli tumani"
            ],

            // 2. Andijon viloyati
            2 => [
                "Andijon shahri", // ➕
                "Andijon tumani",
                "Asaka tumani",
                "Baliqchi tumani",
                "Bo‘z tumani",
                "Buloqboshi tumani",
                "Izboskan tumani",
                "Jalaquduq tumani",
                "Marhamat tumani",
                "Oltinko‘l tumani",
                "Paxtaobod tumani",
                "Shahrixon tumani",
                "Ulug‘nor tumani",
                "Xo‘jaobod tumani",
                "Qo‘rg‘ontepa tumani"
            ],

            // 3. Buxoro viloyati
            3 => [
                "Buxoro shahri", // ➕
                "Buxoro tumani",
                "G‘ijduvon tumani",
                "Jondor tumani",
                "Kogon shahri",  // ➕
                "Kogon tumani",
                "Olot tumani",
                "Peshku tumani",
                "Qorako‘l tumani",
                "Qorovulbozor tumani",
                "Romitan tumani",
                "Shofirkon tumani",
                "Vobkent tumani"
            ],

            // 4. Farg‘ona viloyati
            4 => [
                "Farg‘ona shahri", // ➕
                "Bag‘dod tumani",
                "Beshariq tumani",
                "Buvayda tumani",
                "Dang‘ara tumani",
                "Farg‘ona tumani",
                "Furqat tumani",
                "Qo‘qon shahri",  // ➕
                "Qo‘qon tumani",
                "Quva tumani",
                "Quvasoy shahri", // ➕
                "Quvasoy tumani",
                "Rishton tumani",
                "So‘x tumani",
                "Toshloq tumani",
                "Uchko‘prik tumani",
                "Yozyovon tumani"
            ],

            // 5. Jizzax viloyati
            5 => [
                "Jizzax shahri", // ➕
                "Arnasoy tumani",
                "Baxmal tumani",
                "Do‘stlik tumani",
                "Forish tumani",
                "G‘allaorol tumani",
                "Jizzax tumani",
                "Mirzacho‘l tumani",
                "Paxtakor tumani",
                "Yangiobod tumani",
                "Zafarobod tumani",
                "Zarbdor tumani"
            ],

            // 6. Xorazm viloyati
            6 => [
                "Urganch shahri", // ➕
                "Bog‘ot tumani",
                "Gurlan tumani",
                "Xazorasp tumani",
                "Xonqa tumani",
                "Qo‘shko‘pir tumani",
                "Shovot tumani",
                "Urganch tumani",
                "Yangiariq tumani",
                "Yangibozor tumani"
            ],

            // 7. Namangan viloyati
            7 => [
                "Namangan shahri", // ➕
                "Chortoq tumani",
                "Chust tumani",
                "Kosonsoy tumani",
                "Mingbuloq tumani",
                "Namangan tumani",
                "Norin tumani",
                "Pop tumani",
                "To‘raqo‘rg‘on tumani",
                "Uchqo‘rg‘on tumani",
                "Yangiqo‘rg‘on tumani"
            ],

            // 8. Navoiy viloyati
            8 => [
                "Navoiy shahri", // ➕
                "Konimex tumani",
                "Karmana tumani",
                "Navbahor tumani",
                "Nurota tumani",
                "Qiziltepa tumani",
                "Tomdi tumani",
                "Uchquduq tumani",
                "Xatirchi tumani"
            ],

            // 9. Qashqadaryo viloyati
            9 => [
                "Qarshi shahri", // ➕
                "Chiroqchi tumani",
                "Dehqonobod tumani",
                "G‘uzor tumani",
                "Kasbi tumani",
                "Kitob tumani",
                "Koson tumani",
                "Mirishkor tumani",
                "Muborak tumani",
                "Nishon tumani",
                "Qamashi tumani",
                "Qarshi tumani",
                "Shahrisabz shahri", // ➕
                "Shahrisabz tumani",
                "Yakkabog‘ tumani"
            ],

            // 10. Samarqand viloyati
            10 => [
                "Samarqand shahri", // ➕
                "Bulung‘ur tumani",
                "Ishtixon tumani",
                "Jomboy tumani",
                "Kattaqo‘rg‘on shahri", // ➕
                "Kattaqo‘rg‘on tumani",
                "Narpay tumani",
                "Nurobod tumani",
                "Oqdaryo tumani",
                "Paxtachi tumani",
                "Payariq tumani",
                "Pastdarg‘om tumani",
                "Qo‘shrabot tumani",
                "Samarqand tumani",
                "Toyloq tumani",
                "Urgut tumani"
            ],

            // 11. Sirdaryo viloyati
            11 => [
                "Guliston shahri", // ➕
                "Boyovut tumani",
                "Guliston tumani",
                "Mirzaobod tumani",
                "Oqoltin tumani",
                "Sardoba tumani",
                "Sayxunobod tumani",
                "Sirdaryo tumani",
                "Xovos tumani"
            ],

            // 12. Surxondaryo viloyati
            12 => [
                "Termiz shahri", // ➕
                "Angor tumani",
                "Bandixon tumani",
                "Boysun tumani",
                "Denov tumani",
                "Jarqo‘rg‘on tumani",
                "Qiziriq tumani",
                "Qumqo‘rg‘on tumani",
                "Muzrabot tumani",
                "Oltinsoy tumani",
                "Sariosiyo tumani",
                "Sherobod tumani",
                "Sho‘rchi tumani",
                "Termiz tumani",
                "Uzun tumani"
            ],

            // 13. Toshkent viloyati
            13 => [
                "Angren shahri",
                "Bekobod shahri", // ➕
                "Bekobod tumani",
                "Bo‘ka tumani",
                "Chinoz tumani",
                "Ohangaron shahri", // ➕
                "Ohangaron tumani",
                "Oqqo‘rg‘on tumani",
                "Parkent tumani",
                "Piskent tumani",
                "Quyichirchiq tumani",
                "O‘rtachirchiq tumani",
                "Yuqorichirchiq tumani",
                "Chirchiq shahri", // ➕
                "Zangiota tumani"
            ],

            // 14. Toshkent shahri
            14 => [
                "Bektemir tumani",
                "Chilonzor tumani",
                "Mirzo Ulug‘bek tumani",
                "Mirobod tumani",
                "Olmazor tumani",
                "Sergeli tumani",
                "Shayxontohur tumani",
                "Uchtepa tumani",
                "Yakkasaroy tumani",
                "Yashnobod tumani",
                "Yunusobod tumani"
            ],
        ];


        foreach ($districts as $regionId => $districtList) {
            foreach ($districtList as $district) {
                District::create([
                    'region_id' => $regionId,
                    'name'      => $district,
                ]);
            }
        }
    }
}
