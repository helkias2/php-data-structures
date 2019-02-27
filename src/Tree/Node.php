<?php

declare(strict_types=1);

namespace PhpAi\Tree;

final class Node
{
    /**
     * @var array
     */
    private $point;

    /**
     * @var int
     */
    private $dimension;

    /**
     * @var Node|null
     */
    private $parent;

    /**
     * @var Node|null
     */
    private $left;

    /**
     * @var Node|null
     */
    private $right;

    public function __construct(array $point, int $dimension, ?Node $parent = null)
    {
        $this->point = $point;
        $this->dimension = $dimension;
        $this->parent = $parent;
    }

    public function setLeft(Node $node): void
    {
        $this->left = $node;
    }

    public function setRight(Node $node): void
    {
        $this->right = $node;
    }

    public function point(): array
    {
        return $this->point;
    }

    public function dimension(): int
    {
        return $this->dimension;
    }

    public function parent(): ?Node
    {
        return $this->parent;
    }

    public function left(): ?Node
    {
        return $this->left;
    }

    public function right(): ?Node
    {
        return $this->right;
    }
}
