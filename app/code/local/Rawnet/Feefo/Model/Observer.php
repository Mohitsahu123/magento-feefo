<?php

class Rawnet_Feefo_Model_Observer
{
	/**
	 * Push Data into Feefo via Curl
	 * 
	 * 
	 * Triggered on:
	 * - sales_order_place_after
	 * 
	 * @param object $event
	 * @return object $this   
	 */  
	public function pushOrderDataToFeefo($event)
	{		
		$order = $event->getEvent()->getOrder();
		$orderId = $order->getIncrementId();
		$name = $order->getCustomerName();
		$email = $order->getCustomerName();
		$shippingAddress = $order->getShippingAddress()->getData();
		$billingAddress = $order->getBillingAddress()->getData();
		


		foreach ($order->getAllVisibleItems() as $items)
		{
			//The feefo URL 
			$url = 'http://www.feefo.com/feefo/entersaleremotely.jsp';
			$productUrl =  Mage::getModel('catalog/product')->loadByAttribute('sku',$items->getSku())->getProductUrl();
			
			// get the sku from current product
			$sku = $items->getSku();
			// turn the sku into an id
			$id = Mage::getModel('catalog/product')->getIdBySku($sku);
			// see if there are any parents i.e. the product is configurable
			$parentsIds = Mage::getModel('catalog/product_type_configurable')->getParentIdsByChild($id);
			// if it is configurable
			if (!empty($parentsIds))
			{
				$finalId = $parentsIds[0];
				$product = Mage::getModel('catalog/product')->load($finalId);
				$sku = $product->getSku();
			}
			
			$params = array( 
			 'logon' => 'www.yourdomain.com',
			 'password' => 'yourusername', 
			 'email' => $billingAddress['email'], 
			 'name' => $shippingAddress['firstname']. ' ' .$shippingAddress['lastname'], 
			 'date' => date('d/m/Y'), 
			 'description' => $items->getName(), 
			 'productsearchcode' => $sku, 
			 'orderref' => $orderId, 
			 'productlink' => $productUrl, 
			 'customerref' => $orderId.time(), 
			 'amount' => '10.99' 
			); 
			 
			//Build up the query and use curl to execute it. 
			$data = http_build_query($params, '', '&'); 
			$ch=curl_init(); 
			curl_setopt($ch, CURLOPT_URL, $url); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
			curl_setopt($ch, CURLOPT_POST, 1); 
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data); 
			$reply=curl_exec($ch); 
			curl_close($ch); 
		}
	}
}