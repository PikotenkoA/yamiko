<?php


namespace App\Form;



use Symfony\Component\Form\CallbackTransformer;

class MoneyTransformer extends CallbackTransformer
{
    public function __construct()
    {
        parent::__construct(
            function ($valueInCent) {
                return sprintf('%0.2f', $valueInCent/100);

            },
            function ($value) {
                return is_numeric($value) ? round($value * 100) : $value;
            }
        );
    }
}