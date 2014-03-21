<?php

class Rawnet_Feefo_Model_Reviews extends Mage_Core_Model_Abstract
{

	protected $feed;
	protected $sku;

	public function getReviewData($sku)
	{
		$this->sku = $sku;
		$xml = 'http://www.feefo.com/feefo/xmlfeed.jsp?logon=www.yourdomain.com&vendorref='.$sku;
		$this->feed = simplexml_load_file($xml);
	}

	public function getReviewsProductPageTop()
	{
		if (isset($this->feed))
		{
			$overallRating = array('reviews' => 0, 'ratings' => 0);
			$counter = 0;
			foreach($this->feed as $product)
			{				
				if ($product->PRODUCTCODE == $this->sku)
				{
					$overallRating['reviews']++;
					$overallRating['ratings'] += $product->HREVIEWRATING;
					$counter++;
				}
			}
		}
		if ($counter > 0) 
		{
			$average = ceil($overallRating['ratings']/$overallRating['reviews']);
			return '<div class="reviewsTop">
	<div class="rating rating'.$average.'"><span></span></div>
	<a id="jumpToReviews" href="#"><span>Reviews</span> ('.$counter.')</a>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$(\'#jumpToReviews\').on(\'click\', function(e) {
		e.preventDefault();
		$(\'html, body\').animate({
			scrollTop: $(\'#feefoReviews\').offset().top
		}, 500);
	});
});
</script>
';
		}
	}

	public function getAllReviews($sku)
	{		
		$overallRating = array('reviews' => 0, 'ratings' => 0);
		$htmlTop = '<div id="feefoReviews"><div class="reviewsCenter">';
		$html = '';
		foreach ($this->feed as $product)
		{			
			if ($product->PRODUCTCODE == $sku)
			{
				$overallRating['reviews']++;
				$overallRating['ratings'] += $product->HREVIEWRATING;
				$class = ($overallRating['reviews']>3) ? ' hide' : '';
				$bloonyFeedbackClass = (!empty($product->FURTHERCOMMENTSTHREAD->POST->VENDORCOMMENT)) ? ' bloonyFeedback' : '';
				$html .= '
	<div class="review'.$class.$bloonyFeedbackClass.'">
		<div class="customerFeedback">
			<div class="rating rating'.$product->HREVIEWRATING.'"><span>'.$product->RATING.'</span></div>
			<h3>'.$product->DESCRIPTION.'</h3>
			<p class="date">'.$product->DATE.'</p>
			<p>'.$product->CUSTOMERCOMMENT.'</p>
		</div>';

				if (!empty($product->FURTHERCOMMENTSTHREAD->POST->VENDORCOMMENT))
				{
					$html .= '
	<div class="vendorFeedback">
		<h2>Perfect Party says on '.$product->FURTHERCOMMENTSTHREAD->POST->DATE.'</h2>
		<p>'.$product->FURTHERCOMMENTSTHREAD->POST->VENDORCOMMENT.'</p>
	</div>';
				}		
		
	$html .= '</div>';
	
			}
		}

		
		$average = ceil($overallRating['ratings']/$overallRating['reviews']);

		$htmlTop .= '<h2>Customer Reviews ('.$overallRating['reviews'].')<h2>';
		$htmlTop .= '<h3 class="average average'.$average.'">Average '.$average.' out of '. $overallRating['reviews'].'</h3>';

		
		if ( $overallRating['reviews'] > 3 )
		{
			$html .= '<div class="readAllReviewsHolder">
	<div class="balloon">
		<span class="reviewsCount">'.$overallRating['reviews'].'</span>
	</div>
	<span class="reviews">Reviews</span>
	<a class="readAllReviews" href="#">Read all reviews</a>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	$(\'.readAllReviews\').on(\'click\', function(e) {
		e.preventDefault();
		$(\'.review\').fadeIn();
		$(\'.readAllReviewsHolder\').fadeOut(function() {
			$(this).remove();
		});
	});
});
</script>
';
		}

		$html .= '</div></div>';
		
		if ($overallRating['reviews']>0)
		{
			return $htmlTop.$html;
		}

	}

}