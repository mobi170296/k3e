<!DOCTYPE html>
<html>
    <head>
        <title></title>
        <!--<script src="/scripts/jquery-3.3.1.min.js"></script>-->
        <script src="/scripts/mj.js"></script>
    </head>
    <body>
        <div id="id" data-action="add"></div>
        <div class="cls"></div>
        <div class="cls"></div>
        <div class="cls"></div>
        <div class="cls"></div>
        <div class="cls">
            <a>A tag</a>
        </div>
        <input/>
    </body>
    <script>
        var source = new EventSource("/sse.php");
        source.onmessage = function(event) {
            $('#id').html(event.data);
        };
    </script>
</html>