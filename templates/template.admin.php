<script>
    $('body').toggleClass('is-active');
    $("#login-form").submit(function(event) {
        event.preventDefault();
        var ajaxRequest;
        var data = $(this).serialize();
        ajaxRequest= $.ajax({
            url: "admin",
            type: "post",
            data: data
        });
        ajaxRequest.done(function (response){

            if(response.indexOf("Succ") >= 0){
                $("#login-form").hide();
                location.reload();
            }else if(response.indexOf("Bad")  >= 0){
                $(".error").show();
            }
        console.log(response);
        });
    });
</script>