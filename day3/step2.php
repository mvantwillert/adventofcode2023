<?php

declare(strict_types=1);




function loadData(): array
{
    $mappedLines = [];
    
    $fn = fopen("input.txt",'r');
    $baseStreak = [
        'number' => '',
        'indices' => []
    ];
    
    $specialCharacters = [];
    while (!feof($fn)) {
        $line = str_split(trim(preg_replace('/\s\s+/', ' ', fgets($fn))));
        $currentLine = [];
        $specialCharactersForLine = [];
        $numberFound = false;
        
        $streak = $baseStreak;
        foreach ($line as $index => $character) {
            preg_match('/\d/', $character, $isInt);
            
            if (count($isInt) === 0) {
                if ($character === '*') {
                    $specialCharactersForLine[] = $index;
                }
                
                if($numberFound){
                    $currentLine[] = $streak;
                    $streak = $baseStreak;
                }
                
                $numberFound = false;
                
                continue;
            }
            
            $numberFound = true;
            
            $streak['number'] .= $character;
            $streak['indices'][] = $index;
            
            
            if($index === count($line) - 1){
                $currentLine[] = $streak;
            }
        }
        
        $mappedLines[] = $currentLine;
        $specialCharacters[] = $specialCharactersForLine;
    }
    
    return [
        $mappedLines,
        $specialCharacters
    ];
}

function checkSymbolWithinRange(array $range, array $comparison): bool
{
    return count(array_intersect($range, $comparison)) > 0;
}

[$mappedLines, $gearLines] = loadData();

$count = [];

foreach ($gearLines as $index => $gears) {
    $previousLine = $mappedLines[$index - 1] ?? [];
    $currentLine = $mappedLines[$index];
    $nextLine = $mappedLines[$index + 1] ?? [];
    
    foreach ($gears as $gearIndex) {
        $number1 = false;
        $number2 = false;
        
        $rangeToCompare = [$gearIndex - 1, $gearIndex, $gearIndex + 1];
        foreach ($previousLine as $number) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $number['indices']);
            if ($symbolFoundInRange) {
                if($number1 === false) {
                    $number1 = (int) $number['number'];
                    continue;
                }
                
                $number2 = (int) $number['number'];
                break;
            }
        }
        
        foreach ($currentLine as $number) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $number['indices']);
            if ($symbolFoundInRange) {
                if($number1 === false) {
                    $number1 = (int) $number['number'];
                    continue;
                }
                
                $number2 = (int) $number['number'];
                break;
            }
        }
        
        foreach ($nextLine as $number) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $number['indices']);
            if ($symbolFoundInRange) {
                if($number1 === false) {
                    $number1 = (int) $number['number'];
                    continue;
                }
                
                $number2 = (int) $number['number'];
                break;
            }
        }
        
        if($number1 !== false && $number2 !== false) {
            $count[] = $number1 * $number2;
            
            echo 'counting ' . $number1 . ' with ' . $number2 . ' for gear on line ' . $index . PHP_EOL;
        }
    }
}

echo array_sum($count);