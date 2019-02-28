<?php
declare(strict_types=1);

namespace PhpAi\Benchmarks\Heap;

use PhpAi\Heap\BinaryHeap;
use PhpBench\Benchmark\Metadata\Annotations\BeforeMethods;
use PhpBench\Benchmark\Metadata\Annotations\Iterations;
use PhpBench\Benchmark\Metadata\Annotations\Revs;
use PhpBench\Benchmark\Metadata\Annotations\Warmup;

/**
 * @BeforeMethods({"init"})
 */
final class BinaryHeapBench
{
    /**
     * @var int[]
     */
    private $numbers = [];

    /**
     * @var BinaryHeap
     */
    private $binaryHeap;

    /**
     * @var \SplMinHeap
     */
    private $splMinHeap;

    /**
     * @var GenericSplHeap
     */
    private $genericSplHeap;

    public function init(): void
    {
        $this->binaryHeap = new BinaryHeap(function($x){return $x;});
        $this->splMinHeap = new \SplMinHeap();
        $this->genericSplHeap = new GenericSplHeap();
        for($i=0; $i<1000; $i++) {
            $int = \random_int(-1000, 1000);
            $this->numbers[] = $int;
            $this->binaryHeap->push($int);
            $this->splMinHeap->insert($int);
            $this->genericSplHeap->insert($int);
        }
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchBinaryHeap(): void
    {
        $this->binaryHeap->push(\random_int(-1000, 1000));

        $this->binaryHeap->peek();
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchSplMinHeap(): void
    {
        $this->splMinHeap->insert(\random_int(-1000, 1000));

        $this->splMinHeap->top();
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchGenericSplHeap(): void
    {
        $this->genericSplHeap->insert(\random_int(-1000, 1000));

        $this->genericSplHeap->top();
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchNativeSort(): void
    {
        $this->numbers[] = \random_int(-1000, 1000);
        sort($this->numbers);

        $this->numbers[0];
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchNativeForeach(): void
    {
        $this->numbers[] = \random_int(-1000, 1000);
        $min = PHP_INT_MAX;
        foreach ($this->numbers as $number) {
            if($number < $min) {
                $min = $number;
            }
        }

        $min;
    }

    /**
     * @Warmup(2)
     * @Revs(1000)
     * @Iterations(5)
     */
    public function benchNativeMin(): void
    {
        $this->numbers[] = \random_int(-1000, 1000);
        min($this->numbers);
    }
}
