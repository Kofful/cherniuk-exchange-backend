<?php

namespace App\Service;

class CodeGenerator
{
    public function generate($length = 64): string
    {
        $input = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $input_length = strlen($input);
        $random_string = '';
        for($i = 0; $i < $length; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }
}
