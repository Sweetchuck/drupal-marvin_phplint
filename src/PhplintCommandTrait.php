<?php

declare(strict_types=1);

namespace Drupal\marvin_phplint;

use Robo\Contract\TaskInterface;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\PhpLint\PhpLintTaskLoader;

trait PhplintCommandTrait {

  use GitTaskLoader;
  use PhpLintTaskLoader;

  /**
   * @param ?\Robo\Symfony\ConsoleIO $io
   *
   * @return \Robo\Collection\CollectionBuilder
   */
  abstract protected function collectionBuilder($io = NULL);

  /**
   * @param string $workingDirectory
   * @param array<string> $fileNamePatterns
   * @param null|string $gitHookName
   *
   * @return \Robo\Contract\TaskInterface|\Robo\Collection\CollectionBuilder
   */
  protected function getTaskLintPhplintExtension(
    string $workingDirectory,
    array $fileNamePatterns,
    array $phpCommand,
    ?string $gitHookName = NULL,
  ): TaskInterface {
    $options = [];
    $options['workingDirectory'] = $workingDirectory;
    $options['phpExecutable'] = $phpCommand['executable'] ?? 'php';

    if ($gitHookName === 'pre-commit') {
      return $this
        ->collectionBuilder()
        ->addTask($this
          ->taskGitListStagedFiles()
          ->setDiffFilter(['d' => FALSE])
          ->setWorkingDirectory($workingDirectory)
          ->setPaths($fileNamePatterns))
        ->addTask($this
          ->taskGitReadStagedFiles()
          ->setWorkingDirectory($workingDirectory)
          ->setCommandOnly(TRUE)
          ->deferTaskConfiguration('setPaths', 'fileNames'))
        ->addTask($this
          ->taskPhpLintInput($options)
          ->deferTaskConfiguration('setFiles', 'files'));
    }

    return $this
      ->taskPhpLintFiles($options)
      ->setFileNamePatterns($fileNamePatterns);
  }

}
