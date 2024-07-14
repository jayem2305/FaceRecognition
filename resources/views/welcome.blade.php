<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Face Recognition</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        #video-container {
            width: 200px; 
            height: 200px; 
            overflow: hidden;
            border-radius: 50%; 
            position: relative;
            border: 10px solid #0d6efd;
        }
        
        #video {
            width: 100%;
            height: 100%;
            object-fit: cover; /* Cover the container */
            transform: scaleX(-1);
            -webkit-transform: scaleX(-1); /* For Safari */
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="col-lg-12 text-center ">
        <div class="card position-absolute top-50 start-50 translate-middle"  style="width: 25rem;">
        <div class="card-body">
        <h1 >Scan Face</h1>
        <h6 id="status">Get ready to scan your face</h6>
        <div id="video-container" class="mx-auto">
            <video id="video" autoplay></video>
        </div>
        <h6>Face Scan Tips:</h6>
        <ul>
            <li>Make Sure there is enough light around</li>
            <li>Remove All Accessories (e.g: EyeGlasses, Earings, Contact lense etc.)</li>
            <li>Place your Face in the Frame</li>
            <li>Click Scan and wait a moment for Automatic Scanning</li>
        </ul>
        <button type="button" class="btn btn-outline-primary col-lg-12">Face Scan</button>
    </div>
    </div>
    </div>
    </div>
    <script>
        // Check if getUserMedia is supported
        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
            navigator.mediaDevices.getUserMedia({ video: true })
                .then(function(stream) {
                    // Attach the video stream to the video element
                    var video = document.getElementById('video');
                    video.srcObject = stream;
                    video.play();
                })
                .catch(function(error) {
                    console.error("Error accessing the camera: ", error);
                });
        } else {
            alert("getUserMedia is not supported in this browser.");
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
     document.querySelector('.btn').addEventListener('click', function() {
        document.getElementById('video-container').style.borderColor = '#ffc107'; // Yellow border for in-process
        document.getElementById('status').textContent = "Please Wait...";
        document.getElementById('status').style.color = '#000000'; 
        var video = document.getElementById('video');
        var canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        var context = canvas.getContext('2d');
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob(function(blob) {
            var formData = new FormData();
            formData.append('image', blob, 'face.jpg');
            
            fetch('/scan-face', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                    if (data.status === 'success') {
                        document.getElementById('video-container').style.borderColor = '#198754'; // Green border for success
                        document.getElementById('status').textContent = "Successfully Scanned";
                        document.getElementById('status').style.color = '#198754'; 
                    } else{
                        document.getElementById('video-container').style.borderColor = '#dc3545';
                        document.getElementById('status').textContent = "No face detected";
                        document.getElementById('status').style.color = '#dc3545'; 
                    }
                       
                   
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('video-container').style.borderColor = '#dc3545'; // Red border for error
                });
        });
    });
</script>

</body>
</html>