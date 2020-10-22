<?php
namespace Dichotomic;

use Dichotomic\ResolverInterface;
use \RecursiveIteratorIterator;
use \RecursiveArrayIterator;

abstract class Resolver implements ResolverInterface
{
    const SEPARATOR = ':';

    /**
     * Have to be described in children
     * $algo is an array describing algorithm to resolve
     *
     * @var array $algo
     */
    protected $algo;

    public function __construct()
    {
        $this->checkMethods();
    }

    /**
     * List all methods needed to be used in $algo and check if
     * all methods are created in child class
     *
     * @return array
     */
    private function listUnavailableMethods(): array
    {

        $unavailableMethodsList = [];

        foreach( new RecursiveIteratorIterator(
                     new RecursiveArrayIterator($this->algo),
                     RecursiveIteratorIterator::SELF_FIRST)
                 as $algoKey => $value) {

            // By default, key is the name of a method
            $methodName = $algoKey;

            // Check if method needs parameters
            if (strstr($algoKey, ':')) {
                list($methodName, $parameter) = explode(self::SEPARATOR, $algoKey);
            }

            // If algo is a method and if method doesn't exist in child
            if (is_string($algoKey) && !method_exists($this, $methodName)) {
                $unavailableMethodsList[] = $methodName;
            }
        }
        return $unavailableMethodsList;
    }

    /**
     * Checks if all necessary methods are implemented in child class in terms of $algo
     * and if not ...
     *
     * @throws \Exception
     */
    public function checkMethods()
    {
        if (count($this->listUnavailableMethods()) > 0) {
            $message  = 'You have to implement all methods necessary for your algorithm resolution';
            $message .= ' Methods ' . implode('(), ', $this->listUnavailableMethods()) . '() are missing';
            throw new \Exception($message);
        }
        return true;
    }


    /**
     * this method iterates array $algo and execute methods whose name are same as array keys
     * and then reduce the array in terms of results of the method;
     * In the above example, execute colors().
     * If result is true, then execute blue();
     * if result of blue() is true, then execute light() ...
     *
     * $a = [
     *  'colors' => [
     *      true => [
     *          'blue' => [
     *              true => 'light',
     *              false => 'dark'
     *          ]
     *      false => 'black'
     *   ]
     * ];
     *
     * @throws \Exception
     */
    public function execute(): void
    {
        if (!$this->checkMethods()) {
            return;
        }

        // For each key in $algo
        foreach ($this->algo as $key => $step) {

            // If $algo[$key] is not an array
            if (!is_array($step[$this->{$key}()])) {

                // Function name is the key with sometimes some parameters
                $functionName = $this->getFunctionName($step[$this->{$key}()]);
                $param = $this->getFunctionParameters($step[$this->{$key}()]);

                // Then execute function
                $this->{$functionName}($param);

            } else {

                // We reduce $algo
                $this->algo = $step[$this->{$key}()];

                // And then redo
                $this->execute();
            }
        }
    }

    /**
     * @param string $step
     * @param string $wanted
     * @return string
     *
     * This methods separate the name of of a function and its parameter
     * We use ':' as separator
     */
    private function getMembers(string $step, string $wanted): string
    {
        list($functionName, $parameter) = explode(':', $step);
        return ${$wanted};
    }

    /**
     *
     * returns the name of the method to use by removing parameters
     *
     * @param string $function
     * @return string
     */
    private function getFunctionName(string $function): string
    {
        if (!strstr($function, ':')) {
            return $function;
        }
        return $this->getMembers($function, 'functionName');
    }

    /**
     * Returns parameter of the method by removing method name
     *
     * @param string $function
     * @return string|null
     */
    private function getFunctionParameters(string $function): ?string
    {
        if (!strstr($function, ':')) {
            return null;
        }
        return $this->getMembers($function, 'parameter');
    }

}
