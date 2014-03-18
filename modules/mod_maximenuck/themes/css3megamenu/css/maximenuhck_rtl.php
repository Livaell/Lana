<?php
header('content-type: text/css');
$id = htmlspecialchars ( $_GET['monid'] , ENT_QUOTES );
?>

/*-----------------------------------------------------------------------------------------------------------
	This theme is largely inspired by the Mega menu tutorial on net.tutsplus.com :
	http://net.tutsplus.com/tutorials/html-css-techniques/how-to-build-a-kick-butt-css3-mega-drop-down-menu/

	Ce theme est largement inspire du tutoriel de Mega menu sur net.tutsplus.com
	http://net.tutsplus.com/tutorials/html-css-techniques/how-to-build-a-kick-butt-css3-mega-drop-down-menu/
-------------------------------------------------------------------------------------------------------------*/

.clr {clear:both;}

/*---------------------------------------------
---	 	menu container						---
----------------------------------------------*/

/* menu */
div#<?php echo $id; ?> {
	font-size:14px;
	line-height:21px;
	text-align:right;
        zoom:1;
}

/* container style */
div#<?php echo $id; ?> ul.maximenuck {
    overflow: visible !important;
    display: block !important;
    float: none !important;
    visibility: visible !important;
	opacity: 1 !important;
    /*position: static !important;*/
    list-style:none;
    /*width:940px; */
    margin:0 auto;
    /*height:43px;*/
    height: auto;
    padding:0px 20px 0px 20px;
    -moz-border-radius: 10px;
    -webkit-border-radius: 10px;
    border-radius: 10px;
    background: #014464;
    background: -moz-linear-gradient(top,  #0272a7 0%, #013953 100%);
    background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,#0272a7), color-stop(100%,#013953));
    background: -webkit-linear-gradient(top,  #0272a7 0%,#013953 100%);
    background: -o-linear-gradient(top,  #0272a7 0%,#013953 100%);
    background: -ms-linear-gradient(top,  #0272a7 0%,#013953 100%);
    background: linear-gradient(top,  #0272a7 0%,#013953 100%);
    border: 1px solid #002232;
    -moz-box-shadow:inset 0px 0px 1px #edf9ff;
    -webkit-box-shadow:inset 0px 0px 1px #edf9ff;
    box-shadow:inset 0px 0px 1px #edf9ff;
    text-align: right;
    zoom: 1;
}

div#<?php echo $id; ?> ul.maximenuck:after {
    content: " ";
    display: block;
    height: 0;
    clear: both;
    visibility: hidden;
    font-size: 0;
}

/*---------------------------------------------
---	 	Root items - level 1				---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 {
    background : none;
    list-style : none;
    border: 1px solid transparent;
    /*float:right;*/
    text-align:center;
    padding: 4px 9px 2px 9px;
    margin-left: 10px;
    margin-top: 2px;
    cursor: pointer;
    vertical-align: middle;
    box-shadow: none;
    filter: none;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active {
	border: 1px solid #777777;
	background: #F4F4F4;
	background: -moz-linear-gradient(top, #F4F4F4, #EEEEEE);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#F4F4F4), to(#EEEEEE));
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1 > span.separator {
	font-size:14px;
	color: #EEEEEE;
	display:block;
	text-decoration:none;
	text-shadow: 1px 1px 1px #000;
	min-height : 34px;
    outline : none;
    background : none;
    border : none;
    padding : 0;
    white-space: normal;
}

/* parent item on mouseover (if subemnus exists) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent:hover,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent:hover {
	-moz-border-radius: 5px 5px 0px 0px;
	-webkit-border-radius: 5px 5px 0px 0px;
	border-radius: 5px 5px 0px 0px;
}

/* item color on mouseover */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover > a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active > a span.titreck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1:hover > span.separator,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.active > span.separator {
    color : #161616;
	text-shadow: 1px 1px 1px #ffffff;
}

/* arrow image for parent item */
div#<?php echo $id; ?> ul.maximenuck li.level1.parent > a,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent > span.separator {
	padding-left:21px;
	background:url("../images/drop.gif") no-repeat left 8px;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.parent:hover > a,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent:hover > span.separator {
	background:url("../images/drop.gif") no-repeat left 8px;
}

/* arrow image for submenu parent item */
div#<?php echo $id; ?> ul.maximenuck li.level1.parent li.parent > a,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent li.parent > span.separator,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.parent:hover > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.parent.active > a {
	padding-left:21px;
	background:url("../images/drop-left.gif") no-repeat left 8px;
}

/* styles for right position */
div#<?php echo $id; ?> ul.maximenuck li.align_right,
div#<?php echo $id; ?> ul.maximenuck li.menu_right {
	float:right !important;
	margin-right:0px !important;
}

div#<?php echo $id; ?> ul.maximenuck li.align_right div.floatck,
div#<?php echo $id; ?> ul.maximenuck li div.floatck.fixRight {
	right:auto;
	right:-1px;
	top:auto;
	-moz-border-radius: 5px 0px 5px 5px;
    -webkit-border-radius: 5px 0px 5px 5px;
    border-radius: 5px 0px 5px 5px;
}


/* arrow image for submenu parent item to open right */
div#<?php echo $id; ?> ul.maximenuck li.level1.parent div.floatck.fixRight li.parent > a,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent div.floatck.fixRight li.parent > span.separator,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right li.parent > a,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right li.parent > span.separator {
	padding-right:21px;
	background:url("../images/drop-right.gif") no-repeat right 8px;
}

/* margin for right elements that rolls to the right */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck.fixRight,
div#<?php echo $id; ?> ul.maximenuck li.level1.parent.menu_right div.floatck div.floatck  {
    margin-right : 180px;
}

