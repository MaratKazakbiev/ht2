<?php

namespace Maratkazakbiev\HtOtus1;

use InvalidArgumentException;
class MathBracketsResolver
{
    private $string;
    public function __construct($string)
    {
        $this->string = $string;
    }

    /**
     * @return string
     */
    public function getString()
    {
        return $this->string;
    }

    public function Resolve() :bool
    {
        $cleanString = $this->getString();
        $iterator = 0;
        $openBrackets = 0;
        $closedBrackets  = 0;
        $allowed = [" ", "\n", "\t", "\r"];

        try {
            if (str_starts_with($cleanString, ')')){
                throw new InvalidArgumentException('неверный символ в начале примера');
            }

            if (str_ends_with($cleanString, '(')){
                throw new InvalidArgumentException('неверный символ в конце примера');
            }


            while ($iterator < strlen($cleanString) ){
                switch ($cleanString[$iterator]){
                    case '(':
                        ++$openBrackets;
                        break;
                    case ')':
                        ++$closedBrackets;
                        break;
                    default:
                    if (!in_array($cleanString[$iterator] , $allowed)){
                        throw new InvalidArgumentException('неверный символ в строке');
                    }
                }
                ++$iterator;
            }
        }catch (ErrorException $exception){
            echo "Ошибка " . $exception->getMessage();
            return false;
        }

        if ($openBrackets == $closedBrackets) {
            echo 'Пример правильный';
            return true;
        }  else{
            echo 'Пример неправильный';
            return false;
        }
    }
}