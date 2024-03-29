<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="UTF-8">
    <title>RawBT Integration Demo</title>
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <style>
        html {
            background-color: grey;
            padding: 32px;
        }

        body {
            max-width: 640px;
            margin: 0 auto;
            padding: 32px;
            background-color: white;
        }

        button {
            background-color: #6e89ff;
            color: white;
            padding: 16px;
            border: none;
        }

        pre {
            background-color: #f0f0f0;
            border-left: #6e89ff solid 3px
        }

        p {
            text-align: right;
        }

        a {
            color: #6e89ff;
            text-decoration: none;
        }

        a:before {
            content: '\1F855';
            margin-right: 4px;
        }
    </style>
    <script>
        // for php demo call
        function ajax_print(url, btn) {
            b = $(btn);
            b.attr('data-old', b.text());
            b.text('wait');
            $.get(url, function(data) {
                window.location.href = data; // main action
            }).fail(function() {
                alert("ajax error");
            }).always(function() {
                b.text(b.attr('data-old'));
            })
        }
    </script>

</head>

<body>
    <img src="resources/rawbtlogo.png" alt="black & white picture">
    <h1>RawBT Integration Demo</h1>
    <pre>

    window.location.href = ajax_backend_data;

</pre>
    <br />
    <button onclick="ajax_print('/cetak',this)">RECEIPT</button>

    <p><a href="https://rawbt.ru/">Visit RawBT site</a></p>
</body>

</html>
