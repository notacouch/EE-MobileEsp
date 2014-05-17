<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MobileEsp Extension class
 *
 * @author          EE Add-On: Abed Islam, MobileEsp: Hand Interactive
 * @link            https://github.com/notacouch/EE-MobileEsp
 * @license         http://creativecommons.org/licenses/by-sa/3.0/
 */
class Mdetect_ext {

  // --------------------------------------------------------------------
  // PROPERTIES
  // --------------------------------------------------------------------

  /**
   * Extension settings
   *
   * @access      public
   * @var         array
   */
  public $settings = array();

  /**
   * Extension name
   *
   * @access      public
   * @var         string
   */
  public $name = 'MobileESP';

  /**
   * Extension version
   *
   * @access      public
   * @var         string
   */
  public $version = '0.1.214'; // j.n.r; j = major version, n = minor, r = revision of MobileESP

  /**
   * Extension description
   *
   * @access      public
   * @var         string
   */
  public $description = 'Provides various MobileESP methods as global variables.';

  /**
   * Do settings exist?
   *
   * @access      public
   * @var         bool
   */
  public $settings_exist = 'n'; // @TODO: create settings for which MobileESP methods should be made available

  /**
   * Documentation link
   *
   * @access      public
   * @var         string
   */
  public $docs_url = 'http://www.hand-interactive.com/detect/mobileesp_demo_php.htm';

  // --------------------------------------------------------------------

  /**
   * EE Instance
   *
   * @access      private
   * @var         object
   */
  private $EE;
  
  /**
   * MobileESP Instance
   * 
   * @access      private
   * @var         object
   */
  private $mdetect;
  
  /**
   * Current class name
   *
   * @access      private
   * @var         string
   */
  private $class_name;
  
  /**
   * Current site id
   *
   * @access      private
   * @var         int
   */
  private $site_id;
  
  /**
   * Default settings
   *
   * @access      public
   * @var         array
   */
  private $default_settings = array(
  );

  // --------------------------------------------------------------------
  // METHODS
  // --------------------------------------------------------------------

//  Removed for PHP 5.5
//  /**
//   * Legacy Constructor
//   *
//   * @see         __construct()
//   */
//  public function Mdetect_ext($settings = array())
//  {
//    $this->__construct($settings);
//  }

  // --------------------------------------------------------------------

  /**
   * PHP 5 Constructor
   *
   * @access      public
   * @param       mixed     Array with settings or FALSE
   * @return      null
   */
  public function __construct($settings = array())
  {
    // Get global instance
    $this->EE =& get_instance();
    
    // Get site id
    $this->site_id = $this->EE->config->item('site_id');

    // Set Class name
    $this->class_name = ucfirst(get_class($this));
    
    // Set settings
    $this->settings = $this->_get_site_settings($settings);
    
    $this->EE->load->library('uagent_info');
    $this->mdetect = new $this->EE->uagent_info();
  }
  
