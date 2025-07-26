<?php

namespace Hubleto;

class Terminal
{

  public static function isLaunchedFromTerminal(): bool
  {
    return (php_sapi_name() === 'cli');
  }

  /**
   * Print special strings setting a specified color
   *
   * @param string $fgColor
   * @param string $bgColor
   * 
   * @return void
   * 
   */
  public static function color(string $fgColor, string $bgColor = 'black'): void
  {
    if (php_sapi_name() !== 'cli') {
      return;
    }

    $bgSequences = [
      'black' => "\033[40m",
      'red' => "\033[41m",
      'green' => "\033[42m",
      'yellow' => "\033[43m",
      'blue' => "\033[44m",
      'purple' => "\033[45m",
      'cyan' => "\033[46m",
      'white' => "\033[47m",
    ];

    echo $bgSequences[$bgColor] ?? '';

    $fgSequences = [
      'black' => "\033[30m",
      'red' => "\033[31m",
      'green' => "\033[32m",
      'yellow' => "\033[33m",
      'blue' => "\033[34m",
      'purple' => "\033[35m",
      'cyan' => "\033[36m",
      'white' => "\033[37m",
    ];

    echo $fgSequences[$fgColor] ?? '';
  }

  /**
   * Read input from terminal/console
   *
   * @return string
   * 
   */
  public static function readRaw(): string
  {
    $clih = fopen("php://stdin", "r");
    $input = fgets($clih);
    $input = trim($input);
    return $input;
  }

  /**
   * Read input from terminal/console and return $default is none is entered.
   *
   * @param string $message
   * @param string $default
   * 
   * @return string
   * 
   */
  public static function read(string $message, string $default = ''): string
  {
    self::yellow($message . (empty($default) ? '' : ' (press Enter for \'' . $default . '\')') . ': ');

    $input = self::readRaw();
    if (empty($input)) {
      $input = $default;
    }

    self::white('  -> ' . $input . "\n");

    return $input;
  }

  /**
   * Get user selection from pre-defined options using terminal/console.
   *
   * @param array $options
   * @param string $message
   * @param string $default
   * 
   * @return string
   * 
   */
  public static function choose(array $options, string $message, string $default = ''): string
  {
    self::yellow($message . "\n");
    foreach ($options as $key => $option) {
      self::white(' ' . (string) $key . ' = ' . (string) $option . "\n");
    }
    self::yellow('Select one of the options, provide a value' . (empty($default) ? '' : ' or press Enter for \'' . $default . '\'') . ': ');

    $input = self::readRaw();
    if (is_numeric($input)) {
      $input = (string) ($options[$input] ?? '');
    }
    if (empty($input)) {
      $input = $default;
    }

    self::white('  -> ' . $input . "\n");

    return $input;
  }

  /**
   * Ask for user confirmation
   *
   * @param string $question
   * @param array $yesAnswers Possible answers representing confirmation.
   * 
   * @return bool
   * 
   */
  public static function confirm(string $question, $yesAnswers = ['yes', 'y', '1']): bool
  {
    $answer = self::read($question);
    return in_array(strtolower($answer), $yesAnswers);
  }

  /**
   * Print message in terminal in yellow color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function yellow(string $message): void
  {
    self::color('yellow');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in green color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function green(string $message): void
  {
    self::color('green');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in red color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function red(string $message): void
  {
    self::color('red');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in blue color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function blue(string $message): void
  {
    self::color('blue');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in cyan color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function cyan(string $message): void
  {
    self::color('cyan');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in white color
   *
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function white(string $message): void
  {
    self::color('white');
    echo $message;
    self::color('white');
  }

  /**
   * Print message in terminal in specified color
   *
   * @param string $bgColor
   * @param string $fgColor
   * @param string $message
   * 
   * @return void
   * 
   */
  public static function colored(string $bgColor, string $fgColor, string $message): void
  {
    self::color($fgColor, $bgColor);
    echo $message;
    self::color('white', 'black');
    echo "\n";
  }

  public static function insertCodeToFile(string $file, string $tag, array $codeLines): bool
  {
    $inserted = false;

    if (!is_file($file)) {
      return false;
    }

    $lines = file($file);
    $newLines = [];
    foreach ($lines as $line) {
      $newLines[] = $line;
      if (str_starts_with(trim($line), $tag)) {
        $identSize = strlen($line) - strlen(ltrim($line));
        foreach ($codeLines as $codeLine) {
          $newLines[] = str_repeat(' ', $identSize) . trim($codeLine) . "\n";
        }
        $inserted = true;
      }
    }

    if ($inserted) {
      file_put_contents($file, join("", $newLines));
      self::yellow("Code inserted into '{$file}' under '{$tag}'.\n");
    }

    return $inserted;
  }

}
