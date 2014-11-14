<?php
/**
 * @package LogDirParser
 */

/**
 * Payment log directory parser
 *
 * @link http://www.php.net/manual/en/class.iterator.php
 */
class LogDirParser implements Iterator
{
    /**
     * Directory instance
     * @link http://www.php.net/manual/en/class.dir.php
     * @var Directory
     */
    protected $_dir;

    /**
     * PaymentLog instance for current file in directory
     * @var PaymentLog
     */
    protected $_currentPaymentLog;

    /**
     * Payment log index
     * @var int
     */
    protected $_currentIndex;

    /**
     * @param string $path path to log files directory
     */
    public function __construct($path)
    {
        $this->setDir($path);
    }

    /**
     * Set logs directory
     * @param string $path directory path
     */
    public function setDir($path)
    {
        if (!is_readable($path) || !is_dir($path)) {
            throw new LogDirParser_Exception("Unable to open log directory: $path");
        }

        $this->_currentIndex = 0;
        $this->_dir = dir($path);
        $this->_readNextFile();
    }

    /**
     * Returns current PaymentLog instance
     * @return PaymentLog|FALSE
     */
    public function current()
    {
        return null === $this->_currentPaymentLog ? false : $this->_currentPaymentLog;
    }

    /**
     * Returns current payment log file number
     * @return int
     */
    public function key()
    {
        return $this->_currentIndex;
    }

    /**
     * Reads next log file
     */
    public function next()
    {
        if ($this->_readNextFile()) {
            $this->_currentIndex++;
        }
    }

    /**
     * Rewinds directory iterator
     */
    public function rewind()
    {
        $this->_currentIndex = 0;
        $this->_dir->rewind();
        $this->_readNextFile();
    }

    /**
     * Checks if current position in directory iterator is valid
     * @return bool
     */
    public function valid()
    {
        return $this->_currentPaymentLog !== null;
    }

    /**
     * Reads next file in a directory and initialized PaymentLog for this file
     * @return bool
     */
    protected function _readNextFile()
    {
        do {
            $filename = $this->_dir->read();
        } while ($filename == '.' || $filename == '..');

        if (false === $filename) {
            $this->_currentPaymentLog = null;
            return false;
        }

        $this->_currentPaymentLog = new PaymentLog($this->_dir->path . $filename);
        return true;
    }
}
