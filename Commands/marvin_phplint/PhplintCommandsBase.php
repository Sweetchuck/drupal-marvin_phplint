<?php

declare(strict_types = 1);

namespace Drush\Commands\marvin_phplint;

use Drupal\marvin\Utils as MarvinUtils;
use Drush\Commands\marvin\LintCommandsBase;
use Robo\Contract\TaskInterface;
use Sweetchuck\Robo\Git\GitTaskLoader;
use Sweetchuck\Robo\PhpLint\PhpLintTaskLoader;

class PhplintCommandsBase extends LintCommandsBase {

  use GitTaskLoader;
  use PhpLintTaskLoader;

  protected static string $classKeyPrefix = 'marvin.phplint';

  protected string $customEventNamePrefix = 'marvin:phplint';

  /**
   * @return \Robo\Contract\TaskInterface|\Robo\Collection\CollectionBuilder
   */
  protected function getTaskLintPhplintExtension(string $workingDirectory): TaskInterface {
    $config = $this->getConfig();

    $gitHook = $config->get('marvin.gitHook');
    $options = [];
    $options['workingDirectory'] = $workingDirectory;

    if ($gitHook === 'pre-commit') {
      return $this
        ->collectionBuilder()
        ->addTask($this
          ->taskGitListStagedFiles()
          ->setDiffFilter(['d' => FALSE])
          ->setWorkingDirectory($workingDirectory)
          ->setPaths(MarvinUtils::drupalPhpExtensionPatterns()))
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
      ->setFileNamePatterns(MarvinUtils::drupalPhpExtensionPatterns());
  }

}
