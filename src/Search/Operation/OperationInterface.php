<?php

namespace AdinanCenci\FileEditor\Search\Operation;

interface OperationInterface
{
    /**
     * @param mixed $leftOperand
     *   The first operand.
     * @param mixed $rightOperand
     *   The second operand.
     * @param bool $negate
     *   Wether to negate the operation's result or not.
     */
    public function __construct(mixed $leftOperand, mixed $rightOperand, bool $negate = false);

    /**
     * Compares the operands and returns wether they match or not.
     *
     * @return bool
     *   True if they match, false otherwise.
     */
    public function matches(): bool;
}
