<?php

require __DIR__ . '/vendor/autoload.php';

use MindMap\Command\MindMapCommand;
use MindMap\Model\MindMap;
use MindMap\Model\Node;
use Symfony\Component\Console\Application;


$application = new Application('MindMap CLI', '1.0.0');
$application->add(new MindMapCommand(new MindMap(), new Node()));
$application->run();