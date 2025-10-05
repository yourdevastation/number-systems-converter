<?php

namespace NumberSystem;

require_once "Utils.php";

use NumberSystem\Utils;

class HexadecimalSystem
{

    public static function convert(string $number, int $initSys): string
    {
        switch ($initSys) {
            case 2:
                echo "\033[34mConverting from binary to hexadecimal...\033[0m\n";
                return self::convertFromBinary($number);
                return $number;
            case 8:
                echo "\033[34mConverting from octal to hexadecimal...\033[0m\n";
                return self::convertFromOctal($number);
            case 10:
                echo "\033[34mConverting from decimal to hexdecimal...\033[0m\n";
                return self::convertFromDecimal($number);
            case 16:
                echo "\033[34mYour input number already in decimal system!\033[0m\n";
                echo "\033[34mExiting...\033[0m\n";
                return $number;
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

        $fractTetrads = $isFractional ? self::buildTetrads($fract, $isFractional) : [];

        $fract = $isFractional ? "." . implode("", array_map(
            fn($fractTetrad) =>
            Utils::$hexTetradToDigit[$fractTetrad],
            $fractTetrads
        )) :
            "";

        $tetrads = self::buildTetrads($number, false);
        $result = "";

        $result = implode("", array_map(
            fn($tetrad) =>
            Utils::$hexTetradToDigit[$tetrad],
            $tetrads
        ));

        return $isNegative ? "-" . $result . $fract : $result . $fract;
    }

    private static function buildTetrads(string $number, bool $isFractional): array
    {
        $len = strlen($number);
        $toMultOfFour = (4 - $len % 4) % 4;
        $fullLen = $len + $toMultOfFour;
        $number = !$isFractional ? str_repeat("0", $toMultOfFour) . $number :
            $number . str_repeat("0", $toMultOfFour);
        $result = [];
        for ($i = 0; $i <= $fullLen - 4; $i += 4) {
            array_push($result, substr($number, $i, 4));
        }
        return $result;
    }

    private static function convertFromOctal(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $triads = implode("", self::buildTriads($number));

        return self::convertFromBinary($triads);
    }

    private static function buildTriads(string $number): array
    {
        $len = strlen($number);
        $result = [];
        for ($i = 0; $i < $len; $i++) {
            if ($number[$i] === "." || $number[$i] === ",") {
                array_push($result, $number[$i]);
                continue;
            }
            array_push($result, Utils::$digitToOctalTriad[$number[$i]]);
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
            $carry = "0";
            $newRemainder = "";
            for ($i = 0; $i < strlen($remainder); $i++) {
                $digit = intval(Utils::$hexToDec[$carry]) * 10 + intval($remainder[$i]);
                $carry = Utils::$decToHex[$digit % 16];
                if ($newRemainder !== "" || intval($digit / 16) !== 0) {
                    $newRemainder .= intval($digit / 16);
                }
            }
            $remainder = $newRemainder;
            $result = $carry . $result;
        }
        $result = $result === "" ? "0" : $result;
        return $isNegative ? "-" . $result . $fract : $result . $fract;
    }

    private static function convertDecimalFract(string $fract, int $precision): string
    {
        $fract = "0." . $fract;
        $float = floatval($fract);
        $i = 0;
        $result = "";
        while ($float !== 0.0 && $i < $precision) {
            $result .= Utils::$decToHex[floor($float * 16)];
            $float = $float * 16 - floor($float * 16);
            $i++;
        }
        return $result;
    }

    public static function isValidHexadecimal(string $number): bool
    {
        return $number !== "" && preg_match('/^[+-]?[\dA-Fa-f]+([\.\,]{1}[\dA-Fa-f]{1,})?$/', $number);
    }
}
