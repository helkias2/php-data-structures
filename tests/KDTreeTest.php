<?php

declare(strict_types=1);

namespace PhpAi\Tests;

use PhpAi\Tree\KDTree;
use PHPUnit\Framework\TestCase;

final class KDTreeTest extends TestCase
{
    public function testNotAllowForEmptyTree(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new KDTree([], function () {
        });
    }

    public function testSearchNearest(): void
    {
        $points = [
            [1, 2],
            [2, 2],
            [2, 3],
            [3, 4],
            [5, 6],
            [7, 8],
            [9, 10],
            [11, 12]
        ];

        $tree = new KDTree($points, function ($a, $b) {
            return (($a[0] - $b[0]) ** 2) + (($a[1] - $b[1]) ** 2);
        });

        self::assertEquals([
            [3, 4],
            [5, 6]
        ], $tree->nearestNodes([5, 5], 2));
    }

    public function testSearchNearestWithCustomDimensions(): void
    {
        $points = [
            [1, 2, 'cat'],
            [3, 4, 'dog'],
            [5, 6, 'dog'],
            [7, 8, 'cat'],
        ];

        $tree = new KDTree($points, function ($a, $b) {
            $sum = 0;
            for ($i = 0; $i < count($a); ++$i) {
                $sum += ($a[$i] - $b[$i]) ** 2;
            }

            return $sum;
        }, [0, 1]);

        self::assertEquals([
            [3, 4, 'dog'],
            [5, 6, 'dog']
        ], $tree->nearestNodes([5, 5], 2));
    }
}
