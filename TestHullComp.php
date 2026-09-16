<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . "/../Entity2/CodeLength.php";
require_once __DIR__ . "/../Service2/HullCompService.php";
require_once __DIR__ . "/../Service2/PolynomialService.php";
require_once __DIR__ . "/../Service2/FactorizationService.php";

use Entity2\CodeLength;
use Entity2\BinaryPolynomial;
use Service2\HullCompServiceImpl;
use Service2\PolynomialServiceImpl;
use Service2\FactorizationServiceImpl;



function testConstructXnPlusOne(): void
{
    $service = new PolynomialServiceImpl;
    $codelength = new CodeLength(7);
    $polynomial = $service->constructXnPlusOne($codelength);

    $expectedValue = 513;
    
    if ($polynomial->getValue() === $expectedValue) {
        echo "Test Passed! Polynomial value is " . $polynomial->getValue() . "\n";
    } else {
        echo "Test Failed! Expected " . $expectedValue . ", got " . $polynomial->getValue() . "\n";
    }
}

function testAddPolynomial(): void
{
    $service = new PolynomialServiceImpl;
    $value1 = new BinaryPolynomial(3);
    $value2 = new BinaryPolynomial(2);
    $polynomialAddition = $service->addPolynomial($value1, $value2);

    $expectedValue = 1;
    
    if ($polynomialAddition->getValue() === $expectedValue) {
        echo "Test Passed! Polynomial value is " . $polynomialAddition->getValue() . "\n";
    } else {
        echo "Test Failed! Expected " . $expectedValue . ", got " . $polynomialAddition->getValue() . "\n";
    }

}

function testMultiplyPolynomial(): void
{
    $service = new PolynomialServiceImpl;
    $value1 = new BinaryPolynomial(2);
    $value2 = new BinaryPolynomial(9);
    $multiplyPolynomial = $service->multiplyPolynomial($value1, $value2);

    $expectedValue = 8;

    if ($multiplyPolynomial->getValue() === $expectedValue) {
        echo "Test Passed! Polynomial value is " . $multiplyPolynomial->getValue() . "\n";
    } else {
        echo "Test Failed! Polynomial value is " . $multiplyPolynomial->getValue() . " it should be " . $expectedValue . "\n";
    }
}

function testQuotientFromPolynomialDivision(): void
{
    $service = new PolynomialServiceImpl;
    $terbagi = new BinaryPolynomial(13);
    $pembagi = new BinaryPolynomial(5);
    $quotientPolynomial = $service->dividePolynomial($terbagi, $pembagi);

    $expectedValue = 3;

    if ($quotientPolynomial->getValue() === $expectedValue) {
        echo "Test Passed! Polynomial value is " . $quotientPolynomial->getValue() . "\n";
    } else {
        echo "Test Failed! Polynomial value is " . $quotientPolynomial->getValue() . " it should be " . $expectedValue . "\n";
    }
}

function testRemainderFromPolynomialDivision(): void
{
    $service = new PolynomialServiceImpl;
    $terbagi = new BinaryPolynomial(13);
    $pembagi = new BinaryPolynomial(5);
    $remainderPolynomial = $service->remainderPolynomial($terbagi, $pembagi);

    $expectedValue = 2;

    if ($remainderPolynomial->getValue() === $expectedValue) {
        echo "Test Passed! Polynomial bitmask " . $terbagi->getValue() .  " divided by " . $pembagi->getValue() . " has remainder with value " . $remainderPolynomial->getValue() . "\n";
    } else {
        echo "Test Failed! Polynomial value is " . $remainderPolynomial->getValue() . " it should be " . $expectedValue . "\n";
    }
}

function testIsIrreducible(): void
{
    $polynomialService = new PolynomialServiceImpl;
    $service = new FactorizationServiceImpl($polynomialService);

    $polynomial = new BinaryPolynomial(1);
    $isIrreducible = $service->isIrreducible($polynomial);

    $expectedAnswer = true;

    if ($isIrreducible === $expectedAnswer) {
        echo "TEST PASSED! Irreducibility of " . $polynomial->getValue() . " is correct ";
    } else {
        echo "Polynomial " . $polynomial->getValue() . " is not irreducible, it is reducible" . PHP_EOL;
    }
}

