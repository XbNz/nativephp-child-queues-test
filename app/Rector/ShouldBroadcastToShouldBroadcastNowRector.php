<?php

namespace App\Rector;

use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class ShouldBroadcastToShouldBroadcastNowRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('', [
            new CodeSample(
            ),
        ]);
    }

    public function getNodeTypes(): array
    {
        return [Class_::class];
    }

    public function refactor(Node $node): ?Node
    {
        if (!$node instanceof Class_) {
            return null;
        }

        foreach ($node->implements as $key => $implement) {
            if ($this->isName($implement, ShouldBroadcast::class)) {
                $node->implements[$key] = new \PhpParser\Node\Name\FullyQualified(ShouldBroadcastNow::class);
            }
        }

        return $node;
    }
}
