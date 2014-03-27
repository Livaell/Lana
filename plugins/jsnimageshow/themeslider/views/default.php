<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id: default.php 16844 2012-10-10 09:33:56Z giangnd $
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die( 'Restricted access' );
if (!defined('DS'))
{
	define('DS', DIRECTORY_SEPARATOR);
}
$objJSNUtils = JSNISFactory::getObj('classes.jsn_is_utils');
$url 		 = $objJSNUtils->overrideURL();
$user 		 = JFactory::getUser();
?>
<script type="text/javascript">
	(function($){
		$(document).ready(function ()
		{
			$('#jsn-is-themeslider').tabs();
			$.JSNISThemeSlider.initialize();
			$.JSNISThemeSlider.visual();

			$('#thumbnail-active-state-color-selector').ColorPicker({
				color: $('#thumbnail_active_state_color').val(),
				onShow: function (colpkr) {
					$(colpkr).fadeIn(500);
					return false;
				},
				onHide: function (colpkr) {
					$(colpkr).fadeOut(500);
					return false;
				},
				onChange: function (hsb, hex, rgb) {
					$('#thumbnail_active_state_color').val('#' + hex);
					$('#thumbnail-active-state-color-selector div').css('backgroundColor', '#' + hex);
					$.JSNISThemeSlider.changeValueVisualElement('thumbnailPanel', $('#thumbnail_active_state_color'));
				}
			});

			$('#jsn-slider-preview').stickyfloat({
				   duration: 0
		    });

			$('#click_action').change(function() {
				if ($(this).val() == 'open_image_link') {
					$('#jsn-open-link-in').css('display', 'block');
				} else {
					$('#jsn-open-link-in').css('display', 'none');
				}
			});
			//$('#click_action').trigger('change');
		})
	})(jQuery);
