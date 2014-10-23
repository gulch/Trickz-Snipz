<?php

class Singleton
{
    // object instance
    private static $instance;

    private function __construct()
    {

    }

    private function __clone()
    {

    }

    public static function getInstance()
    {
        if (self::$instance === null)
        {
            self::$instance = new self;
        }

        return self::$instance;
    }

    public function doAction()
    {
        echo 'It is work!';
    }
}


$singleton = Singleton::getInstance();
$singleton->doAction();

// або так
Singleton::getInstance()->doAction();