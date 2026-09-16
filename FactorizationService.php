<?php

namespace Service2 {

    use Entity2\BinaryPolynomial;

    interface FactorizationService {

        function isIrreducible(BinaryPolynomial $polynomial): bool;

        function findFactor(BinaryPolynomial $f): BinaryPolynomial;

        function factorize(BinaryPolynomial $f): array;

        function generateDivisor(array $irreducibleFactors): array;
    }

    class FactorizationServiceImpl implements FactorizationService {
        
        private PolynomialService $polynomialService;

        public function __construct(PolynomialService $polynomialService)
        {
            $this->polynomialService = $polynomialService;
        }
        
        public function isIrreducible(BinaryPolynomial $polynomial): bool
        {
            $polyDeg = $polynomial->degree();

            if ($polyDeg === 0) {
                return false;
            }

            if ($polyDeg === 1) {
                return true;
            }

            for ($d = 1; $d <= (int) floor($polyDeg / 2); $d++) {

                $start = 1 << $d;
                $end = 1 << ($d + 1);

                for ($bitmask = $start; $bitmask < $end; $bitmask++) {

                    $candidate = new BinaryPolynomial($bitmask);
                    $remainder = $this->polynomialService->remainderPolynomial($polynomial, $candidate);

                    if ($remainder->isZero()) {
                        return false;
                    }
                }
            }
            return true;
        }

        public function findFactor(BinaryPolynomial $f): BinaryPolynomial
        {
            $degree = $f->degree();

            for ($d = 1; $d <= (int) floor($degree / 2); $d++) {

                $start = 1 << $d;
                $end = 1 << ($d + 1);

                for ($bitmask = $start; $bitmask < $end; $bitmask++) {

                    $candidate = new BinaryPolynomial($bitmask);

                    if ($this->polynomialService->remainderPolynomial($f, $candidate)->isZero()) {
                        return $candidate;
                    }
                }
            }
            throw new \RuntimeException("No factor found");
        }

        public function factorize(BinaryPolynomial $f): array
        {
            if ($this->isIrreducible($f)) {
                return [$f];
            }

            $factor = $this->findFactor($f);
            $quotient = $this->polynomialService->dividePolynomial($f, $factor);
            
            return array_merge(
                $this->factorize($factor),
                $this->factorize($quotient)
            );
        }

        public function generateDivisor(array $irreducibleFactors): array
        {
            $n = count($irreducibleFactors);
            $totalCombination = 1 << $n;
            $divisors = [];

            for ($i = 0; $i < $totalCombination; $i++) {
                $currentProduct = new BinaryPolynomial(1);

                for ($j = 0; $j < $n; $j++) {
                    if (($i & (1 << $j)) !== 0) {
                        $currentProduct = $this->polynomialService->multiplyPolynomial(
                            $currentProduct,
                            $irreducibleFactors[$j]
                        );
                    }
                    $divisors[$currentProduct->getValue()] = $currentProduct;
                }
            }
            ksort($divisors);
            return array_values($divisors);
        }
    }
}