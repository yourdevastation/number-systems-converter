<?php

namespace NumberConverter;

require_once "systems/Binary.php";
require_once "systems/Octal.php";
require_once "systems/Decimal.php";
require_once "systems/Hexadecimal.php";
require_once "systems/Utils.php";

use NumberSystem\BinarySystem;
use NumberSystem\OctalSystem;
use NumberSystem\DecimalSystem;
use NumberSystem\HexadecimalSystem;
use NumberSystem\Utils;

class NumberConverter
{
    private $validOptions = ["2", "8", "10", "16", "0"];

    public function menu()
    {
        $initNumSys = 10;
        $dstNumSys = 2;
        Utils::setPrecision(6);
        echo "\033[32mWelcome to the Number System Converter!\033[0m\n";
        echo "\033[32mYou can convert numbers between binary, octal, decimal, and hexadecimal systems.\033[0m\n";
        echo "\033[32mDefault initial number system is decimal (10).\033[0m\n";
        echo "\033[32mDefault target number system is binary (2).\033[0m\n";
        echo "\033[32mDefault precision for float numbers is 6.\033[0m\n";
        echo "\033[32mYou can change these settings in the menu below.\033[0m\n";
        echo "\033[32mLet's start!\033[0m\n";
        echo "\n";
        $option = -1;
        do {
            echo "\033[36mList of available options:\033[0m\n";
            echo "1) Choose initial number system\n";
            echo "2) Choose target number system\n";
            echo "3) Change precision for float numbers\n";
            echo "4) Start conversion\n";
            echo "0) Exit\n";
            $option = trim(readline("Option: "));
            if (!in_array($option, ["1", "2", "3", "4", "0"], true)) {
                echo "\033[31mWrong option!\033[0m\n";
                echo "\033[33mEnter 1, 2, 3, 4 or 0\033[0m\n";
            } else {
                switch ($option) {
                    case "1":
                        $initNumSys = $this->chooseInitialNumberSystem();
                        break;
                    case "2":
                        $dstNumSys = $this->chooseDestinationNumberSystem();
                        break;
                    case "3":
                        $this->changePrecision();
                        break;
                    case "4":
                        $result = $this->convert($this->readNum($initNumSys), $initNumSys, $dstNumSys);
                        echo "Result: " . $result . "\n";
                        break;
                    case "0":
                        echo "\033[34mExiting...\033[0m\n";
                        goto label;
                        break;
                }
            }
            label:
            exit;
        } while ($option !== 0);
    }

    public function run()
    {
        $initNumSys = $this->chooseInitialNumberSystem();
        if ($initNumSys == 0) {
            echo "\033[34mExiting...\033[0m\n";
            return;
        }

        $dstNumSys = $this->chooseDestinationNumberSystem();
        if ($dstNumSys == 0) {
            echo "\033[34mExiting...\033[0m\n";
            return;
        }

        $inputNum = $this->readNum($initNumSys);
        if ($inputNum == 'X') {
            echo "\033[34mExiting...\033[0m\n";
            return;
        }
        $result = $this->convert($inputNum, $initNumSys, $dstNumSys);

        echo "Result: " . $result . "\n";
    }

    private function chooseInitialNumberSystem(): int
    {
        do {
            echo "\033[36mChoose the initial number system (2, 8, 10, or 16)\033[0m\n";
            echo "2 - binary\n";
            echo "8 - octal\n";
            echo "10 - decimal\n";
            echo "16 - hexadecimal\n";
            echo "0 - exit\n";

            $initNumSys = trim(readline("Number system: "));

            if (!in_array($initNumSys, $this->validOptions, true)) {
                echo "\033[31mWrong option!\033[0m\n";
                echo "\033[33mEnter 2, 8, 10 or 16\033[0m\n";
            }
        } while (!in_array($initNumSys, $this->validOptions, true));
        return (int)$initNumSys;
    }

    private function chooseDestinationNumberSystem(): int
    {
        do {
            echo "\033[36mChoose the destination number system (2, 8, 10, or 16)\033[0m\n";
            echo "2 - binary\n";
            echo "8 - octal\n";
            echo "10 - decimal\n";
            echo "16 - hexadecimal\n";
            echo "0 - exit\n";

            $dstNumSys = trim(readline("Number system: "));

            if (!in_array($dstNumSys, $this->validOptions, true)) {
                echo "\033[31mWrong option!\033[0m\n";
                echo "\033[33mEnter 2, 8, 10 or 16\033[0m\n";
            }
        } while (!in_array($dstNumSys, $this->validOptions, true));
        return (int)$dstNumSys;
    }

    private function readNum(int $initSys): string
    {
        $num = "";
        do {
            echo "\033[34m(You can type 'X' to exit)\033[0m\n";
            $num = trim(readline("\033[36mEnter your number in $initSys system: \033[0m"));
            if ($num == 'X') {
                break;
            }
        } while (!$this->isValidNumber($num, $initSys));
        return $num;
    }

    private function isValidNumber(string $num, int $initSys): bool
    {
        switch ($initSys) {
            case 2:
                return BinarySystem::isValidBinary($num);
            case 8:
                return OctalSystem::isValidOctal($num);
            case 10:
                return DecimalSystem::isValidDecimal($num);
            case 16:
                return HexadecimalSystem::isValidHexadecimal($num);
            default:
                echo "\033[31mUnknown system!\033[0m\n";
                return false;
        }
    }

    public function convert(string $number, int $initSys, int $dstSys): string
    {
        if ($initSys == $dstSys) {
            echo "\033[33mDon't need to covert!\033[0m\n";
            echo "\033[33mNumber is already in the target system\033[0m\n";
            echo "\033[33mExiting...\033[0m\n";
        }
        switch ($dstSys) {
            case 2:
                return BinarySystem::convert($number, $initSys);
            case 8:
                return OctalSystem::convert($number, $initSys);
            case 10:
                return DecimalSystem::convert($number, $initSys);
            case 16:
                return HexadecimalSystem::convert($number, $initSys);
            default:
                echo "\033[31mUnknown system!\033[0m\n";
                return "N/A";
        }
    }

    private function changePrecision(): void
    {
        $precision = -1;
        do {
            $precision = (int)trim(readline("\033[36mEnter new precision (1-10): \033[0m"));
            if ($precision < 1 || $precision > 10) {
                echo "\033[31mWrong precision!\033[0m\n";
                echo "\033[33mEnter a number between 1 and 10\033[0m\n";
            } else {
                Utils::setPrecision($precision);
                echo "\033[34mPrecision changed to $precision\033[0m\n";
            }
        } while ($precision < 1 || $precision > 10);
    }
}
