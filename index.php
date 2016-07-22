<?php
    // include lib for work with directories
    include_once('lib/Scanner.php');


    $scanner = new Scanner();

//    print('Show folder like array');
//    print_r($scanner->scanFolder('/Users/oleksandrpaiziak/Downloads/Eshopper', 'array'));
//    print('Show folder like json');
//    print_r($scanner->scanFolder('/Users/oleksandrpaiziak/Downloads/Eshopper', 'json'));
    print('Show current folder');
    print_r($scanner->scanFolder('', 'array'));
//    print('Incorrect folder');
//    print_r($scanner->scanFolder('/bla/bla/bla', 'array'));


?>