<?php
/*------------------------------------------------------------------------
# author    Roland Soos
# copyright Copyright (C) 2013 Nextendweb.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-3.0.txt GNU/GPL
-------------------------------------------------------------------------*/
defined('_JEXEC') or die('Restricted access');
?><?php
nextendimport('nextend.image.color');
nextendimport('nextend.parse.font');

$font = new NextendParseFont($data->get('titlefont'));
$context['titlefont'] = '";'.$font->printTab().'"';
    
$context['margin'] = NextendParse::parseUnit($data->get('margin'), ' ');

for($i = 1; $i < 6; $i++){
    
    $context['level'.$i.'margin'] = '"'.NextendParse::parseUnit($data->get('level'.$i.'margin'), ' ').'"';
    
    $context['level'.$i.'padding'] = '"'.NextendParse::parseUnit($data->get('level'.$i.'padding'), ' ').'"';
    
    $border = NextendParse::parse($data->get('level'.$i.'border'));
    $context['level'.$i.'borderstyle'] = $border[6];
    unset($border[6]);
    $context['level'.$i.'bordercolor'] = '#'.$border[5];
    unset($border[5]);
    $context['level'.$i.'borderwidth'] = NextendParse::parseUnit($border, ' ');
    
    
    
    $minus = NextendParse::parse($data->get('level'.$i.'minus'));
    $context['level'.$i.'minusimage'] = '"'.$minus[0].'"';
    $context['level'.$i.'minusposition'] = $minus[1];
    $context['level'.$i.'minuscolor'] = '"'.$minus[2].'"';
    $context['level'.$i.'minuscolorize'] = '"'.$minus[3].'"';
    
    $plus = NextendParse::parse($data->get('level'.$i.'plus'));
    $context['level'.$i.'plusimage'] = '"'.$plus[0].'"';
    $context['level'.$i.'plusposition'] = $plus[1];
    $context['level'.$i.'pluscolor'] = '"'.$plus[2].'"';
    $context['level'.$i.'pluscolorize'] = '"'.$plus[3].'"';
    
    $font = new NextendParseFont($data->get('level'.$i.'textfont'));
    $context['level'.$i.'font-text'] = '";'.$font->printTab().'"';
    $font->mixinTab('Active');
    $context['level'.$i.'font-active'] = '";'.$font->printTab('Active').'"';
    $font->mixinTab('Link');
    $context['level'.$i.'font-link'] = '";'.$font->printTab('Link').'"';
    $font->mixinTab('Hover');
    $context['level'.$i.'font-hover'] = '";'.$font->printTab('Hover').'"';
}
