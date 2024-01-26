<?php require_once('Connections/rtconn.php'); ?>
<?php require_once('includes/absolutpath.php'); ?>
<?php require_once('includes/seourl.php'); ?>
<?php if(empty($_GET['loc']) || $_GET['loc'] === NULL): $_GET['loc'] = 0; endif; ?>
<?php if(empty($_GET['pag']) || $_GET['pag'] === NULL && $row_Page['idloc_pag'] === 0): $_GET['pag'] = "mainPage"; endif; ?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

mysql_select_db($database_rtconn, $rtconn);
$query_Locationtop = "SELECT * FROM locations_loc WHERE locations_loc.active_loc=1 ORDER BY locations_loc.order_loc";
$Locationtop = mysql_query($query_Locationtop, $rtconn) or die(mysql_error());
$row_Locationtop = mysql_fetch_assoc($Locationtop);
$totalRows_Locationtop = mysql_num_rows($Locationtop);

mysql_select_db($database_rtconn, $rtconn);
$query_Page = "SELECT * FROM pages_pag WHERE slugurl_pag = '".$_GET["pag"]."'";
$Page = mysql_query($query_Page, $rtconn) or die(mysql_error());
$row_Page = mysql_fetch_assoc($Page);
$totalRows_Page = mysql_num_rows($Page);

mysql_select_db($database_rtconn, $rtconn);
$query_Addresses = "SELECT * FROM officeloction_loc";
$Addresses = mysql_query($query_Addresses, $rtconn) or die(mysql_error());
$row_Addresses = mysql_fetch_assoc($Addresses);
$totalRows_Addresses = mysql_num_rows($Addresses);

mysql_select_db($database_rtconn, $rtconn);
$query_Emmergencypg = "SELECT * FROM emergency_ecy";
$Emmergencypg = mysql_query($query_Emmergencypg, $rtconn) or die(mysql_error());
$row_Emmergencypg = mysql_fetch_assoc($Emmergencypg);
$totalRows_Emmergencypg = mysql_num_rows($Emmergencypg);

mysql_select_db($database_rtconn, $rtconn);
$query_Emmergencyft = "SELECT * FROM emergency_ecy";
$Emmergencyft = mysql_query($query_Emmergencyft, $rtconn) or die(mysql_error());
$row_Emmergencyft = mysql_fetch_assoc($Emmergencyft);
$totalRows_Emmergencyft = mysql_num_rows($Emmergencyft);

mysql_select_db($database_rtconn, $rtconn);
$query_Socialtop = "SELECT * FROM sitenetwork_snk WHERE active_snk = 1 ORDER BY order_snk ASC";
$Socialtop = mysql_query($query_Socialtop, $rtconn) or die(mysql_error());
$row_Socialtop = mysql_fetch_assoc($Socialtop);
$totalRows_Socialtop = mysql_num_rows($Socialtop);

mysql_select_db($database_rtconn, $rtconn);
$query_Socialft = "SELECT * FROM sitenetwork_snk WHERE active_snk = 1 ORDER BY order_snk ASC";
$Socialft = mysql_query($query_Socialft, $rtconn) or die(mysql_error());
$row_Socialft = mysql_fetch_assoc($Socialft);
$totalRows_Socialft = mysql_num_rows($Socialft);

$colname_Phone = "-1";
if (isset($_GET['loc'])) {
  $colname_Phone = (get_magic_quotes_gpc()) ? $_GET['loc'] : addslashes($_GET['loc']);
}
mysql_select_db($database_rtconn, $rtconn);
$query_Phone = sprintf("SELECT * FROM phones_phe WHERE idloc_phe = %s AND active_phe=1", GetSQLValueString($colname_Phone, "int"));
$Phone = mysql_query($query_Phone, $rtconn) or die(mysql_error());
$row_Phone = mysql_fetch_assoc($Phone);
$totalRows_Phone = mysql_num_rows($Phone);

