<?php

echo $action = $_POST['action'];

parse_str($_POST['dataform'], $hasil);


//echo "<br/>";
//echo "First Name: ".$hasil['firstName']."<br/>";
//echo "Last Name: ".$hasil['lastName']."<br/>";
//echo "CC Number: ".$hasil['cc-number']."<br/>";
//echo "Billing :".implode(',',$hasil['billing'])."<br/>";

$gambarku = $_FILES["fotoku"];

$ccnumber = trim($hasil['ccnumber']);

if (!empty($gambarku["name"]) and !empty($ccnumber)){
	$namafile = $gambarku["name"];		//nama filenya
	preg_match("/([^\.]+$)/", $namafile, $ext);		//Regex: mencari string sesudah titik terakhir, saved in array ext
	$file_ext = strtolower($ext[1]);
	$namafilebaru = $hasil['ccnumber'].".".$ext[1];	//nama file barunya [ccnumber].png
    $file = $gambarku["tmp_name"];						//source filenya
    //perform the upload operation
	$extensions= array("jpeg","jpg","png");				//extensi file yang diijinkan
	//Kirim pesan error jika extensi file yang diunggah tidak termasuk dalam extensions
	$errors = array();
	if(in_array($file_ext,$extensions) === false)
	 $errors[] = "Extensi yang diperbolehkan jpeg atau png.";

	//Kirim pesan error jika ukuran file > 500kB
	$file_size = $gambarku['size'];
	if($file_size > 2097152)
	 $errors[] = "Ukuran file harus lebih kecil dari 2MB.";

	//Upload file
	if(empty($errors)){
		if(move_uploaded_file($file, "uploads/" . $namafilebaru))
			echo "Uploaded dengan nama $namafilebaru";
	}
}else echo $errors[] = "Lengkapi nomor kartu kredit dan gambarnya. ";
echo "<br/>";

if(!empty($errors)){
	echo "Error : ";
	foreach ($errors as $val)
		echo $val;
}

if($action == 'create')
{
	$sql= "INSERT INTO `tbl_user` VALUES ('$hasil[firstname]','$hasil[lastname]','$hasil[username]','$hasil[email]','$hasil[address]','$hasil[address2]','$hasil[ccname]','$hasil[ccnumber]','$hasil[ccexpire]','$hasil[cvv]','{$namafilebaru}')";
}
elseif ($action == 'update')
{
	$sql = "UPDATE tbl_user SET firstname =  '$hasil[firstname]', lastName = '$hasil[lastname]',foto = '$namafilebaru' where firstname = '$hasil[firstname]'";
}
elseif($action == 'delete')
{
	$sql = "DELETE from tbl_user where firstname = '$hasil[firstname]'";
}
elseif($action == 'read')
{
	$sql = "SELECT * from `tbl_user`";
}

else {
	echo "ERROR ACTION";
	exit();
}
$conn = new mysqli("localhost","root","","billingdata");
if ($conn->connect_errno) {
  echo "Failed to connect to MySQL: " . $conn -> connect_error;
  exit();
}else{
  echo "Database connected. ";
}

if ($conn->query($sql) === TRUE) {
	echo "Query $action with syntax $sql suceeded !";

}
elseif ($conn->query($sql) === FALSE){
	echo "Error: $sql" .$conn -> error;
}
else
{
	$result = $conn->query($sql);
	if($result->num_rows > 0)

	{
		echo "<table id='tresult' class='table table-striped table-bordered'>";
		echo "<thead><th>Firstname</th><th>Lastname</th></th><th>Username</th></th><th>Email</th></th><th>Address</th><th>Address2</th><th>Name On Card</th><th>Credit Card Number</th><th>Card Expire</th><th>CVV</th><th>Your Photo</th></thead>";
		while($row = $result->fetch_assoc())
		{
			echo "<tr>
			<td>".$row['firstname']."</td>
			<td>".$row['lastname']."</td>
			<td>".$row['username']."</td>
			<td>".$row['email']."</td>
			<td>".$row['address']."</td>
			<td>".$row['address2']."</td>
			<td>".$row['ccname']."</td>
			<td>".$row['ccnumber']."</td>
			<td>".$row['ccexpire']."</td>
			<td>".$row['cvv']."</td>
			</tr>";
		}
		echo "</tbody>";
		echo "</table>";
	}
}
$conn->close();
?>
