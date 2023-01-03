<?php

declare(strict_types = 1);

namespace Drupal\Tests\marvin_phplint\Unit\Commands;

use Drush\Commands\marvin_phplint\PhplintCommandsBase;
use Robo\Config\Config;

/**
 * @group marvin
 * @group marvin_phplint
 * @group drush-command
 *
 * @covers \Drush\Commands\marvin_phplint\PhplintCommandsBase
 */
class PhplintCommandsBaseTest extends CommandsTestBase {

  public function testGetClassKey(): void {
    $commands = new PhplintCommandsBase($this->composerInfo);

    $methodName = 'getClassKey';
    $class = new \ReflectionClass($commands);
    $method = $class->getMethod($methodName);
    $method->setAccessible(TRUE);

    static::assertSame('marvin.phplint.a', $method->invokeArgs($commands, ['a']));
  }

  public function testGetConfigValue(): void {
    $configData = [
      'marvin' => [
        'phplint' => [
          'my_key' => 'my_value',
        ],
      ],
    ];

    $configData = array_replace_recursive(
      $this->getDefaultConfigData(),
      $configData
    );
    $config = new Config($configData);

    $commands = new PhplintCommandsBase($this->composerInfo);
    $commands->setConfig($config);

    $methodName = 'getConfigValue';
    $class = new \ReflectionClass($commands);
    $method = $class->getMethod($methodName);
    $method->setAccessible(TRUE);

    static::assertSame('my_value', $method->invokeArgs($commands, ['my_key']));
  }

  public function testGetCustomEventNamePrefix(): void {
    $commands = new PhplintCommandsBase($this->composerInfo);
    $methodName = 'getCustomEventNamePrefix';
    $class = new \ReflectionClass($commands);
    $method = $class->getMethod($methodName);
    $method->setAccessible(TRUE);

    static::assertSame('marvin:phplint', $method->invokeArgs($commands, []));
  }

}