div#<?php echo $id; ?> ul.maximenuck li div.floatck.fixRight{
	-moz-border-radius: 5px 0px 5px 5px;
    -webkit-border-radius: 5px 0px 5px 5px;
    border-radius: 5px 0px 5px 5px;
}


/*---------------------------------------------
---	 	Sublevel items - level 2 to n		---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li div.floatck ul.maximenuck2,
div#<?php echo $id; ?> ul.maximenuck2{
    background : transparent;
    margin : 0 !important;
    padding : 0 !important;
    border : none !important;
    box-shadow: none !important;
    width : 100%; /* important for Chrome and Safari compatibility */
    position: static !important;
    overflow: visible !important;
    display: block !important;
    float: none !important;
    visibility: visible !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.maximenuck,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck{
	font-size:12px;
	position:static;
	text-shadow: 1px 1px 1px #ffffff;
	padding: 5px 0px;
	margin: 0px 0px 4px 0px;
	float:none !important;
	text-align:right;
	background : none;
    list-style : none;
	display: block !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.maximenuck:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck:hover{
	background: transparent;
}

/* all links styles */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck span.separator,
div#<?php echo $id; ?> ul.maximenuck2 a,
div#<?php echo $id; ?> ul.maximenuck2 li.maximenuck span.separator {
	font-size:14px;
	font-weight : normal;
	color: #a1a1a1;
	display:block;
	text-decoration:none;
	text-transform : none;
	/*text-shadow: 1px 1px 1px #000;*/
    outline : none;
    background : none;
    border : none;
    padding : 0 5px;
    white-space: normal;
}

/* submenu link */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li a,
div#<?php echo $id; ?> ul.maximenuck2 li a {
	color:#015b86;
	text-shadow: 1px 1px 1px #ffffff;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 a,
div#<?php echo $id; ?> ul.maximenuck2 a {
	font-size:12px;
	color:#161616;
	display: block;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > h2 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li:hover > h3 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 li.active > a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > h2 a,
div#<?php echo $id; ?> ul.maximenuck2 li:hover > h3 a,
div#<?php echo $id; ?> ul.maximenuck2 li.active > a{
	color:#029feb;
	background: transparent;
}


/* link image style */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck > a img {
    margin : 3px;
    border : none;
}

