

<?php include('config.php'); ?>
<table border="1">
<?php
$paring = 'SELECT * FROM info';
$valjund = mysqli_query($yhendus, $paring);
while($rida = mysqli_fetch_assoc($valjund)){
echo '<tr>
<td>'.$rida['nimi'].'</td>
<td>'.$rida['asukoht'].'</td>
<td>'.$rida['hinne'].'</td>
<td>'.$rida['id'].'</td>
<td><a href="'.$_SERVER['PHP_SELF'].'?delete_id='.$rida["id"].'">kustuta</a></td>
<td><a href="'.$_SERVER['PHP_SELF'].'?edit_id='.$rida["id"].'">muuda</a></td>
</tr>';
}
if(isset($_GET['delete_id'])){
$id = $_GET['delete_id'];
$kustuta_paring = "DELETE FROM info WHERE id='$id'";
$kustuta_valjund = mysqli_query($yhendus, $kustuta_paring);
echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$_SERVER['PHP_SELF'].'">';
}
?>
</table>
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
<label for="nimi">nimi:</label><br>
<input type="text" id="nimi" name="nimi"><br>
<label for="asukoht">asukoht:</label><br>
<input type="text" id="asukoht" name="asukoht"><br>
<label for="hinne">hinne:</label><br>
<input type="text" id="hinne" name="hinne"><br>
<label for="id">id:</label><br>
<input type="text" id="id" name="id"><br><br>
<input type="submit" name="submit" value="Lisa">
</form>
<?php
if (isset($_POST['submit'])) {
$nimi = $_POST['nimi'];
$asukoht = $_POST['asukoht'];
$hinne = $_POST['hinne'];
$id = $_POST['id'];
$lisamine_paring = $yhendus->prepare("INSERT INTO info (nimi, asukoht, hinne, id) VALUES (?, ?, ?, ?)");
$lisamine_paring->bind_param("ssii", $nimi, $asukoht, $hinne, $id);
$lisamine_paring->execute();
$lisamine_paring->close();
echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$_SERVER['PHP_SELF'].'">';
}
if (isset($_GET['edit_id'])) {
$edit_id = $_GET['edit_id'];
$result = mysqli_query($yhendus, "SELECT * FROM info WHERE id=$edit_id");
$asukoht = mysqli_fetch_assoc($result);
?>
<h2>Muuda albumit</h2>
<form method="post" action="">
<input type="hidden" name="id" value="<?php echo $asukoht['id']; ?>">
<input type="text" name="nimi" value="<?php echo $asukoht['nimi']; ?>" required>
<input type="text" name="asukoht" value="<?php echo $asukoht['asukoht']; ?>" required>
<input type="number" name="hinne" value="<?php echo $asukoht['hinne']; ?>" required>

<button type="submit" name="edit">Muuda asja</button>
</form>


<?php
}
if (isset($_POST['edit'])) {
$id = $_POST['id'];
$nimi = $_POST['nimi'];
$asukoht = $_POST['asukoht'];
$hinne = $_POST['hinne'];
$id = $_POST['id'];
$muutmine_paring = "UPDATE info SET nimi='$nimi', asukoht='$asukoht', hinne='$hinne', id='$id' WHERE id='$id'";
mysqli_query($yhendus, $muutmine_paring);
echo '<META HTTP-EQUIV="Refresh" Content="0; URL='.$_SERVER['PHP_SELF'].'">';
}
mysqli_close($yhendus);
?>

<form action="07_logout.php" method="post">
<input type="submit" name="logout" value="Logi välja">
</form>