$colname_Phone247 = "-1";
if (isset($_GET['loc'])) {
  $colname_Phone247 = (get_magic_quotes_gpc()) ? $_GET['loc'] : addslashes($_GET['loc']);
}
mysql_select_db($database_rtconn, $rtconn);
$query_Phone247 = sprintf("SELECT * FROM phones_phe WHERE idloc_phe = %s AND active_phe=1", GetSQLValueString($colname_Phone247, "int"));
$Phone247 = mysql_query($query_Phone247, $rtconn) or die(mysql_error());
$row_Phone247 = mysql_fetch_assoc($Phone247);
$totalRows_Phone247 = mysql_num_rows($Phone247);

$colname_Sliders = "-1";
if (isset($_GET["loc"])) {
  $colname_Sliders = (get_magic_quotes_gpc()) ? $_GET["loc"] : addslashes($_GET["loc"]);
}
mysql_select_db($database_rtconn, $rtconn);
$query_Sliders = sprintf("SELECT * FROM banners_ban WHERE banners_ban.idloc_ban=%s AND banners_ban.visible_ban=1 ORDER BY banners_ban.order_ban", GetSQLValueString($colname_Sliders, "int"));
$Sliders = mysql_query($query_Sliders, $rtconn) or die(mysql_error());
$row_Sliders = mysql_fetch_assoc($Sliders);
$totalRows_Sliders = mysql_num_rows($Sliders);

mysql_select_db($database_rtconn, $rtconn);
$query_Locations = "SELECT * FROM locations_loc WHERE locations_loc.active_loc=1 ORDER BY locations_loc.order_loc";
$Locations = mysql_query($query_Locations, $rtconn) or die(mysql_error());
$row_Locations = mysql_fetch_assoc($Locations);
$totalRows_Locations = mysql_num_rows($Locations);

mysql_select_db($database_rtconn, $rtconn);
$query_Types = "SELECT * FROM rooftypes_res WHERE rooftypes_res.active_res=1 ORDER BY rooftypes_res.order_res";
$Types = mysql_query($query_Types, $rtconn) or die(mysql_error());
$row_Types = mysql_fetch_assoc($Types);
$totalRows_Types = mysql_num_rows($Types);

mysql_select_db($database_rtconn, $rtconn);
$query_Services1 = "SELECT whatwedo_wdo.name_wdo, whatwedo_wdo.colnro_wdo FROM whatwedo_wdo WHERE whatwedo_wdo.colnro_wdo=1 ORDER BY whatwedo_wdo.order_wdo";
$Services1 = mysql_query($query_Services1, $rtconn) or die(mysql_error());
$row_Services1 = mysql_fetch_assoc($Services1);
$totalRows_Services1 = mysql_num_rows($Services1);

mysql_select_db($database_rtconn, $rtconn);
$query_Services2 = "SELECT whatwedo_wdo.name_wdo, whatwedo_wdo.colnro_wdo FROM whatwedo_wdo WHERE whatwedo_wdo.colnro_wdo=2 ORDER BY whatwedo_wdo.order_wdo";
$Services2 = mysql_query($query_Services2, $rtconn) or die(mysql_error());
$row_Services2 = mysql_fetch_assoc($Services2);
$totalRows_Services2 = mysql_num_rows($Services2);

mysql_select_db($database_rtconn, $rtconn);
$query_Services3 = "SELECT whatwedo_wdo.name_wdo, whatwedo_wdo.colnro_wdo FROM whatwedo_wdo WHERE whatwedo_wdo.colnro_wdo=3 ORDER BY whatwedo_wdo.order_wdo";
$Services3 = mysql_query($query_Services3, $rtconn) or die(mysql_error());
$row_Services3 = mysql_fetch_assoc($Services3);
$totalRows_Services3 = mysql_num_rows($Services3);

mysql_select_db($database_rtconn, $rtconn);
$query_Services4 = "SELECT whatwedo_wdo.name_wdo, whatwedo_wdo.colnro_wdo FROM whatwedo_wdo WHERE whatwedo_wdo.colnro_wdo=4 ORDER BY whatwedo_wdo.order_wdo";
$Services4 = mysql_query($query_Services4, $rtconn) or die(mysql_error());
$row_Services4 = mysql_fetch_assoc($Services4);
$totalRows_Services4 = mysql_num_rows($Services4);

