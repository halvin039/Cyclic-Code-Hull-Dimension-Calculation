<?php

namespace Entity2 {

    class CodeLength {

        private int $n;

        public function __construct(int $n = 0)
        {
            if ($n % 2 === 0) {
                throw new \InvalidArgumentException("Integer n cannot be even");
            }
            
            $this->n = $n;
        }

        public function getN(): int
        {
            return $this->n;
        }

        public function setN(int $n): void
        {
            $this->n = $n;
        }
    }

    class BinaryPolynomial {

        private int $value;

        public function __construct(int $value)
        {
            if ($value < 0) {
                throw new \InvalidArgumentException("Polynomial bitmask cannot be zero");
            }

            $this->value = $value;
        }

        public function getValue(): int
        {
            return $this->value;
        }

        public function degree(): int
        {
            if ($this->value === 0) {
                return -1;
            }

            return strlen(decbin($this->value)) - 1;
        }

         public function isZero(): bool
        {
            return $this->value === 0;
        }
    }
}

