<?php
/**
 * @author JoomlaShine.com Team
 * @copyright JoomlaShine.com
 * @link joomlashine.com
 * @package JSN ImageShow - Theme Classic
 * @version $Id$
 * @license GNU/GPL v2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
<div id="jsn-slider-preview">
	<div class="jsn-slider-preview-wrapper">
		<div class="jsn-slider-preview-container">
			<div class="jsn-slider-preview-background"
				onclick="jQuery.JSNISThemeSlider.toogleTab(0);"></div>
			<div class="slider-control"
				onclick="jQuery.JSNISThemeSlider.toogleTab(3);">
				<div class="play_pause_button"></div>
			</div>
			<div class="slider-slide-arrow">
				<span class="prev_button"
					onclick="jQuery.JSNISThemeSlider.toogleTab(3);"></span> <span
					class="next_button" onclick="jQuery.JSNISThemeSlider.toogleTab(3);"></span>
			</div>
			<div class="slider-caption"
				onclick="jQuery.JSNISThemeSlider.toogleTab(1);">
				<p class="slider-title">Lorem ipsum dolor sit amet, consectetur
					adipiscing elit</p>
				<p class="slider-description">Donec auctor eros vel ligula sagittis
					venenatis. Pellentesque et risus dui, et varius orci. Suspendisse
					pulvinar commodo lacus vel pharetra</p>
				<p class="slider-link">
					<a href="javascript:void(0);" class="slider-a-link">http://joomlashine.com</a>
				</p>
			</div>
			<div class="paginations"
				onclick="jQuery.JSNISThemeSlider.toogleTab(2);">
				<span class="info_slide_dots"> <span class="image_number">1</span> <span
					class="image_number">2</span> <span class="image_number">3</span> <span
					class="image_number image_number_select">4</span> <span
					class="image_number">5</span> <span class="image_number">6</span> <span
					class="image_number">7</span> <span class="image_number">8</span> <span
					class="image_number">9</span> <span class="image_number">10</span>
					<span class="image_number">11</span> </span> <span
					class="info_slide" onclick="jQuery.JSNISThemeSlider.toogleTab(2);">
					<span class="image_number">1</span> <span class="image_number">2</span>
					<span class="image_number">3</span> <span
					class="image_number image_number_select">4</span> <span
					class="image_number">5</span> <span class="image_number">6</span> <span
					class="image_number">7</span> <span class="image_number">8</span> </span>
			</div>
		</div>
	</div>
</div>
