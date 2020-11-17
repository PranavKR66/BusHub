<?php
   require 'vendor/autoload.php';

  use Aws\DynamoDb\DynamoDbClient;
  use Aws\S3\S3Client;
  use Aws\DynamoDb\Marshaler;

  $sdk = new Aws\Sdk([
    'region'   => 'us-east-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$s3 = $sdk->createS3();
$marshaler = new Marshaler();

?>
<html>
<head>
<style>
p.inline {display: inline-block;}
span { font-size: 13px;}
</style>
<style type="text/css" media="print">
    @page 
    {
        size: auto;   /* auto is the initial value */
        margin: 0mm;  /* this affects the margin in the printer settings */

    }
</style>
</head>
<body>
	<div style="margin-left: 5%">
		<?php
		#include 'barcode128.php';
		$email = $_POST['email'];
		$name  = $_POST['name'];
		$phone = $_POST['phone'];
		#echo "<p class='inline'><span ><b>Email: $email</b></span>".bar128(stripcslashes($_POST['phone']))."<span ><b>NAME: ".$name." </b><span></p>&nbsp&nbsp&nbsp&nbsp";
		
		$barcode = new \Com\Tecnick\Barcode\Barcode();

		$tableName = 'bushubtab';

		$params = ['TableName' => $tableName,'Select' => 'COUNT'];

		$result = $dynamodb->scan($params);
    	        $count = current($result)['Count'];

    		$index = $count + 1;
		
		$bobj = $barcode->getBarcodeObj('C128C', substr((string)$index.(string)$phone,0,6), -3, -50, 'black', array(0, 0, 0, 0));
	        $imageData = $bobj->getPngData();

    		$bucketName = 'bushubbucket';
    		$fileformat = '.png';

		$data = ['index' => $index,'email' => $email,'name' => $name,'phone' => $phone,];
		$dynamodb->putItem(['TableName' => $tableName,'Item'      => $marshaler->marshalItem($data)]);
		$s3->putObject(['Bucket' => $bucketName,'Key'    => (string)$index.$fileformat,'Body'   => $imageData,'ACL'    => 'public-read',]);

		?>
	</div>
	<h1 style = "text-align: center;">Congratulation! Your Ticket Has Been Confirmed!</h1>
</body>
</html>
