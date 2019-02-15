<?php


namespace App\Security;


class TokenGenerator
{
    private const CHARACTERS = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    public function getRandomSecureToken(int $length = 30): string
    {
        $charactersLength = mb_strlen(self::CHARACTERS);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= self::CHARACTERS[random_int(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}