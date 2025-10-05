<?php

namespace NumberSystem;

require_once "Utils.php";

use NumberSystem\Utils;

class OctalSystem
{
    public static function convert(string $number, int $initSys): string
    {
        switch ($initSys) {
            case 2:
                echo "\033[34mConverting from binary to octal...\033[0m\n";
                return self::convertFromBinary($number);
                return $number;
            case 8:
                echo "\033[34mYour input number already in octal system!\033[0m\n";
                echo "\033[34mExiting...\033[0m\n";
                return $number;
            case 10:
                echo "\033[34mConverting from decimal to octal...\033[0m\n";
                return self::convertFromDecimal($number);
            case 16:
                echo "\033[34mConverting from hexadecimal to octal...\033[0m\n";
                return self::convertFromHexadecimal($number);
            default:
                echo "\033[31mError! Unknown number system\033[0m\n";
                return "0";
        }
    }

    private static function convertFromBinary(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $isNegative = false;
        Utils::checkForSign($number, $isNegative);

        $isFractional = false;
        $fract = "";
        Utils::checkIsFractional($number, $fract, $isFractional);

        $number = substr($number, strpos($number, "1"));
        $fract = substr($fract, 0, strrpos($fract, "1") + 1);

        $fractTriads = $isFractional ? self::buildTriads($fract, $isFractional) : [];

        $fract = $isFractional ? "." . implode("", array_map(
            fn($fractTriad) =>
            Utils::$octalTriadToDigit[$fractTriad],
            $fractTriads
        )) :
            "";

        $triads = self::buildTriads($number, false);

        $result = implode("", array_map(
            fn($triad) =>
            Utils::$octalTriadToDigit[$triad],
            $triads
        ));

        return $isNegative ? "-" . $result . $fract : $result . $fract;
    }

    private static function buildTriads(string $number, bool $isFractional): array
    {
        $len = strlen($number);
        $toMultOfThree = (3 - $len % 3) % 3;
        $fullLen = $len + $toMultOfThree;
        $number = !$isFractional ? str_repeat("0", $toMultOfThree) . $number :
            $number . str_repeat("0", $toMultOfThree);
        $result = [];
        for ($i = 0; $i <= $fullLen - 3; $i += 3) {
            array_push($result, substr($number, $i, 3));
        }
        return $result;
    }

    private static function convertFromDecimal(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $isNegative = false;
        Utils::checkForSign($number, $isNegative);

        $isFractional = false;
        $fract = "";
        Utils::checkIsFractional($number, $fract, $isFractional);
        $fract = $isFractional ? "." . self::convertDecimalFract($fract, Utils::getPrecision()) : "";

        $result = "";
        $remainder = $number;
        while ($remainder !== "0" && $remainder !== "") {
            $carry = 0;
            $newRemainder = "";
            for ($i = 0; $i < strlen($remainder); $i++) {
                $digit = $carry * 10 + intval($remainder[$i]);
                $carry = $digit % 8;
                if ($newRemainder !== "" || intval($digit / 8) !== 0) {
                    $newRemainder .= intval($digit / 8);
                }
            }
            $remainder = $newRemainder;
            $result = $carry . $result;
        }
        $result = $result === "" ? "0" : $result;
        return $isNegative ? "-" . $result . $fract : $result . $fract;
    }

    private static function convertFromHexadecimal(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $tetrads = implode("", self::buildTetrads($number));

        return self::convertFromBinary($tetrads);
    }

    private static function buildTetrads(string $number): array
    {
        $len = strlen($number);
        $result = [];
        for ($i = 0; $i < $len; $i++) {
            if ($number[$i] === "." || $number[$i] === ",") {
                array_push($result, $number[$i]);
                continue;
            }
            array_push($result, Utils::$digitToHexTetrad[strtoupper($number[$i])]);
        }
        return $result;
    }

    private static function convertDecimalFract(string $fract, int $precision): string
    {
        $fract = "0." . $fract;
        $float = floatval($fract);
        $i = 0;
        $result = "";
        while ($float !== 0.0 && $i < $precision) {
            $result .= floor($float * 8);
            $float = $float * 8 - floor($float * 8);
            $i++;
        }
        return $result;
    }

    public static function isValidOctal(string $number): bool
    {
        return $number !== "" && preg_match('/^[+-]?[0-7]+([\.\,]{1}[0-7]{1,})?$/', $number);
    }
}