function testFindFactor(): void
{
    $polynomialService = new PolynomialServiceImpl;
    $service = new FactorizationServiceImpl($polynomialService);

    $polynomial = new BinaryPolynomial(6);
    $factor = $service->findFactor($polynomial);

    if ($factor !== null) {
        echo "TEST PASSED! The factor " . $factor->getValue() . " of " . $polynomial->getValue() . " is correct " . PHP_EOL;
    } else {
        echo "TEST FAILED. Expected a factor, found none";
    }
}

function testFindFactorThrowsExceptionForIrreducible(): void
{
    $polynomialService = new PolynomialServiceImpl();
    $service = new FactorizationServiceImpl($polynomialService);

    $polynomial = new BinaryPolynomial(7);

    try {
        $service->findFactor($polynomial);
        echo "TEST FAILED! Expected an exception for an irreducible polynomial, but none was thrown.";
    } catch (\RuntimeException $e) {
        echo "TEST PASSED! Correctly threw exception: " . $e->getMessage() . PHP_EOL;
    }
}

function testFactorizeBinaryPolynomial(): void
{
    $polynomialService = new PolynomialServiceImpl;
    $service = new FactorizationServiceImpl($polynomialService);

    $irreduciblePolynomial = new BinaryPolynomial(33);
    $factors1 = $service->factorize($irreduciblePolynomial);

    echo "Factors of " . $irreduciblePolynomial->getValue() . " (irreducible): ";
    foreach ($factors1 as $p) {
        echo $p->getValue() . PHP_EOL;
    }

    $reduciblePolynomial = new BinaryPolynomial(2147483649);
    $factors2 = array_map(fn($p) => $p->getValue(), $service->factorize($reduciblePolynomial));

    echo "Factors of " . $reduciblePolynomial->getValue() . " (Reducible):   [" . implode(", ", $factors2) . "]\n";
}

function testGenerateDivisor(): void
{
    $polynomialService = new PolynomialServiceImpl;
    $service = new FactorizationServiceImpl($polynomialService);

    $reduciblePolynomial = new BinaryPolynomial(33);
    $factors = $service->factorize($reduciblePolynomial);

    $divisorOfPolynomial = array_values($service->generateDivisor($factors));
    $totalDivisor = count($divisorOfPolynomial);

    for ($i = 0; $i < $totalDivisor; $i++) {
        $d = $divisorOfPolynomial[$i];
        $remainder = $polynomialService->remainderPolynomial($reduciblePolynomial, $d);
        
        if ($remainder->isZero()) {
            echo "All divisor is shown to be a real divisor: " . $d->getValue() . " is the divisor of " . $reduciblePolynomial->getValue() . "\n";
        } else {
            echo "There is something wrong";
        }
    }

}

function testCheckParityPolynomial(): void
{
    $service1 = new PolynomialServiceImpl;
    $service2 = new FactorizationServiceImpl($service1);
    $codeLength = new CodeLength(5);
    $valueXnPlus1 = $service1->constructXnPlusOne($codeLength)->getValue();
    $polynomial = new BinaryPolynomial($valueXnPlus1);

    $irreduciblePolynomial = $service2->factorize($polynomial);
    $divisors = array_values($service2->generateDivisor($irreduciblePolynomial));
    $divisor = $divisors[1];
    $checkParityPolynomial = $service1->getParityCheckPolynomial($codeLength, $divisor);
    
    $product = $service1->multiplyPolynomial($divisor, $checkParityPolynomial);
    if ($product->getValue() === $valueXnPlus1) {
        echo "TEST SUCCESS: The Product of Divisor: " . $divisor->getValue() . " and Parity Check polynomial: " . $checkParityPolynomial->getValue() . " matches x^n + 1 (Value: {$product->getValue()})\n";
    } else {
        echo "TEST FAILED: Product does not match x^n + 1\n";
    }
}

function testReciprocalPolynomial(): void
{
    $service = new PolynomialServiceImpl;
    $value = new BinaryPolynomial(5);
    $reciprocal = $service->reciprocalPolynomial($value);

    $expectedValue = 5;

    if ($reciprocal->getValue() === $value->getValue()) {
        echo "TEST PASSED! Polynomial reciprocal value " . $reciprocal->getValue() . " is same as polynomial value " . $value->getValue() . "\n";
    } elseif ($reciprocal->getValue() === $expectedValue) {
        echo "TEST PASSED! Expected " . $expectedValue . ", got " . $reciprocal->getValue() . "\n";
    } else {
        echo "TEST FAILED! Expected " . $expectedValue . ", got " . $reciprocal->getValue() . "\n";
    }

}

