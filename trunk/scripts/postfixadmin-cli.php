#!/usr/bin/php
<?php
/**
 * Command-line code generation utility to automate administrator tasks.
 *
 * Shell dispatcher class
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *                                                              1785 E. Sahara Avenue, Suite 490-204
 *                                                              Las Vegas, Nevada 89104
 * Modified for PostfixAdmin by Valkum 2011
 * Modified for PostfixAdmin by Christian Boltz 2011-2013
 *
 * Copyright 2010
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @filesource
 * @copyright           Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link                                http://postfixadmin.sourceforge.net/ Postfixadmin on Sourceforge
 * @package                     postfixadmin
 * @subpackage          -
 * @since                       -
 * @version                     $Revision$
 * @modifiedby          $LastChangedBy$
 * @lastmodified        $Date$
 * @license                     http://www.opensource.org/licenses/mit-license.php The MIT License
 */


class PostfixAdmin {
/**
 * Version 
 *
 * @var string
 * @access protected
 */
  var $version ='0.2';

/**
 * Standard input stream.
 *
 * @var filehandle
 * @access public
 */
        var $stdin;
/**
 * Standard output stream.
 *
 * @var filehandle
 * @access public
 */
        var $stdout;
/**
 * Standard error stream.
 *
 * @var filehandle
 * @access public
 */
        var $stderr;
/**
 * Contains command switches parsed from the command line.
 *
 * @var array
 * @access public
 */
        var $params = array();
/**
 * Contains arguments parsed from the command line.
 *
 * @var array
 * @access public
 */
        var $args = array();
/**
 * The file name of the shell that was invoked.
 *
 * @var string
 * @access public
 */
        var $shell = null;
/**
 * The class name of the shell that was invoked.
 *
 * @var string
 * @access public
 */
        var $shellClass = null;
/**
 * The command called if public methods are available.
 *
 * @var string
 * @access public
 */
        var $shellCommand = null;
/**
 * The name of the shell in camelized.
 *
 * @var string
 * @access public
 */
        var $shellName = null;
/**
 * Constructor
 *
 * @param array $args the argv.
 */
        function __construct($args = array()) {
                set_time_limit(0);
                $this->__initConstants();
                $this->parseParams($args);
                $this->__initEnvironment();
                /*$this->dispatch();
                die("\n");*/
        }
/**
 * Defines core configuration.
 *
 * @access private
 */
        function __initConstants() {
                if (function_exists('ini_set')) {
                        ini_set('display_errors', '1');
                        ini_set('error_reporting', E_ALL);
                        ini_set('html_errors', false);
                        ini_set('implicit_flush', true);
                        ini_set('max_execution_time', 0);
                }
                
                define('DS', DIRECTORY_SEPARATOR);
                define('CORE_INCLUDE_PATH', dirname(__FILE__));
                define('CORE_PATH', dirname(CORE_INCLUDE_PATH) ); # CORE_INCLUDE_PATH/../
                
                if(!defined('POSTFIXADMIN')) { # already defined if called from setup.php
                        define('POSTFIXADMIN', 1); # checked in included files
                }


        }
/**
 * Defines current working environment.
 *
 * @access private
 */
        function __initEnvironment() {
                $this->stdin = fopen('php://stdin', 'r');
                $this->stdout = fopen('php://stdout', 'w');
                $this->stderr = fopen('php://stderr', 'w');

                if (!$this->__bootstrap()) {
                        $this->stderr("");
                        $this->stderr("Unable to load.");
                        $this->stderr("\tMake sure /config.inc.php exists in " . PATH);
                        exit();
                }


                if (basename(__FILE__) !=  basename($this->args[0])) {
                        $this->stderr('Warning: the dispatcher may have been loaded incorrectly, which could lead to unexpected results...');
                        if ($this->getInput('Continue anyway?', array('y', 'n'), 'y') == 'n') {
                                exit();
                        }
                }

                $this->shiftArgs();
                
                

        }
/**
 * Initializes the environment and loads the Cake core.
 *
 * @return boolean Success.
 * @access private
 */
        function __bootstrap() {
                if ($this->params['webroot'] != '' ) {
                        define('PATH', $this->params['webroot'] );
                } else {
                        define('PATH', CORE_PATH);
                }            
                if (!file_exists(PATH)) {
                        $this->stderr( PATH . " don't exists");
                        return false;
                
                }

                if (!require_once(PATH . '/common.php')) {
                        $this->stderr("Failed to load " . PATH . '/common.php');
                        return false;
                }

                return true;
        }

/**
 * Dispatches a CLI request
 *
 * @access public
 */
        function dispatch() {
        $CONF = Config::read('all');

			if (!isset($this->args[0])) {
					$this->help();
					return;
			}

			$this->shell = $this->args[0];
			$this->shiftArgs();
			$this->shellName = ucfirst($this->shell);
			$this->shellClass = $this->shellName . 'Handler';
			

			if ($this->shell == 'help') {
				$this->help();
				return;
			}
# TODO: move shells/shell.php to model/ to enable autoloading
			if (!class_exists('Shell')) {
					require CORE_INCLUDE_PATH . DS . "shells" . DS . 'shell.php';
			}
			$command = 'help'; # not the worst default ;-)
			if (isset($this->args[0])) {
					$command = $this->args[0];
			}

			$this->shellCommand = $command;
			$this->shellClass = 'Cli' . ucfirst($command);

            if (ucfirst($command) == 'Add' || ucfirst($command) == 'Update') {
                $this->shellClass = 'CliEdit';
            }

            if (!class_exists($this->shellClass)) {
                $this->stderr('Unknown task ' . $this->shellCommand);
                return;
            }

            $shell = new $this->shellClass($this);

            $shell->handler_to_use = ucfirst($this->shell) . 'Handler';

            if (!class_exists($shell->handler_to_use)) {
                $this->stderr('Unknown module ' . $this->shell);
                return;
            }

            $task = ucfirst($command);

            $shell->new = 0;
            if ($task == 'Add') {
                $shell->new = 1;
            }

# TODO: add a way to Cli* to signal if the selected handler is supported (for example, not all *Handler support changing the password)

			if (strtolower(get_parent_class($shell)) == 'shell') {
				$shell->initialize();

				$handler = new $shell->handler_to_use;
				if (in_array($task, $handler->taskNames)) {
					$this->shiftArgs();
					$shell->startup();


					if (isset($this->args[0]) && $this->args[0] == 'help') {
						if (method_exists($shell, 'help')) {
							$shell->help();
							exit();
						} else {
							$this->help();
						}
					}

					$shell->execute();
					return;
				}
			}

			$classMethods = get_class_methods($shell);

			$privateMethod = $missingCommand = false;
			if ((in_array($command, $classMethods) || in_array(strtolower($command), $classMethods)) && strpos($command, '_', 0) === 0) {
				$privateMethod = true;
			}

			if (!in_array($command, $classMethods) && !in_array(strtolower($command), $classMethods)) {
				$missingCommand = true;
			}

			$protectedCommands = array(
				'initialize','in','out','err','hr',
				'createfile', 'isdir','copydir','object','tostring',
				'requestaction','log','cakeerror', 'shelldispatcher',
				'__initconstants','__initenvironment','__construct',
				'dispatch','__bootstrap','getinput','stdout','stderr','parseparams','shiftargs'
			);

			if (in_array(strtolower($command), $protectedCommands)) {
				$missingCommand = true;
			}

			if ($missingCommand && method_exists($shell, 'main')) {
				$shell->startup();
				$shell->main();
			} elseif (!$privateMethod && method_exists($shell, $command)) {
				$this->shiftArgs();
				$shell->startup();
				$shell->{$command}();
			} else {
				$this->stderr("Unknown {$this->shellName} command '$command'.\nFor usage, try 'postfixadmin-cli {$this->shell} help'.\n\n");
			}
        }

/**
 * Prompts the user for input, and returns it.
 *
 * @param string $prompt Prompt text.
 * @param mixed $options Array or string of options.
 * @param string $default Default input value.
 * @return Either the default value, or the user-provided input.
 * @access public
 */
        function getInput($prompt, $options = null, $default = null) {
                if (!is_array($options)) {
                        $print_options = '';
                } else {
                        $print_options = '(' . implode('/', $options) . ')';
                }

                if ($default == null) {
                        $this->stdout($prompt . " $print_options \n" . '> ', false);
                } else {
                        $this->stdout($prompt . " $print_options \n" . "[$default] > ", false);
                }
                $result = fgets($this->stdin);

                if ($result === false){
                        exit;
                }
                $result = trim($result);

                if ($default != null && empty($result)) {
                        return $default;
                }
                return $result;
        }
/**
 * Outputs to the stdout filehandle.
 *
 * @param string $string String to output.
 * @param boolean $newline If true, the outputs gets an added newline.
 * @access public
 */
        function stdout($string, $newline = true) {
                if ($newline) {
                        fwrite($this->stdout, $string . "\n");
                } else {
                        fwrite($this->stdout, $string);
                }
        }
/**
 * Outputs to the stderr filehandle.
 *
 * @param string $string Error text to output.
 * @access public
 */
        function stderr($string) {
                fwrite($this->stderr, 'Error: '. $string . "\n");
        }

/**
 * Parses command line options
 *
 * @param array $params Parameters to parse
 * @access public
 */
        function parseParams($params) {
                $this->__parseParams($params);

                $defaults = array('webroot' => CORE_PATH);

                $params = array_merge($defaults, array_intersect_key($this->params, $defaults));

                $isWin = array_filter(array_map('strpos', $params, array('\\')));

                $params = str_replace('\\', '/', $params);


                if (!empty($matches[0]) || !empty($isWin)) {
                        $params = str_replace('/', '\\', $params);
                }

                $this->params = array_merge($this->params, $params);
        }
/**
 * Helper for recursively paraing params
 *
 * @return array params
 * @access private
 */
        function __parseParams($params) {
                $count = count($params);
                for ($i = 0; $i < $count; $i++) {
                        if (isset($params[$i])) {
                                if ($params[$i] != '' && $params[$i]{0} === '-') {
                                        $key = substr($params[$i], 1);
                                        $this->params[$key] = true;
                                        unset($params[$i]);
                                        if (isset($params[++$i])) {
                                                if ($params[$i]{0} !== '-') {
                                                        $this->params[$key] = str_replace('"', '', $params[$i]);
                                                        unset($params[$i]);
                                                } else {
                                                        $i--;
                                                        $this->__parseParams($params);
                                                }
                                        }
                                } else {
                                        $this->args[] = $params[$i];
                                        unset($params[$i]);
                                }

                        }
                }
        }
/**
 * Removes first argument and shifts other arguments up
 *
 * @return boolean False if there are no arguments
 * @access public
 */
        function shiftArgs() {
                if (empty($this->args)) {
                        return false;
                }
                unset($this->args[0]);
                $this->args = array_values($this->args);
                return true;
        }

