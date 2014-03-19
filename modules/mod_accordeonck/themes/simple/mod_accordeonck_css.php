<?php
header('content-type: text/css');
$id = 'ul#'.htmlspecialchars ( $_GET['cssid'] , ENT_QUOTES );
?>

/*--------------------------------------------
--	Simple theme Menu Accordeon CK			--
--	This is a simple theme with annotations	--
--	you can fill it like you want			--
--	to put your own CSS						--
--------------------------------------------*/

/* main UL container */
<?php echo $id; ?> {
    padding: 5px;
    margin: 0;
}

/* style for all links */
<?php echo $id; ?> li {
    border-bottom: 1px solid #ddd;
    margin: 0;
    text-align: left;
    list-style: none;
    color: #666;
	background: none;
}

/* style for all links from the second level */
<?php echo $id; ?> li ul li {
    list-style-type : square;
    margin: 0 0 0 20px;
	border-bottom: none;
	border-top: 1px solid #ddd;
}

/* style for all anchors */
<?php echo $id; ?> li a {
    margin: 0;
    color: #555;
    text-align: left;
    display: block;
    padding-bottom: 7px;
    padding-left: 15px;
    padding-right: 4px;
    padding-top: 7px;
    text-decoration: none;
	background: none;
}

/* style for all anchors on mouseover */
<?php echo $id; ?> li a:hover, <?php echo $id; ?> ul li a:focus {
    color: #000;
}

/* style for all link descriptions */
<?php echo $id; ?> li a span.accordeonckdesc {
	display: block;
}