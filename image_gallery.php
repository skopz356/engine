
<?php
require "functions.php";

    define("UPLOADS_READ", './uploads/img/');
    define("UPLOADS_WRITE", $_SERVER['DOCUMENT_ROOT'].'/uploads/img/');
    if(isset($_SESSION["login"])){
        if($_SESSION["login"] == TRUE){
            
            if(!empty($_FILES['picture']))
            {
                for($i = 0; $i < count($_FILES['picture']); $i++){
                    $upload = UPLOADS_WRITE.transliterateString(basename($_FILES['picture']['name'][$i]));
                    
                    if(move_uploaded_file($_FILES['picture']['tmp_name'][$i], $upload)) {
                    echo "<script>  window.location.replace(window.location.href); </script>";
                    } 
                }
            }
        }
    }
        
    //writing all images
    function writeAllImages(){
        $fileList = glob(UPLOADS_READ.'*.{jpg,gif,png}', GLOB_BRACE);
        ?>
        <div class="row gallery">
        <?php

        foreach($fileList as $filename){
            $name= substr($filename ,-strripos($filename, '.'));
            $name = substr($name,strripos($name, '/')+1 );
            echo '<div class="col-lg-4" data-src="'.$filename.'">'."\xA";
            echo '<img src="'.$filename.'" class="'.'img-fluid'.'">', '<br>'."\xA"; 
            echo '</div>'."\xA";
        }
        if(session_status()!=PHP_SESSION_ACTIVE) session_start();
        if(!(isset($_SESSION["login"]))){?>

            <script type="text/javascript">
            $(document).ready(function() {
                $('.gallery').lightGallery({
                    thumbnail:true,
                }); 
            });
            </script>
        <?php
        }

        ?>

        </div>
        <?php

        if (isset($_SESSION["login"])){
            if ($_SESSION["login"] == TRUE){ ?>
                <buttom id="showGallery" class="btn btn-primary">Pridat dalsi</button>
                <form method="post" id="form-gallery" enctype="multipart/form-data">
                    <input type="file" name="picture[]" multiple="">
                    <input type="submit" value="Nahrát obrázek" name="submit">
                </form>
                <script>
                    $('#form-gallery').submit(function (even){
                        click(document.querySelector('#form-gallery'));
                        event.preventDefault();
                        var ajaxRequest;
                        var data = new FormData($(this));
                        console.log(data);
                        ajaxRequest= $.ajax({
                            url: 'engine/image_gallery.php',
                            type: 'POST',
                            data: data,
                            cache: false,
                            dataType: 'json',
                            processData: false, 
                            contentType: false
                        });
                    });

                    $('.img-fluid').each(function(){
                        var delBtn = "<form method='post' class='delete-form'><input type='submit' name='submitDel' value=''></form>";
                        var src = $(this).attr('src').substring(1);
                        var inputPath = $("<input>").attr({
                                type: "hidden",
                                name: "path",
                                value: src
                            });
                        $(this).css('position', 'relative');
                        $(this).after(delBtn);
                        $(this).next().append(inputPath);
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