</script>
<table class="jsn-showcase-theme-settings">
	<tr>
		<td id="jsn-theme-parameters-wrapper">
			<div id="jsn-is-themeslider" class="jsn-tabs">
				<ul>
					<li><a href="#themeslider-image-tab"><?php echo JText::_('THEME_SLIDER_IMAGE_PRESENTATION'); ?>
					</a></li>
					<li><a href="#themeslider-caption-tab"><?php echo JText::_('THEME_SLIDER_CAPTION_PRESENTATION'); ?>
					</a></li>
					<li><a href="#themeslider-thumbnail-tab"><?php echo JText::_('THEME_SLIDER_THUMBNAIL_PRESENTATION'); ?>
					</a></li>
					<li><a href="#themeslider-toolbar-tab"><?php echo JText::_('THEME_SLIDER_TOOLBAR_PRESENTATION'); ?>
					</a></li>
					<li><a href="#themeslider-slideshow-tab"><?php echo JText::_('THEME_SLIDER_SLIDESHOW_PRESENTATION'); ?>
					</a></li>
				</ul>
				<div id="themeslider-image-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_TRANSITION_EFFECT_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_TRANSITION_EFFECT_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_TRANSITION_EFFECT_TITLE');?>
									</label>
									<div class="controls">
									<?php echo $lists['imgTransitionEffect']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themeslider-caption-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_CAPTION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_CAPTION_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SHOW_CAPTION_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['captionShowCaption']; ?>
									</div>
								</div>
								<div class="control-group" <?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip" title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_CAPTION_POSITION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_CAPTION_POSITION_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_CAPTION_POSITION_TITLE'); ?></label>
									<div class="controls"><?php echo $lists['captionPosition']; ?></div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_CAPTION_OPACITY_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_CAPTION_OPACITY_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_CAPTION_OPACITY_TITLE'); ?>
									</label>
									<div class="controls">
										<input name="caption_caption_opacity" type="number"
											value="<?php echo $items->caption_caption_opacity; ?>"
											class="informationPanel input-mini" size="5" /> %
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_TITLE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_TITLE_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SHOW_TITLE_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['captionShowTitle']; ?>
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_TITLE_CSS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_TITLE_CSS_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_TITLE_CSS_TITLE'); ?>
									</label>
									<div class="controls">
										<textarea rows="5" name="caption_title_css" class="informationPanel input-xlarge"><?php echo $items->caption_title_css; ?></textarea>
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_DESC_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_DESC_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SHOW_DESC_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['captionShowDescription']; ?>
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_DESC_CSS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_DESC_CSS_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_DESC_CSS_TITLE'); ?>
									</label>
									<div class="controls">
										<textarea rows="5" name="caption_description_css" class="informationPanel input-xlarge"><?php echo $items->caption_description_css; ?></textarea>
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_LINK_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SHOW_LINK_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SHOW_LINK_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['captionShowLink']; ?>
									</div>
								</div>
								<div class="control-group"
								<?php echo (($items->caption_show_caption == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_LINK_CSS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_LINK_CSS_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_LINK_CSS_TITLE'); ?>
									</label>
									<div class="controls">
										<textarea rows="5" name="caption_link_css" class="informationPanel input-xlarge"><?php echo $items->caption_link_css; ?></textarea>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themeslider-thumbnail-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_PRESENTATION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_PRESENTATION_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_THUMBNAIL_PANEL_PRESENTATION_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['thumbnailPanelPresentation']; ?>
									</div>
								</div>
								<div class="control-group" <?php echo (($items->thumbnail_panel_presentation == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_POSITION_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_POSITION_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_THUMBNAIL_PANEL_POSITION_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['thumbnailPanelPosition']; ?>
									</div>
								</div>
								<div class="control-group" <?php echo (($items->thumbnail_panel_presentation == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PRESENTATION_MODE_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PRESENTATION_MODE_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_THUMBNAIL_PRESENTATION_MODE_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['thumbnailPresentationMode']; ?>
									</div>
								</div>
								<div class="control-group" <?php echo (($items->thumbnail_panel_presentation == 'hide') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_ACTIVE_STATE_COLOR_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_THUMBNAIL_PANEL_ACTIVE_STATE_COLOR_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_THUMBNAIL_PANEL_ACTIVE_STATE_COLOR_TITLE'); ?>
									</label>
									<div class="controls">
										<input class="thumbnailPanel input-mini" type="text" size="15"
											value="<?php echo (!empty($items->thumbnail_active_state_color))?$items->thumbnail_active_state_color:'#CC3333'; ?>"
											readonly="readonly" name="thumbnail_active_state_color"
											id="thumbnail_active_state_color" />
										<div class="color-selector"
											id="thumbnail-active-state-color-selector">
											<div style="background-color: <?php echo (!empty($items->thumbnail_active_state_color))?$items->thumbnail_active_state_color:'#CC3333'; ?>"></div>
										</div>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_SLIDER_CLICK_ACTION_TITLE');?>::<?php echo JText::_('THEME_SLIDER_CLICK_ACTION_DESC'); ?>"><?php echo JText::_('THEME_SLIDER_CLICK_ACTION_TITLE');?></label>
										<div class="controls">
											<?php echo $lists['clickAction']; ?>
										</div>
								</div>
								<div id="jsn-open-link-in" class="control-group" <?php echo (($items->click_action == 'no_action') ? 'style="display: none;"' : '');?>>
									<label class="control-label hasTip" title="<?php echo JText::_('THEME_SLIDER_OPEN_LINK_IN_TITLE');?>::<?php echo JText::_('THEME_SLIDER_OPEN_LINK_IN_DESC'); ?>"><?php echo JText::_('THEME_SLIDER_OPEN_LINK_IN_TITLE');?></label>
									<div class="controls">
										<?php echo $lists['openLinkIn']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themeslider-toolbar-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SIDE_ARROWS_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SIDE_ARROWS_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SIDE_ARROWS_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['toolbarNavigationArrowsPresentation']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SLIDE_SLIDESHOW_PLAYER_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SLIDE_SLIDESHOW_PLAYER_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SLIDE_SLIDESHOW_PLAYER_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['toolbarSlideshowPlayerPresentation']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div id="themeslider-slideshow-tab" class="jsn-bootstrap">
					<div class="form-horizontal">
						<div class="row-fluid show-grid">
							<div class="span12">
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SLIDE_TIMMING_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_SLIDE_TIMMING_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_SLIDE_TIMMING_TITLE'); ?>
									</label>
									<div class="controls">
										<input type="number" name="slideshow_slide_timming"
											value="<?php echo $items->slideshow_slide_timming; ?>"
											class="slideshowPanel input-mini" size="5">
											<?php echo JText::_('THEME_SLIDER_SECONDS');?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_AUTO_PLAY_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_AUTO_PLAY_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_AUTO_PLAY_TITLE'); ?>
									</label>
									<div class="controls">
									<?php echo $lists['slideShowAutoPlay']; ?>
									</div>
								</div>
								<div class="control-group">
									<label class="control-label hasTip"
										title="<?php echo htmlspecialchars(JText::_('THEME_SLIDER_PAUSE_ON_MOUSEOVER_TITLE'));?>::<?php echo htmlspecialchars(JText::_('THEME_SLIDER_PAUSE_ON_MOUSEOVER_DESC')); ?>"><?php echo JText::_('THEME_SLIDER_PAUSE_ON_MOUSEOVER_TITLE'); ?>
									</label>
									<div class="controls">
										<?php echo $lists['slideshowPauseOnMouseOver']; ?>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</td>
		<td id="jsn-theme-preview-wrapper">
			<div>
				<?php include dirname(__FILE__).DS.'preview.php'; ?>
			</div>
		</td>
	</tr>
</table>
<!--  important -->
<input
	type="hidden" name="theme_name"
	value="<?php echo strtolower($this->_showcaseThemeName); ?>" />
<input
	type="hidden" name="theme_id"
	value="<?php echo (int) @$items->theme_id; ?>" />
<!--  important -->
<div style="clear: both;"></div>
