<?php

require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/app/AppKernel.php';

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Input\ArrayInput;

use Doctrine\Bundle\DoctrineBundle\Command\CreateDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\DropDatabaseDoctrineCommand;
use Doctrine\Bundle\DoctrineBundle\Command\Proxy\CreateSchemaDoctrineCommand;

$kernel = new AppKernel('test', true);
$kernel->boot();

$application = new Application($kernel);

//удаляем тестовую базу, если она есть
/*$command = new DropDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command'       => 'doctrine:database:drop',
    '--force'       => true,
    '--if-exists'   => true
));
$command->run($input, new ConsoleOutput());

//создаем новую тестовую базу
$command = new CreateDatabaseDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:database:create',
));
$command->run($input, new ConsoleOutput());

//накатываем миграции на базу
$command = new CreateSchemaDoctrineCommand();
$application->add($command);
$input = new ArrayInput(array(
    'command' => 'doctrine:schema:create',
));
$command->run($input, new ConsoleOutput());*/
