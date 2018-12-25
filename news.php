<?php 
    
    if(session_status()!=PHP_SESSION_ACTIVE) session_start();
    require "functions.php";
    function getLastId(){
        require($_SERVER['DOCUMENT_ROOT']."/conn.php");
        $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
        $sql = "SELECT id FROM news ORDER BY id DESC LIMIT 0, 1";
        $result = $conn->query($sql);
        if($result->num_rows > 0){
            $s = (string)$conn->query($sql)->fetch_object()->id;
            return $s;
        }
        else{
            return 0;
        }
    }

    if(isset($_SESSION["login"])){
        if ($_SESSION["login"] == TRUE){
            if(isset($_POST["title"])){
                require($_SERVER['DOCUMENT_ROOT']."/conn.php");
                $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
                $files_name = array();
                if(!empty($_FILES['picture'])){
                    for($i = 0; $i < count($_FILES['picture']); $i++){
                        $upload = $_SERVER['DOCUMENT_ROOT'].'/uploads/img/news/'.transliterateString(basename($_FILES['picture']['name'][$i]));
                        $upload_red = '/uploads/img/news/'.transliterateString(basename($_FILES['picture']['name'][$i]));
                        if(strpos($upload_red, '.png') || strpos($upload_red, '.jpg') || strpos($upload_red, '.jpg') || strpos($upload_red, '.svg')){
                            array_push($files_name, $upload_red);
                        }
                        if(move_uploaded_file($_FILES['picture']['tmp_name'][$i], $upload)) {
                            
                        } 
                    }
                }
                $files_name = implode(',', $files_name);
                $title = $_POST["title"];
                $subtitle = $_POST["subtitle"];
                $content = $_POST["content"];
                $url = strtolower(transliterateString($title))."_".getLastId();
                if(empty($_FILES['picture'])){
                    $sql = "INSERT INTO news (title, subtitle, content, url) VALUES ('$title', '$subtitle', '$content', '$url')";    

                }else{
                    $sql = "INSERT INTO news (title, subtitle, content, url, images) VALUES ('$title', '$subtitle', '$content', '$url', '$files_name')";
                }
                if ($conn->query($sql) === TRUE) {
                    echo "Succ";
                }
                else {
                    echo "Error updating record: " . $conn->error;
                    echo $sql;
                }
            }
        }
    }

    function render_news(){
        if(!isset($_GET["article"])){
            require($_SERVER['DOCUMENT_ROOT']."/conn.php");
            $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
            $sql = "SELECT * FROM news";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo '<article class="new col-xl-6">';
                echo "<h2>".$row["title"]."</h2>";
                echo "<h3>".$row["subtitle"]."</h3>";
                echo "<div>".$row["content"]."</div>";
                echo "<button class='show-article' data-id='".$row["url"]."'>Zobrazit</button>";
                echo "</article>";
                
            }
            ?>
            <script>
                $('.show-article').click(function(event){
                    event.preventDefault();
                    window.location.href="/novinky/"+ $(this).data("id");
                });
            </script>	
            <?php
        }

        if(isset($_GET["article"])){
            require($_SERVER['DOCUMENT_ROOT']."/conn.php");
            $conn = new mysqli($server, $user, $password, $db_name) or die("Connect failed: %s\n". $conn -> error);
            $id = $_GET["article"];
            $id = substr($id, -1)+1;
            $sql = "SELECT * FROM news WHERE id=$id";
            $result = $conn->query($sql);
            while($row = $result->fetch_assoc()){
                echo "<article>";
                echo "<h2>".$row["title"]."</h2>";
                echo "<h3>".$row["subtitle"]."</h3>";
                echo "<div>".$row["content"]."</div>";
                foreach(explode(',', $row["images"]) as $img){
                    echo '<img src="'.$img.'">';

                }
                echo "<article>";
            }
            echo '<a href="/novinky">Vsechny novinky</a>';
        
        }

        if(isset($_SESSION["login"])){
            if (($_SESSION["login"] == TRUE) && (!isset($_GET["article"]))){?>

                <button id="open-form" class="btn btn-primary">Pridat dalsi</button>
                
                <form method="post" id="form-new-image" enctype="multipart/form-data">
                    <input name="title" type="text" placeholder="Nadpis">
                    <input name="subtitle" type="text" placeholder="Podtitulek">
                    <textarea name="content" placeholder="Obsah"></textarea>
                    <input type="file" name="picture[]" multiple="">
                    <input type="submit" value="Přidat novinku" name="submit">
                </form>
            
            
                <script>
                    $('#open-form').click(function(){
                        $('#add-new').toggle();
                    });
                    
                    $("#form-new-image").submit(function(event){
                        event.preventDefault();
                        var formData = new FormData(this);
                        ajaxRequest= $.ajax({
                            url: 'engine/news.php',
                            type: 'POST',
                            data: formData,
                            cache: false,
                            contentType: false,
                            processData: false
                            
                        });
                        ajaxRequest.done(function(response){
                            if(response == "Succ"){
                                location.reload();
                            }else{
                                console.log(response);
                            }
                        });
                    });
                    $('.new').each(function(){
                        $form = $('<form class="delete-form"></form>').append('<input type="submit" value="" name="submitDel">');
                        var id = $(this).find('.show-article').data("id");
                        id = id.substring(id.length-1, id.length);
                        var inputPath = $("<input>").attr({
                                type: "hidden",
                                name: "id",
                                value: id
                            });
                        $(this).css('position', 'relative');
                        $form.append(inputPath);
                        $(this).append($form);
                    });
                    $('.delete-form').submit(function(event){
                        event.preventDefault();
                        var ajaxRequest;
                        var data = $(this).serialize();
                        ajaxRequest = $.ajax({
                            url: "engine/delete.php",
                            type: 'post',
                            data: data     
                        });  
                        
                        ajaxRequest.done(function(response){
                            if(response == "Succ"){
                                location.reload();
                            }
                            else{
                                console.log(response);
                            }
                        
                        });
                        
                    });
                </script>
            <?php
            }
        }
    }

?>