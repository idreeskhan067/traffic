<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Warden Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link 
        rel="stylesheet" 
        href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" 
        integrity="sha256-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC0=" 
        crossorigin=""
    />
    <style>
        #wardenMap {
            height: 400px;
            width: 100%;
            margin-top: 30px;
            border: 2px solid #ddd;
            border-radius: 10px;
        }
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
    </style>
</head>
<body>

    <h2>Welcome, Warden!</h2>
    <p>Your current location is shown on the map below:</p>

    <div id="wardenMap"></div>

    <script 
        src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" 
        integrity="sha256-o9N1j7kGRtTMTzN9LzLTA5MVMlG1S3EKkS7CcHkTjPs=" 
        crossorigin="">
    </script>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            var map = L.map('wardenMap').setView([30.3753, 69.3451], 5); // Default to Pakistan

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 18,
            }).addTo(map);

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    map.setView([lat, lng], 15);

                    L.marker([lat, lng])
                        .addTo(map)
                        .bindPopup("Your current location")
                        .openPopup();

                    // Optional: Send to backend
                    fetch("/api/save-location", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ lat, lng })
                    });

                }, function () {
                    alert("Location access denied.");
                });
            } else {
                alert("Geolocation not supported.");
            }
        });
    </script>

</body>
</html>
