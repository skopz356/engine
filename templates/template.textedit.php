

        <script>
$(".editable").each(function(i){
        var input = $("<input>").attr({
            type: "hidden",
            name: "id",
            value: $(this).attr("value")
        });
        var inputPage = $("<input>").attr({
            type: "hidden",
            name: "page",
            value: '<?php echo getPageName() ?>'
        });
        var parent = "text";
        if ($(this).parent().is("h1,h2, h3, h4, h5, h6")){
        parent = "heading"
        }
        var inputType = $("<input>").attr({
            type: "hidden",
            name: "type",
            value: parent 
        });
        var s = document.getElementsByClassName("textareedit");
        //$(this).after("<form method='post'class='change-form'><textarea class='textareedit' name='editarea' ></textarea><input value='Odeslat'type='submit'><form>");
        var textarea = $('<textarea name="editarea"></textarea>').text($(this).html());
        $(this).wrap("<form method='post'class='change-form'></form>")
        $(this).css('display', 'none');
        $(this).after('<input type="submit" value="Upravit">');
        $(this).after(textarea);


        textarea = $(this).find("~ textarea");
        textarea.summernote();
        //var x = s[i];
        //x.value = $(this).text();
        var form = $(this).closest('form');
        form.append(input, inputPage, inputType);
        

    });
    $(".change-form").submit(function(event){
        event.preventDefault();
        var data = $(this).serialize();
        var ajaxRequest;
        ajaxRequest= $.ajax({
            url: "textedit",
            type: "post",
            data: data
        });
        ajaxRequest.done(function(res){
            if(res.indexOf("Succ" >= 0)){
            var succ = $("body").append("<div class='succSave'>Uspesne</div>");
            location.reload();
        }
        console.log(res);
        });

    });

        



        </script>





        
		

    