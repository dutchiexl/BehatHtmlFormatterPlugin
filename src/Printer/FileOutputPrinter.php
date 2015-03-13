<?php
/**
 * Very simple FileOutputPrinter for BehatHTMLFormatter
 * @author David Raison <david@tentwentyfour.lu>
 */

namespace emuse\BehatHTMLFormatter\Printer;

use Behat\Testwork\Output\Exception\BadOutputPathException;
use Behat\Testwork\Output\Printer\OutputPrinter as PrinterInterface;


class FileOutputPrinter implements PrinterInterface {


    /**
     * Renderer used
     * @param $renderer BaseRenderer
     */
    private $renderer;  
    
  /**
   * @param  $outputPath where to save the generated report file
   */
  private $outputPath;

  /**
   * @param  $base_path Behat base path
   */
  private $base_path;


  public function __construct($renderer, $base_path) {
    $this->renderer = $renderer;
    $this->base_path = $base_path;
  }

  /**
   * Verify that the specified output path exists or can be created,
   * then sets the output path.
   *
   * @param String $path Output path relative to %paths.base%
   *
   */
  public function setOutputPath($path) {
    $outpath = $path;
    if (!file_exists($outpath)) {
      if (!mkdir($outpath, 0755, TRUE)) {
        throw new BadOutputPathException(
          sprintf(
            'Output path %s does not exist and could not be created!',
            $outpath
          ),
          $outpath
        );
      }
    }
    else {
      if (!is_dir(realpath($outpath))) {
        throw new BadOutputPathException(
          sprintf(
            'The argument to `output` is expected to the a directory, but got %s!',
            $outpath
          ),
          $outpath
        );
      }
    }
    $this->outputPath = $outpath;
  }

  /**
   * Returns output path
   *
   * @return String output path
   */
  public function getOutputPath() {
    return $this->outputPath;
  }

  /**
   * Sets output styles.
   *
   * @param array $styles
   */
  public function setOutputStyles(array $styles) {

  }

  /**
   * Returns output styles.
   *
   * @return array
   */
  public function getOutputStyles() {

  }

  /**
   * Forces output to be decorated.
   *
   * @param Boolean $decorated
   */
  public function setOutputDecorated($decorated) {

  }

  /**
   * Returns output decoration status.
   *
   * @return null|Boolean
   */
  public function isOutputDecorated() {
    return TRUE;
  }

  /**
   * Sets output verbosity level.
   *
   * @param integer $level
   */
  public function setOutputVerbosity($level) {

  }

  /**
   * Returns output verbosity level.
   *
   * @return integer
   */
  public function getOutputVerbosity() {
  
  }

    /**
     * Writes message(s) to output console.
     *
     * @param string|array $messages message or array of messages
     */
    public function write($messages = '')
    {
        $file = $this->outputPath . DIRECTORY_SEPARATOR . 'report.html';
        file_put_contents($file, $messages);
        $this->copyAssets();
    }
    
    
    /**
     * Writes newlined message(s) to output console.
     *
     * @param string|array $messages message or array of messages
     */
    public function writeln($messages = '')
    {
        $file = $this->outputPath . DIRECTORY_SEPARATOR . 'report.html';
        file_put_contents($file, $messages, FILE_APPEND);
    }
    
    /**
     * Writes  message(s) at start of the output console.
     *
     * @param string|array $messages message or array of messages
     */
    public function writeBeginning($messages = '') {
        $file = $this->outputPath . DIRECTORY_SEPARATOR . 'report.html';
        $fileContents = file_get_contents($file);
        file_put_contents($file, $messages . $fileContents);
    }

  /**
   * Copies the assets folder to the report destination.
   */
  public function copyAssets() {
    // If the assets folder doesn' exist in the output path, copy it
    $source = realpath(dirname(__FILE__));
    $assets_source = realpath($source . '/../../assets/' . $this->renderer);
    if ($assets_source === false) {
        //There is no assets to copy for this report format
        return ;
    } 
    $destination = $this->outputPath . DIRECTORY_SEPARATOR . 'assets';
    $this->recurse_copy($assets_source, $destination);
  }

  /**
   * Recursivly copy a path.
   * @param $src
   * @param $dst
   */
  private function recurse_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    while (FALSE !== ($file = readdir($dir))) {
      if (($file != '.') && ($file != '..')) {
        if (is_dir($src . '/' . $file)) {
          $this->recurse_copy($src . '/' . $file, $dst . '/' . $file);
        }
        else {
          copy($src . '/' . $file, $dst . '/' . $file);
        }
      }
    }
    closedir($dir);
  }

  /**
   * Clear output console, so on next write formatter will need to init (create) it again.
   */
  public function flush() {

  }
}
