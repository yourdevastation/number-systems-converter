<?php

namespace NumberSystem;

require_once "Utils.php";

use NumberSystem\Utils;

class BinarySystem
{
    private int $precision;

    public static function convert(string $number, int $initSys): string
    {
        switch ($initSys) {
            case 2:
                echo "\033[34mYour input number already in binary system!\033[0m\n";
                echo "\033[34mExiting...\033[0m\n";
                return $number;
            case 8:
                echo "\033[34mConverting from octal to binary...\033[0m\n";
                return self::convertFromOctal($number);
            case 10:
                echo "\033[34mConverting from decimal to binary...\033[0m\n";
                return self::convertFromDecimal($number);
            case 16:
                echo "\033[34mConverting from hexadecimal to binary...\033[0m\n";
                return self::convertFromHexadecimal($number);
            default:
                echo "\033[31mError! Unknown number system\033[0m\n";
                return "0";
        }
    }

    private static function convertFromOctal(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $isNegative = false;
        Utils::checkForSign($number, $isNegative);

        $isFractional = false;
        $fract = "";
        Utils::checkIsFractional($number, $fract, $isFractional);

        $result = "";
        $fractResult = "";
        for ($i = 0; $i < strlen($number); $i++) {
            $result .= Utils::$digitToOctalTriad[$number[$i]];
        }

        if (str_starts_with($result, "0")) {
            if ($result === Utils::$digitToOctalTriad[0]) {
                $result = "0";
            } else {
                $firstNonZero = stripos($result, "1");
                $result = substr($result, $firstNonZero);
            }
        }

        $fractPart = "";
        if ($isFractional) {
            $fractResult = self::convertOctalFract($fract);
            $fractResult = substr($fractResult, 0, strrpos($fractResult, "1") + 1);
            $fractPart = "." . $fractResult;
        }

        if ($isNegative) {
            return "-" . $result . $fractPart . ", " .
                self::calculateSignMagnitude($result, $fractResult);
        }

        return $result . $fractPart;
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

        $result = "";
        $fractResult = "";
        $remainder = $number;
        while ($remainder !== "0" && $remainder !== "") {
            $carry = 0;
            $newRemainder = "";
            for ($i = 0; $i < strlen($remainder); $i++) {
                $digit = $carry * 10 + intval($remainder[$i]);
                $carry = $digit % 2;
                if ($newRemainder !== "" || intval($digit / 2) !== 0) {
                    $newRemainder .= intval($digit / 2);
                }
            }
            $remainder = $newRemainder;
            $result = $carry . $result;
        }

        $result = $result === "" ? "0" : $result;

        $fractPart = "";
        if ($isFractional) {
            $fractResult .= self::convertDecimalFract($fract, Utils::getPrecision());
            $fractPart = "." . $fractResult;
        }
        if ($isNegative) {
            return "-" . $result . $fractPart . ", " .
                self::calculateSignMagnitude($result, $fractResult);
        }
        return $result . $fractPart;
    }

    private static function convertFromHexadecimal(string $number): string
    {
        if ($number === "0" || $number === "") {
            return "0";
        }
        $isNegative = false;
        Utils::checkForSign($number, $isNegative);

        $isFractional = false;
        $fract = "";
        Utils::checkIsFractional($number, $fract, $isFractional);

        $result = "";
        $fractResult = "";
        $number = strtoupper($number);
        for ($i = 0; $i < strlen($number); $i++) {
            $result .= Utils::$digitToHexTetrad[$number[$i]];
        }

        if (str_starts_with($result, "0")) {
            if ($result === Utils::$digitToHexTetrad[0]) {
                $result = "0";
            } else {
                $firstNonZero = stripos($result, "1");
                $result = substr($result, $firstNonZero);
            }
        }

        $fractPart = "";
        if ($isFractional) {
            $fractResult = self::convertHexFract($fract);
            $fractResult = substr($fractResult, 0, strrpos($fractResult, "1") + 1);
            $fractPart = "." . $fractResult;
        }

        if ($isNegative) {
            return "-" . $result . $fractPart . ", " .
                self::calculateSignMagnitude($result, $fractResult);
        }

        return $result . $fractPart;
    }

    private static function calculateSignMagnitude(string $number, string $fract)
    {
        $len = strlen($number);
        $fullLen = ($len < 8) ? 8 : (($len < 16) ? 16 : (($len < 32) ? 32 : 64));
        $addition = "1" . str_repeat("0", $fullLen - $len - 1);
        $fullNumber = $addition . $number;
        $fullNumber = self::formatSignMagnitude($fullNumber);
        $fractPart = "";
        if ($fract !== "") {
            $fractLen = strlen($fract);
            $addition = str_repeat("0", $fullLen - $fractLen);
            $fractPart = "." . $fract . $addition;
        }
        return "sign-magnitude: " . $fullNumber . $fractPart;
    }

    private static function formatSignMagnitude(string $number): string
    {
        $formatedNumber = "";
        for ($i = 0; $i <= strlen($number) - 4; $i += 4) {
            $formatedNumber .= substr($number, $i, 4) . " ";
        }
        return trim($formatedNumber);
    }

    private static function convertDecimalFract(string $fract, int $precision): string
    {
        $fract = "0." . $fract;
        $float = floatval($fract);
        $i = 0;
        $result = "";
        while ($float !== 0.0 && $i < $precision) {
            $result .= floor($float * 2);
            $float = $float * 2 - floor($float * 2);
            $i++;
        }
        return $result;
    }

    private static function convertOctalFract(string $fract)
    {
        $result = "";
        for ($i = 0; $i < strlen($fract); $i++) {
            $result .= Utils::$digitToOctalTriad[$fract[$i]];
        }
        return $result;
    }

    private static function convertHexFract(string $fract)
    {
        $result = "";
        $fract = strtoupper($fract);
        for ($i = 0; $i < strlen($fract); $i++) {
            $result .= Utils::$digitToHexTetrad[$fract[$i]];
        }
        return $result;
    }

    public static function isValidBinary(string $number): bool
    {
        return $number !== "" && preg_match('/^[+-]?[01]+([\.\,]{1}[01]{1,})?$/', $number);
    }
}
