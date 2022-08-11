<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class HumanNameFormatterHelper
{
    public $TITLE = '';
    public $FIRSTNAME = '';
    public $MIDDLEINITIAL = '';
    public $LASTNAME = '';
    public $SUFFIX = '';

    private static $TITLES = [
        'Mr',
        'Ms',
        'Mrs',
        'Messrs',
        'Mmes',
        'Msgr',
        'Prof',
        'Rev',
        'Hon',
        'Esq',
        'St',
        'Dr',
        'PhD',
        'Sis',
        'Br',
        'Chan',
        'Chapin',
        'Fr',
        'Gov',
        'Miss',
        'Mme',
        'M',
        'Pres',
        'Rabbi',
        'Rep',
        'Revs',
        'Sen',
        'Sra',
        'Srta',
    ];

    private static $SUFFIXES = [
        'Jr',
        'Sr',
        'I',
        'II',
        'III',
        'IV',
        'Esq',
        'V',
        'Ret',
        'USA',
        'USAF',
        'USAMS',
        'USN'
    ];

    private static $MIDDLEINITIALS = [
        'A.',
        'B.',
        'C.',
        'D.',
        'E.',
        'F.',
        'G.',
        'H.',
        'I.',
        'J.',
        'K.',
        'L.',
        'M.',
        'N.',
        'O.',
        'P.',
        'Q.',
        'R.',
        'S.',
        'T.',
        'U.',
        'V.',
        'X.',
        'Y.',
        'Z.'
    ];

    private function setTitle($title)
    {
        // Remove dot
        $tempTitle = str_replace(".", "", $title);
        // Check the title exist in list of TITLES
        if(in_array(Str::title($tempTitle), self::$TITLES)){
            $this->TITLE = Str::title($title);
            return;
        }

        // If the title doesn't exist in the list of title
        // automatically it will assign it to the FIRSTNAME
        $this->FIRSTNAME = Str::title($title);
        return;
    }

    private function setFirstname($firstname)
    {
        // Check the variable FIRSTNAME has value
        if(empty($this->FIRSTNAME))
        {
            $this->FIRSTNAME = Str::title($firstname);
            return;
        }

        if($this->FIRSTNAME === $firstname)
        {
            return;
        }

        $this->FIRSTNAME .= " ". Str::title($firstname);
        return;
    }

    private function setMiddleInitial($middleInitial)
    {
        if(in_array(Str::title($middleInitial), self::$MIDDLEINITIALS)){
            $this->MIDDLEINITIAL = Str::title($middleInitial);
            return;
        }

        if(strlen($middleInitial) <= 2)
        {
            $this->MIDDLEINITIAL = Str::title($middleInitial);
            return;
        }

        $this->setFirstname($middleInitial);
        return;
    }

    private function setLastname($lastname)
    {
        if($this->LASTNAME === Str::title($lastname))
        {
            return;
        }

        $this->LASTNAME = Str::title($lastname);
        return;
    }

    private function setSuffix($suffix)
    {
        $tempSuffix = str_replace(".", "", $suffix);

        if(in_array(Str::title($tempSuffix), self::$SUFFIXES)){
            $this->SUFFIX = Str::title($suffix);
            return;
        }

        $this->setLastname($suffix);
        return;
    }

    private function lengthIsTwo($name)
    {
        $this->setTitle($name[0]);
        $this->setSuffix($name[count($name)-1]);
        return;
    }

    private function lengthIsThree($name)
    {
        $this->setTitle($name[0]);
        $this->setSuffix($name[count($name) -1]);
        if(empty($this->SUFFIX)){
            $this->setMiddleInitial($name[count($name)-2]);
        }else{
            $this->setSuffix($name[count($name)-2]);
        }
        return;
    }

    private function lengthIsFour($name)
    {
        $this->setTitle($name[0]);
        $this->setFirstname($name[count($name)-3]);

        if(empty($this->SUFFIX))
        {
            $this->setSuffix($name[count($name)-1]);
        }else{
            $this->setLastname($name[count($name)-1]);
        }

        if(empty($this->SUFFIX)){
            $this->setMiddleInitial($name[count($name)-2]);
        }else{
            $this->setSuffix($name[count($name)-2]);
        }

        return;
    }

    private function lengthIsFive($name)
    {
        $this->setTitle($name[0]);
        $this->setFirstname($name[count($name)-4]);

        if(empty($this->SUFFIX))
        {
            $this->setSuffix($name[count($name)-1]);
        }else{
            $this->setLastname($name[count($name)-1]);
        }

        if(empty($this->MIDDLEINITIAL)){
            $this->setMiddleInitial($name[count($name)-3]);
        }else{
            $this->setSuffix($name[count($name)-3]);
        }

        if(empty($this->SUFFIX)){
            $this->setMiddleInitial($name[count($name)-2]);
        }else{
            $this->setSuffix($name[count($name)-2]);
        }

    }

    public function parse(string $name)
    {
        // Separate the string name by spaces
        $name = explode(" ", $name);
        if(count($name) == 2){
            $this->lengthIsTwo($name);
        }elseif(count($name) == 3){
            $this->lengthIsThree($name);
        }elseif(count($name) == 4){
            $this->lengthIsFour($name);
        }elseif(count($name) == 5){
            $this->lengthIsFive($name);
        }

        return $this;
    }

    public function getFullName()
    {
        return $this->FIRSTNAME ." ". $this->LASTNAME;
    }

    public function getFullName2()
    {
        return $this->LASTNAME .", ".$this->FIRSTNAME;
    }

}
