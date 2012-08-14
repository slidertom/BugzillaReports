<?php
/*
	Copyright by Tomas Rapkauskas bugzilla_base/license.txt 
	All rights reserved.
	To use this component please contact slidertom@gmail.com to obtain a license.
*/

class CProduct
{
	public $m_id;
	public $m_name;
	public $m_open_bug_count;
};

function get_product_opened_bugs_count($dbh, $product_id)
{
	$result = 0;
	try
	{
		$sql = "SELECT COUNT(*) FROM bugs where (bug_status='NEW' OR bug_status='ASSIGNED' OR bug_status='REOPENED') AND product_id ='$product_id'";
		$result = $dbh->query($sql);
	}
	catch(PDOException $e)
	{
		echo $e->getMessage();
		return 0;
	}
	
	foreach ($result as $row)
	{
		return $row['COUNT(*)'];
	}

	return $result;
}

function products_get(&$dbh)
{
	$sql = "SELECT * FROM products";
	$qr  = $dbh->query($sql);
	$products = array();
	
	foreach ($qr as $row)
	{
		$product                   = new CProduct();
		$product->m_id             = $row['id'];
		$product->m_name           = $row['name'];
		$product->m_open_bug_count = get_product_opened_bugs_count($dbh, $product->m_id);
		$products[$product->m_id]  = $product;
	}
	return $products;
}

function products_to_combo(&$products)
{
	$first_id = -1;
	foreach($products as $product)
	{
		$id = $product->m_id;
		if ( $first_id == -1 )
		{
			$first_id = $id;
		}
		
		echo "<option value=$id>$product->m_name (bugs count $product->m_open_bug_count)</option>";
	}
	
	return $first_id;
}

// returns selected product value in the combo box
function products_create_combo($dbh)
{
	$products = products_get($dbh);
	
	echo "<select name='Product' id='Product'>";
	$first_id = products_to_combo($products);
	echo"</select>";
	
	if ( count($products) > 0 )
	{
		return $first_id;
	}
	
	return -1;
}

function get_product_id_by_name($products, $product_name)
{
	foreach ($products as $product)
		if ($product_name == $product->m_name)
			return $product->m_id;
	
	return NULL;
}

?>