  /**
   * Run device tests and add those to global variables
   * Executed at the sessions_end extension hook
   *
   * @access      public
   * @return      null
   */
  public function sessions_end($SESS)
  {
    $data = array();
    
    // note to self
    // originally had TRUE:FALSE as 'TRUE':'FALSE', this would be fine for template variables
    // but for global conditional variables {if global_var} would always work even for 'FALSE'
    // TRUE:FALSE provides either 1 or empty.

    // Basic Conditionals
    $data['is_android']                 = $this->mdetect->DetectAndroid() ? TRUE : FALSE;
    $data['is_ios']                     = $this->mdetect->DetectIos() ? TRUE : FALSE;
    $data['is_ipad']                    = $this->mdetect->DetectIpad() ? TRUE : FALSE;
    $data['is_iphone']                  = $this->mdetect->DetectIphone() ? TRUE : FALSE;
    $data['is_iphone_or_ipod']          = $this->mdetect->DetectIphoneOrIpod() ? TRUE : FALSE;
    $data['is_android_iphone_or_ipod']  = ($data['is_android'] || $data['is_iphone_or_ipod']);
    $data['is_mobile']                  = $this->mdetect->DetectMobileLong() ? TRUE : FALSE;
    $data['is_tablet']                  = $this->mdetect->DetectTierTablet() ? TRUE : FALSE;
    
    // Reverse of the conditionals to avoid Advanced Conditional if:else
    foreach($data as $is => $bool)
    {
      $isnt         = str_replace('is_', 'is_not_', $is);
      $data[$isnt]  = ! $bool;
    }
    
    // Is iOS or Android
    if ($data['is_ios'])
      $data['is_ios_or_android'] = TRUE;
    else if ($data['is_android'])
      $data['is_ios_or_android'] = TRUE;
    else
      $data['is_ios_or_android'] = FALSE;
    
    // Is Neither Mobile nor iPad (desktop, tablet, what else?)
    if ($data['is_mobile'])
      $data['neither_mobile_nor_ipad'] = FALSE;
    else if ($data['is_ipad'])
      $data['neither_mobile_nor_ipad'] = FALSE;
    else
      $data['neither_mobile_nor_ipad'] = TRUE;
    
    // Is neither mobile, android, nor ios (desktop, non-android tablets, what else?)
    if ( ($data['is_mobile'] === FALSE) && ($data['is_android'] === FALSE) && ($data['is_ios'] === FALSE) )
      $data['neither_mobile_android_nor_ios'] = TRUE;
    else
      $data['neither_mobile_android_nor_ios'] = FALSE;
    
    // --------------------------------------
    // Finally, add data to global vars
    // Swapping $data and existing global vars makes no difference...
    // --------------------------------------

    $this->EE->config->_global_vars = array_merge($data, $this->EE->config->_global_vars);

    return $SESS;
  }
  
  // --------------------------------------------------------------------

  /**
   * Activate extension
   *
   * @access      public
   * @return      null
   */
  public function activate_extension()
  {
    $this->EE->db->insert('extensions', array(
      'class'    => $this->class_name,
      'method'   => 'sessions_end',
      'hook'     => 'sessions_end',
      'priority' => 1,
      'version'  => $this->version,
      'enabled'  => 'y',
      'settings' => serialize($this->settings)
    ));
  }

  // --------------------------------------------------------------------

  /**
   * Update extension
   *
   * @access      public
   * @param       string    Saved extension version
   * @return      null
   */
  public function update_extension($current = '')
  {
    if ($current == '' OR $current == $this->version)
    {
      return FALSE;
    }

    // init data array
    $data = array();

    // Update to MSM compatible extension settings
    if (version_compare($current, '0.1.214', '<'))
    {
      if ( ! isset($this->settings[$this->site_id]))
      {
        $data['settings'] = serialize(array($this->site_id => $this->settings));
      }
    }

    // Add version to data array
    $data['version'] = $this->version;

    // Update records using data array
    $this->EE->db->where('class', $this->class_name);
    $this->EE->db->update('exp_extensions', $data);
  }

  // --------------------------------------------------------------------

  /**
   * Disable extension
   *
   * @access      public
   * @return      null
   */
  public function disable_extension()
  {
    // Delete records
    $this->EE->db->where('class', $this->class_name);
    $this->EE->db->delete('exp_extensions');
  }
  
  // --------------------------------------------------------------------
  // PRIVATE METHODS
  // --------------------------------------------------------------------

  /**
   * Get current settings from DB
   *
   * @access      private
   * @return      mixed
   */
  private function _get_current_settings()
  {
    $query = $this->EE->db->select('settings')
           ->from('extensions')
           ->where('class', $this->class_name)
           ->limit(1)
           ->get();

    return @unserialize($query->row('settings'));
  }

  // --------------------------------------------------------------------

  /**
   * Get settings for this site
   *
   * @access      private
   * @return      mixed
   */
  private function _get_site_settings($current = array())
  {
    $current = (array) $current;

    return isset($current[$this->site_id]) ? $current[$this->site_id] : array_merge($this->default_settings, $current);
  }
}
// END CLASS

/* End of file ext.mdetect.php */
/* Location: /system/expressionengine/third_party/mdetect/ext.mdetect.php */