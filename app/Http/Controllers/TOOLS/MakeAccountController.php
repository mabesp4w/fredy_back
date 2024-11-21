<?php

namespace App\Http\Controllers\TOOLS;

use Illuminate\Support\Str;

class MakeAccountController
{
    public function password($char = 8)
    {
        // membuat password
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
        $password = '';
        for ($i = 0; $i < $char; $i++) {
            $index = rand(0, strlen($characters) - 1);
            $password .= $characters[$index];
        }

        return $password;
    }

    public function email($email)
    {
        // Daftar gelar yang ingin dihapus
        $gelar_pattern = '/^(Dra\.|Drs\.|Prof\.|Dr\.|Ir\.|M\.|S\.)\s*/i';

        // Menghapus gelar jika ditemukan di awal string
        $email_without_gelar = preg_replace($gelar_pattern, '', $email);

        // Membagi string berdasarkan tanda koma atau titik
        $array_teks = preg_split('/[,.]/', $email_without_gelar, -1, PREG_SPLIT_NO_EMPTY);
        // Mengambil elemen pertama dari array hasilnya
        $remove_koma = reset($array_teks);

        // Membuat slug dari teks tanpa koma
        $slug = Str::slug($remove_koma, '_');

        // Menambahkan domain email di belakang slug
        $new_email = Str::finish($slug, '@febuogp.com');

        return $new_email;
    }
}
