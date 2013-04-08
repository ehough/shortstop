<?php

__throwback::$config = array(

    'name'         => 'ehough_shortstop',
    'autoload'     => dirname(__FILE__) . '/../../main/php',
    'dependencies' => array(

        array('ehough/chaingang', 'git://github.com/ehough/chaingang.git', 'src/main/php'),
        array('ehough/curly', 'git://github.com/ehough/curly.git', 'src/main/php'),
        array('ehough/epilog', 'git://github.com/ehough/epilog.git', 'src/main/php'),
        array('ehough/tickertape', 'git://github.com/ehough/tickertape.git', 'src/main/php'),
        array('ehough/mockery', 'git://github.com/ehough/mockery.git', 'src/main/php'),
    )
);