/* img style without link (in separator) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck img {
    border : none;
}

/* item title */
div#<?php echo $id; ?> span.titreck {
    /*text-transform : none;
    font-weight : normal;
    font-size : 14px;
    line-height : 18px;*/
    text-decoration : none;
    min-height : 17px;
    float : none !important;
    float : right;
}

/* item description */
div#<?php echo $id; ?> span.descck {
    display : block;
    text-transform : none;
    font-size : 10px;
    text-decoration : none;
    height : 12px;
    line-height : 12px;
    float : none !important;
    float : right;
}

/* submenus container */
div#<?php echo $id; ?> div.floatck {
	width : 180px; /* default width */
	margin: 2px -10px 0 0px;
	text-align:right;
	padding:5px 5px 0 5px;
	border:1px solid #777777;
	border-top:none;
	background:#F4F4F4;
	background: -moz-linear-gradient(top, #EEEEEE, #BBBBBB);
	background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#EEEEEE), to(#BBBBBB));
	-moz-border-radius: 5px 0px 5px 5px;
	-webkit-border-radius: 5px 0px 5px 5px;
	border-radius: 5px 0px 5px 5px;
    filter: none;
}

/*---------------------------------------------
---	 	Columns management					---
----------------------------------------------*/

/* child blocks position (from level2 to n) */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck div.floatck {
    margin : -30px 180px 0 0;
	-moz-border-radius: 5px;
	-webkit-border-radius: 5px;
	border-radius: 5px;
	border:1px solid #777777;
}

div#<?php echo $id; ?> ul.maximenuck li div.floatck div.maximenuck2 {
    width : 180px; /* default width */
	margin: 0;
	padding: 0;
	float: right !important;
}


/* h2 title */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h2 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h2 span.separator,
div#<?php echo $id; ?> ul.maximenuck2 h2 a,
div#<?php echo $id; ?> ul.maximenuck2 h2 span.separator {
	font-size:21px;
	font-weight:400;
	letter-spacing:-1px;
	margin:7px 0 14px 0;
	padding-bottom:14px;
	border-bottom:1px solid #666666;
	line-height:21px;
	text-align:right;
}

/* h3 title */
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h3 a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck ul.maximenuck2 h3 span.separator,
div#<?php echo $id; ?> ul.maximenuck2 h3 a,
div#<?php echo $id; ?> ul.maximenuck2 h3 span.separator {
	font-size:14px;
	margin:7px 0 14px 0;
	padding-bottom:7px;
	border-bottom:1px solid #888888;
	line-height:21px;
	text-align:right;
}

/* paragraph */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li p,
div#<?php echo $id; ?> ul.maximenuck2 li p {
	line-height:18px;
	margin:0 0 10px 0;
	font-size:12px;
	text-align:right;
}




/* image shadow with specific class */
div#<?php echo $id; ?> ul.maximenuck .imgshadow { /* Better style on light background */
	background:#FFFFFF !important;
	padding:4px;
	border:1px solid #777777;
	margin-top:5px;
	-moz-box-shadow:0px 0px 5px #666666;
	-webkit-box-shadow:0px 0px 5px #666666;
	box-shadow:0px 0px 5px #666666;
}

/* blackbox style */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox {
	background-color:#333333 !important;
	color: #eeeeee;
	text-shadow: 1px 1px 1px #000;
	padding:4px 6px 4px 6px !important;
	margin: 0px 4px 4px 4px !important;
	-moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    border-radius: 5px;
	-webkit-box-shadow:inset 0 0 3px #000000;
	-moz-box-shadow:inset 0 0 3px #000000;
	box-shadow:inset 0 0 3px #000000;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox:hover {
	background-color:#333333 !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox a,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox a {
	color: #fff;
	text-shadow: 1px 1px 1px #000;
	display: inline !important;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.blackbox:hover > a,
div#<?php echo $id; ?> ul.maximenuck2 li.blackbox:hover > a{
	text-decoration: underline;
}

