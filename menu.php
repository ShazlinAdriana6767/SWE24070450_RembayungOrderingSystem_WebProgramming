<?php
// Single source of truth for the menu. Prices are always taken from here
// on the server, never from the browser.
$MENU = [
  'Food' => [
    'f01' => ['Nasi Putih', 3.90],
    'f02' => ['Ikan Siakap Sambal Berlada', 68.90],
    'f03' => ['Gulai Utara Ikan Jenahak', 39.90],
    'f04' => ['Ikan Tenggiri Masak Kicap', 39.90],
    'f05' => ['Siput Sedut Masak Lemak Cili Api', 35.90],
    'f06' => ['Gulai Utara Ikan Temenung', 29.90],
    'f07' => ['Set Ikan Rebus & Budu Picit', 22.90],
    'f08' => ['Ikan Pekasam Goreng', 19.90],
    'f09' => ['Ikan Keli Sambal Berlada', 15.90],
    'f10' => ['Ikan Masin Rangup', 5.90],
    'f11' => ['Sotong Sambal Tumis Petai', 43.90],
    'f12' => ['Udang Sambal Tumis Petai', 39.90],
    'f13' => ['Daging Salai Masak Lemak Cili Api', 39.90],
    'f14' => ['Ayam Masak Kicap', 29.90],
    'f15' => ['Ayam Salai Masak Lemak Cili Api', 29.90],
  ],
  'Beverage' => [
    'b01' => ['Senandung Senja / Kaseh Kasturi', 15.90],
    'b02' => ['Kopi Santan', 14.90],
    'b03' => ['Jus Buah / Jus Tembikai', 14.90],
    'b04' => ['Kopi Ais', 8.90],
    'b05' => ['Sirap Bandung Cincau Ais', 7.90],
    'b06' => ['Teh O Limau Ais', 6.90],
    'b07' => ['Teh Tarik Kampung Panas', 6.90],
    'b08' => ['Sirap Selasih Ais', 4.90],
    'b09' => ['Teh O Panas', 4.90],
    'b10' => ['Air Kosong Ais', 0.50],
    'b11' => ['Air Suam', 0.30],
  ],
  'Dessert' => [
    'd01' => ['Pengat Nangka bersama Pulut', 22.90],
    'd02' => ['Buah Campur Asam Boi', 22.90],
    'd03' => ['Cucur Udang', 16.90],
    'd04' => ['Tapai Pulut Aiskrim', 15.90],
    'd05' => ['Cekodok Pisang', 15.90],
  ],
];

function menu_lookup(array $menu): array {
    $flat = [];
    foreach ($menu as $cat => $items) {
        foreach ($items as $id => [$name, $price]) {
            $flat[$id] = ['name' => $name, 'price' => $price, 'category' => $cat];
        }
    }
    return $flat;
}