// testAddPolynomial();
// testConstructXnPlusOne();
// testMultiplyPolynomial();
// testQuotientFromPolynomialDivision();
// testRemainderFromPolynomialDivision();
// testIsIrreducible();
// testFindFactor();
// testFindFactorThrowsExceptionForIrreducible();
// testFactorizeBinaryPolynomial();
// testGenerateDivisor();
// testCheckParityPolynomial();
// testReciprocalPolynomial();

echo "----------------------------------------------------------------" . PHP_EOL;

// try {
//     // 1. Initialize dependencies (adjust class names to match your project)
//     $polynomialService = new PolynomialServiceImpl();
//     $factorizationService = new FactorizationServiceImpl($polynomialService);
//     $hullService = new HullCompServiceImpl($polynomialService, $factorizationService);

//     // 2. Define a code length (e.g., n = 5)
//     $codeLength = new CodeLength(5);

//     echo "Testing for code length n = " . $codeLength->getN() .  PHP_EOL;

//     // 3. Test inputNThroughGenerateDivisor
//     $divisors = $hullService->inputNThroughGenerateDivisor($codeLength);
//     echo "Generated " . count($divisors) . " divisors successfully " . PHP_EOL;

//     if (empty($divisors)) {
//         throw new \RuntimeException("No divisors found for this code length.");
//     }

//     // Pick the first available divisor to test with
//     $g = reset($divisors);
//     echo "Selected generator polynomial, g value: " . $g->getValue()  . PHP_EOL;

//     // 4. Test inputPolynomialGenThroughReciprocal
//     $components = $hullService->inputPolynomialGenThroughReciprocal($codeLength, $g);
//     echo "Reciprocal pipeline executed. h degree: " . $components['h']->degree() . ", h* value: " . $components['h_star']->getValue() . PHP_EOL;

//     // 5. Test evaluateHull
//     $hullDim = $hullService->evaluateHull($codeLength, $g);
//     echo "<strong>Calculated Hull Dimension: " . $hullDim . PHP_EOL;

// } catch (\Exception $e) {
//     echo "Error during testing: " . $e->getMessage();
// }

echo "----------------------------------------------------------" . PHP_EOL;

$polynomialService = new PolynomialServiceImpl();
$factorizationService = new FactorizationServiceImpl($polynomialService);
$hullService = new HullCompServiceImpl($polynomialService, $factorizationService);

// 2. Define code length and fetch divisors
$codeLength = new CodeLength(1);
$divisors = $hullService->inputNThroughGenerateDivisor($codeLength);

if (empty($divisors)) {
    echo "No divisors found for this code length.\n";
    exit;
}

// 3. Display available divisors for you to choose from
echo "Available Divisors:\n";
$keys = array_keys($divisors);
foreach ($keys as $index => $key) {
    $divisor = $divisors[$key];
    echo "  [{$index}] Value: {$divisor->getValue()} | Degree: {$divisor->degree()}\n";
}

// 4. Choose a divisor (Interactively via CLI, or manually change the index below)
$selectedIndex = 0; // Change this number manually, or use readline() below:

if (function_exists('readline')) {
    $input = readline("Enter the index of the divisor you want to test: ");
    if ($input !== '' && is_numeric($input)) {
        $selectedIndex = (int) $input;
    }
}

if (!isset($keys[$selectedIndex])) {
    echo "Invalid selection.\n";
    exit;
}

$chosenKey = $keys[$selectedIndex];
$g = $divisors[$chosenKey];

echo "\n--- Testing with Selected Divisor ---\n";
echo "Selected Divisor Value: " . $g->getValue() . " (Degree: " . $g->degree() . ")\n";

// 5. Run the reciprocal pipeline and hull evaluation
$components = $hullService->inputPolynomialGenThroughReciprocal($codeLength, $g);
echo "Reciprocal h degree: " . $components['h']->degree() . " | h* value: " . $components['h_star']->getValue() . "\n";

$hullDim = $hullService->evaluateHull($codeLength, $g);
echo "Calculated Hull Dimension: " . $hullDim . "\n";