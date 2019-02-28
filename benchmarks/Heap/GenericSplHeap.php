<?php
declare(strict_types=1);

namespace PhpAi\Benchmarks\Heap;


final class GenericSplHeap extends \SplHeap
{
    protected function compare($value1, $value2)
    {
        if($value1 === $value2) {
            return 0;
        }

        return $value1 > $value2 ? 1 : -1;
    }
}
