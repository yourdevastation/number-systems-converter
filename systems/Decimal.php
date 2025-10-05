<?php

namespace NumberSystem;
require_once "Utils.php";
use NumberSystem\Utils;

class DecimalSystem
{
    public static function convert(string $number, int $initSys): string
    {
        switch ($initSys) {
            case 2:
                echo "\033[34mConverting from binary to decimal...\033[0m\n";
                return self::convertFromBase($number, "2");
                return $number;
            case 8:
                echo "\033[34mConverting from octal to decimal...\033[0m\n";
                return self::convertFromBase($number, "8");
            case 10:
                echo "\033[34mYour input number already in decimal system!\033[0m\n";
                echo "\033[34mExiting...\033[0m\n";
                return $number;
            case 16:
                echo "\033[34mConverting from hexadecimal to decimal...\033[0m\n";
                return self::convertFromBase($number, "16");
            default:
                echo "\033[31mError! Unknown number system\033[0m\n";
                return "0";
        }
    }

    private static function convertFromBase(string $number, string $base): string
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
        $power = strlen($number) - 1;
        $intCapacity = ($base === "2") ? 32 : (($base === "8") ? 10 : 8);
        if (strlen($number) <= $intCapacity) {
            $integer = 0;
            for ($i = 0; $i < strlen($number); $i++) {
                $integer += intval(Utils::$hexToDec[strtoupper($number[$i])]) *
                            pow(intval($base), $power - $i);
            }
            $result .= $integer;
        } else {
            for ($i = 0; $i < strlen($number); $i++) {
                $powerOfBase = self::arithmeticPower($base, $power - $i);
                $inc = self::arithmeticMultiply(Utils::$hexToDec[strtoupper($number[$i])],
                                                $powerOfBase);
                if ($inc !== "0") {
                    $result = self::arithmeticSum($result, $inc);
                }
            }
        }

        if ($isFractional) {
            $fractResult = self::convertFract($fract, $base);
            $result .= $fractResult;
        }

        return $isNegative ? "-" . $result : $result;
    }

    private static function convertFract(string $fract, string $base) : string {
        $power = 1;
        $result = 0.0;
        for ($i = 0; $i < strlen($fract); $i++) {
            $result += intval(Utils::$hexToDec[strtoupper($fract[$i])]) *
                       pow(intval($base), -($power + $i));
        }
        $str = $result > 0.0 ? (string)$result : "0.0";
        return substr($str, strpos($str, "."));
    }

    public static function arithmeticPower(string $num, int $power): string
    {
        if ($power === 0) {
            return "1";
        }
        $result = $num;
        for ($i = 1; $i < $power; $i++) {
            $result = self::arithmeticMultiply($result, $num);
        }
        return $result;
    }

    public static function arithmeticMultiply(string $num1, string $num2): string
    {
        if ($num1 === "0" || $num2 === "0") {
            return "0";
        }
        if ($num1 === "" || $num2 === "") {
            return "0";
        }

        $minLen = 0;
        $maxLen = 0;
        $longNum = "";
        $shortNum = "";
        self::findLongestNum($num1, $num2, $minLen, $maxLen, $longNum, $shortNum);

        $results = [];
        for ($i = $minLen - 1; $i >= 0; $i--) {
            $result = "";
            $carry = 0;
            for ($j = $maxLen - 1; $j >= 0; $j--) {
                $multOfTwo = intval($longNum[$j]) * intval($shortNum[$i]) + $carry;
                if ($multOfTwo > 9 && $j > 0) {
                    $carry = intval($multOfTwo / 10);
                    $multOfTwo %= 10;
                } else {
                    $carry = 0;
                }
                $result = $multOfTwo . $result;
            }
            $result = $result . str_repeat("0", $minLen - 1 - $i);
            array_push($results, $result);
        }
        $mult = "0";
        foreach ($results as $value) {
            $mult = self::arithmeticSum($mult, $value);
        }
        return $mult;
    }

    private static function arithmeticSum(string $num1, string $num2): string
    {
        $minLen = 0;
        $maxLen = 0;
        $longNum = "";
        $shortNum = "";
        self::findLongestNum($num1, $num2, $minLen, $maxLen, $longNum, $shortNum);
        if ($shortNum === "0" || $minLen === 0) {
            return $longNum;
        }
        $result = "";
        $carry = 0;
        for ($i = $minLen - 1, $j = $maxLen - 1; $i >= 0; $i--, $j--) {
            $sumOfTwo = intval($shortNum[$i]) + intval($longNum[$j]) + $carry;
            if ($sumOfTwo > 9) {
                $carry = intval($sumOfTwo / 10);
                $sumOfTwo %= 10;
            } else {
                $carry = 0;
            }

            $result = $sumOfTwo . $result;

            if ($i === 0 && $j > 0) {
                while ($carry > 0 && $j-- > 0) {
                    $sum = intval($longNum[$j]) + $carry;
                    $result = $sum % 10 . $result;
                    $carry = intval($sum / 10);
                }
                if ($j) {
                    $result = substr($longNum, 0, $j) . $result;
                }
            }
        }
        if ($carry) {
            $result = $carry . $result;
        }
        return $result;
    }

    private static function findLongestNum(
        string $num1,
        string $num2,
        int &$minLen,
        int &$maxLen,
        string &$longNum,
        string &$shortNum
    ) {
        $len1 = strlen($num1);
        $len2 = strlen($num2);
        if ($len1 >= $len2) {
            $maxLen = $len1;
            $minLen = $len2;
            $longNum = $num1;
            $shortNum = $num2;
        } else {
            $maxLen = $len2;
            $minLen = $len1;
            $longNum = $num2;
            $shortNum = $num1;
        }
    }

    public static function isValidDecimal(string $number): bool
    {
        return $number !== "" && preg_match('/^[+-]?\d+([\.\,]{1}\d{1,})?$/', $number);
    }
}
