<?php

// debuggers
use Symfony\Component\VarDumper\Cloner\VarCloner;
use Symfony\Component\VarDumper\Dumper\CliDumper;
use Symfony\Component\VarDumper\Dumper\ContextProvider\CliContextProvider;
use Symfony\Component\VarDumper\Dumper\ContextProvider\SourceContextProvider;
use Symfony\Component\VarDumper\Dumper\HtmlDumper;
use Symfony\Component\VarDumper\Dumper\ServerDumper;
use Symfony\Component\VarDumper\VarDumper;
if (class_exists('Symfony\Component\VarDumper\VarDumper')) {
    $cloner         = new VarCloner();
    $fallbackDumper = \in_array(\PHP_SAPI, ['cli', 'phpdbg']) ? new CliDumper() : new HtmlDumper();
    $dumper         = new ServerDumper('tcp://127.0.0.1:9912', $fallbackDumper, [
        'cli'    => new CliContextProvider(),
        'source' => new SourceContextProvider(),
    ]);

    VarDumper::setHandler(function ($var) use ($cloner, $dumper) {
        $dumper->dump($cloner->cloneVar($var));
    });
}
$conf['php_console'] = false;
try {

    if ($conf['php_console'] === true &&
        // PHP_CONSOLE doesn't work on PHP 7.4 yet
        version_compare(PHP_VERSION, 7.4, '<') &&
        class_exists('PhpConsole\Handler')) {
        \PhpConsole\Connector::setPostponeStorage(new \PhpConsole\Storage\File(BASE_PATH . '/../error_logs/php_console.postponed.log'));
        $phpConsoleHandler = PhpConsole\Handler::getInstance();

// Get the reference to the connector instance
        $connector = $phpConsoleHandler->getConnector();
        $connector->setSourcesBasePath(BASE_PATH);

/* Set a password for PHPConsole Connector
$connector->setPassword('yohoho123', true);
 */

/* enable in SSL mode only
$connector->enableSslOnlyMode();
 */

//* Override default Handler behavior:

    }

} catch (\Exception $e) {
    die($e->getMessage());
}
