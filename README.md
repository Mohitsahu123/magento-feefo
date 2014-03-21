magento-feefo
=============

Magento Plugin for Feefo Integration

* Upload the files under app to your Magento installation,
* Within /app/code/local/Rawnet/Feefo/Model/Observer.php change www.yourdomain.com and yourusername to your own credentials,
* Within /app/code/local/Rawnet/Feefo/Model/Reviews.php change www.yourdomain.com and yourusername to your own credentials,

Usage

To generate reviews within your template

Instantiate the Feefo Module

$reviews = Mage::getModel('feefo/reviews');

Pass the product sku in

$reviews->getReviewData($sku);

Display reviews in the template

echo $reviews->getAllReviews($sku);

To Do

Build an adminstration so Feefo account details can be added from within the main Magento adminstration
