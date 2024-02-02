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
    
    $specialCharactersArray = [];
    while (!feof($fn)) {
        
        $line = str_split(trim(preg_replace('/\s\s+/', ' ', fgets($fn))));
        $currentLine = [];
        
        $specialCharactersForLine = [];
        $numberFound = false;
        
        $streak = $baseStreak;
        foreach ($line as $index => $character) {
            preg_match('/\d/', $character, $isInt);
            
            if (count($isInt) === 0) {
                if ($character !== '.') {
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
        $specialCharactersArray[] = $specialCharactersForLine;
    }
    
    return [
        $mappedLines,
        $specialCharactersArray
    ];
}

function checkSymbolWithinRange(array $range, array $comparison): bool
{
    return count(array_intersect($range, $comparison)) > 0;
}

[$mappedLines, $mappedCharacters] = loadData();

$count = [];

foreach ($mappedLines as $lineIndex => $line) {
    $previousLine = $mappedCharacters[$lineIndex - 1] ?? false;
    $currentLine = $mappedCharacters[$lineIndex];
    $nextLine = $mappedCharacters[$lineIndex + 1] ?? false;
    
    foreach ($line as $numberArray) {
        $numberIndices = $numberArray['indices'];
        $numberToCount = $numberArray['number'];
        $rangeToCompare = [$numberIndices[0] - 1, ...$numberIndices, end($numberIndices) + 1];
        
        if ($previousLine) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $previousLine);
            if ($symbolFoundInRange) {
                $count[] = $numberToCount;
                continue;
            }
        }
        
        if ($currentLine) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $currentLine);
            if ($symbolFoundInRange) {
                $count[] = $numberToCount;
                continue;
            }
        }
        
        if ($nextLine) {
            $symbolFoundInRange = checkSymbolWithinRange($rangeToCompare, $nextLine);
            
            if ($symbolFoundInRange) {
                $count[] = $numberToCount;
                continue;
            }
        }
        
        echo 'Number has not been found ' . $numberToCount . ' on line ' . $lineIndex . PHP_EOL;
    }
}

echo array_sum($count);