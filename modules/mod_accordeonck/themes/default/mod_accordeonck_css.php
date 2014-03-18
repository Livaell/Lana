<?php
header('content-type: text/css');
$id = 'ul#'.htmlspecialchars ( $_GET['cssid'] , ENT_QUOTES );
?>

/*--------------------------------------------
--	Default theme Menu Accordeon CK			--
--	This is a blank theme with annotations	--
--	you can fill it like you want			--
--	to put your own CSS						--
--------------------------------------------*/


/* main UL container */
<?php echo $id; ?> {

}

/* style for all links */
<?php echo $id; ?> li {
	background: none;
}

/* style for all anchors */
<?php echo $id; ?> li a {
	background: none;
}

/* style for all link descriptions */
<?php echo $id; ?> li a span.accordeonckdesc {
	display: block;
}

/*--------------------
--	Level 1			--
---------------------*/

/* first level (root) link */
<?php echo $id; ?> li.level1 {

}

/* first level (root) link anchor */
<?php echo $id; ?> li.level1 > a {

}

/* first level (root) link description */
<?php echo $id; ?> li.level1 > a span.accordeonckdesc {

}

/*--------------------
--	Level 2			--
---------------------*/

/* second level link */
<?php echo $id; ?> li.level2 {

}

/* second level link anchor */
<?php echo $id; ?> li.level2 > a {

}

/* second level link description */
<?php echo $id; ?> li.level2 > a span.accordeonckdesc {

}

/*--------------------
--	Level 3 and more --
---------------------*/

/* third and deeper level link */
<?php echo $id; ?> li.level2 li {

}

/* third and deeper link anchor */
<?php echo $id; ?> li.level3 a {

}

/* third and deeper link description */
<?php echo $id; ?> li.level3 a span.accordeonckdesc {

}
