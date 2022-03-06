<html>
    <head>
        <title>2pyo create</title>
        <script>
        history.pushState(null, null, location.href); 
        window.onpopstate = function(event){ 
	        history.go(1); 
        };
        function copy_to_clipboard(){
            var copyText = document.getElementById("myInput");
            copyText.select();
            document.execCommand("Copy");
            console.log('Copied!');
        }
        </script>
    </head>
    <body>
        <p>create success</p>
        <?php
            echo '<input id="myInput" value="http://160.251.45.250/page/' . $argv[1] . '">';
            echo '<button onclick="copy_to_clipboard()">클립보드로 복사</button>';
        ?>
    </body>
</html>