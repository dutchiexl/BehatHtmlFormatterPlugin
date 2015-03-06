<?php
/**
 * Very simple FileOutputPrinter for BehatHTMLFormatter
 * @author David Raison <david@tentwentyfour.lu>
 */

namespace emuse\BehatHTMLFormatter\Printer;

use Behat\Testwork\Output\Printer\OutputPrinter as PrinterInterface;
use Behat\Testwork\Output\Exception\BadOutputPathException;


class FileOutputPrinter implements PrinterInterface
{

    /**
     * @param  $outputPath where to save the generated report file
     */
    private $outputPath;

    /**
     * @param  $base_path Behat base path
     */
    private $base_path;


    public function __construct($base_path)
    {
        $this->base_path = $base_path;
    }

    /**
     * Verify that the specified output path exists or can be created,
     * then sets the output path.
     *
     * @param String $path Output path relative to %paths.base%
     *
     */
    public function setOutputPath($path)
    {
        $outpath = $path;
        if (!file_exists($outpath)) {
            if (!mkdir($outpath, 0755, true))
                throw new BadOutputPathException(
                    sprintf(
                        'Output path %s does not exist and could not be created!',
                        $outpath
                    ),
                    $outpath
                );
        } else {
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
    public function getOutputPath()
    {
        return $this->outputPath;
    }

    /**
     * Sets output styles.
     *
     * @param array $styles
     */
    public function setOutputStyles(array $styles)
    {

    }

    /**
     * Returns output styles.
     *
     * @return array
     */
    public function getOutputStyles()
    {

    }

    /**
     * Forces output to be decorated.
     *
     * @param Boolean $decorated
     */
    public function setOutputDecorated($decorated)
    {

    }

    /**
     * Returns output decoration status.
     *
     * @return null|Boolean
     */
    public function isOutputDecorated()
    {
        return true;
    }

    /**
     * Sets output verbosity level.
     *
     * @param integer $level
     */
    public function setOutputVerbosity($level)
    {

    }

    /**
     * Returns output verbosity level.
     *
     * @return integer
     */
    public function getOutputVerbosity()
    {

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
     * Clear output console, so on next write formatter will need to init (create) it again.
     */
    public function flush()
    {

    }
}
