<?php

/**
* Get Reviews from Reputation Loop
*
* @return string
*
* @throws \Exception
*/
function getReputationLoopReviews() {
    $url = 'http://test.localfeedbackloop.com/api?apiKey=61067f81f8cf7e4a1f673cd230216112&noOfReviews=10&internal=1&yelp=1&google=1&offset=50&threshold=1';

    if (function_exists('curl_version')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        if (ini_get('open_basedir') == '' && ini_get('safe_mode') == 'Off') {
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        }
        
        $data = curl_exec($ch);
        curl_close($ch);

        return $data;
    }
    else if (file_get_contents(__FILE__) && ini_get('allow_url_fopen')) {
        return file_get_contents($url);
    }
    else {
        throw new Exception('You have neither cUrl installed nor allow_url_fopen activated. Please setup one of those!');
    }
}



/**
*
* @param int     review rating.
* @return string
*
*/
function starRatings($rating) {
	$rating = (int) $rating;
	$stars ='';
	$r = 0;
    for ($i=0; $i < 5; $i++) {
    	if($r < $rating) {
    		$markup = '<i class="fa fa-star active"></i>';
    		$r++;
    	} 
    	else {
    		$markup = '<i class="fa fa-star"></i>';
    	}

    	$stars .= $markup;
    }

    return $stars;
}


$data = getReputationLoopReviews();

if($data) {
	$reputationLoop = json_decode($data, true);
	$business = $reputationLoop['business_info'];
	$reviews = $reputationLoop['reviews'];
	$reviewFrom = array('0'=>'internal', '1'=>'yelp', '2'=>'google');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>ReputationLoop Test Review</title>
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
  <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
  
  <style media="screen" type="text/css">
	body {
		background-color: #f1f1f1;
	}
	.wrapper {
		width: 800px;
		margin: 20px auto 10px;
		padding: 20px;
		background-color: #fff;
		border-radius: 5px;
		box-shadow: 0px 4px 4px -5px
	}
	.business-overview {
		text-align: center;
		border-bottom: 1px solid #f1f1f1;
		margin-bottom: 30px;
		padding-bottom: 20px;
	}
	.business-overview .business-name {
		font-weight: bold;
	}
	.business-overview .overall-rating {
		margin-top: 40px;
	}
	.business-overview .overall-rating p {
		margin: 0;
	}
	.business-overview .overall-rating h1 {
		font-weight: bold;
		font-size: 50px;
		margin: 0;
	}
	.reviews .header {
		margin-bottom: 30px;
		border-bottom: 1px solid rgb(241, 241, 241);
		padding-bottom: 10px;
	}

	.reviews .review {
		margin-bottom: 30px;
		padding-bottom: 20px;
		border-radius: 20px;
		box-shadow: 0px 2px 4px -5px;
	}
	.reviews .review:last-child {
		box-shadow: none;
	}
	.reviews .review .date {
		color: #c8c8c8;
		margin-top: 5px;
	}
	.reviews .review .stars {
		margin-bottom: 10px;
	}
	.reviews .review .description {
		margin-bottom: 20px;
	}
	.reviews .review .name {
		font-weight: bold;
	}
	.reviews .review .stars .fa-star {
		color: #ccc;
		font-size: 20px;
		margin-left: 5px;
	}
	.reviews .review .stars .fa-star:first-child {
		margin-left: 0px;
	}
	.reviews .review .stars .active {
		color: #73B143;
	}


  </style>

</head>

<body>
	<div class="wrapper">
	    <div class="business-overview">
	            <div class="row">
	                <div class="col-xs-6 business-info">
	                    <h2 class="business-name"> <?php echo $business['business_name']; ?> </h2>
	                    <p class="business-address"> 
	                    	<?php echo $business['business_address']; ?> <br> 
	                    	<span class="business-phone"><?php echo $business['business_phone']; ?> <span>
	                    </p>
	                </div>
	                <div class="col-xs-6 overall-rating">
	                    <p> Overall Rating </p>
	                    <h1 class="total-avg-rating"> <?php echo $business['total_rating']['total_avg_rating']; ?> </h1>
	                    <p class="number-of-reviews"> OUT OF <?php echo $business['total_rating']['total_no_of_reviews']; ?> </p>
	                </div>
	            </div>
	    </div>
	    <div class="reviews">
	        <?php if ($reviews) { ?>
	        		<h3 class="header">Reviews</h3>
				<?php foreach($reviews as $review) { ?>
	            <div class="row review">
	                <div class="col-xs-1">
	                	<img src="<?php echo $reviewFrom[$review['review_from']].'_logo.png'; ?>" title="<?php echo ucfirst($reviewFrom[$review['review_from']]); ?>">
	                    
	                </div>
	                <div class="col-xs-6">
	                	<div class="stars"> <?php echo starRatings($review['rating']); ?> </div>
	                	<div class="description"> <?php echo $review['description']; ?> </div>
	                	<div class="name"> <?php echo $review['customer_name'] .' '.$review['customer_last_name'] ?> </div>
	                </div>
	                <div class="col-xs-5 date">
	                    Reviwed on: <?php echo date("Y-m-d", strtotime($review['date_of_submission'])); ?>
	                </div>
	            </div>
	            <?php } ?>
	            <?php } else { ?>
	            	<div class="row review">
	                	<div class="col-xs-12">
	                		<a target="_blank" href="<?php echo $business['external_url']; ?>">
								<strong>Click here to write a review </strong>
							</a>
	                	</div>
	                </div>

				<?php } ?>

	    </div>
    </div>
</body>
</html>