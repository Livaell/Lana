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

$titlegradient = explode('-',$data->get('titlegradient'));
$context['titlegradientenabled'] = $titlegradient[0];
$context['titlegradientstart'] = '#'.$titlegradient[1];
$context['titlegradientstop'] = '#'.$titlegradient[2];

$font = new NextendParseFont($data->get('titlefont'));
$context['titlefont'] = '";'.$font->printTab().'"';

    
$context['margin'] = NextendParse::parseUnit($data->get('margin'), ' ');

$borderradius = NextendParse::parse($data->get('borderradius'));
$borderradiusunit = $borderradius[4];
$context['borderradiustr'] = $borderradius[1].$borderradiusunit;
$context['borderradiusbr'] = $borderradius[2].$borderradiusunit;
$context['borderradiusbl'] = $borderradius[3].$borderradiusunit;
$context['borderradiustl'] = $borderradius[0].$borderradiusunit;


for($i = 1; $i < 6; $i++){
    
    $context['level'.$i.'margin'] = '"'.NextendParse::parseUnit($data->get('level'.$i.'margin'), ' ').'"';
    
    $context['level'.$i.'padding'] = '"'.NextendParse::parseUnit($data->get('level'.$i.'padding'), ' ').'"';
    
    $borderradius = NextendParse::parse($data->get('level'.$i.'borderradius'));
    $borderradiusunit = $borderradius[4];
    $context['level'.$i.'borderradiustr'] = $borderradius[1].$borderradiusunit;
    $context['level'.$i.'borderradiusbr'] = $borderradius[2].$borderradiusunit;
    $context['level'.$i.'borderradiusbl'] = $borderradius[3].$borderradiusunit;
    $context['level'.$i.'borderradiustl'] = $borderradius[0].$borderradiusunit;

    $bg = $data->get('level'.$i.'bg');
    $gradient = explode('-',$bg);
    $context['level'.$i.'bg0enabled'] = $gradient[0];
    $context['level'.$i.'bg0start'] = '#'.$gradient[1];
    $context['level'.$i.'bg0stop'] = '#'.$gradient[2];
    
    $specialbg = NextendParse::parse($data->get('level'.$i.'bgspecial'));

    for($j = 0; $j < 3; $j++){
        $gradient = explode('-',$specialbg[$j]);
        $context['level'.$i.'bg'.($j+1).'enabled'] = $gradient[0];
        $context['level'.$i.'bg'.($j+1).'start'] = '#'.$gradient[1];
        $context['level'.$i.'bg'.($j+1).'stop'] = '#'.$gradient[2];
    }
    
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