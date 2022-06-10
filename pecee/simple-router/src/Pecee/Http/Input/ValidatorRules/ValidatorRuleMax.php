<?php

namespace Pecee\Http\Input\ValidatorRules;

use Pecee\Http\Input\IInputItem;
use Pecee\Http\Input\InputFile;
use Pecee\Http\Input\InputValidatorRule;

class ValidatorRuleMax extends InputValidatorRule
{

    protected $tag = 'max';
    protected $requires = array('string', 'file', 'array', 'numeric');

    /**
     * @return float|int
     */
    private function getMax()
    {
        if (sizeof($this->getAttributes()) > 0) {
            return is_int($this->getAttributes()[0]) ? intval($this->getAttributes()[0]) : floatval($this->getAttributes()[0]);
        }
        return 0;
    }

    /**
     * @param IInputItem $input
     * @return float|int|null
     */
    private function getNumber(IInputItem $input)
    {
        if (is_a($input, InputFile::class))
            return intval($input->getSize()) / 1024; // Size in Kb
        $input_value = $input->getValue();
        if (is_array($input_value))
            return count($input_value);
        if (is_string($input_value))
            return strlen($input_value);
        if (is_numeric($input_value))
            return is_int($input_value) ? $input_value : floatval($input_value);
        return null;
    }

    public function validate(IInputItem $inputItem): bool
    {
        return $this->getNumber($inputItem) <= $this->getMax();
    }

    public function getErrorMessage(): string
    {
        return 'The Input %s is too big';
    }

}