$colname_Emergency = "-1";
if (isset($_GET["loc"])) {
  $colname_Emergency = (get_magic_quotes_gpc()) ? $_GET["loc"] : addslashes($_GET["loc"]);
}
mysql_select_db($database_rtconn, $rtconn);
$query_Emergency = sprintf("SELECT * FROM emergency_ecy WHERE emergency_ecy.idloc_ecy=%s", GetSQLValueString($colname_Emergency, "int"));
$Emergency = mysql_query($query_Emergency, $rtconn) or die(mysql_error());
$row_Emergency = mysql_fetch_assoc($Emergency);
$totalRows_Emergency = mysql_num_rows($Emergency);
?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?php echo $row_Page['description_pag']; ?>">
	<meta name="keywords" content="<?php echo $row_Page['keywords_pag']; ?>">
    <meta name="author" content="RoofToday.com">
	<link rel="apple-touch-icon" sizes="180x180" href="/favicons/apple-touch-icon.png">
	<link rel="icon" type="image/png" sizes="32x32" href="/favicons/favicon-32x32.png">
	<link rel="icon" type="image/png" sizes="16x16" href="/favicons/favicon-16x16.png">
	<link rel="manifest" href="/favicons/site.webmanifest">
	<link rel="mask-icon" href="/favicons/safari-pinned-tab.svg" color="#5bbad5">
	<link rel="shortcut icon" href="/favicons/favicon.ico">
	<meta name="msapplication-TileColor" content="#2d89ef">
	<meta name="msapplication-config" content="/favicons/browserconfig.xml">
	<meta name="theme-color" content="#ffffff">
	
	<meta property="og:title" content="<?php echo $row_Page['title_pag']; ?>">
	<meta property="og:site_name" content="RoofToday.com">
	<meta property="og:type" content="website">
	<meta property="og:description" content="<?php echo $row_Page['description_pag']; ?>">
	<meta property="og:image" content="https://www.rooftoday.com/images/roof-today.png">
	<meta property="og:url" content="https://www.rooftoday.com/">
	<meta property="og:image:alt" content="<?php echo $row_Page['header_pag']; ?>">
	
    <title><?php echo $row_Page['title_pag']; ?></title>
	<script src="https://kit.fontawesome.com/0e27bfd207.js" crossorigin="anonymous"></script>
    <link rel="canonical" href="https://www.rooftoday.com/">
    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet" type="text/css">
    <!-- Custom styles for this template -->
	<link rel="stylesheet" type="text/css" href="slick/slick.css"/>
	<!-- Add the new slick-theme.css if you want the default styling-->
	<link rel="stylesheet" type="text/css" href="slick/slick-theme.css"/>
    <link href="css/master.css" rel="stylesheet">
	<script src="js/jquery.min.js"></script>
	<script src="js/bootstrap.bundle.min.js"></script>
	<script type="text/javascript" src="slick/slick.min.js"></script>
	 <script type="text/javascript">
    $(document).ready(function(){
     $('.listlocation').slick({
  dots: false,
  arrows: false,
  autoplay: true,
  infinite: true,
  speed: 300,
  slidesToShow: 5,
  slidesToScroll: 1,
  responsive: [
    {
      breakpoint: 1280,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 1,
        infinite: true,
        dots: false
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 1
      }
    },
    {
      breakpoint: 480,
      settings: {
	  	speed: 100,
        slidesToShow: 2,
        slidesToScroll: 1
      }
    }
    // You can unslick at a given breakpoint now by adding:
    // settings: "unslick"
    // instead of a settings object
  ]
});
    });
  </script>
