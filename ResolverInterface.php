<?php



interface ResolverInterface
{
    /**
     * function to resolve algorithm
     *
     * @return void
     */
    public function execute(): void;

    /**
     * Parse algotithm and check if necessary methods are written
     *
     * @throws \Exception
     */
    public function checkMethods();
}
