<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Simple widget usage</title>
    <!-- <script src='includes/embedded.js'></script> -->
    <script src='includes/clicksign.js'></script>
</head>

<body>

    <div id='signature-box'></div>

    <input id='request_signature_key' value="d0e1030f-4e20-4563-9540-c072ee0e5b89" />
    <input type='button' value='Load' onclick='run()' />
    <div id='container' style='height: 600px'></div>

    <script type='text/javascript'>
        var widget,
            input = document.getElementById('request_signature_key');

        function run() {
            var request_signature_key = input.value;

            if (widget) {
                widget.umount();
            }

            widget = new Clicksign(request_signature_key);

            widget.endpoint = 'https://sandbox.clicksign.com';
            widget.origin = 'https://0.0.0.0:8085/index.php';
            widget.mount('container');

            widget.on('loaded', function(ev) {
                console.log('loaded!');
            });
            widget.on('signed', function(ev) {
                console.log('signed!');
            });
        };
    </script>
</body>

</html>