<script>
$(document).ready(function(){
// Select all links with hashes
$('a[href*="#"]')
  // Remove links that don't actually link to anything
  .not('[href="#"]')
  .not('[href="#0"]')
  .click(function(event) {
    // On-page links
    if (
      location.pathname.replace(/^\//, '') == this.pathname.replace(/^\//, '') 
      && 
      location.hostname == this.hostname
    ) {
      // Figure out element to scroll to
      var target = $(this.hash);
      target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
      // Does a scroll target exist?
      if (target.length) {
        // Only prevent default if animation is actually gonna happen
        event.preventDefault();
        $('html, body').animate({
          scrollTop: target.offset().top
        }, 1000, function() {
          // Callback after animation
          // Must change focus!
          var $target = $(target);
          $target.focus();
          if ($target.is(":focus")) { // Checking if the target was focused
            return false;
          } else {
            $target.attr('tabindex','-1'); // Adding tabindex for elements not focusable
            $target.focus(); // Set focus again
          };
        });
      }
    }
  });
});
</script>
<style>
.banner-bg {
    background-image: url('images/banners/<?php echo $row_Page['pagebanner_pag']; ?>');
    background-attachment: scroll;
    background-position: center;
    background-repeat: no-repeat;
    background-size: cover;
    padding: 1rem;
}
</style>
</head>

<body>
<section class="sectop">
	<div class="container">
		<div class="row">
			<div class="col-12 col-sm-7 col-md-8 text-center text-sm-left"><i class="far fa-clock text-danger"></i> 24/7 Emergency Roof Repairs</div>
			<div class="col-12 col-sm-5 col-md-4 text-center text-sm-right">
			<ul class="list-inline mb-0">
			  	<?php do { ?>
			  	  <li class="list-inline-item"><a href="<?php echo $row_Socialtop['netlink_snk']; ?>" class="text-dark"><i class="<?php echo $row_Socialtop['iconcode_snk']; ?>"></i></a></li>
			  	  <?php } while ($row_Socialtop = mysql_fetch_assoc($Socialtop)); ?></ul>
			</div>
		</div>
	</div>
</section>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top py-3">
	<div class="container">
  <a class="navbar-brand" href="<?php echo $host; ?>"><img src="<?php echo $host; ?>images/roof-today.png" alt="<?php echo $row_Page['title_pag']; ?>" class="img-fluid"></a>
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarText">
    <ul class="navbar-nav ml-auto mr-auto">
      <li class="nav-item">
        <a class="nav-link" href="#section1">ROOFING SERVICES</a>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="locDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">CHOOSE YOUR LOCATION</a>
		<div class="dropdown-menu" aria-labelledby="locDropdown">
          <?php do { ?>
            <a class="dropdown-item" href="<?php echo $host. strtolower($row_Locationtop['locstate_loc']). '/'. seoUrl($row_Locationtop['loccounty_loc']). '/'. seoUrl($row_Locationtop['loccity_loc']);?>"><?php echo $row_Locationtop['menulink_loc']; ?></a>
            <?php } while ($row_Locationtop = mysql_fetch_assoc($Locationtop)); ?>
        </div>
      </li>
	  <li class="my-2 pl-4 pr-3">
        <a class="btn btn-success" href="<?php echo $host; ?>roof-services/free-estimate">FREE ESTIMATE</a>
      </li>
    </ul>
    <span class="navbar-text">
      <div class="callwrapper my-0">
	  	<div class="calltext">CALL / TEXT</div>
		<div class="callphone"><?php echo $row_Phone['phone_phe']; ?></div>
	  </div>
    </span>
  </div>
  </div>
</nav>
<section class="d-block">
<div id="rooftopCarousel" class="carousel slide carousel-fade" data-ride="carousel">
<div class="carousel-caption d-block">
	<h1 class="banner-header"><?php echo $row_Sliders['title_ban']; ?></h1>
	<h3 class="banner-subheader"><?php echo $row_Sliders['subheader_ban']; ?></h3>
	<div class="top-search">
	<form action="<?php echo $host. 'roof-services/free-estimate'; ?>" method="post" name="frmaddress" id="frmaddress">
	<div class="panel panel-default">
        <div class="panel-body frmsch">
			<div class="form-search">
				<div class="row">
					<div class="col-8 col-sm-7 col-md-8 col-lg-9">
						<div class="form-group-custom">
							<input type="text" name="autocomplete" id="autocomplete" class="form-control" placeholder="Start with your address." onFocus="geolocate()" required>
						</div>
					</div>
				<div class="col-4 col-sm-5 col-md-4 col-lg-3">
				<div class="button-wrapper d-none d-sm-block"><button type="submit" name="addressbtn" id="addressbtn" class="btn btn-success btn-block btn-form">GET YOUR QUOTE</button></div>
				<div class="button-wrapper d-block d-sm-none"><button type="submit" name="addressbtn" id="addressbtn" class="btn btn-success btn-block btn-form">GO!</button></div>
				</div>
				</div>
			</div>
		</div>
    </div>
	<div id="address">
	<input type="hidden" name="street_number" id="street_number">
	<input type="hidden" name="route" id="route">
	<input type="hidden"  name="locality" id="locality">
	<input type="hidden" name="administrative_area_level_1" id="administrative_area_level_1">
	<input type="hidden" name="postal_code" id="postal_code">
	<input type="hidden" name="country" id="country">
	</div>
	</form>
	</div>
	</div>
  <div class="carousel-inner">
    <?php do { ?>
      <div class="carousel-item<?php if($row_Sliders['order_ban']==1): echo ' active'; else: echo''; endif; ?>"> <img src="<?php echo $host; ?>images/sliders/<?php echo $row_Sliders['banner_ban']; ?>" alt="First slide" width="1920" height="1032" class="img-fluid"> </div>
      <?php } while ($row_Sliders = mysql_fetch_assoc($Sliders)); ?></div>
</div>
</section>
<section class="bg-primary">
<div class="container">
	<div class="row">
		<div class="p-2 text-white text-center col-12"><strong>OUR ROOFING SERVICE LOCATIONS</strong></div>
	</div>
</div>
</section>
<section class="loclist">
  <div class="listlocation">
      <?php do { ?>
      <div>
        <div class="float-left mr-3 text-green iconwrapper"><i class="fas fa-map-marker-alt"></i></div>
        <div class="float-left">
          <a class="text-dark" href="<?php echo $host. strtolower($row_Locations['locstate_loc']). '/'. seoUrl($row_Locations['loccounty_loc']). '/'. seoUrl($row_Locations['loccity_loc']);?>"><h5 class="my-0"><strong><?php echo $row_Locations['loccity_loc']; ?></strong></h5></a>
          <p my-0 p-0>All of <?php echo $row_Locations['loccounty_loc']; ?> County</p></div>
      </div>
        <?php } while ($row_Locations = mysql_fetch_assoc($Locations)); ?></div>
</section>
	<section class="my-5">
		<div class="container"><?php echo $row_Locations['loccity_loc']; ?>
			<div class="row">
				<div class="col-12 col-sm-12 col-md-12 col-lg-7 col-xl-6">
				<h2><?php echo $row_Page['header_pag']; ?></h2>
				<h3><?php echo $row_Page['subheader_pag']; ?></h3>
					<?php echo $row_Page['body_pag']; ?>
				</div>
				<div class="col-12 col-sm-12 col-md-12 col-lg-5 col-xl-6">
					<img src="<?php echo $host; ?>images/pages/<?php echo $row_Page['pagepicture_pag']; ?>" alt="<?php echo $row_Page['title_pag']; ?>" class="img-fluid">
				</div>
			</div>
		</div>
	</section>
	<?php 
// Show IF Conditional region1 
if (@$row_Page['showlmore_pag'] == 1) {
?>
      <section class="container my-5">
        <div class="row d-none d-md-block">
          <div class="col-12 alert247">
            <div class="alert-text"><?php echo $row_Emergency['message_ecy']; ?></div>
            <div class="alert-phone text-center">
              <div class="alertemmergency">
                <div class="alertclock"><i class="<?php echo $row_Emergency['icon_ecy']; ?>"></i></div>
                <div class="alert24hours"><?php echo $row_Emergency['header_ecy']; ?></div>
                <div class="arlertecy"><?php echo $row_Emergency['emergency_ecy']; ?></div>
                <div class="alertphone"><?php echo $row_Phone247['phone_phe']; ?></div>
              </div>
            </div>
          </div>
        </div>
        <div class="row d-block d-md-none">
          <div class="col-12 bg-green">
            <div class="alert-text"><?php echo $row_Emergency['message_ecy']; ?></div>
          </div>
          <div class="col-12 bg-blue">
            <div class="alert-phone text-center">
              <div class="alertemmergency">
                <div class="alertclock"><i class="<?php echo $row_Emergency['icon_ecy']; ?>"></i></div>
                <div class="alert24hours"><?php echo $row_Emergency['header_ecy']; ?></div>
                <div class="arlertecy"><?php echo $row_Emergency['emergency_ecy']; ?></div>
                <div class="alertphone"><?php echo $row_Phone247['phone_phe']; ?></div>
              </div>
            </div>
          </div>
        </div>
      </section>
      <?php } 
// endif Conditional region1
?><?php 
// Show IF Conditional region2 
if (@$row_Page['showtypes_pag'] == 1) {
?>
      <section class="my-5">
        <div class="container">
          <div class="row">
            <div class="col-12 text-center"><span class="text-lightsm">WE SPECIALIZE IN</span></div>
            <div class="col-12 text-center mb-3">
              <h3 class="headline">ALL ROOF TYPES & DESIGNS</h3>
            </div>
          </div>
          <div class="row">
            <?php do { ?>
              <div class="col-12 col-sm-6 col-md-6 col-lg-3 mb-4">
                <div class="card"> <img src="<?php echo $host. 'images/types/'. $row_Types['thumbnail_res']; ?>" alt="<?php echo $row_Types['rooftype_res']; ?>" class="card-img-top">
                    <div class="card-body">
                      <h5 class="card-title"><?php echo $row_Types['rooftype_res']; ?></h5>
                      <p class="card-text"><?php echo $row_Types['shortdesc_res']; ?></p>
                    </div>
                </div>
              </div>
              <?php } while ($row_Types = mysql_fetch_assoc($Types)); ?>
          </div>
        </div>
      </section>
      <?php } 
// endif Conditional region2
?>
	  <?php 
// Show IF Conditional region3 
if (@$row_Page['showrepairs_pag'] == 1) {
?>
        <section class="my-5">
          <div class="container">
            <div class="row mb-3">
			  <div class="col-sm-12 col-md-12 text-center"><a href="<?php echo $host; ?>roof-services/free-estimate" class="btn btn-warning btn-lg mb-4">GET A QUOTE</a></div>
              <div class="col-sm-12 col-md-12 text-center"><span class="text-lightsm">WHAT WE DO</span></div>
              <div class="col-sm-12 col-md-12 text-center">
                <h3 class="headline">ROOFING SERVICES & REPAIRS</h3>
              </div>
            </div>
            <div class="row" id="section1">
              <div class="col-sm-6 col-md-6 col-lg-3">
                <ul class="list-unstyled">
                  <?php do { ?>
                    <li><i class="far fa-check-square text-muted"></i> <?php echo $row_Services1['name_wdo']; ?></li>
                    <?php } while ($row_Services1 = mysql_fetch_assoc($Services1)); ?>
                </ul>
              </div>
              <div class="col-sm-6 col-md-6 col-lg-3">
                <ul class="list-unstyled">
                  <?php do { ?>
                    <li><i class="far fa-check-square text-muted"></i> <?php echo $row_Services2['name_wdo']; ?></li>
                    <?php } while ($row_Services2 = mysql_fetch_assoc($Services2)); ?>
                </ul>
              </div>
              <div class="col-sm-6 col-md-6 col-lg-3">
                <ul class="list-unstyled">
                  <?php do { ?>
                    <li><i class="far fa-check-square text-muted"></i> <?php echo $row_Services3['name_wdo']; ?></li>
                    <?php } while ($row_Services3 = mysql_fetch_assoc($Services3)); ?>
                </ul>
              </div>
              <div class="col-sm-6 col-md-6 col-lg-3">
                <ul class="list-unstyled">
                  <?php do { ?>
                    <li><i class="far fa-check-square text-muted"></i> <?php echo $row_Services4['name_wdo']; ?></li>
                    <?php } while ($row_Services4 = mysql_fetch_assoc($Services4)); ?>
                </ul>
              </div>
            </div>
          </div>
        </section>
        <?php } 
// endif Conditional region3
?>
    <footer class="mt-5 bg-dark text-white">
	<div class="container">
      <div class="row">
        <div class="col-12 col-sm-12 col-md-4 text-center text-md-left">
			<img src="<?php echo $host; ?>images/roof-today.png" alt="<?php echo $row_Page['title_pag']; ?>" class="img-fluid mt-3">
			<h5 class="mt-3"><strong>Our Locations:</strong></h5>
			<ul class="list-unstyled small">
            <?php do { ?>
            <li class="address-footer"><?php echo $row_Addresses['location_loc']; ?></li>
            <?php } while ($row_Addresses = mysql_fetch_assoc($Addresses)); ?>
		  </ul>
        </div>
        <div class="col-12 col-sm-6 col-md-4">
			<div class="row">
				<div class="col-12">
					<div class="footeremmergency text-center text-md-right">
						<div class="footerclock"><i class="<?php echo $row_Emmergencyft['icon_ecy']; ?>"></i></div>
						<div class="footer24hours"><?php echo $row_Emmergencyft['header_ecy']; ?></div>
						<div class="footerecy"><?php echo $row_Emmergencyft['emergency_ecy']; ?></div>
						<div class="footerphone"><?php echo $row_Phone247['phone_phe']; ?></div>
					</div>
				</div>
			</div>
        </div>
        <div class="col-12 col-sm-6 col-md-4 text-center text-md-right">
			<div class="footerrevolution">
			<img src="images/roof-revolution.png" alt="" class="img-fluid">
			</div>
        </div>
		<div class="col-12">
		<div class="row my-3">
			<div class="col-12 col-sm-7 col-md-6 text-center text-md-left copyright-wrapper">Copyright &copy; <?php echo date("Y"); ?> RoofToday.com</div>
			<div class="col-12 col-sm-5 col-md-6 text-center text-md-right">
			  <ul class="list-inline mb-0">
			  	<?php do { ?>
			  	  <li class="list-inline-item mb-0"><a href="<?php echo $row_Socialft['netlink_snk']; ?>" class="text-white"><i class="<?php echo $row_Socialft['iconcode_snk']; ?> fa-2x"></i></a></li>
			  	  <?php } while ($row_Socialft = mysql_fetch_assoc($Socialft)); ?></ul>
			</div>
		</div>
		</div>
      </div>
	</div>
    </footer>
<?php require_once('includes/autocomplete.php'); ?>
</body>
</html>
<head>
	<script async custom-element="amp-ad" src="https://cdn.ampproject.org/v0/amp-ad-0.1.js"></script>
<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5889948157508068"
     crossorigin="anonymous"></script>
</head>
<body>
	<amp-ad width="100vw" height="320"
     type="adsense"
     data-ad-client="ca-pub-5889948157508068"
     data-ad-slot="6933244247"
     data-auto-format="rspv"
     data-full-width="">
  <div overflow=""></div>
</amp-ad>
	<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-5889948157508068"
     crossorigin="anonymous"></script>
<!-- Berkode -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-5889948157508068"
     data-ad-slot="6933244247"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script>
</body>
<?php
mysql_free_result($Page);

mysql_free_result($Addresses);

mysql_free_result($Emmergencyft);

mysql_free_result($Socialtop);

mysql_free_result($Socialft);

mysql_free_result($Phone);

mysql_free_result($Phone247);

mysql_free_result($Sliders);

mysql_free_result($Types);

mysql_free_result($Services1);

mysql_free_result($Services2);

mysql_free_result($Services3);

mysql_free_result($Services4);

mysql_free_result($Emergency);

mysql_free_result($Locationtop);

mysql_free_result($Locations);

mysql_free_result($Emmergencypg);
?>
