<?php
/**
 * Class for performing various tests in subversion pre-commit hooks
 */
class SvnPreCommit
{
    /**
     * Commit message string
     * @var string
     */
    protected $_logMessage;

    /**
     * Commit files list
     * @var array
     */
    protected $_commitList;

    /**
     * Changed files list
     * @var array
     */
    protected $_changedFiles;

    /**
     * Subversion repository path
     * @var string
     */
    protected $_repository;

    /**
     * Transaction number
     * @var int
     */
    protected $_transaction;

    /**
     * Class constructor
     *
     * @param string $repository
     * @param string $transaction
     * @param array $tests array of test names to run
     */
    public function __construct($repository, $transaction, array $tests)
    {
        $this->_repository = $repository;
        $this->_transaction = $transaction;
        exit($this->_runTests($tests));
    }

    /**
     * Run subversion pre-commit tests
     *
     * @param array $tests array of test names to run
     * @return int result code, 0 == all test passed, other value represents
     *  number of failed tests
     */
    protected function _runTests(array $tests)
    {
        $result = 0;
        $messages = '';
        foreach ($tests as $k => $v) {
            if (is_numeric($k)) {
                $test = $v;
                $params = array();
            } else {
                $test = $k;
                $params = $v;

                if (!is_array($params)) {
                    throw new Exception('Test arguments should be in an array.');
                }
            }

            $method = "_test$test";
            $msg = '';
            array_unshift($params, $msg);
            $result += !call_user_func_array(array($this, $method), $params);
            if ($msg) {
                $messages .= " *) $msg\n";
            }
        }

        if ($messages) {
            $messages = rtrim($messages);
            fwrite(STDERR, "----------------\n$messages\n----------------");
        }

        return $result;
    }

    /**
     * Get commit log message
     *
     * @return string
     */
    protected function _getLogMessage()
    {
        if (null !== $this->_logMessage) {
            return $this->_logMessage;
        }

        $output = null;
        $cmd = "svnlook log -t '{$this->_transaction}' '{$this->_repository}'";
        exec($cmd, $output);
        $this->_logMessage = implode($output);
        return $this->_logMessage;
    }

    /**
     * Get content of file from current transaction
     *
     * @param string $file
     * @return string
     * @throws Exception
     */
    protected function _getFileContent($file)
    {
        $content = '';
        $cmd = "svnlook cat -t '{$this->_transaction}' '{$this->_repository}' '$file' 2>&1";

        // can't use exec() here because it will strip trailing spaces
        $handle = popen($cmd, 'r');
        while (!feof($handle)) {
            $content .= fread($handle, 1024);
        }
        $return = pclose($handle);

        if (0 != $return) {
            throw new Exception($content, $return);
        }

        return $content;
    }

    /**
     * Copy commited file into temporary folder and return its path
     *
     * @param string $file
     * @return string
     */
    protected function _getTmpFile($file)
    {
        $content = $this->_getFileContent($file);
        $filename = tempnam(sys_get_temp_dir(), 'svnprecommit');
        file_put_contents($filename, $content);
        return $filename;
    }

    /**
     * Get svn properties for file
     *
     * @param string $file
     * @return array
     */
    protected function _getFileProps($file)
    {
        $props = array();
        $cmd = "svnlook proplist -t '{$this->_transaction}' '{$this->_repository}' '$file'";
        $output = null;
        exec($cmd, $output);

        foreach ($output as $line) {
            $propname = trim($line);
            $cmd = "svnlook propget -t '{$this->_transaction}' '{$this->_repository}' $propname"
                . " '$file'";
            $output2 = null;
            exec($cmd, $output2);
            $propval = trim(implode($output2));

            $props[] = "$propname=$propval";
        }

        return $props;
    }

    /**
     * Get commit files list
     *
     * @return array filenames are keys and status letters are values
     */
    protected function _getCommitList()
    {
        if (null !== $this->_commitList) {
            return $this->_commitList;
        }

        $output = null;
        $cmd = "svnlook changed -t '{$this->_transaction}' '{$this->_repository}'";
        exec($cmd, $output);

        $list = array();
        foreach ($output as $item) {
            $pos = strpos($item, ' ');
            $status = substr($item, 0, $pos);
            $file = trim(substr($item, $pos));

            $list[$file] = $status;
        }

        $this->_commitList = $list;
        return $this->_commitList;
    }

