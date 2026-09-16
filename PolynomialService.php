<?php

namespace Service2 {

    use Entity2\CodeLength;
    use Entity2\BinaryPolynomial;

    interface PolynomialService {

        function constructXnPlusOne(CodeLength $n): BinaryPolynomial;

        function addPolynomial(BinaryPolynomial $a, BinaryPolynomial $b): BinaryPolynomial;

        function multiplyPolynomial(BinaryPolynomial $a, BinaryPolynomial $b): BinaryPolynomial;

        function dividePolynomial(BinaryPolynomial $terbagi, BinaryPolynomial $pembagi): BinaryPolynomial;

        function remainderPolynomial(BinaryPolynomial $terbagi, BinaryPolynomial $pembagi): BinaryPolynomial;

        function getParityCheckPolynomial(CodeLength $n, BinaryPolynomial $g): BinaryPolynomial;

        function reciprocalPolynomial(BinaryPolynomial $h): BinaryPolynomial;

        function gcdPolynomial(BinaryPolynomial $f, BinaryPolynomial $g): BinaryPolynomial;

        function lcmPolynomial(BinaryPolynomial $f, BinaryPolynomial $g): BinaryPolynomial;

    }

    class PolynomialServiceImpl implements PolynomialService {

        function constructXnPlusOne(CodeLength $n): BinaryPolynomial
        {
            $length = $n->getN();

            if ($length <= 0) {
                throw new \InvalidArgumentException("Integer n cannot be zero");
            }

            $polynomialXnPlusOne = (1 << $length) | 1;

            return new BinaryPolynomial($polynomialXnPlusOne);
        }

        public function addPolynomial(BinaryPolynomial $a, BinaryPolynomial $b): BinaryPolynomial
        {
            $value1 = $a->getValue();
            $value2 = $b->getValue();

            $polynomialAddition = $value1 ^ $value2;

            return new BinaryPolynomial($polynomialAddition);
        }

        public function multiplyPolynomial(BinaryPolynomial $a, BinaryPolynomial $b): BinaryPolynomial
        {
            $value1 = $a->getValue();
            $value2 = $b->getValue();
            $result = 0;

            while($value2 > 0) {
                if ($value2 & 1) {
                    $result ^= $value1;
                }
                $value1 <<= 1;
                $value2 >>= 1;
            }

            return new BinaryPolynomial($result);
        }

        public function dividePolynomial(BinaryPolynomial $terbagi, BinaryPolynomial $pembagi): BinaryPolynomial
        {
            $sisa = $terbagi->getValue();
            $div = $pembagi->getValue();

            if ($div === 0) {
                throw new \InvalidArgumentException("Division by zero polynomial is undefined");
            }

            $degPembagi = $pembagi->degree();
            $hasilBagi = 0;

            while ($sisa !== 0) {
                $degSisa = (new BinaryPolynomial($sisa))->degree();

                if ($degSisa < $degPembagi) {
                    break;
                }

                $shift = $degSisa - $degPembagi;
                $hasilBagi ^= (1 << $shift);
                $sisa ^= ($div << $shift); 
            }

            return new BinaryPolynomial($hasilBagi);
        }

        public function remainderPolynomial(BinaryPolynomial $terbagi, BinaryPolynomial $pembagi): BinaryPolynomial
        {
            $sisa = $terbagi->getValue();
            $div = $pembagi->getValue();

            if ($div === 0) {
                throw new \InvalidArgumentException("Division by zero polynomial is undefined");
            }

            $degPembagi = $pembagi->degree();
            $hasilBagi = 0;

            while ($sisa !== 0) {
                $degSisa = (new BinaryPolynomial($sisa))->degree();

                if ($degSisa < $degPembagi) {
                    break;
                }

                $shift = $degSisa - $degPembagi;
                $hasilBagi ^= (1 << $shift);
                $sisa ^= ($div << $shift); 
            }

            return new BinaryPolynomial($sisa);
        }

        public function getParityCheckPolynomial(CodeLength $n, BinaryPolynomial $g): BinaryPolynomial
        {
            $xnPlus1 = $this->constructXnPlusOne($n);

            return $this->dividePolynomial($xnPlus1, $g);
        }

        public function reciprocalPolynomial(BinaryPolynomial $h): BinaryPolynomial
        {
            $c = $h->getValue();
            $t = 0;

            while ($c > 0) {
                $t = ($t << 1) | ($c & 1);
                $c >>= 1;
            }
            return new BinaryPolynomial($t);
        }

        public function gcdPolynomial(BinaryPolynomial $f, BinaryPolynomial $g): BinaryPolynomial
        {
            while (!$g->isZero()) {
                $remainder = $this->remainderPolynomial($f, $g);

                $f = $g;
                $g = $remainder;
            }
            return $f;
        }

        public function lcmPolynomial(BinaryPolynomial $f, BinaryPolynomial $g): BinaryPolynomial
        {
            if ($f->isZero() || $g->isZero()) {
                return new BinaryPolynomial(0);
            }
            
            $gcd = $this->gcdPolynomial($f, $g);
            $product = $this->multiplyPolynomial($f, $g);

            return $this->dividePolynomial($product, $gcd);
        }
    
    }
}