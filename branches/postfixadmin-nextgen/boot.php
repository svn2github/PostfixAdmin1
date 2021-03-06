<?php
// $id$

/**
 * @package PFA
 * Boot
 * 
 * @copyright 
 * @version $Revision: $:
 * @author $Author: $:
 * @date $Date: $:
 */

/**
 * Basic defines for timing functions.
 */
	define('SECOND', 1);
	define('MINUTE', 60);
	define('HOUR', 3600);
	define('DAY', 86400);
	define('WEEK', 604800);
	define('MONTH', 2592000);
	define('YEAR', 31536000);
  
  /**
 * Basic defines for data functions.
 */
  define('KB', 1024);
	define('MB', 1048576);
	define('GB', 1073741824);
  
  /**
 * Basic boot definitions.
 */
  define('PFA_BOOT_CONFIGURATION', 0);
  define('PFA_BOOT_DATABASE', 1);
  define('PFA_BOOT_VARIABLES',2);
  define('PFA_BOOT_SESSION',3);
  define('PFA_BOOT_LANGUAGE',4);
  define('PFA_BOOT_FULL',5);
  
  

 /**
 * @param string $class
 * __autoload implementation, for use with spl_autoload_register().
 */
function pfa_autoload($class) {

    if(class_exists($class)) {
      return true;
    }
    $path = $class . '.class.php';
    if(is_file(ROOT . $path)) {
        require_once(ROOT . $path);
        return true;
    } elseif(is_file(LIB . $path)) {
        require_once(LIB . $path);
        return true;
    } elseif(is_file(LIB . DS . "database" . DS . $class . ".php")) {
      require_once(LIB . DS . 'database' . DS . $class . '.php');
      return true;
    } elseif(is_file($path)) {
        require_once($path);
        return true;
    }
    error(2, "Class not found: $class");
    return false;
}
spl_autoload_register('pfa_autoload');

class PFA {

  /**
   * @access private
   * @var object instance
   * Singleton-Instanz des FrontControllers
   */
  private static $instance = null;

  var $view = null;
  
  function __construct($phase, $new_phase = true) {
    require(LIB."helper.php");
    static $phases = array(
      PFA_BOOT_CONFIGURATION,
      PFA_BOOT_DATABASE,
      PFA_BOOT_VARIABLES,
      PFA_BOOT_SESSION,
      PFA_BOOT_LANGUAGE,
      PFA_BOOT_FULL,
    );
  

    static $final_phase;
    static $stored_phase = -1;
  
    // When not recursing, store the phase name so it's not forgotten while
    // recursing.
    if ($new_phase) {
      $final_phase = $phase;
    }
    if (isset($phase)) {
      // Call a phase if it has not been called before and is below the requested
      // phase.
      while ($phases && $phase > $stored_phase && $final_phase > $stored_phase) {
        $current_phase = array_shift($phases);

        // This function is re-entrant. Only update the completed phase when the
        // current call actually resulted in a progress in the bootstrap process.
        if ($current_phase > $stored_phase) {
          $stored_phase = $current_phase;
        }

        switch ($current_phase) {
          case PFA_BOOT_CONFIGURATION:
            $this->_pfa_boot_config();
            break;

          case PFA_BOOT_DATABASE:
            $this->_pfa_boot_database();
            break;

          // case PFA_BOOT_VARIABLES:
            // _drupal_bootstrap_variables();
            // break;

          // case PFA_BOOT_SESSION:
            // require_once DRUPAL_ROOT . '/' . variable_get('session_inc', 'includes/session.inc');
            // §this->init_sess();
            // break;
          // case PFA_BOOT_LANGUAGE:
            // $this->init_lang();
            // break;
  
          case PFA_BOOT_FULL:
            $this->_pfa_boot_full();
            break;
        }
      }
    }
    return $stored_phase;
  }
  private function __clone() {}
  
  function getInstance($phase = PFA_BOOT_FULL) {
    {
    if(self::$instance === null)
    {
      self::$instance = new PFA($phase);
    }
    return self::$instance;
  }
  }
  
  function run($path = NULL) {
    $read_only_path = !empty($path) ? $this->path->build($path) : $this->path->cPath;
    $cPath = !is_array($read_only_path) ? explode('/', $read_only_path) : $read_only_path;
    
	
    if( is_file(ROOT . DS . 'controller'  . DS . $cPath['class']. '_controller.php') ) {  
      require_once(ROOT . DS . 'controller' . DS . $cPath['class']. '_controller.php');

    } elseif (is_file(ROOT . DS . 'plugin' . DS . $cPath['class'] . DS . $cPath['class']. '_controller.php')) {
      require_once(ROOT . DS . 'plugin' . DS . $cPath['class'] . DS . $cPath['class']. '_controller.php');
	  }
    elseif (is_file(ROOT.DS.'page'.DS.$cPath['class'].'php')) {
     
    }
	  $this->controller = new $cPath['class'];
      $viewName = isset($this->controller->view) ? $this->controller->view : $cPath['class'];
      $this->view = PFAView::getInstance($viewName);
      
      $page_result = $this->controller->{$cPath['method']}();
      $this->view->display($cPath['method'], $page_result);
	  

  }
  
  function _pfa_boot_config() {
    PFAConf::load('main');
    PFAConf::load('local');
  }

  function _pfa_boot_database() {
    if (!$this->pfa_installed()) {
      pfa_redir('install.php');
    }
    PFAConf::load('database');
    $this->db = PFADatabase::getInstance();
    
  }
  
  function _pfa_boot_full() {
    $this->path = new PFAPath();
  }
  
  function pfa_installed() {
    return PFAConf::read('MODE') != 'INSTALL';
  }
  
}



  