    /**
     * Get array of modified and added files
     *
     * @param array $filetypes array of file types used for filtering
     * @return array
     */
    protected function _getChangedFiles(array $filetypes=array())
    {
        if (null === $this->_changedFiles) {
            $list = $this->_getCommitList();
            $files = array();
            foreach ($list as $file => $status) {
                if ('D' == $status || substr($file, -1) == DIRECTORY_SEPARATOR) {
                    continue;
                }

                $files[] = $file;
            }
            $this->_changedFiles = $files;
        }

        $files = array();
        foreach ($this->_changedFiles as $file) {
            $extension = pathinfo($file, PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            if ($filetypes && !in_array($extension, $filetypes)) {
                continue;
            }
            $files[$file] = $extension;
        }

        return $files;
    }

    /**
     * Check if log message validates length rules
     *
     * @param string $msg error messages placeholder
     * @param int $minlength minimum length of log message
     * @return bool
     */
    protected function _testLogMessageLength(&$msg, $minlength = 1)
    {
        $length = strlen(trim($this->_getLogMessage()));
        if ($length < $minlength) {
            if ($minlength <= 1) {
                $msg = "Log message should not be empty. Please specify descriptive log message.";
            } else {
                $msg = "You log message is too short ($length). It should be at least $minlength"
                    . " characters long.";
            }
            return false;
        }

        return true;
    }

    /**
     * Check if tabs are used as indents instead of spaces
     *
     * @param string $msg error messages placeholder
     * @param array $filetypes array of file types which should be tested
     * @return bool
     */
    protected function _testTabIndents(&$msg, array $filetypes = array())
    {
        $result = true;
        $files = $this->_getChangedFiles($filetypes);
        foreach ($files as $file => $extension) {
            $content = $this->_getFileContent($file);

            // check if file contains tabs
            $m = null;
            $tablines = preg_match_all('/.*\\t.*/m', $content, $m);

            if ($tablines) {
                $result = false;
                $msg .= "\t[$file] Tabs found on $tablines lines\n";
            }
        }

        if (!$result) {
            $msg = rtrim($msg);
            $msg = "You should use four spaces instead of tabs. Following files violate this rule:\n$msg";
        }

        return $result;
    }

    /**
     * Check if there are trailing spaces in files
     *
     * @param string $msg error messages placeholder
     * @param array $filetypes array of file types which should be tested
     * @return bool
     */
    protected function _testTrailingSpaces(&$msg, array $filetypes = array())
    {
        $result = true;
        $files = $this->_getChangedFiles($filetypes);
        foreach ($files as $file => $extension) {
            $content = $this->_getFileContent($file);

            // check if file contains trailing spaces
            $m = null;
            $spacelines = preg_match_all('/\\h$/m', $content, $m);

            if ($spacelines) {
                $result = false;
                $msg .= "\t[$file] Trailing spaces found on $spacelines lines\n";
            }
        }

        if (!$result) {
            $msg = rtrim($msg);
            $msg = "Trailing spaces are not allowed. Following files violate this rule:\n$msg";
        }

        return $result;
    }

    /**
     * Check coding standards using phpcs
     *
     * @param string $msg
     * @param string $standard
     * @param array $filetypes
     * @return bool
     */
    protected function _testCodingStandards(&$msg, $standard, array $filetypes = array())
    {
        $result = true;
        $files = $this->_getChangedFiles($filetypes);
        foreach ($files as $file => $extension) {
            $filename = $this->_getTmpFile($file);
            $cmd = "phpcs --standard='{$standard}' '$filename'";
            $output = null;
            $return = null;
            exec($cmd, $output, $return);

            // remove temporary file
            unlink($filename);

            if ($return) {
                // replace temporary file name in the output with actual file name
                foreach ($output as &$line) {
                    if (!strncmp('FILE: ', $line, 6)) {
                        $line = str_replace("FILE: {$filename}", "FILE: {$file}", $line);
                        break;
                    }
                }

                $result = false;
                $msg .= implode("\n", $output);
            }
        }

        if (!$result) {
            $msg = rtrim($msg);
            $msg = str_replace("\n", "\n\t", $msg);
            $msg = "Coding standards violations found:\n$msg";
        }

        return $result;
    }

    /**
     * Check if files have required svn properties set
     *
     * @param string $msg error messages placeholder
     * @param array $proprules svn properties rules
     * @return bool
     */
    protected function _testSvnProperties(&$msg, array $proprules = array())
    {
        $result = true;
        $files = $this->_getChangedFiles(array_keys($proprules));
        foreach ($files as $file => $extension) {
            $props = $this->_getFileProps($file);
            foreach ($proprules[$extension] as $proprule) {
                if (!in_array($proprule, $props)) {
                    $result = false;
                    $msg .= "\t[$file]\n";
                    break;
                }
            }
        }

        if (!$result) {
            $rules = '';
            foreach ($proprules as $filetype => $rule) {
                $rule = "*.$filetype = " . implode(',', $rule);
                $rules .= "\t$rule\n";
            }
            $rules = rtrim($rules);

            $msg = rtrim($msg);
            $msg = "Some files are missing required svn properties.\n"
                . "\t= Rules =\n$rules\n"
                . "\t= Files violating these rules =\n$msg";
        }

        return $result;
    }

    /**
     * Check if files violate eol rules
     *
     * @param string $msg error messages placeholder
     * @param array $eolrules
     * @return bool
     * @throws Exception
     */
    protected function _testEol(&$msg, array $eolrules = array())
    {
        $patterns = array(
            'CR' => '(?:\\x0d(?!\\x0a))',
            'LF' => '(?:(?<!\\x0d)\\x0a)',
            'CRLF' => '(?:\\x0d\\x0a)',
        );

        $result = true;
        $files = $this->_getChangedFiles(array_keys($eolrules));
        foreach ($files as $file => $extension) {
            $content = $this->_getFileContent($file);
            $rules = explode(',', $eolrules[$extension]);

            // create reqular expression for checking eol violations
            foreach ($rules as $eol) {
                $eol = strtoupper(trim($eol));
                if (!isset($patterns[$eol])) {
                    throw new Exception("Unknown EOL: $eol");
                }
                unset($patterns[$eol]);
            }

            $m = null;
            $pattern = '/' . implode('|', $patterns) . '/';
            $badlines = preg_match_all($pattern, $content, $m);

            if ($badlines) {
                $result = false;
                $msg .= "\t[$file] Not allowed EOL characters found on $badlines lines\n";
            }
        }

        if (!$result) {
            $msg = rtrim($msg);
            $msg = "Some files are violating EOL rules:\n$msg";
        }

        return $result;
    }
}
