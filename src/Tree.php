<?php

declare(strict_types=1);

namespace PhpAi;

use PhpAi\Tree\Node;

interface Tree
{
    public function root(): Node;
}
