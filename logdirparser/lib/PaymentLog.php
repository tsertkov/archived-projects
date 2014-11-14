<?php
/**
 * @package LogDirParser
 */

/**
 * Payment log file parser
 */
class PaymentLog
{
    /**
     * Payment log file path
     * @var string
     */
    protected $_filename;

    /**
     * Total amount of collected payments
     * @var int
     */
    protected $_totalCollected = 0;

    /**
     * Total amount of rejected payments
     * @var int
     */
    protected $_totalRejected = 0;

    /**
     * Minimum date in a log file
     * @var string
     */
    protected $_minDate;

    /**
     * Maximum date in a log file
     * @var string
     */
    protected $_maxDate;

    /**
     * @param string $filename payment log file path
     */
    public function __construct($filename)
    {
        $this->_filename = $filename;
        $this->_parse($filename);
    }

    /**
     * Parse payment log file
     *
     * Parses log file and collects total value of collected and rejected payments
     *
     * @param string $filename  payment log file path
     */
    protected function _parse($filename)
    {
        if (!is_readable($filename) || !is_file($filename)) {
            return;
        }

        $handle = fopen($filename, 'r');
        if (!$handle) {
            return;
        }

        // read file line by line, no temporary variables used, works with very BIG files
        while (!feof($handle)) {
            $logLine = fgets($handle);
            $this->_parseLogLine($logLine);
        }

        fclose($handle);
    }

    /**
     * Parse one line from log file
     * @param string $logLine
     */
    protected function _parseLogLine($logLine)
    {
        $args = explode(',', $logLine);

        if (4 == count($args)) {
            // collected payment
            $this->_totalCollected += $args[3];
        } elseif (5 == count($args)) {
            //rejected payment
            $this->_totalRejected += $args[4];
        } else {
            return;
        }

        // simple date comparision works here because of date format used in log files

        $date = $args[1];
        if ($date > $this->_maxDate) {
            $this->_maxDate = $date;
        }

        if (null === $this->_minDate || $date < $this->_minDate) {
            $this->_minDate = $date;
        }
    }

    /**
     * Get name of log file
     * @return string
     */
    public function getFilename()
    {
        return basename($this->_filename);
    }

    /**
     * Get total amount of collected payments
     * @return int
     */
    public function getTotalCollected()
    {
        return $this->_totalCollected;
    }

    /**
     * Get total amount of rejected payments
     * @return int
     */
    public function getTotalRejected()
    {
        return $this->_totalRejected;
    }

    /**
     * Get minimum date in log file
     * @return string
     */
    public function getMinDate()
    {
        return $this->_minDate;
    }

    /**
     * Get maximum date in log file
     * @return string
     */
    public function getMaxDate()
    {
        return $this->_maxDate;
    }
}
