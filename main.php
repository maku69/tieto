<?php include('config.php'); ?>
<table border="1">
<?php
$items_per_page = 10;
$current_page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start_index = ($current_page - 1) * $items_per_page;
$search_query = isset($_GET['search']) ? mysqli_real_escape_string($yhendus, $_GET['search']) : '';
$search_condition = $search_query ? "WHERE nimi LIKE '%$search_query%' OR asukoht LIKE '%$search_query%'" : '';
$paring = "SELECT *, AVG(hinne) AS hinne FROM info $search_condition GROUP BY id LIMIT $start_index, $items_per_page";
$valjund = mysqli_query($yhendus, $paring);
while ($rida = mysqli_fetch_assoc($valjund)) {
    echo '<tr>
    <td>' . $rida['nimi'] . '</td>
    <td>' . $rida['asukoht'] . '</td>
    <td>' . $rida['hinne'] . '</td>
    <td>' . $rida['id'] . '</td>
    <td><a href="'.$_SERVER['PHP_SELF'].'?view_id='.$rida['id'].'">Vaata ligemalt</a></td>
    </tr>';
}
?>
</table>
<form method="GET" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    <input type="text" name="search" placeholder="Otsi...">
    <button type="submit">Otsi</button>
</form>
<div>
<?php
$total_items_query = mysqli_query($yhendus, "SELECT COUNT(*) AS total_items FROM info $search_condition");
$total_items = mysqli_fetch_assoc($total_items_query)['total_items'];
$total_pages = ceil($total_items / $items_per_page);
for ($i = 1; $i <= $total_pages; $i++) {
echo '<a href="'.$_SERVER['PHP_SELF'].'?page='.$i.'&search='.$search_query.'">'.$i.'</a> ';
}
?>
</div>
<form action="07_login.php" method="post">
    <input type="submit" value="Login" name="logout">
</form>
<form action="07_login.php" method="post">
    <input type="submit" value="Login" name="logout">
</form>
<?php
if (isset($_GET['view_id']) && is_numeric($_GET['view_id'])) {
$view_id = $_GET['view_id'];
$view_query = "SELECT * FROM info WHERE id = $view_id";
$view_result = mysqli_query($yhendus, $view_query);
$view_row = mysqli_fetch_assoc($view_result);
if ($view_row) {
echo '<h2>Info</h2>';
echo '<p>Nimi: ' . $view_row['nimi'] . '</p>';
echo '<p>Asukoht: ' . $view_row['asukoht'] . '</p>';
echo '<p>Hinne: ' . $view_row['hinne'] . '</p>';
echo '<p>kiri: ' . $view_row['kiri'] . '</p>'; 
echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?view_id='.$view_id.'">
<label for="hinne">Lisa hinne (1-5): </label>
<input type="number" name="hinne" min="1" max="5" required>
<button type="submit">Lisa hinne</button>
</form>';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['hinne'])) {
$new_rating = intval($_POST['hinne']);
if ($new_rating >= 1 && $new_rating <= 5) {
$update_query = "UPDATE info SET hinne = ((hinne + $new_rating) / 2) WHERE id = $view_id";
mysqli_query($yhendus, $update_query);
}
}
echo '<h3>Kommentaarid</h3>';
$comments_query = "SELECT * FROM comments WHERE item_id = $view_id ORDER BY created_at DESC";
$comments_result = mysqli_query($yhendus, $comments_query);
while ($comment_row = mysqli_fetch_assoc($comments_result)) {
echo '<div>';
echo '<p><strong>' . $comment_row['name'] . '</strong> (' . $comment_row['created_at'] . '): ' . $comment_row['comment'] . '</p>';
echo '</div>';
}
echo '<h3>Lisa kommentaar</h3>';
echo '<form method="POST" action="'.$_SERVER['PHP_SELF'].'?view_id='.$view_id.'">
<label for="name">Nimi: </label>
<input type="text" name="name" required>
<label for="comment">Kommentaar: </label>
<textarea name="comment" required></textarea>
<button type="submit">Lisa kommentaar</button>
</form>';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['name']) && isset($_POST['comment'])) {
$name = mysqli_real_escape_string($yhendus, $_POST['name']);
$comment = mysqli_real_escape_string($yhendus, $_POST['comment']);
$insert_comment_query = "INSERT INTO comments (item_id, name, comment) VALUES ($view_id, '$name', '$comment')";
mysqli_query($yhendus, $insert_comment_query);
}
}
}
mysqli_close($yhendus);
?>
