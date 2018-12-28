<div class="main">
    <?php
require $_SERVER['DOCUMENT_ROOT'] . "/conn.php";
require_once "engine/base/functions.php";
$conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n" . $conn->error);
$page_array = getArrayDatabase("page");
$sql = "SELECT * FROM page ORDER BY position";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<form class='change-position'>";
    echo $row["name"];
    echo '<input type="hidden" name="id" value="' . $row["id"] . '">';
    echo "<select name='position'>";
    for ($i = 1; $i <= getColumnCount("page"); $i++) {
        echo "<option " . (($row["position"] == $i) ? 'selected="selected"' : "") . ">" . $i . "</option>";
    }
    echo "<select>";
    echo '<label for="visible">Viditelné</label>';
    echo "<input type='checkbox' name='visible' value='1'" . (($row["visible"] == 1) ? "checked" : "") . ">";
    if ($row["parent_id"] != null) {
        echo "Zařazeno " . $conn->query("SELECT name from page WHERE id=" . $row["parent_id"])->fetch_object()->name;
    }
    echo "</form>";
    echo "<form class='delete-page'>";
    echo "<input name='id' type='hidden' value='" . $row["id"] . "'>";
    echo "<input type='submit' value='Odstranit'>";
    echo "</form>";
}
?>

    <form id="add-page">
        <input type="text" name="page_name" placeholder="Jmeno">
        <input type="text" name="page_url" placeholder="Adresa">
        <?php
echo "<select name='parent_id'>";
echo "<option value='null'>...</option>";
for ($d = 0; $d < count($page_array); $d++) {
    echo '<option value="' . $page_array[$d]["id"] . '">' . $page_array[$d]["name"] . "</option>";
}
echo "</select>";
?>
        <input type="submit" value="Přidat stránku">
    </form>
    <form id="add-content">
        <input type="text" name="page_heading" placeholder="Nadpis">
        <textarea type="text" name="page_text" placeholder="Obsah"></textarea>
        <?php
echo "<select name='page_id'>";
for ($d = 0; $d < count($page_array); $d++) {
    echo '<option value="' . $page_array[$d]["id"] . '">' . $page_array[$d]["name"] . "</option>";
}
echo "</select>";
?>
        <input type="submit" value="Přidat blok">
    </form>
    <script>
        $('#add-page, #add-content').submit(function(event) {
            var ajaxRequest;
            event.preventDefault();
            ajaxRequest = $.ajax({
                url: $(this).attr("id"),
                data: $(this).serialize(),
                type: "post"
            })
            ajaxRequest.done(function(res) {
                if (res.indexOf("Succ" >= 0)) {
                    location.reload();
                    console.log(res);
                } else {
                    console.log(res);
                }
            })

        });
        $('.change-position').change(function(event) {
            var ajaxRequest;
            event.preventDefault();
            ajaxRequest = $.ajax({
                url: "position",
                data: $(this).serialize(),
                type: "post"
            });
            ajaxRequest.done(function(response) {
                if (response.indexOf("Succ") >= 0) {
                    location.reload();
                }
                console.log(response);

            });
        });

        $('.delete-page').submit(function(e) {
            e.preventDefault();
            var ajaxRequest;
            ajaxRequest = $.ajax({
                url: "delete_page",
                data: $(this).serialize(),
                type: "post"
            });
            ajaxRequest.done(function(response) {
                if (response.indexOf("Succ") >= 0) {
                    location.reload();
                }
                console.log(response);

            });


        })
    </script>

</div>