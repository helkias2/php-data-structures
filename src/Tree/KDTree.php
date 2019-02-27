<?php

declare(strict_types=1);

namespace PhpAi\Tree;

use PhpAi\Heap\BinaryHeap;
use PhpAi\Tree;

final class KDTree implements Tree
{
    /**
     * @var Node
     */
    private $root;

    /**
     * @var callable
     */
    private $metric;

    /**
     * @var array
     */
    private $dimensions;

    public function __construct(array $points, callable $metric, array $dimensions = null)
    {
        if ($points === []) {
            throw new \InvalidArgumentException('At least one point in space required.');
        }

        $this->dimensions = $dimensions ?? array_keys($points[0]);

        $this->root = $this->buildTree($points, 0);
        $this->metric = $metric;
    }

    public function root(): Node
    {
        return $this->root;
    }

    /**
     * @return Node[]
     */
    public function nearestNodes(array $point, int $maxNodes): array
    {
        $bestNodes = new BinaryHeap(function ($n) {
            return -$n[1];
        });
        $this->nearestSearch($point, $this->root, $bestNodes, $maxNodes);

        return array_map(function (array $node) {
            return $node[0]->point();
        }, array_slice($bestNodes->nodes(), 0, min($maxNodes, $bestNodes->size())));
    }

    private function buildTree(array $points, int $depth, ?Node $parent = null): Node
    {
        $dimension = $depth % count($this->dimensions);
        $size = count($points);

        if ($size === 1) {
            return new Node($points[0], $dimension, $parent);
        }

        usort($points, function ($a, $b) use ($dimension):int {
            return $a[$this->dimensions[$dimension]] - $b[$this->dimensions[$dimension]];
        });

        $median = (int) floor($size / 2);
        $node = new Node($points[$median], $dimension, $parent);

        if (($leftPoints = array_slice($points, 0, $median)) !== []) {
            $node->setLeft($this->buildTree($leftPoints, $depth + 1, $node));
        }
        if (($rightPoints = array_slice($points, $median + 1)) !== []) {
            $node->setRight($this->buildTree($rightPoints, $depth + 1, $node));
        }

        return $node;
    }

    private function nearestSearch(array $point, Node $node, BinaryHeap $bestNodes, int $maxNodes): void
    {
        $distance = ($this->metric)(array_intersect_key($point, $this->dimensions), array_intersect_key($node->point(), $this->dimensions));
        if ($node->left() === null && $node->right() === null) {
            if ($bestNodes->size() < $maxNodes || $distance < $bestNodes->peek()[1]) {
                $this->pushNode($node, $distance, $bestNodes, $maxNodes);
            }

            return;
        }

        if ($node->right() === null) {
            $bestChild = $node->left();
        } elseif ($node->left() === null) {
            $bestChild = $node->right();
        } else {
            if ($point[$this->dimensions[$node->dimension()]] < $node->point()[$this->dimensions[$node->dimension()]]) {
                $bestChild = $node->left();
            } else {
                $bestChild = $node->right();
            }
        }

        if ($bestChild !== null) {
            $this->nearestSearch($point, $bestChild, $bestNodes, $maxNodes);
        }

        if ($bestNodes->size() < $maxNodes || $distance < $bestNodes->peek()[1]) {
            $this->pushNode($node, $distance, $bestNodes, $maxNodes);
        }

        $linearPoint = [];
        foreach ($this->dimensions as $dimension) {
            if ($dimension === $node->dimension()) {
                $linearPoint[$dimension] = $point[$dimension];
            } else {
                $linearPoint[$dimension] = $node->point()[$dimension];
            }
        }

        $linearDistance = ($this->metric)($linearPoint, array_intersect_key($node->point(), $this->dimensions));

        if ($bestNodes->size() < $maxNodes || abs($linearDistance) < $bestNodes->peek()[1]) {
            if ($bestChild === $node->left()) {
                $otherChild = $node->right();
            } else {
                $otherChild = $node->left();
            }

            if ($otherChild !== null) {
                $this->nearestSearch($point, $otherChild, $bestNodes, $maxNodes);
            }
        }
    }

    private function pushNode(Node $node, float $distance, BinaryHeap $bestNodes, int $maxNodes): void
    {
        $bestNodes->push([$node, $distance]);
        if ($bestNodes->size() > $maxNodes) {
            $bestNodes->pop();
        }
    }
}
