<?php
/**
 * Simple PopUp - Joomla Plugin
 * 
 * @package    Joomla
 * @subpackage Plugin
 * @author Anders Wasén
 * @link http://wasen.net/
 * @license		GNU/GPL, see LICENSE.php
 * plg_simplefilegallery is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
 
// Import library dependencies
jimport('joomla.plugin.plugin');

define('SPU_PATH', dirname(__FILE__).DIRECTORY_SEPARATOR.'simplepopup');

class plgContentSimplePopUp extends JPlugin
{
   /**
    * Constructor
    *
    * For php4 compatability we must not use the __constructor as a constructor for
    * plugins because func_get_args ( void ) returns a copy of all passed arguments
    * NOT references.  This causes problems with cross-referencing necessary for the
    * observer design pattern.
    */
	
	
    function plgContentSimplePopUp( &$subject, $config )
    {
			
			parent::__construct( $subject, $config );
 
            // load plugin parameters
            $this->_plugin = &JPluginHelper::getPlugin( 'content', 'simplepopup' );
            //$this->params = new JParameter( $this->_plugin->params );
			
    }
 
	function onContentPrepare($context, &$article, &$params, $page = 0)
	{
		JPlugin::loadLanguage( 'plg_content_simplepopup', JPATH_ADMINISTRATOR );		//Load the plugin language file - not in contructor in case plugin called by third party components
		$application = &JFactory::getApplication();

		$this->spuindex = -1;
		$this->spuindexinit = 0;
		
		$regex = "#{simplepopup\b(.*?)\}(.*?){/simplepopup}#s";
		
		$article->text = preg_replace_callback( $regex, array('plgContentSimplePopUp', 'render'), $article->text, -1, $count );
		
	}
	
	
	function render( &$matches )
    {
		
		$html = '';
		
		$this->spuindex += 1;
		
		$spu_debug = $this->params->get( 'spu_debug', '0' );
		
		if ($spu_debug === '1') {
			echo '<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
			echo '~~~~~~~~~~~~~ Simple PopUp - DEBUGGING ~~~~~~~~~~~~~<br/>';
			echo '~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
			$tmp = '';
			$ix = 0;
			do {
				if (!isset($matches[$ix])) break;
				$tmp = $matches[$ix];
				echo '<br/>['.$ix.']='.$tmp;
				$ix++;
			} while (strlen($tmp) > 0);
			
			echo '<br/>spuindex = ' . $this->spuindex.'<br/>';
		}
		
		// Message is always in index zero. Remove brackets with RegExp
		$bracket_reg = '/{+\s*\/*\s*([A-Z][A-Z0-9]*)\b[^}]*\/*\s*}+/i';
		$this->popupmsg = preg_replace( $bracket_reg, '', $matches[0] );
		
		// Clear all "session" vars
		$this->popup = 'true';
		$this->popupurl = '';
		$this->popupmulti = 'false';
		$this->popupname = '';
		$this->popupanchor = '';
		$this->popuprel = '';
		$this->popuphidden = '';
		$this->popuptitle = '';
		$this->resizeOnWindowResize = 'true';
		
		// Check if there are any other parameters
		if (isset($matches[1])) {
			// Get all params and put into array
			$spuparams = explode(' ', $matches[1]);
			for ($ix = 0; $ix < count($spuparams); $ix++) {
				if ($spu_debug === '1') echo "spuparams[$ix]=".$spuparams[$ix]."<br/>";
				// Get rid of &nbsp;
				$spuvals = trim(str_replace(chr(160), '', $spuparams[$ix]));

				$pos = strpos($spuvals, '=');
				if ($pos !== false) {
				
					$spuvals = explode('=', $spuvals);
					// Not sure where those 194 chars comes from but better get rid of them!
					$spuval = trim(str_replace(chr(194), '', $spuvals[1]));
					
					switch (strtolower($spuvals[0])) {
						case 'hidden':
							$this->popuphidden = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popuphidden=".$this->popuphidden."<br/>";
							break;
						case 'title':
							// Title may contain spaces, search for ending quote
							
							// This is the first word
							$title = str_replace('\'', '', str_replace('"', '', $spuval));
							
							for ($jx = $ix; $jx < count($spuparams); $jx++) {
							
								// Make sure we don't steal any parameter
								$pos = strpos($spuparams[$jx], '=');
								if ($pos === false) {
									
									// Move one forward for main loop as we found a single word
									$ix++;
									
									if (strpos($spuparams[$jx], '"') >= 0) {
										// It's the last word
										$title .= ' '.str_replace('\'', '', str_replace('"', '', $spuparams[$jx]));
									} else {
										$title .= ' '.$spuparams[$jx];
									}
								}
							}
							
							$this->popuptitle = $title;
							if ($spu_debug === '1') echo "popuptitle=".$this->popuptitle."<br/>";
							break;
						case 'gallery':
							$this->popuprel = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popuprel=".$this->popuprel."<br/>";
							break;
						case 'link':
							$this->popupanchor = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popupanchor=".$this->popupanchor."<br/>";
							break;
						case 'url':
							$this->popupurl = str_replace('\'', '', str_replace('"', '', $spuval));
							if ($spu_debug === '1') echo "popupurl=".$this->popupurl."<br/>";
							break;
						case 'multi':
							$spuval = str_replace('\'', '', str_replace('"', '', $spuval));
							if (strtolower($spuval) === 'true') $this->popupmulti = 'true';
							if ($spu_debug === '1') echo "popupmulti=".$this->popupmulti."<br/>";
							break;
						case 'name':
							$this->popupname = str_replace('\'', '', str_replace('"', '', $spuval));
							//No Spaces in name!
							$this->popupname = str_replace(' ', '', $this->popupname);
							if ($spu_debug === '1') echo "popupname=[".$this->popupname."]<br/>";
							break;
						case 'popup':
							$spuval = str_replace('\'', '', str_replace('"', '', $spuval));
							if (strtolower($spuval) === 'false') $this->popup = 'false';
							if ($spu_debug === '1') echo "popup=".$this->popup."<br/>";
							break;
					}
				}
			}
			
		}
		
		//echo $this->popupmulti;
		//echo $this->popupurl;
		
		// Prevent pop-up if SFU is uploading, else SFU info pop-up is blocked by this...
		if (isset($_SESSION['sfu_mid'])) {
			$mid = $_SESSION["sfu_mid"];
			if (isset($_FILES["uploadedfile$mid"]["name"])) {
				if ($_FILES["uploadedfile$mid"]["name"] > 0) {
					$this->popup = 'false';
				}
			}
		}
		
		if (plgContentSimplePopUp::getMobileBrowser()) $this->resizeOnWindowResize = 'false';
		
		if ($spu_debug === '1') {
			if ($this->resizeOnWindowResize == 'false') {
				echo "Mobile browser detected!<br/>";
			} else {
				echo "Standard browser is used!<br/>";
			}
		}
		
		ob_start();

		// This is only a test to call external PHP file
		//$html .= '<div>'.SPUAjaxServlet::getPopUp('HEJ!').'</div>';
	
		if (strlen($this->popupanchor) > 0) {

			$rel = '';
			$hidden = '';
			$title = '';
			
			$this->spuindexinit += 1;
			if (strlen($this->popuprel) > 0) $rel = ' rel="'.$this->popuprel.'"';
			if (strtolower($this->popuphidden) === 'true') $hidden = ' style="display: none;"';
			if (strlen($this->popuptitle) > 0) $title = ' title="'.$this->popuptitle.'"';
			
			echo '<a id="'.$this->popupanchor.'"'.$rel.$hidden.$title.' href="#spu'.$this->popupanchor.'">'.$this->popupmsg.'</a>', chr(10);
		}
			
        if (is_readable(SPU_PATH.DIRECTORY_SEPARATOR.'default.php') && $this->spuindex == $this->spuindexinit && strlen($this->popupanchor) == 0) {

			include(SPU_PATH.DIRECTORY_SEPARATOR.'default.php');
			// If first FancyBox is not to pop-up, create it as secondary
			if ($this->popup === 'false') {
				echo plgContentSimplePopUp::addPopUp($this->spuindex);
			}
		} elseif ($this->spuindex > $this->spuindexinit) {

			//include(SPU_PATH.DIRECTORY_SEPARATOR.'default-multi.php');
			echo plgContentSimplePopUp::addPopUp($this->spuindex);
		} else {

			//JError::raiseError(500, JText::_('Failed to load default.php'));
		}
		$html = ob_get_clean();
        
		if ($spu_debug === '1') echo '<br/>~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~<br/>';
		
        return $html;
		
    }
	
	function getMobileBrowser() {
		$ret = false;
		$useragent = $_SERVER['HTTP_USER_AGENT'];
		if(preg_match('/android.+mobile|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i',$useragent)||preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i',substr($useragent,0,4))) $ret = true;
		return $ret;
	
	}


	function addPopUp($idx) {
		
		$html2 = '';
		$spu_autodimensions = $this->params->get( 'spu_autodimensions', 'false' );
		
		if (strlen($this->popupname) == 0) $this->popupname = 'spuSimplePoPup'.$idx;
		
		$spu_boxwidth = $this->params->get( 'spu_boxwidth', '400' );
		$spu_boxheight = $this->params->get( 'spu_boxheight', 'auto' );
		$spu_autodimensions = $this->params->get( 'spu_autodimensions', 'false' );
		
		echo '<script language="javascript" type="text/javascript">', chr(10);
		echo '<!--', chr(10);
		echo '	jQuery(document).ready(function() {', chr(10);

		echo '		var autodim = '.$spu_autodimensions.';', chr(10);
				
		echo '		jQuery("#'.$this->popupname.'").fancybox({', chr(10);
		echo '			\'titlePosition\'		: \'inside\',', chr(10);
		echo '			\'transitionIn\'		: \'elastic\',', chr(10);
		echo '			\'transitionOut\'		: \'elastic\',', chr(10);
		echo '			\'hideOnOverlayClick\': false,', chr(10);
		echo '			\'hideOnContentClick\': false,', chr(10);
		echo '			\'showCloseButton\'	: true,', chr(10);
		if ($spu_autodimensions === 'false') {
			echo '			\'autoDimensions\'	: false,', chr(10);
			echo '			\'width\'	: \''.$spu_boxwidth.'\',', chr(10);
			echo '			\'height\'	: \''.$spu_boxheight.'\',', chr(10);
		} else {
			echo '			\'autoDimensions\'	: true,', chr(10);
		}
		echo '			\'titleShow\'	: true,', chr(10);
		echo '			\'resizeOnWindowResize\'	: '.$this->resizeOnWindowResize.',', chr(10);
		echo '			\'centerOnScroll\'	: '.$this->resizeOnWindowResize.',', chr(10);
		echo '			\'titlePosition\'	: \'inside\'', chr(10);
		
					 
		echo '		});', chr(10);
					
		echo '	});', chr(10);

		echo '	-->', chr(10);
		echo '</script>', chr(10);
		
		//echo '<a id="'.$this->popupname.'" href="#spu'.$this->popupname.'">111</a>', chr(10);
		
		echo '<div style="display: none;">', chr(10);
		echo '	<div id="spu'.$this->popupname.'" class="spu_content">', chr(10);
		
		if(strlen($this->popupurl) > 0) {
			$pagecontent = file_get_contents($this->popupurl, FILE_TEXT);
			$pagecontent = mb_convert_encoding($pagecontent, 'UTF-8', mb_detect_encoding($pagecontent, 'UTF-8, ISO-8859-1', true));

			if ($pagecontent === false) $pagecontent = 'URL ('.$this->popupurl.') failed to load. Please inform the site administrator!';
			$this->popupmsg = $pagecontent;
		}
		
		echo '		'.$this->popupmsg, chr(10);
		echo '	</div>', chr(10);
		echo '</div>', chr(10);
		
		echo $html2;
		
		return false;
	
	}
 
}
?>
