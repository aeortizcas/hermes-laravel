<?php

/*
 * Polyfill para entornos donde mbstring se compiló sin mbregex
 * (p. ej. binarios estáticos): solo define mb_split si no existe.
 * La semántica equivale a preg_split con el modificador /u.
 */

if (! function_exists('mb_split')) {
    /**
     * @param  int  $limit  si es -1, sin límite (igual que mb_split).
     * @return array<int, string>|false
     */
    function mb_split(string $pattern, string $string, int $limit = -1): array|false
    {
        $delimiter = '~';
        $escaped = str_replace($delimiter, '\\'.$delimiter, $pattern);

        return preg_split($delimiter.$escaped.$delimiter.'u', $string, $limit === -1 ? -1 : $limit);
    }
}