        function help() {
                $this->stdout("\nWelcome to Postfixadmin-CLI v" . $this->version);
                $this->stdout("---------------------------------------------------------------");
                /* users shouldn't need to specify -webroot - therefore let's "hide" it ;-)
                $this->stdout("Options:");
                $this->stdout(" -webroot: " . $this->params['webroot']);
                $this->stdout("");
                $this->stdout("Changing Paths:");
                $this->stdout("your webroot should be the same as your postfixadmin path");
                $this->stdout("to change your path use the '-webroot' param.");
                $this->stdout("Example: -webroot r/absolute/path/to/postfixadmin");
                */

                $this->stdout("Usage:");
                $this->stdout("    postfixadmin-cli <module> <task> [--option value --option2 value]");
                $this->stdout("");
                $this->stdout("Available modules:");

                $modules = explode(',','admin,domain,mailbox,alias,aliasdomain');
                foreach ($modules as $module) {
                    $this->stdout("    $module");
                }
                $this->stdout("");
                $this->stdout("Most modules support the following tasks:");
                $this->stdout("    view      View an item");
                $this->stdout("    add       Add an item");
                $this->stdout("    update    Update an item");
                $this->stdout("    delete    Delete an item");
                $this->stdout("    scheme    Print database scheme (useful for developers only)");
                $this->stdout("    help      Print help output");
                $this->stdout("");
                $this->stdout("");
                $this->stdout("For module-specific help, see:");
                $this->stdout("");
                $this->stdout("    postfixadmin-cli <module> help");
                $this->stdout("        print a detailed list of available commands");
                $this->stdout("");
                $this->stdout("    postfixadmin-cli <module> <task> help");
                $this->stdout("        print a list of available options.");
                $this->stdout("");

                /*
                $this->stdout("\nAvailable Commands:");
                foreach ($this->commands() AS $command => $desc) {
                        if (is_array($desc)) {
                                $this->stdout($command . ":");
                                foreach($desc AS $command2 => $desc2) {
                                        $this->stdout(sprintf("%-20s %s", "   ".$command2 .": ", $desc2));
                                }
                                $this->stdout("");
                        } else {
                                $this->stdout(sprintf("%-20s %s", $command .": ",  $desc));
                        }
                }
                $this->stdout("\nTo run a command, type 'postfixadmin-cli command [args]'");
                $this->stdout("To get help on a specific command, type 'postfixadmin-cli command help'");
                */
                exit();

        }
/**
 * Removes first argument and shifts other arguments up
 *
 * @return array List of commands
 * @access public
 */
/* currently unused (and outdated)
        function commands() {
        
        
# TODO: this list is incomplete  
        return array(
                'mailbox' => array(
                           'add'=> 'Adds a new mailbox.', 
                           'update'=> 'Updates a mailbox.', 
                           'delete' => 'Deletes a mailbox.', 
                           'pw' => 'Changes the PW for a mailbox.',
                ), 
                'alias' => array(
                            'add' => 'Adds a new alias.',
                            'update' => 'Updates a alias.',
                            'delete' => 'Deletes a alias.',
                ), 
                );
        
        
        
        
        }
*/


}


define ("POSTFIXADMIN_CLI", 1);

$dispatcher = new PostfixAdmin($argv);

$CONF = Config::read('all');

$dispatcher->dispatch();

/* vim: set expandtab softtabstop=4 tabstop=4 shiftwidth=4: */
?>
