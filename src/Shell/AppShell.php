<?php
namespace App\Shell;

use App\Lib\CakeboxExecute;
use App\Lib\CakeboxInfo;
use Cake\Console\Shell;
use Cake\Core\Configure;
use Cake\Log\Log;

/**
 * Application Shell class extended by all cakebox-console shells.
 */
class AppShell extends Shell
{

    /**
     * Instance of CakeboxInfo available to all Shells.
     *
     * @var \App\Lib\CakeboxInfo
     */
    protected $cbi;

    /**
     * Instance of CakeboxExecute available to all Shells.
     *
     * @var \App\Lib\CakeboxExecute
     */
    protected $execute;

    /**
     * Initialization. Used to disconnect default loggers from consoleIO output
     * and instantiating Cakebox objects.
     *
     * @return void
     */
    public function initialize() {
        $this->_io->setLoggers(false);
        $this->cbi = new CakeboxInfo();
        $this->execute = new CakeboxExecute();
        parent::initialize();
    }

    /**
     * Override Cake\Console\Shell method to return different welcome screen.
     *
     * @return void
     */
    protected function _welcome()
    {
        $this->hr();
        $this->out(sprintf('<info>CakePHP %s Console</info>', 'v' . Configure::version()));
        $this->hr();
    }

    /**
    * Convenience function adds a "hr" splitter element to the logs to easily
    * identify various actions when reading the plain logfile.
    *
    * @param string $message
    * @return void
    */
    public function logStart($message) {
        log::debug(str_repeat("=", 80));
        $this->out($message, 1, Shell::QUIET);
        log::info($message);
    }

    /**
     * Only output debug message to screen when script is run with --verbose argument
     *
     * @param string $message
     * @return void
     */
    public function logDebug($message) {
        log::debug($message);
        $this->out($message, 1, Shell::VERBOSE);
    }

    /**
     * Always output info message to screen (even when using --quiet).
     *
     * @param string|array $message
     * @return void
     */
    public function logInfo($message) {
        if (is_string($message)) {
            log::info($message);
            $this->out($message, 1, Shell::QUIET);
            return;
        }
        foreach ($message as $entry) {
            $this->out($entry, 1, Shell::QUIET);
        }
    }

    /**
    * Always output warning message to screen (even when using --quiet)
    *
    * @param string $message
    * @return void
    */
    public function logWarning($message) {
        log::warning($message);
        $this->out($message, 1, Shell::QUIET);
    }

    /**
    * Always output warning message to screen (even when using --quiet)
    *
    * @param string $message
    * @return void
    */
    public function logError($message) {
        log::warning($message);
        $this->out("<error>$message</error> <info>See cakebox log for details.</info>", 1, Shell::QUIET);
    }

    /**
     * Exit PHP script with exit code 0 to inform bash about success.
     *
     * @return void
     */
    public function exitBashSuccess($message = null)
    {
        if (count($this->execute->debug()) != 0) {
            foreach ($this->execute->debug() as $entry) {
                $this->out($entry, 1, Shell::VERBOSE);
            }
        }
        if ($message) {
            $this->logInfo($message);
        }
        exit (0);
    }

    /**
     * Exit PHP script with exit code 0 to inform bash about success.
     *
     * @return void
     */
    public function exitBashWarning($message)
    {
        if (count($this->execute->debug()) != 0) {
            foreach ($this->execute->debug() as $entry) {
                $this->out($entry, 1, Shell::VERBOSE);
            }
        }
        $this->logInfo($message);
        exit (0);
    }

    /**
     * Show most recent execute log message to user before exiting PHP script
     * with exit code 1 to inform bash about errors.
     *
     * @return void
     */
    public function exitBashError($message)
    {
        if (count($this->execute->debug()) != 0) {
            foreach ($this->execute->debug() as $entry) {
                $this->out($entry, 1, Shell::QUIET);
            }
        }
        $this->logError($message);
        exit (1);
    }
}
