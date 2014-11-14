```
NB! Implemented in 2008 and never touched since then.
```

SvnPreCommit
============

PHP class used in SVN pre-commit hook for validating commit against configured rules.

The following rules are supported:

- minimum commit log message length,
- only soft tabs used for indentation,
- no trailing spaces,
- commited files should have specified svn properties,
- coding standards using [phpcs](http://pear.php.net/package/PHP_CodeSniffer/)
- EOL type.

## pre-commit script example

```
#!/usr/bin/env PATH=/usr/bin php
<?php

require '/var/svn/hook-scripts/SvnPreCommit.php';

new SvnPreCommit($argv[1], $argv[2], array(
    'LogMessageLength' => array(3),
    'TabIndents' => array(array('php', 'phtml')),
    'TrailingSpaces' => array(array('php', 'phtml', 'ini')),
    'SvnProperties' => array(array(
        'php' => array('svn:keywords=Revision', 'svn:eol-style=LF'),
        'phtml' => array('svn:keywords=Revision', 'svn:eol-style=LF'),
        'html' => array('svn:eol-style=LF'),
        'htm' => array('svn:eol-style=LF'),
        'css' => array('svn:eol-style=LF'),
        'xsl' => array('svn:eol-style=LF'),
        'xml' => array('svn:eol-style=LF'),
        'txt' => array('svn:eol-style=LF'),
        'ini' => array('svn:eol-style=LF'),
        'js' => array('svn:eol-style=LF'),
        'java' => array('svn:keywords=Revision', 'svn:eol-style=LF'),
    )),
));
```