/* greybox style */
div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.greybox,
div#<?php echo $id; ?> ul.maximenuck2 li.greybox {
	background:#f0f0f0 !important;
	border:1px solid #bbbbbb;
	padding: 4px 6px 4px 6px !important;
	margin: 0px 4px 4px 4px !important;
	-moz-border-radius: 5px;
    -webkit-border-radius: 5px;
    -khtml-border-radius: 5px;
    border-radius: 5px;
}

div#<?php echo $id; ?> ul.maximenuck li ul.maximenuck2 li.greybox:hover,
div#<?php echo $id; ?> ul.maximenuck2 li.greybox:hover {
	background:#ffffff !important;
	border:1px solid #aaaaaa;
}


/*---------------------------------------------
---	 	Module in submenus					---
----------------------------------------------*/

/* module title */
div#<?php echo $id; ?> ul.maximenuck div.maximenuck_mod > div > h3,
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod > div > h3 {
    width : 100%;
    font-weight : bold;
	color: #555;
	border-bottom: 1px solid #555;
	text-shadow: 1px 1px 1px #000;
	font-size: 16px;
}

div#<?php echo $id; ?> div.maximenuck_mod {
    width : 100%;
    padding : 0;
    white-space : normal;
}

div#<?php echo $id; ?> div.maximenuck_mod div.moduletable {
    border : none;
    background : none;
}

div#<?php echo $id; ?> div.maximenuck_mod  fieldset{
    width : 100%;
    padding : 0;
    margin : 0 auto;
    overflow : hidden;
    background : transparent;
    border : none;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a {
    border : none;
    margin : 0;
    padding : 0;
    display : inline;
    background : transparent;
    font-weight : normal;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod a:hover {

}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod ul {
    margin : 0;
    padding : 0;
    width : 100%;
    background : none;
    border : none;
    text-align : right;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod li {
    margin : 0 0 0 15px;
    padding : 0;
    background : none;
    border : none;
    text-align : right;
    font-size : 11px;
    float : none;
    display : block;
    line-height : 20px;
    white-space : normal;
}

/* login module */
div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod #form-login ul {
    right : 0;
    margin : 0;
    padding : 0;
    width : 100%;
}

div#<?php echo $id; ?> ul.maximenuck2 div.maximenuck_mod #form-login ul li {
    margin : 2px 0;
    padding : 0 5px;
    height : 20px;
    background : transparent;
}



/*---------------------------------------------
---	 	Fancy styles (floating cursor)		---
----------------------------------------------*/

div#<?php echo $id; ?> .maxiFancybackground {
    list-style : none;
    padding: 0 !important;
    margin: 0 !important;
    border: none !important;
}

div#<?php echo $id; ?> .maxiFancybackground .maxiFancycenter {
    border-top: 1px solid #fff;
}



/*---------------------------------------------
---	 	Button to close on click			---
----------------------------------------------*/

div#<?php echo $id; ?> span.maxiclose {
    color: #fff;
}

/*---------------------------------------------
---	 Stop the dropdown                  ---
----------------------------------------------*/

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.nodropdown div.floatck,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck div.floatck li.maximenuck.nodropdown div.floatck,
div#<?php echo $id; ?> .maxipushdownck div.floatck div.floatck {
    position: static;
    background:  none;
    border: none;
    left: auto;
    margin: 3px;
}

div#<?php echo $id; ?> ul.maximenuck li.level1.parent ul.maximenuck2 li.maximenuck.nodropdown li.maximenuck,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.nodropdown li.maximenuck {
    background: none;
    text-indent: 5px;
}

div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck.parent.nodropdown > a,
div#<?php echo $id; ?> ul.maximenuck li.maximenuck.level1.parent ul.maximenuck2 li.maximenuck.parent.nodropdown > span.separator,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.parent.nodropdown > a,
div#<?php echo $id; ?> .maxipushdownck ul.maximenuck2 li.maximenuck.parent.nodropdown > span.separator {
    background:  none;
}

/*---------------------------------------------
---	 Full width				                ---
----------------------------------------------*/

div#<?php echo $id; ?> li.fullwidth > div.floatck {
margin: 0;
padding: 0;
width: 100% !important;
}