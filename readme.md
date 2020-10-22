#Dichotomic Algorithm resolver

If you have to implement an algorithm that is a succession of questions which responses are true or false,
you can use Algorithm resolver to help you to implement its resolution.

##setup
```composer require frvaillant/dichotomic```

##Create your algo Class
```
use Dichotomic\Resolver;

class BirdAlgo extends Resolver

{

}
```

##Make the array representing your algorithm
This is a basic example used in order to determinate some bird species answering a few questions :
Has this bird a hooked beak ? If yes is it nocturnal ? if yes has it some feather crests ? If no it is an owl.

```PHP
use Dichotomic\Resolver;

class BirdAlgo extends Resolver

{
    private $result;

    protected $algo = [
        'hasHookedBeak' => [
            true  => [
                'isNocturnal' => [
                    true => [
                        'hasFeatherCrests' => [
                            true => 'ended:hibou',
                            false => 'ended:chouette'
                        ]
                    ],
                    false => [
                        'hasPointedWings' => [
                            true => 'ended:faucon',
                            false => 'ended:aigle'
                        ]
                    ]
                ]
            ],
            false => 'ended:null'
        ]
    ];
}
```

##implement all methods you need in BirdAlgo class
```PHP
protected function hasHookedBeak(): bool
    {
        // Ce qu'il faut pour tester si l'oiseau a un bec crochu
        return true;
    }

    protected function isNocturnal(): bool
    {
        // Ce qu'il faut pour tester si l'oiseau est nocturne
        return true;
    }

    protected function hasFeatherCrests(): bool
    {
        // Ce qu'il faut pour tester si l'oiseau a des aigrettes
        return false;
    }

    protected function hasPointedWings(): bool
    {
        // Ce qu'il faut pour tester si l'oiseau a des ailes pointues
        return true;
    }

    protected function ended($result): void
    {
        // Retourne le résultat $result de l'algorithme
        $this->result = $result;
    }

    public function getResult()
    {
        return $this->result;
    }
```

##Create index.php file
```PHP 
$algo = new BirdAlgo();
$algo->execute();
echo $algo->getResult();

```
