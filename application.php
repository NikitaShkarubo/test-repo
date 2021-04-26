#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Console\Application;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

// Set up DI container
$containerBuilder = new ContainerBuilder();
$loader = new YamlFileLoader($containerBuilder, new FileLocator(__DIR__ . '/config'));
$loader->load('services.yaml');

$application = new Application('commission-fee-calculator', '1.0.0');
$command = $containerBuilder->get('calculate_commission_fee_command');
$application->add($command);
$application->setDefaultCommand($command->getName(), true);

$application->run();