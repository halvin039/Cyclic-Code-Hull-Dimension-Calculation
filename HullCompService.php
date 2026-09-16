<?php

namespace Service2 {

    use Entity2\CodeLength;
    use Entity2\BinaryPolynomial;

    Interface HullCompService {

        function inputNThroughGenerateDivisor(CodeLength $codeLength): array;

        function inputPolynomialGenThroughReciprocal(CodeLength $codeLength,BinaryPolynomial $g): array;

    }

    class HullCompServiceImpl implements HullCompService {

        private PolynomialService $polynomialService;
        private FactorizationService $factorizationService;

        public function __construct(PolynomialService $polynomialService, FactorizationService $factorizationService)
        {
            $this->polynomialService = $polynomialService;
            $this->factorizationService = $factorizationService;
        }

        function inputNThroughGenerateDivisor(CodeLength $codeLength): array
        {
            $xnPlus1 = $this->polynomialService->constructXnPlusOne($codeLength);
            $polynomial = new BinaryPolynomial($xnPlus1->getValue());

            $factors = $this->factorizationService->factorize($polynomial);

            $divisors = $this->factorizationService->generateDivisor($factors);

            return $divisors;
        }

        public function inputPolynomialGenThroughReciprocal(CodeLength $codeLength, BinaryPolynomial $g): array
        {
            $n = $codeLength->getN();

            $xnPlus1 = $this->polynomialService->constructXnPlusOne($codeLength);
            $h = $this->polynomialService->dividePolynomial($xnPlus1, $g);



            $hStar = $this->polynomialService->reciprocalPolynomial($h);

            return [
                    'g' => $g,
                    'h' => $h,
                    'h_star' => $hStar
                ];
        }

        public function evaluateHull(CodeLength $codeLength, BinaryPolynomial $g): int
        {
            $n = $codeLength->getN();

            // 1. Get components from your current method
            $components = $this->inputPolynomialGenThroughReciprocal($codeLength, $g);
            $hStar = $components['h_star'];

            // 2. Hull generator = LCM of g(x) and h*(x)
            $hullGenerator = $this->polynomialService->lcmPolynomial($g, $hStar);

            // 3. Hull dimension = n - degree of the LCM generator
            $hullDimension = $n - $hullGenerator->degree();

            return $hullDimension;
        }
    }
}